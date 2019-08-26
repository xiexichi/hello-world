<template>
	<view class="category">
		<van-row v-for="(item, index) in items" :key="index">
			<van-col span="24">
				<navigator :url="'/pages/products/list?id=' + item.category_id" class="category-head">
					<image :src="'https://api.25boy.cn/Public/img/categorys-tit-' + item.category_id + '.png'" mode="widthFix"></image>
				</navigator>
			</van-col>
			<van-col span="8" v-for="(v, idx) in item.subCategory" :key="idx">
				<navigator :url="'/pages/products/list?id=' + v.category_id" v-if="v.img_url">
					<image :src="v.img_url + '!w200'" mode="widthFix"></image>
				</navigator>
			</van-col>
		</van-row>
	</view>
</template>

<script>
export default {
	data() {
		return {
			items: []
		}
	},
	methods: {
		getCategorys () {
			let _this = this
			_this.$http.get('category/muneCategory').then(res => {
				if (res.code === 0) {
					_this.items = res.rs
				}
			})
		}
	},
	onLoad () {
		this.getCategorys()
	}
}
</script>

<style lang="less">
.category{
	padding-bottom: 30upx;
	image{display:block;margin:auto;}
	&-head{text-align:center;margin:80upx auto 30upx auto;}
}
</style>
