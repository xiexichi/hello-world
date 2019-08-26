<template>
	<div class="container">
		<!-- 已经登录 -->
		<div class="user-head" v-if="is_login">
			<div class="user-avatar">
				<img src="http://img.25miao.com/3/2017120122250917738.jpg!w200" mode="aspectFit" class="user-avatar-image">
			</div>
			<div class="user-info">
				<div class="user-name">昵称</div>
				<div class="user-level">
					<van-tag type="success">VIP会员</van-tag>
				</div>
			</div>
			<div class="user-qrcode">
				<van-icon name="qr" />
			</div>
		</div>
		<!-- 未登录 -->
		<div class="user-head" v-else>
			<div class="user-avatar">
				<open-data type="userAvatarUrl" class="user-avatar-image"></open-data>
			</div>
			<button class="btn-text" open-type="getUserInfo" @getuserinfo="getUserInfo">
				<div class="user-info">
						<div class="user-name">
							<open-data type="userNickName"></open-data>
						</div>
						<text class="user-bindtip">点击绑定手机号成为会员</text>
				</div>
				<van-icon name="arrow" class="arrow-right" />
			</button>
		</div>
		
		<div class="user-list">
			<van-cell-group custom-class="mt--2" class="mt--2">
				<van-cell title="线上订单" icon="column" is-link />
			<van-cell title="门店订单" icon="shop" is-link />
			</van-cell-group>
			<van-cell-group custom-class="mt--1" class="mt--1">
				<van-cell title="账户余额" icon="gold-coin" is-link>
						<j-price value="50" />
				</van-cell>
				<van-cell title="优惠券" icon="coupon" is-link>
					<van-tag round type="danger">2张可用</van-tag>
				</van-cell>
				<van-cell title="我的收藏" icon="star" is-link />
				<van-cell title="浏览历史" icon="clock" is-link />
			</van-cell-group>
			<van-cell-group custom-class="mt--1" class="mt--1">
				<van-cell title="联系客服" icon="service" is-link />
				<van-cell title="设置" icon="setting" is-link />
			</van-cell-group>
		</div>
	</div>
</template>

<script>
import USER from '@/utils/user'
import UTIL from '@/utils/util'

export default {
	data () {
		return {
			user: {},
			is_login: false
		}
	},
	methods: {
		// 获取微信用户信息
		getUserInfo (e) {
			// 保存用户信息
			if (UTIL.empty(e.mp.detail.userInfo) === false) {
				USER.setUserInfo(e.mp.detail.userInfo)
				uni.navigateTo({
					url: '/pages/public/login?gourl=close'
				})
			} else {
				uni.showToast({
					title: '微信授权失败',
					icon: 'none'
				})
			}
		},
		// 商品列表
		autologin () {
		  let _this = this
			
			uni.showLoading({
				title: '加载中'
			})
			
			// uni.login(OBJECT)
		  // 自动登录
		  _this.$http.post('my/autoLogin', {type: 'weixin'}).then(res => {
		    uni.hideLoading()
				uni.stopPullDownRefresh()
				if (res.code === 0) {
					_this.user = res.data
				}
		  })
		}
	},
	onLoad () {
		this.is_login = USER.checkLogin()
	}
}
</script>

<style lang="less">
page{background-color:#eeeeee;}
.arrow-right{color:#999;}
.btn-text{flex:2;display:flex;justify-content:space-between;align-items:center;}
.user{
	&-head{
		background-color:#FFFFFF;padding:20upx 30upx;display:flex;align-items:center;justify-content:space-between;
	}
	&-avatar{width:120upx;height:120upx;overflow:hidden;background:#EEEEEE;}
	&-avatar-image{width:120upx;height:120upx;}
	&-info{flex:2;margin:0 30upx;}
	&-name{font-size:30upx;color:#000000;margin-bottom:8upx;font-weight:700;}
	&-bindtip{color:red;}
	&-qrcode{
		.van-icon{font-size:80upx;}
	}
}
</style>
