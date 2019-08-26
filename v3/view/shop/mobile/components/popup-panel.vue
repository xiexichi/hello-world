<template>
	<view class="popup">
		<view class="uni-mask" v-show="show" @click="hidePopup"></view>
		<view class="uni-popup uni-popup-bottom" :hidden="!show">
			<view class="popup-title solid-bottom" v-if="title">
				<view class="popup-title-name">{{ title }}</view>
				<view class="popup-title-icon" @click="hidePopup"><van-icon name="cross" /></view>
			</view>
			<view class="popup-content">
				<slot></slot>
			</view>
			<view class="popup-btn" @click="hidePopup" v-if="button">{{button}}</view>
		</view>
	</view>
</template>

<script>
export default {
	name: 'popup-panel',
  props: {
		title: {
			type: String,
			default: ''
		},
		button: {
			type: String,
			default: ''
		},
		show: {
			type: Boolean,
			default: false
		}
  },
	methods: {
		hidePopup (e) {
			this.$emit('toggle', e)
		}
	}
}
</script>

<style lang="less">
.popup {
	.uni-mask {
		position: fixed;
		z-index: 998;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		background-color: rgba(0, 0, 0, .3);
	}
	.uni-popup {
		position: fixed;
		z-index: 999;
		background-color: #ffffff;
		box-shadow: 0 0 30upx rgba(0, 0, 0, .1);
	}
	.uni-popup-bottom {
		left: 0;
		bottom: 0;
		width: 100%;
		border-top-left-radius:16upx;
		border-top-right-radius:16upx;
	}
	&-title{
		line-height:100upx;height:100upx;display:flex;padding:0 30upx;align-items:center;justify-content:space-between;
		&-name{font-size:32upx;color:#000;font-weight:600;}
		&-icon{color:#555;font-size:40upx;}
	}
	&-btn{line-height:100upx;height:100upx;text-align:center;}
}
</style>