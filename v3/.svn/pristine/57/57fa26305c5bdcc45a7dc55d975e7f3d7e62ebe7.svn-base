<template>
	<view class="container">
		<!-- 轮播图 -->
		<swiper class="goods-swipe" v-if="slides && slides.length" autoPlay>
		  <swiper-item v-for="(slide, index) in slides" :key="index">
		    <div class="goods-swipe-item">
					<img :src="slide + '!w800'" lazy-load class="goods-swipe-image" >
		    </div>
		  </swiper-item>
		</swiper>
		<!-- 基本信息 -->
		<view class="background-white goods-base">
			<view class="goods-title">{{goods.product_name}}</view>
			<view class="goods-price mt--1">
				<j-price :value="goods.price" icon="sub" custom-class="mr--1" class="mr--1" />
				<j-price :value="goods.market_price" icon="sub" status="del" custom-class="price-del" class="price-del" />
			</view>
			<view class="goods-activity mt--1" v-if="activitys && activitys.length">
				<van-tag class="goods-activity-tag" v-for="(tag, index) in activitys" :key="index" type="success">{{tag.title}}</van-tag>
			</view>
			<view class="goods-delivery mt--1">配送: {{goods.delivery_desc}} <j-price :value="goods.ship_price" /></view>
			<view class="goods-presale mt--1" v-if="presale_date">预计发货时间: {{presale_date}}</view>
		</view>
		<!-- 选择颜色栏 -->
		<van-cell :title="selectedText" size="large" border is-link clickable @click="toggleSkuLayer" />
		<!-- 商品详情 -->
		<van-panel title="商品详情" custom-class="mt--2" class="mt--2">
			<view class="goods-detail">
				<image class="image" mode="widthFix" v-for="(src, index) in productdetail" :key="index" :src="src"></image>
			</view>
		</van-panel>
		
		<popup-panel :show="sku_show" @toggle="toggleSkuLayer" class="sku-popup">
			<goods-sku :item="goods_specs" @add-cart="addCart" @close="toggleSkuLayer" />
		</popup-panel>
		
		<van-goods-action class="shadow">
			<!--  #ifdef APP-PLUS || H5 -->
			<van-goods-action-mini-btn icon="chat-o" text="客服" url="/pages/index/contact" />
			<van-goods-action-mini-btn icon="cart-o" text="购物车" :info="cartTotalNum > 0 ? cartTotalNum : ''" to="/pages/cart/index" replace />
			<van-goods-action-mini-btn icon="like-o" text="收藏" />
			<van-goods-action-big-btn primary text="加入购物车" @click="toggleSkuLayer" />
			<!--  #endif -->
			<!--  #ifdef MP -->
			<van-goods-action-icon icon="chat-o" text="客服" url="/pages/index/contact" />
			<van-goods-action-icon icon="cart-o" text="购物车" :info="cartTotalNum > 0 ? cartTotalNum : ''" url="/pages/cart/index" link-type="switchTab" />
			<van-goods-action-icon icon="like-o" text="收藏" />
			<van-goods-action-button text="加入购物车" @click="toggleSkuLayer" />
			<!--  #endif -->
		</van-goods-action>
	</view>
</template>

<script>
import TIME from '@/utils/time'
import UTIL from '@/utils/util'
import USER from '@/utils/user'
import GoodsSku from '../../components/goods-sku'
import PopupPanel from '../../components/popup-panel'
import Jprice from '../../components/j-price'

export default {
	components: {
	  'j-price': Jprice,
		GoodsSku, PopupPanel
	},
	data() {
		return {
			id: 0,
			goods: {},
			sku_show: false,
			skued: '',
			cartTotalNum: 0
		}
	},
	computed: {
	  slides () {
	    return this.goods.images || []
	  },
		activitys () {
	    return this.goods.event
	  },
		presale_date () {
			let presale = this.goods.presale_date || ''
			const nowtime = TIME.timestamp()
	    if (nowtime < TIME.timestamp(presale)) {
				return TIME.formatTime('yyyy-MM-dd', presale)
			}
			return false
	  },
		productdetail () {
			let content = this.goods.content || ''
			return UTIL.getImagesSrc(content)
	  },
		goods_specs () {
			const props = this.goods.stockprops
			let keys = this.goods.sizes || []
			let children = {}
			for (var i in props) {
				children[props[i].name] = props[i].size
			}
			console.log(this.slides)
			let item = {
				children: children,
				keys: keys,
				product_img: this.slides[0] || '',
				item_code: this.goods.sku_sn,
				price: this.goods.price,
				title: this.goods.product_name,
				total_stock: this.goods.total_quantity
			}
			return item
	  },
		// 已选择颜色尺码
		selectedText () {
			if (typeof(this.skued) && this.skued.color) {
				return '已选择: ' + this.skued.color + '，' + this.skued.size
			}
			return '选择: 颜色/尺码'
		}
	},
	methods: {
		// 获取商品信息
	  async getGoodsInfo () {
	    let _this = this
	    let params = {}
	
	    uni.showLoading({
				title: '加载中'
			})
			
	    _this.$http.get('product/single', {product_id: _this.id}).then(res => {
	      uni.hideLoading()
				uni.stopPullDownRefresh()
	      if (res.code === 0) {
	        _this.goods = res.rs
	      } else {
					uni.showToast({
						title: res.msg,
						icon: 'none',
						duration: 2000
					})
	      }
	    })
	  },
		// 选择商品规格层
		toggleSkuLayer (type) {
			if (! this.is_login) {
				USER.showLoginTips()
				return false
			}
			if (type === 'close')
				this.sku_show = false
			else
				this.sku_show = !this.sku_show
		},
		// 添加到购物车
		addCart (e) {
			this.skued = e
			let _this = this
			let params = {
				product_id: _this.id,
				sku_sn: _this.goods.sku_sn,
				color_prop: _this.skued.color,
				size_prop: _this.skued.size,
				quantity: _this.skued.quantity || 1
			}
			uni.showLoading({
				title: '处理中'
			})
			_this.$http.post('cart/add', params).then(res => {
				uni.hideLoading()
				if (res.code === 0) {
					uni.showToast({
						title: '添加成功'
					})
					_this.cartTotalNum = res.rs.total
					_this.toggleSkuLayer()
				} else if (res.code === -6) {
					USER.showLoginTips()
				} else {
					uni.showModal({
						title: '异常提示',
						content: res.msg
					})
				}
			})
		},
		// 购物车数量
		getCartTotal () {
			let _this = this
			_this.$http.post('cart/getTotal').then(res => {
				if (res.code === 0) {
					_this.cartTotalNum = res.total || 0
				}
			})
		}
	},
	onLoad(options) {
		this.id = options.id || 0
		this.is_login = USER.checkLogin()
		this.getGoodsInfo()
		this.is_login && this.getCartTotal()
	}
}
</script>

<style lang="less">
body, page{background-color:#f2f2f2;}
.sku-popup {overflow: visible !important;}
.goods{
  &-swipe {
    width:750upx;height:750upx;z-index:1;
    &-image{width:100%;height:750upx;display:block;}
  }
	&-activity-tag{margin-right:10upx;;}
	&-base{padding:20upx 30upx;color:#666666;border-bottom:1px solid #EEEEEE;}
	&-title{color:#000000;font-size:32upx;}
	&-price{color:#b01f23;font-size:42upx;font-weight:700;}
	&-presale{color:red;}
	&-detail{padding:0 10upx;padding-bottom:120upx;}
}
</style>
