<template>
	<div class="container register">
		<div class="register-title">
			请输入手机号码
		</div>
		<div class="register-tips">
			绑定手机号专享会员福利，可查询线上线下订单
			<van-button type="info" size="small" custom-class="mt--2" class="mt--2" plain>使用电子邮箱登入</van-button>
		</div>
		<div class="register-box">
			<view class="cu-form-group">
				<view class="title" @click="showCountryCode">+{{country_codes[countryIndex]}} <van-icon name="arrow-down" custom-class="arrow-down" class="arrow-down" /></view>
				<input placeholder="请输入手机号码" type="number" @input="setInput" v-model="phone" :focus="true"></input>
				<view class="cu-capsule" v-show="phone" @click="phone=''">
					<van-icon name="clear" color="#c9c9c9" size="38upx" />
				</view>
			</view>
			<van-button type="primary" custom-class="mt--3" class="mt--3" :disabled="disabled" @click="next" block>下一步</van-button>
		</div>
	</div>
</template>

<script>
export default {
	data () {
		return {
			phone: '15218938652',
			countryIndex: 0,
			country_codes: ['86', '00852', '00853']
		}
	},
	computed: {
		disabled () {
			return (this.phone == '' || this.phone.length != 11) ? true : false
		}
	},
	methods: {
		setInput (e) {
			this.phone = e.detail.value
		},
		// 下一步
		next () {
			let _this = this
			const phone = _this.phone || ''
			const countryCode = _this.country_codes[_this.countryIndex] || ''
			
			// 验证手机号
			if (phone == '') {
				uni.showToast({
					title: '请输入手机号',
					icon: 'none'
				})
				return false
			}
			
			// 验证后回调方法
			const _callback = function (signType) {
				// 发送验证码
				_this.$http.post('message/phoneCode', {phone: phone, countryCode: countryCode}).then(res => {
					if (res.code === 0) {
						// 下一步
						uni.navigateTo({
							url: '/pages/public/verify?signType=' + signType + '&phone=' + phone + '&countryCode=' + countryCode
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
			
			// 检查是否已经注册
			uni.showLoading({
				title: '验证中'
			})
			let signType = 'register'	// 登录or注册
			_this.$http.post('my/checkIsRegister', {account: phone}).then(res => {
				if (res.code === 0) {
					if (res.data.is_register == 1) {
						signType = 'login'
					}
					_callback (signType)
				}
		  })
		},
		// 弹出国家代码选择层
		showCountryCode () {
			let _this = this
			uni.showActionSheet ({
				itemList: _this.country_codes,
				success: function (res) {
					_this.countryIndex = res.tapIndex
				},
				fail: function (res) {
					console.log(res.errMsg)
				}
			})
		}
	}
}
</script>

<style lang="less">
page{background-color:#eeeeee;}
.arrow-down{position:relative;top:3upx;margin-left:6upx;font-size:24upx;}
.register{
	padding:30upx;
	&-title{font-size:42upx;color:#000000;margin-top:50upx;}
	&-tips{color:#999999;font-size:28upx;margin-top:10upx;}
	&-box{margin-top:160upx;}
}
</style>
