<template>
	<view class="container">
		<view class="checkout">
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
						<radio-group class="block" @change="oChangePayment">
							<van-cell :title="item.name" :value="item.desc" :is-link="item.desc ? true : false"	v-for="(item, index) in payment" :key="index">
								<view slot="icon" class="payment-cellicon">
									<radio :value="item.code" class="green payment-radio" />
									<image :src="item.icon" class="payment-icon"></image>
								</view>
							</van-cell>
						</radio-group>
					</van-cell-group>
				</view>
			</view>
			
			<view class="cu-card case no-card">
				<view class="cu-item checkout-deduction">
					<uni-list>
						<uni-list-item :title="item.name" v-for="(item, index) in deductionList" :key="index" :show-switch="true" :show-arrow="false"></uni-list-item>
						<uni-list-item title="选择优惠券" :show-badge="true" :badge-text="couponRowTxt" badge-type="error" @click="togglePopup('coupon')" />
					</uni-list>
				</view>
			</view>
			
			<view class="cu-bar bg-white mt--2">
				<view class='action'>由25BOY提供线上发货</view>
			</view>
			<view class="cu-card case no-card">
				<view class="cu-item">
					<van-cell :title="shipItem.name" :value="shipFee > 0 ? '¥'+shipFee : '免配送费'" icon="logistics" :border="false" is-link @click="togglePopup('delivery')" />
					<cart-item class="cart-item" v-for="(item, index) in items" :key="index" :item="item" />
				</view>
			</view>
			
			<view class="cu-card case no-card mt--1">
				<view class="cu-form-group">
					<input placeholder="买家留言" v-model="buyer_note"></input>
				</view>
			</view>

			<view class="cu-card case no-card mt--2 mb--5">
				<view class="cu-item">
					<van-cell-group>
						<radio-group class="block">
							<van-cell :title="item.title" :value="item.value" title-class="cell-title" value-class="cell-price" v-for="(item, index) in checkoutTotal" :key="index" />
						</radio-group>
					</van-cell-group>
				</view>
			</view>
			
			<van-submit-bar :price="total*100" button-text="提交订单" currency="¥" @submit="submitOrder" custom-class="van-hairline--top" class="van-hairline--top">
				<view class="submitbar-left">共{{order.total_quantity}}件</view>
			</van-submit-bar>
		</view>
		
		<popup-panel :show="showDeliveryPopup" title="配送方式" @toggle="togglePopup('delivery')">
			<van-cell size="large" clickable :title="v.name" :label="v.desc" :id="v.id" :data-index="idx" v-for="(v, idx) in deliverys" :key="idx" v-if="deliverys && deliverys.length > 0" @click="onChangeDelivery">
				<van-icon :name="shipItem.id==v.id ? 'checked' : 'circle'" size="50rpx" :color="shipItem.id==v.id ? '#39b54a' : '#eee'" />
			</van-cell>
		</popup-panel>
		
		<popup-panel :show="showCouponPopup" title="选择优惠券" @toggle="togglePopup('coupon')">
			<view class="couponPanel">
				<view class="couponPanel-inputbox">
					<view class="cu-form-group">
						<input placeholder="请输入优惠券码"></input>
						<button class="cu-btn bg-green shadow">应用</button>
					</view>
				</view>
				<view class="couponPanel-mainbox">
					<coupon-list :items="coupons" :current="couponItem.voucher_id" @change="onChangeCoupon" />
				</view>
			</view>
		</popup-panel>
	</view>
</template>

<script>
import UTIL from '@/utils/util'
import USER from '@/utils/user'
import cartItem from '../../components/cart-item'
import couponList from '../../components/coupon-list'
import popupPanel from '../../components/popup-panel'
import Jprice from '../../components/j-price'
import Jempty from '../../components/j-empty'
import {uniList, uniListItem} from '@dcloudio/uni-ui'

export default {
	components: {
		'j-price': Jprice,
		'j-empty': Jempty,
		'cart-item': cartItem,
		'coupon-list': couponList,
		'popup-panel': popupPanel,
		uniList, uniListItem
	},
	data() {
		return {
			cart_ids: '',
			order: {},
			coupons: [],
			showDeliveryPopup: false,
			showCouponPopup: false,
			shipItem: {},
			couponItem: {},
			buyer_note: ''
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
				icon: '//img.25miao.com/2245/0a86a3b0f690b0063abf1ee1dff322e3.png',
				desc: ''
			},{
				name: '支付宝支付',
				code: 'alipay',
				icon: '//img.25miao.com/2245/c506233059ff37799aa795bc43e01c35.png',
				desc: ''
			}]
		},
		// 余额/积分抵扣
		deductionList () {
			let array = []
			if (this.userBalance > 0) {
				array.push({
					name: '使用余额抵扣 ¥' + this.userBalance
				})
			}
			return array
		},
		// 购物车商品
		items () {
			return this.order.carts || []
		},
		couponNum () {
			return this.coupons.length || 0
		},
		checkoutTotal () {
			return [{
				title: '商品金额',
				value: '¥' + (this.order.goodstotal || 0).toFixed(2)
			},{
				title: '运费',
				value: '¥' + this.shipFee
			},{
				title: '店铺活动',
				value: '- ¥' + (this.order.cuttotal || 0).toFixed(2)
			},{
				title: '优惠券',
				value: '- ¥' + (this.couponItem.discount_total || 0).toFixed(2)
			},{
				title: '订单金额',
				value: '¥' + this.total.toFixed(2)
			}]
		},
		// 配送方式
		deliverys () {
			let array = []
			const items = this.order.deliverys || []
			for (var i in items) {
				array.push({
					id: items[i].delivery_id,
					name: items[i].delivery_name,
					desc: items[i].delivery_desc,
					code: items[i].delivery_code,
					is_default: items[i].is_default
				})
			}
			return array
		},
		// 总价
		total () {
			return parseFloat(this.order.paytotal || 0)
		},
		// 配送费
		shipFee () {
			return (this.order.ship_fee || 0).toFixed(2)
		},
		// 钱包余额
		userBalance () {
			return this.order.user_balance || 0
		},
		// 选择优惠券提示
		couponRowTxt () {
			return this.couponItem.discount_total ? ('- ' + this.couponItem.discount_total + ' 元') : (this.couponNum+'张可用')
		}
	},
	watch: {
		// 默认配送方式 
		deliverys (deliverys) {
			let object = {}
			for (var i in deliverys) {
				if (deliverys[i].is_default == 1) {
					object = deliverys[i]
				}
			}
			this.shipItem = object
		}
	},
	methods: {
		getOrder () {
			let _this = this
			let params = {
				cart_ids: this.cart_ids,
				voucher_id: this.couponItem.voucher_id || ''
			}
			uni.showLoading({
				title: '加载中'
			})
			_this.$http.post('order/orderParams', params).then(res => {
				uni.hideLoading()
				if (res.code === 0) {
					_this.order = res.rs
					_this.getCoupons()
				} else {
					uni.showModal({
						title: '异常提示',
						content: res.msg,
						showCancel: false,
						success: function () {
							uni.navigateBack()
						}
					})
				}
			})
		},
		// 查询优惠券
		getCoupons () {
			let _this = this
			let params = {
				cart_ids: this.cart_ids,
				pay_total: this.order.paytotal,
				goods_total: this.order.goodstotal,
				total_quantity: this.order.total_quantity
			}
			_this.$http.post('voucher/getAvailableVoucherGroup', params).then(res => {
				if (res.code === 0) {
					_this.coupons = res.rs || []
				}
			})
		},
		// 选择优惠券
		onChangeCoupon (e) {
			const index = e.currentTarget.dataset.index || 0
			this.couponItem = this.coupons[index]
			this.togglePopup('coupon')
			this.$nextTick(res => {
				this.getOrder()
			})
		},
		// 显示隐藏弹出层
		togglePopup (type) {
			switch (type){
				case 'coupon':
					this.showCouponPopup = !this.showCouponPopup
					break;
				case 'delivery':
					this.showDeliveryPopup = !this.showDeliveryPopup
					break;
			}
		},
		// 提交结算
		submitOrder () {
			let _this = this
			let params = {
				cart_ids: this.cart_ids,
				payment: this.payment_method || '',
				buyer_note: this.buyer_note || '',
				address_id: this.address.id || '',
				delivery_id: this.shipItem.id || '',
				voucher_id: this.couponItem.voucher_id || ''
			}
			if (UTIL.empty(params.address_id)) {
				uni.showModal({
					title: '提示',
					content: '请选择收货地址'
				})
				return false
			}
			if (UTIL.empty(params.payment)) {
				uni.showModal({
					title: '提示',
					content: '请选择支付方式'
				})
				return false
			}
			if (UTIL.empty(params.delivery_id)) {
				uni.showModal({
					title: '提示',
					content: '请选择配送方式'
				})
				return false
			}
			uni.showLoading({
				title: '提交订单'
			})
			_this.$http.post('order/createOrder', params).then (res => {
				if (res.code === 0) {
					_this.$helper.goto('/pages/cart/payment?order_id=' + res.order_id, 'redirect')
				} else {
					uni.hideLoading()
					uni.showModal({
						title: '异常提示',
						content: res.msg
					})
				}
			})
		},
		// 更改配送方式
		onChangeDelivery (e) {
			const index = e.currentTarget.dataset.index || 0
			const id = e.currentTarget.id
			this.shipItem = this.deliverys[index]
			this.togglePopup('delivery')
		},
		// 更新支付方式
		oChangePayment (e) {
			this.payment_method = e.detail.value || ''
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
.cell-title{color:#999;}
.cell-price{font-size:30upx;color:#323232;}
.submitbar-left{font-size:28upx;padding-left:20upx;color:gray;}
.popup-panel .uni-popup-bottom{height:auto;position:fixed;}
.payment{
	&-cellicon{display:flex;}
	&-radio{margin-right:30upx;}
	&-icon{width:50upx;height:50upx;margin-right:30upx;}
}
.checkout{
	margin-bottom:150upx;;
	&-panel{margin-top:20upx;background-color:#ffffff;padding:20upx;font-size:28upx;}
	&-deduction .uni-list:before{background-color:none !important;display:none;}
	&-shop{
		display:flex;justify-content:space-between;align-items:center;padding-bottom:20upx;font-size:30upx;font-weight:700;
		&-name{
			flex:2;margin-left:20upx;
			.van-icon{font-size:38upx;color:#666666;position:relative;top:6upx;}
		}
	}
	&-shipping{background-color:#f2f2f2!important;padding:10upx 20upx!important;}
}
.couponPanel{
	.cu-form-group{background-color:transparent;}
	&-inputbox{background-color:#f7f7f7;margin:30upx;border-radius:12upx;}
}
</style>
