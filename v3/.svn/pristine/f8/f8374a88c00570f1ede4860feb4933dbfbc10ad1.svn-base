<template>
	<div class="coupon">
		<j-empty icon="coupon" title="没有可用的优惠券" iconsize="180rpx" v-if="empty" />
		
		<div class="van-coupon-list__list" :style="'max-height:'+height">
			
			<div class="van-coupon" v-for="(item, index) in items" :key="index" @click="onChange" :data-index="index" v-if="items && items.length > 0">
				<div class="van-coupon__content">
					<div class="van-coupon__head">
						<h2>{{item.discount_total}}<span>元</span></h2>
						<p>可减免金额</p>
					</div>
					<div class="van-coupon__body">
						<h2>{{item.title}}</h2>
						<p>{{item.use_mode == 1 ? '可叠加活动' : '不可叠加活动'}}</p>
						<div class="van-checkbox van-coupon__corner" v-if="current == item.voucher_id">
							<div class="van-checkbox__icon van-checkbox__icon--round van-checkbox__icon--checked">
								<i class="van-icon van-icon-success"></i>
							</div>
						</div>
					</div>
				</div>
				<p class="van-coupon__description">{{item.label}}</p>
			</div>

		</div>
		
	</div>
</template>

<script>
import Jprice from '../components/j-price'
import Jempty from '../components/j-empty'

export default {
	name: 'coupon-list',
	components: {
		'j-price': Jprice,
		'j-empty': Jempty
	},
  props: {
    items: Array,
		current: {
			type: [Number, String],
			default: ''
		},
		height: {
			type: String,
			default: '630rpx'
		}
  },
	computed: {
		empty () {
			return (this.items && this.items.length > 0) ? false : true
		}
	},
	methods: {
		onChange (e) {
			this.$emit('change', e)
		}
	}
}
</script>

<style lang="less">
@import url('../node_modules/vant/lib/coupon/index.less');
@import url('../node_modules/vant/lib/coupon-cell/index.less');
@import url('../node_modules/vant/lib/coupon-list/index.less');
</style>