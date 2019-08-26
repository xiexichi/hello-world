<template>
	<van-popup :show="show" @close="$emit('close')" position="bottom" custom-class="sku-popup">
		<div class="sku-container" v-if="item">
			<div class="van-hairline--bottom sku-header">
				<div class="sku-header__img-wrap">
					<img class="sku-header__img" :src="item.product_img">
				</div>
				<div class="sku-header__goods-info">
					<div class="sku__goods-name ellipsis">{{item.title}}</div>
					<!-- <div class="sku__goods-sku ellipsis">{{item.item_code}}</div> -->
					<div class="sku__goods-price mt--1">
						<span class="sku__price-symbol">￥</span>
						<span class="sku__price-num">{{item.price}}</span>
					</div>
					<div class="sku__close-icon">
						<van-icon name="close" @click="$emit('close')" />
					</div>
				</div>
			</div>

			<div class="sku-body">
				<div class="sku-group-container van-hairline--bottom">
					<div class="sku-row">
						<div class="sku-row__title">颜色：</div>
						<span v-bind:class="['sku-row__item', {'sku-row__item--active': color==currentColor}]" v-for="(val, color) in item.children"
						 :key="color" @click="selectColor(color)"> {{color}} </span>
					</div>
					<div class="sku-row">
						<div class="sku-row__title">尺寸：</div>
						<span v-bind:class="['sku-row__item', {'sku-row__item--active': size===currentSize}]" v-for="size in item.keys"
						 :key="size" @click="selectSize(size)">{{size}}</span>
					</div>
				</div>
				<div class="sku-stepper-stock">
					<div class="sku-stepper-container">
						<div class="sku__stepper-title">购买数量：</div>
						<van-stepper v-model="quantity" min="1" :max="stock" @change="changeQuantity" :disabled="disabledStepper" custom-class="sku__stepper" integer />
					</div>
					<div class="sku__stock">剩余 {{stock}} 件</div>
					<div class="sku__quota" v-if="limitQuantity > 0">每人限购{{limitQuantity}}件</div>
				</div>
			</div>
			<div class="sku-actions">
				<van-button type="primary" block @click="addCart">保存到购物车</van-button>
			</div>

		</div>
	</van-popup>
</template>

<script>
	export default {
		props: {
			show: Boolean,
			item: Object
		},

		data () {
			return {
				// 当前选择颜色尺码key
				currentColor: '',
				currentSize: '',
				quantity: 1,
				// 加入购物车数据
				selectedLength: 0,
				// 限购数量
				limitQuantity: 0
			}
		},

		computed: {
			// 同时选择颜色和尺码才能更改数量
			disabledStepper () {
				return !(this.currentColor && this.currentSize)
			},
			// 剩余库存
			stock () {
				const currentColor = this.currentColor
				const currentSize = this.currentSize
				if (currentSize && currentColor) {
					const spec = this.item.children[currentColor]
					for (var i in spec) {
						if (spec[i].sku === currentSize) {
							return spec[i].num
						}
					}
				}
				return this.item.total_stock
			}
		},

		// 监听
		watch: {
			item (detail) {
				// 初始化数据
				this.currentColor = ''
				this.currentSize = ''
				this.quantity = 1
			}
		},

		methods: {
			// 选择颜色
			selectColor (color) {
				this.currentColor = color
			},
			// 选择尺码
			selectSize (size) {
				this.currentSize = size
			},
			// 修改数量
			changeQuantity (e) {
				this.quantity = e.mp.detail
			},
			// 加入购物车
			addCart () {
				const detail = {
					color: this.currentColor,
					size: this.currentSize,
					quantity: this.quantity
				}
				this.$emit('add-cart', detail)
			}
		}
	}
</script>

<style lang=less>
	.sku-popup {
		overflow: visible !important;
	}

	.sku {
		&-container {
			position: relative;
			font-size: 28upx;
		}

		&-header {
			margin-left: 30upx;
			position: relative;
		}

		&-header__img-wrap {
			position: relative;
			float: left;
			margin-top: -20upx;
			width: 160upx;
			height: 160upx;
			background: #f8f8f8;
			border-radius: 12upx;
			overflow: hidden;
		}

		&-header__img {
			width: 100%;
			height: 100%;
		}

		&-header__goods-info {
			padding: 10upx 100upx 20upx 20upx;
			min-height: 164upx;
			overflow: hidden;
			box-sizing: border-box;
			line-height: 40upx;
		}

		&__goods-name {
			font-size: 28upx;
			color: #666;
		}

		&__goods-sku {
			font-size: 28upx;
		}

		&__goods-price {
			color: #f44;
			vertical-align: middle;
		}

		&__price-num {
			font-size: 32upx;
		}

		&__close-icon {
			top: 20upx;
			right: 30upx;
			font-size: 40upx;
			color: #999;
			position: absolute;
			text-align: center;
		}

		&-body {
			max-height: 700upx;
			overflow-y: scroll;
		}

		&-group-container {
			margin-left: 30upx;
			padding: 24upx 0 4upx;
		}

		&-row {
			margin: 0 30upx 20upx 0;
		}

		&-stepper-stock {
			padding: 24upx 0;
			margin-left: 30upx;
		}

		&-row__title {
			padding-bottom: 20upx;
		}

		&-row__item {
			display: inline-block;
			padding: 6upx 16upx;
			margin: 0 16upx 16upx 0;
			min-width: 60upx;
			font-size: 28upx;
			color: #333;
			text-align: center;
			border: 1px solid #999;
			border-radius: 6upx;
			box-sizing: border-box;
		}

		&-row__item--active {
			color: #fff;
			border-color: #f6b036;
			background: #f6b036;
		}

		&-stepper-stock {
			margin-left: 30upx;
			padding: 24upx 0;
		}

		&-stepper-container {
			margin-right: 40upx;
			height: 60upx;
		}

		&__stepper-title {
			float: left;
			line-height: 60upx;
		}

		&__stepper {
			float: right;
		}

		&__stock {
			display: inline-block;
			margin-right: 20upx;
			color: #999;
			font-size: 24upx;
		}

		&__quota {
			display: inline-block;
			color: #f44;
			font-size: 24upx;
		}
	}
</style>
