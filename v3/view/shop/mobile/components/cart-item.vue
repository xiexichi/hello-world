<template>
	<view class="cart-item" v-if="item">
		<view class="cart-item-checkbox">
			<!-- <checkbox class="round red"></checkbox> -->
		</view>
		<navigator :url="'/pages/products/index?id=' + item.product_id">
			<image class="cart-item-image" mode="aspectFit" :src="item.thumb + '!w200'"></image>
		</navigator>
		<view class="cart-item-detail">
			<view class="cart-item-title van-multi-ellipsis--l2">{{item.product_name}}</view>
			<view class="cart-item-spec">{{item.sku_sn}}, {{item.color_prop}} {{item.size_prop}}</view>
			<view class="cart-item-price">
				<j-price :value="item.re_price" icon="sub" custom-class="mr--1" class="mr--1" />
				<j-price :value="item.product_price" icon="sub" status="del" custom-class="price-del mr--1" class="price-del mr--1" v-if="item.re_price < item.product_price" />
				<view class="cart-item-quantity" v-if="!showNumberbox">x {{item.quantity}}</view>
			</view>
			<view class="cart-item-numberbox" v-if="showNumberbox">
				<j-numberbox :value="item.quantity" min="1" @change="onChangeQiantity" :id="item.cart_id"></j-numberbox>
			</view>
		</view>
	</view>
</template>

<script>
import Jprice from '../components/j-price'
import Jnumberbox from '../components/j-numberbox'

export default {
	components: {
		'j-price': Jprice,
		'j-numberbox': Jnumberbox
	},
  props: {
    item: Object,
		showNumberbox: {
			type: Boolean,
			default: false
		}
  },
	methods: {
		onChangeQiantity (e) {
			this.$emit('change', e)
		}
	}
}
</script>

<style lang="less">
.cart{
	&-item{
		display:flex;align-items:flex-start;justify-content:space-between;position:relative;color:#666666;padding-top:20upx;padding-bottom:20upx;border-top:1px solid #eeeeee;
		&-checkbox{display:flex;align-items:center;margin-right:20upx;}
		&-image{width:150upx;height:150upx;display:block;overflow:hidden;}
		&-detail{padding-left:20upx;position:relative;flex:2;}
		&-quantity{margin-left:10upx;display:inline-block;}
		&-numberbox{position:absolute;right:0;bottom:0;}
		&-title{color:#333333;font-size:30upx;}
		&-spec{color:#999999;margin-top:10upx;margin-bottom:10upx;;}
	}
}
</style>