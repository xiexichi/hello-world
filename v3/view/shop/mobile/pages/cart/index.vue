<template>
	<view class="container">
		<j-empty icon="gouwuchekong" title="购物车为空" tip="把喜欢的东西添加到购物车吧" :button="emptybtn" @emptytap="emptyTap" v-if="is_empty" />
		<view class="cart" v-else>
			<view class="cart-box">
				<view class="cart-head">
					<van-notice-bar text="关注微信公众号包邮" mode="link" @click="noticeTap" />
				</view>
				<view class="cart-main mt--1">
					<view class="cart-panel">
						<view class="cart-shop">
							<!-- <checkbox class="round red cart-shop-checkbox"></checkbox> -->
							<view class="cart-shop-name"><van-icon name="shop-o" custom-class="mr--1" />25BOY</view>
							<view class="cart-shop-price">
								<j-price value="158" icon="sub" />
							</view>
						</view>
						<van-cell :title="activity_title" icon="fire" custom-class="cart-activity" class="cart-activity" :border="false" is-link />
						<cart-item class="cart-item" v-for="(item, index) in items" :key="index" :item="item" @change="onChangeQiantity" :show-numberbox="true" />
					</view>
				</view>
			</view>
			<van-submit-bar :price="total" button-text="结算" currency="¥" @submit="submitCheckout">
				<!-- <checkbox class="round red ml--2" @change="onChange"></checkbox> -->
			</van-submit-bar>
		</view>
	</view>
</template>

<script>
import UTIL from '@/utils/util'
import USER from '@/utils/user'
import cartItem from '../../components/cart-item'
import Jprice from '../../components/j-price'
import Jempty from '../../components/j-empty'

export default {
	components: {
		'cart-item': cartItem,
		'j-price': Jprice,
		'j-empty': Jempty
	},
	data() {
		return {
			is_login: false,
			items: [],
			activity: {}
		}
	},
	computed: {
		// 购物车是否为空
		is_empty () {
			if (! this.is_login || UTIL.empty(this.items)) {
				return true
			} else {
				return false
			}
		},
		// 购物车空操作按钮
		emptybtn () {
			return this.is_login ? '去逛逛' : '登录'
		},
		checkedAll () {
			return true
		},
		// 活动标题
		activity_title () {
			return this.activity.small_title || ''
		},
		total () {
			const carts = this.items
			let total = 0
			for (var i in carts) {
				total += parseFloat(carts[i].re_price) * parseInt(carts[i].quantity)
			}
			return total * 100
		}
	},
	methods: {
		getCarts () {
			uni.showLoading({
				title: '加载中'
			})
			let _this = this
			_this.$http.post('cart/getCarts').then(res => {
				uni.hideLoading()
				if (res.code === 0) {
					_this.items = res.rs.data
					_this.activity = res.event || {}
				}
			})
		},
		// 购物车操作按钮
		emptyTap (e) {
			if (this.is_login) {
				this.$helper.goto('/pages/products/list')
			} else {
				USER.login_backurl = '/pages/cart/index'
				this.$helper.goto('/pages/public/login')
			}
		},
		// 点击公告栏事件
		noticeTap () {
			uni.showToast({
				title: '打开关注公众号指引页面',
				icon: 'none'
			})
		},
		// 修改购买数量
		onChangeQiantity (e) {
			let _this = this
			const id = e.id || ''
			const quantity = e.detail.value || 0
			if (quantity === 0) return false
			let params = {
				cart_id: id,
				quantity: quantity
			}
			// loading...
			uni.showLoading({
				title: '加载中'
			})
			_this.$http.post('cart/updateQuantity', params).then(res => {
				uni.hideLoading()
				if (res.code === 0) {
					_this.items = res.rs.data
					_this.activity = res.event || {}
				} else {
					uni.showToast({
						title: res.msg,
						icon: 'none'
					})
				}
			})
		},
		// 提交结算
		submitCheckout () {
			let cart_ids = []
			const items = this.items
			for (var i in items) {
				cart_ids.push(items[i].cart_id)
			}
			this.$helper.goto('/pages/cart/checkout?cart_ids=' + cart_ids.join(','))
		}
	},
	onShow () {		
		this.is_login = USER.checkLogin()
		if (this.is_login) {
			this.getCarts()
		}
	}
}
</script>

<style lang="less">
page{background-color:#f2f2f2;}
.van-checkbox__icon,.van-checkbox__icon-wrap{width:38upx!important;height:38upx!important;font-size:12px!important;}
.cart{
	&-box{margin-bottom:180upx;}
	&-panel{margin-top:20upx;background-color:#ffffff;padding:20upx;font-size:28upx;}
	&-shop{
		display:flex;justify-content:flex-start;align-items:center;padding-bottom:20upx;font-size:30upx;font-weight:700;
		&-name{
			flex:2;margin-left:20upx;
			.van-icon{font-size:38upx;color:#666666;position:relative;top:6upx;}
		}
	}
	&-activity.van-cell{background-color:#f2f2f2!important;padding:10upx 20upx!important;}
}
</style>
