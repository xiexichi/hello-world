<template>
  <view class="empty">
    <view class="empty__box">
      <van-icon class-prefix="myicon" custom-class="empty__icon" class="empty__icon" v-if="icon" :name="icon" :size="iconsize" />
      <view class="empty__text" v-if="title">{{title}}</view>
      <view class="empty__tip" v-if="tip">{{tip}}</view>
      <van-button v-if="button" @click="emitEmptyTap" type="primary" custom-class="empty__btn" class="empty__btn">{{button}}</van-button>
    </view>
  </view>
</template>

<script>
  export default {
		name: 'j-empty',
    props: {
      icon: {
        type: String,
        default: ''
      },
      title: {
        type: String,
        default: ''
      },
      tip: {
        type: String,
        default: ''
      },
      button: {
        type: String,
        default: ''
      },
      iconsize: {
        type: String,
        default: '300rpx'
      }
    },
    data() {
    	return {}
    },
    methods: {
      emitEmptyTap: function emitEmptyTap(event) {
        var detail = event.detail;
        var option = {};
        this.$emit('emptytap', detail, option);
      }
    }
  }
</script>

<style>
 .empty {
     position: relative;
     display: block;
     width: 100%;
     height: 0;
     padding-bottom: 100%;
     overflow: hidden
 }
 .empty__box {
     position: absolute;
     display: flex;
     top: 0;
     bottom: 0;
     left: 0;
     right: 0;
     flex-direction: column;
     justify-content: center;
     align-items: center
 }
 .empty__btn {
     min-width: 228rpx;
     margin-top: 30rpx;
 }
 .empty__icon {
     text-align:center;
     font-size:300rpx;
     color:rgba(0,0,0,0.3);
 }
 .empty__text {
     margin-top: 30rpx;
     color: #666;
     font-size: 32rpx
 }
 .empty__tip {
     margin-top: 20rpx;
     color: #999;
     font-size: 28rpx
 }
</style>