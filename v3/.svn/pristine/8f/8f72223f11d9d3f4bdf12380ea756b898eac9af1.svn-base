<template>
  <div class="goods-filter">
    <div class="page-topbar">
      <div class="search">
        <div class="search-box">
          <van-search v-model="search" name="search" :placeholder="placeholder" use-action-slot @search="onSearch" @change="setSearchValue" @clear="clearSearch" background="#f2f2f2" />
        </div>
        <div class="search-filter" @click="toggleFilter">
          <div class="J-badge">
            <div class="J-badge-dot" v-if="dot"></div>
						<van-icon class-prefix="myicon" name="filter" class="filter-icon" />
            <div class="search-filter-text">筛选</div>
          </div>
        </div>
      </div>
    </div>
    <van-popup position="right" custom-class="filter" class="filter" :show="show" v-model="show" @close="show=false">
			<uni-nav-bar left-icon="back" left-text="返回" right-text="完成" title="" color="#1989fa" fixed @click-left="toggleFilter" @click-right="toggleFilter('right')" />
      <scroll-view scroll-y class="filter-container">
        <van-panel title="价格范围">
          <div class="prices">
						<input type="number" placeholder="最低" class="prices-input" v-model.lazy="min_price" />
						<div class="prices-slot">-</div>
              <input type="number" placeholder="最高" class="prices-input" v-model.lazy="max_price" />
          </div>
        </van-panel>
        <van-panel title="品牌" :status="'已选 '+filter.brands.length" custom-class="mt--1" class="mt--1" v-show="brands.length">
          <div class="brand">
            <div v-for="(brand, index) in brands" :key="index" class="brand-col">
              <div v-bind:class="['van-ellipsis','brand-name', {'brand-name__selected': brand.active}]" @click="selectFilter('brands', index)">{{brand.name}}</div>
            </div>
          </div>
        </van-panel>
        <van-panel title="商品分类" :status="'已选 '+filter.categorys.length" custom-class="mt--1" class="mt--1" v-show="categorys">
          <div class="categorys-item" v-for="(item, index) in categorys" :key="index">
            <div class="categorys-item-title" @click="toggleCollapse('categorys', item.info.id)">
							<van-icon :name="(activeCategory==item.info.id?'arrow-down':'arrow-right')" class="categorys-item-arrow" />
              {{item.info.name}}
            </div>
            <div v-show="activeCategory==item.info.id">
              <div :class="['van-ellipsis', 'categorys-item-list', {'categorys-item__selected': val.active}]" @click="selectFilter('categorys', index, idx)" v-for="(val, idx) in item.items" :key="idx">
                {{val.name}}
              </div>
            </div>
          </div>
        </van-panel>
      </scroll-view>
    </van-popup>
  </div>
</template>

<script>
import UTIL from '@/utils/util'
import {uniNavBar} from "@dcloudio/uni-ui"
const getBrandsAndCategorys = require('../static/data/getBrandsAndCategorys.json')

export default {
	components: {uniNavBar},
  props: {
    search: String,
    placeholder: String,
    filter: Object,
    show: Boolean,
    dot: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      activeCategory: '',
      filterData: {},
      min_price: '',
      max_price: ''
    }
  },

  computed: {
    brands () {
      return this.filterData.brands || []
    },
    categorys () {
      const categorys = this.filterData.categorys || {}
      return categorys
    }
  },

  // 监听
  watch: {
    min_price (value) {
      this.$emit('change', {type: 'min_price', value: value})
    },
    max_price (value) {
      this.$emit('change', {type: 'max_price', value: value})
    }
  },

  methods: {
    // 获取品牌&分类数据
    getBrandsAndCategorys () {
      let _this = this
      const res = getBrandsAndCategorys
      if (res.code === 0) {
        _this.initFilterData(res.data)
      } else {
        _this.$tip.toast(res.msg)
      }
    },
    // 初始化数据
    initFilterData (data) {
      for (let i in data.brands) {
        data.brands[i].active = false
      }
      for (let i in data.categorys) {
        for (let j in data.categorys[i].items) {
          data.categorys[i].items[j].active = false
        }
      }
      this.filterData = data
    },
    // 设置关键字
    setSearchValue (e) {
      this.search = e.mp.detail
    },
    // 搜索
    onSearch (e) {
      this.$emit('search', this.search)
    },
    // 显示&隐藏面板
    toggleFilter (type) {
      let _this = this
      // 让input blur事件完成再执行
			if (type === 'right') {
				setTimeout(function () {
					_this.$emit('toggle', type)
				}, 250)
			}
    },
    // 选择筛选项
    selectFilter (type, index, idx) {
      let item

      // 修改列表active
      if (type === 'categorys') {
        item = this.categorys[index]['items'][idx]
        item.active = !item.active
        this.categorys[index]['items'][idx] = item
      } else {
        item = this.brands[index]
        item.active = !item.active
        this.brands[index] = item
      }
      // 加入已选数组
      let array = this.filter[type]
      if (UTIL.inArray(item.id, array)) {
        UTIL.delArray(item.id, array)
      } else {
        array.push(item.id)
      }

      const detail = {
        type: type,
        value: array
      }
      this.$emit('change', detail)
    },
    // 折叠面板
    toggleCollapse (type, idx) {
      this.activeCategory = this.activeCategory === idx ? '' : idx
    },
		// 清除文字
		clearSearch () {
			this.$emit('search', '')
		}
  },

  mounted () {
    this.getBrandsAndCategorys()
  }
}
</script>

<style lang=less>
.goods-filter{
  position:fixed;top:0;left:0;z-index:9;background:#f2f2f2;width:100%;box-sizing:border-box;font-size:28upx;
	/*  #ifdef  H5  */
	top:44px;
	/*  #endif  */
}
.search{
  display:flex;box-sizing:border-box;
  &-box{
    flex-grow:2;
  }
  &-filter{
    display:flex;flex-wrap:wrap;flex-grow:0;align-items:center;justify-content:center;align-content:center;padding-right:40upx;text-align:center;
    &-icon{width:100%;height:30upx;background-size:contain;background-repeat:no-repeat;background-position:center center;}
    &-text{width:100%;text-align:center;font-size:20upx;}
  }
}
.uni-navbar-btn-text{font-size:26upx;}
.filter{
  position:fixed;z-index:110;font-size:28upx;color:#333;height:100%;width:60%;
	/*  #ifdef  H5  */
	right:0;margin-top:44px;
	/*  #endif  */
  &-container{background-color:#f8f8f8;height:100%;box-sizing:border-box;}
  .brand{
    padding:5upx;display:flex;flex-wrap:wrap;
    &-col{width:50%;}
    &-name{font-size:24upx;text-align:center;background-color:#eee;color:#333;border-radius:6upx;margin:6upx;padding:5upx;height:52upx;line-height:52upx;}
    &-name__selected{background-color:#f6b036;color:#fff;}
  }
  .categorys{
    &-item{
      border:none !important;padding:20upx 40upx !important;line-height:48upx;border-top:1upx solid #efefef;
      &-title{font-size:28upx;display:flex;}
      &-arrow{font-size:20upx;margin-right:10upx;}
    }
    &-item__selected{background-color:#f6b036 !important;color:#fff !important;}
    &-item-list{
      padding-left:40upx;line-height:70upx;height:70upx;overflow:hidden;text-overflow:ellipsis;border-bottom:1upx solid #efefef;
    }
  }
  .prices{
    padding-bottom:5upx;display:flex;align-items:center;justify-content:center;
    &-input{text-align:center;margin:5upx 10upx;border-bottom:1upx solid #eee;height:24upx;line-height:24upx;box-sizing:border-box;flex:1;}
    &-slot{text-align:center;line-height:34upx;}
  }
}
</style>
