<template>
	<view class="complete">
		<view class="complete-total">
			<view class="complete-total-title">您需要支付</view>
			<j-price :value="order.pay_total" icon="sub" />
		</view>
		<view class="complete-info">
			<van-cell-group>
				<van-cell title="订单编号" :value="order.order_sn" title-width="180rpx" />
				<van-cell title="优惠金额" title-width="180rpx">
					<j-price :value="order.discount" />
				</van-cell>
			</van-cell-group>
		</view>
		<view class="complete-foot">
			<van-button type="primary" block @click="payment">立即支付</van-button>
			<van-button type="default" block hairline custom-class="mt--2" class="mt--2" @click="$helper.goto('/pages/users/order?id='+order_id, 'redirect')">取消</van-button>
		</view>
	</view>
</template>

<script>
import Jprice from '../../components/j-price'

export default {
	components: {
		'j-price': Jprice
	},
	computed: {
		order () {
			return this.orderData.order || {}
		}
	},
	data() {
		return {
			order_id: '',
			orderData: {},
			provider: []
		}
	},
	methods: {
		// 查询订单信息
		getOrder () {
			let _this = this
			if (_this.order_id === '' || _this.order_id === 0) {
				uni.showModal({
					title: '错误',
					content: '参数错误，请返回页面重新提交',
					success () {
						uni.navigateBack()
					}
				})
			}
			uni.showLoading({
				title: '请稍等'
			})
			_this.$http.get('order/getOrder', {order_id: _this.order_id}).then(res => {
				uni.hideLoading()
				if (res.code === 0) {
					_this.orderData = res.rs
				} else {
					uni.showModal({
						title: '提示',
						content: res.msg
					})
				}
			})
		},
		// 发起支付
		payment () {
			let _this = this
			const provider = this.provider[0] || ''
			let params = {
				sn: _this.order.order_sn
			}
			uni.showLoading({
				title: '请稍候'
			})
			_this.$http.post('payment/weixin', params).then(res => {
				uni.hideLoading()
				if (res.code === 0) {
					const payOrder = JSON.parse(res.rs)
					console.log(payOrder)
					uni.requestPayment({
						provider: provider,
						orderInfo: _this.order.order_sn,
						timeStamp: payOrder.timeStamp,
						nonceStr: payOrder.nonceStr,
						package: payOrder.package,
						signType: payOrder.signType,
						paySign: payOrder.paySign,
						success (json) {
							console.log('success', json)
						},
						fail (json) {
							uni.showToast({
								title: json.errMsg,
								icon: 'none'
							})
						}
					})
				}
			})
		}
	},
	onLoad(options) {
		let _this = this
		_this.order_id = options.order_id || ''
		_this.getOrder()
		uni.getProvider({
			service: 'payment',
			success (res) {
				_this.provider = res.provider
			}
		})
	}
}
</script>

<style lang="less">
page, body{background-color:#FFFFFF;}
.complete{
	padding: 50upx;margin-top:50upx;
	&-total{
		text-align:center;font-size:80upx;color:#000;font-weight:700;margin-bottom:80upx;
		&-title{color:#999;font-size:26upx;font-weight:normal;margin-bottom:10upx;}
	}
	&-foot{margin-top:80upx;}
}
</style>
