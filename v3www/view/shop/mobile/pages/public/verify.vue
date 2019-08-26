<template>
	<div class="container register">
		<div class="register-title">
			请输入验证码
		</div>
		<text class="register-tips">
			验证码已发送至手机
			{{phone}}
			请设置登录密码，至少6位字符，不能使用特殊特号
		</text>
		<div class="register-box">
			<view class="cu-form-group">
				<view class="title">验证码</view>
				<input placeholder="请输入验证码" type="number" @input="setInput" v-model="code" data-name="code"></input>
				<button class="cu-btn line-gray sm" @click="getPhoneCode">
					<uni-countdown :show-day="false" :show-minute="false" :show-hour="false" :show-colon="false" :second="second" splitor-color="gray" border-color="#ffffff" @timeup="second = 0" v-if="second > 0" />
					<text v-else>重新获取</text>
				</button>
			</view>
			<view class="cu-form-group" v-if="signType == 'register'">
				<view class="title">密码</view>
				<input placeholder="设置登录密码" type="text" @input="setInput" v-model="password" data-name="password"></input>
			</view>
			
			<van-button type="primary" custom-class="mt--3" class="mt--3" block @click="doneSign">完成</van-button>
		</div>
	</div>
</template>

<script>
import USER from '@/utils/user'
import {uniCountdown} from "@dcloudio/uni-ui"

export default {
	components: {uniCountdown},
	
	data () {
		return {
			phone: '',
			password: '',
			signType: '',
			code: '',
			second: 60
		}
	},
	methods: {
		setInput (e) {
			const name = e.currentTarget.dataset.name
			this[name] = e.detail.value
		},
		// 完成
		doneSign () {
			if (this.signType === 'login') {
				this.doLogin()
			} else {
				this.doRegister()
			}
		},
		// 完成登入
		doLogin () {
			let _this = this
			const userInfo = USER.userInfo || []
			const backurl = USER.login_backurl || '/pages/users/index'
			// 请求参数
			let params = {
				msgcode: _this.code,
				phone: _this.phone,
				image_url: userInfo.avatarUrl || '',
				social_name: userInfo.nickName || ''
			}
			uni.showLoading({
				title: '登录中'
			})
			_this.$http.post('user/fastLogin', params).then(res => {
				uni.hideLoading()
				if (res.code === 0) {
					_this.$store.commit('login', res.rs)
					uni.showToast({
						title: '登录成功',
						icon: 'success',
						duration: 2000
					})
					setTimeout (function(){ 
						_this.$helper.goto(backurl)
					}, 2000)
				} else if (res.msg === 'register') {
					this.doRegister()
				} else {
					uni.showToast({
						title: res.msg,
						icon: 'none'
					})
				}
			})
		},
		// 完成注册
		doRegister () {
			let _this = this
			const userInfo = USER.userInfo || []
			// 请求参数
			let params = {
				msgcode: _this.code,
				phone: _this.phone,
				password: _this.password,
				image_url: userInfo.avatarUrl || '',
				social_name: userInfo.nickName || ''
			}
			uni.showLoading({
				title: '注册中'
			})
			_this.$http.post('user/fastRegister', params).then(res => {
				uni.hideLoading()
				if (res.code === 0) {
					_this.$store.commit('login', res.rs)
					uni.showToast({
						title: '注册成功',
						icon: 'success',
						duration: 2000
					})
					setTimeout (function(){ 
						uni.reLaunch({
							url: '/pages/users/index'
						})
					}, 2000)
				} else if (res.msg === 'register') {
					this.doRegister()
				} else {
					uni.showToast({
						title: res.msg,
						icon: 'none'
					})
				}
			})
		},
		// 重新获取验证码
		getPhoneCode () {
			let _this = this
			const phone = this.phone || ''
			if (this.second > 0) return false
			// 发送验证码
			_this.$http.post('message/phoneCode', {phone: phone, countryCode: _this.countryCode}).then(res => {
				if (res.code === 0) {
					_this.second = 60
					uni.showToast({
						title: '发送成功',
						icon: 'success'
					})
				} else {
					uni.showToast({
						title: res.msg,
						icon: 'none'
					})
				}
				uni.hideLoading()
			})
		}
	},
	onLoad (options) {
		this.phone = options.phone || ''
		this.signType = options.signType || ''
		this.countryCode = options.countryCode || ''
	}
}
</script>

<style lang="less">
page{background-color:#eeeeee;}
.register{
	padding:30upx;
	&-title{font-size:42upx;color:#000000;margin-top:50upx;}
	&-tips{color:#999999;font-size:28upx;margin-top:10upx;display:block;}
	&-box{margin-top:160upx;}
}
</style>
