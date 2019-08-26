<template>
	<div class="container login">
		<image src="https://img.25miao.com/1806/24551523491a3b48703e5747bc1a95ab.png!w800" mode="widthFix" class="login-banner"></image>
		<button class="btn btn-primary" open-type="getPhoneNumber" @bindgetphonenumber="getPhoneNumber">
			<uni-icon type="weixin" size="32upx" /> 微信用户一键登录
		</button>
		<div class="login-other">
			<navigator url="/pages/public/register" class="pt--1 pb--1">输入手机号码登录/注册</navigator>
		</div>
	</div>
</template>

<script>
import {uniIcon} from "@dcloudio/uni-ui"
import USER from '@/utils/user'
import HEPLER from '@/utils/helper'

export default {
	components: {uniIcon},
	
	data () {
		return {
			user: {},
			is_login: false
		}
	},
	methods: {
		// 获取微信用户手机号码
		getPhoneNumber (e) {
			console.log(e)
		}
	},
	onLoad () {
		this.is_login = USER.checkLogin()
		if (this.is_login) {
			HEPLER.goto(USER.login_backurl)
		}
	}
}
</script>

<style lang="less">
page{background-color:#eeeeee;}
.login{
	padding:30upx;
	&-banner{display:block;width:100%;margin:40upx auto 80upx auto;}
	&-other{text-align:center;margin-top:20upx;}
}
</style>
