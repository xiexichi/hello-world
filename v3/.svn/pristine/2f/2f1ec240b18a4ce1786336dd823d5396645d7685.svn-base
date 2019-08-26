<template>
	<view class="container">
		<uni-list class="checkout-address">
			<uni-list-item :title="address.name + ' ' + address.phone" :note="address.detail" :show-extra-icon="true" :extra-icon="{color:'#39b54a',size:'48upx',type:'location-filled'}">
			</uni-list-item>
		</uni-list>
		
		<view class="cu-bar bg-white mt--2">
			<view class='action'>支付方式</view>
		</view>
		<view class="cu-card case no-card">
			<view class="cu-item">
				<van-cell-group>
					<radio-group @change="radioChange" class="block">
						<van-cell :title="item.name" :value="item.desc" is-link	v-for="(item, index) in payment" :key="index">
							<view slot="icon" class="payment-cellicon">
								<radio :value="item.code" class="green payment-radio" />
								<image :src="item.icon" class="payment-icon"></image>
							</view>
						</van-cell>
					</radio-group>
				</van-cell-group>
			</view>
		</view>

		<view class="cu-card case no-card mt--2">
			<view class="cu-item">
				<uni-list class="checkout-deduction">
					<uni-list-item :title="item.name" v-for="(item, index) in deduction" :key="index" :show-switch="true" :show-arrow="false"></uni-list-item>
					<uni-list-item :title="coupons.title" :note="coupons.desc" :show-badge="true" :badge-text="coupons.num" badge-type="error" v-if="coupons"></uni-list-item>
				</uni-list>
			</view>
		</view>
		
		<view class="cu-card case no-card mt--2">
			<view class="cu-item">
				
			</view>
		</view>

	</view>
</template>

<script>
import UTIL from '@/utils/util'
import USER from '@/utils/user'
import {uniList, uniListItem} from '@dcloudio/uni-ui'

export default {
	components: {uniList, uniListItem},
	data() {
		return {
			cart_ids: '',
			order: {}
		}
	},
	computed: {
		// 收货地址
		address () {
			let address = this.order.userDefaultAddress || {}
			address.detail = address.state_name + ',' + address.city_name + ',' + address.district_name + ", " + address.local
			return address
		},
		// 支付方式
		payment () {
			return [{
				name: '微信支付',
				code: 'weixin',
				icon: '//cdn.yamibuy.net/statics/ec-mobilesite/images/pay-bankcard.png',
				desc: '添加信用卡'
			}, {
				name: 'PayPal',
				code: 'paypal',
				icon: '//cdn.yamibuy.net/statics/ec-mobilesite/images/pay-paypal.png',
				desc: ''
			}, {
				name: '支付宝支付',
				code: 'alipay',
				icon: '//cdn.yamibuy.net/statics/ec-mobilesite/images/pay-alipay.png',
				desc: ''
			}]
		},
		// 余额/积分抵扣
		deduction () {
			return [{
				name: '使用0余额抵扣¥0.00'
			}, {
				name: '使用0积分抵扣¥0.00'
			}]
		},
		// 优惠券
		coupons () {
			return {
				title: '请选择使用优惠券',
				desc: '抵扣¥50.00',
				num: 2
			}
		}
	},
	methods: {
		getOrder () {
			let _this = this
			let params = {
				cart_ids: this.cart_ids
			}
			_this.$http.post('order/orderParams', params).then(res => {
				if (res.code === 0) {
					_this.order = res.rs
				}
			})
		},
		// 提交结算
		submitOrder () {
			this.$helper.goto('/pages/cart/checkout')
		}
	},
	onLoad (options) {		
		this.is_login = USER.checkLogin()
		this.cart_ids = options.cart_ids || ''
		if (! this.is_login) {
			USER.login_backurl = '/pages/cart/index'
			this.$helper.goto('/pages/public/login')
		} else {
			this.getOrder()
		}
	}
}
</script>

<style lang="less">
page{background-color:#f2f2f2;}
.payment{
	&-cellicon{display:flex;}
	&-radio{margin-right:30upx;}
	&-icon{width:50upx;height:50upx;margin-right:30upx;}
}
.cart{
	margin-bottom:180upx;;
	&-panel{margin-top:20upx;background-color:#ffffff;padding:20upx;font-size:28upx;}
	&-shop{
		display:flex;justify-content:space-between;align-items:center;padding-bottom:20upx;font-size:30upx;font-weight:700;
		&-name{
			flex:2;margin-left:20upx;
			.van-icon{font-size:38upx;color:#666666;position:relative;top:6upx;}
		}
	}
	&-activity{background-color:#f2f2f2!important;padding:10upx 20upx!important;}
	&-item{
		display:flex;align-items:flex-start;justify-content:space-between;position:relative;height:180upx;color:#666666;padding-top:20upx;padding-bottom:20upx;border-top:1px solid #eeeeee;
		&-checkbox{display:flex;align-items:center;margin-right:20upx;height:180upx;}
		&-image{width:180upx;height:180upx;display:block;}
		&-detail{padding-left:20upx;position:relative;flex:2;}
		&-quantity{position:absolute;right:0;bottom:0;}
		&-title{color:#333333;font-size:30upx;}
		&-spec{color:#999999;margin-top:10upx;margin-bottom:10upx;;}
	}
}
</style>
