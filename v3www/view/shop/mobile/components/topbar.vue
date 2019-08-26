<template>
  <div class="topbar">
    <img src="//api.25boy.cn/Public/images/logo.png" class="logo">
		<van-icon name="scan" class="icon-scan" />
    <van-search placeholder="搜索商品" v-model="value" show-action use-action-slot @search="onSearch">
      <van-icon name="contact" class="icon-contact" slot="action" @click="touser" />
    </van-search>
  </div>
</template>

<script>
export default {
  data () {
    return {
      value: ''
    }
  },
  methods: {
    onSearch (search) {
      window.location.href = '//m.25boy.cn/?m=category&a=search&k=' + search
    },
    touser () {
      window.location.href = '//m.25boy.cn/?m=account'
    }
  }
}
</script>

<style lang="less">
.topbar{
  padding-left:60upx;position:relative;background-color:#f2f2f2;width:100%;box-sizing:border-box;position:fixed;top:0;z-index:50;
  .logo{width:60upx;height:60upx;position:absolute;left:12upx;top:12upx;}
  .van-search{padding:14upx 0 14upx 30upx;z-index:1;}
  .van-search__action{line-height:normal;padding:0 30upx;}
  .icon-contact{font-size:46upx;color:#444;}
  .icon-scan{position:absolute;top:20upx;right:115upx;font-size:40upx;width:40upx;height:40upx;color:#444;z-index:2;}
}
</style>