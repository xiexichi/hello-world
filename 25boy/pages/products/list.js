var app = getApp();

Page({
  data:{
    user:[],
  	loadmore: 'display-none',
  	loadmore_line: 'display-none',
  	page: 0,
    productList:[],
    brandList:[],
    reload: false,
    category_id: '',
    brand_id: '',
    type: '',
    priceSort:'price-asc',
    priceClass:'',
    brandDisplay:'display-none',
    keyword:'',
    isSearch:'',
    searchFocus: false,
    showHomeBtn:true,
    footerData:[]
  },

  onLoad: function(options){
  	var that = this,
  		type = this.data.type,
  		category_id = this.data.category_id,
      brand_id = this.data.brand_id,
      keyword = this.data.keyword

  	// url参数
  	if(options.id != undefined){
  		category_id = options.id
  	}
  	if(options.type != undefined){
  		type = options.type
  	}
    if(options.brand_id != undefined){
      brand_id = options.brand_id
      type = 'brand'
    }
    if(options.keyword != undefined){
      keyword = options.keyword
    }

  	that.setData({
  		type: type,
		  category_id: category_id,
      brand_id: brand_id,
      keyword: keyword,
      footerData:app.footerData
	  })

    // 检查登录
    app.checkLogin(function(res){
        if(res.sessionId != undefined && res.sessionId != ''){
            that.setData({
                user: res
            })
        }
        
        // ajax加载第一页
        that.loadMore(false)
        // 品牌
        that.getBrands()
    })

  	// 记录来源
    app.markFromSource(options,'pages/products/list')

  },

  // 页面上拉触底事件的处理函数
  onReachBottom:function(){
      this.loadMore()
  },


  // 加载更多数据
  loadMore: function(refresh) {
  	var that = this
  	var page = that.data.page
    if(refresh === true){
      var productList = []
    }else{
      var productList = that.data.productList
    }

  	// 检查是否可以加载
  	if(that.data.reload == true){
  		return false
  	}else{
      page+=1
    }

  	// loading
  	that.setData({
  		loadmore:'disply-block',
  		reload: true
  	})

	  // ajax获取产品列表
    app.API.getJSON({
        type:that.data.type,
        category_id:that.data.category_id,
        brand_id:that.data.brand_id,
        search:that.data.keyword,
        pageNo:page,
        sessionId:this.data.user.sessionId
      },function(res){
        if(res.data.code == 0){
    			that.setData({
    				reload: false,
    				loadmore: 'display-none',
    				productList: productList.concat(res.data.rs.data),
            page: page
    			})
    		}else{
    			that.setData({
    				loadmore_line: 'disply-block',
    				loadmore: 'display-none',
    				reload: true,
            page: page
    			})
    		}
    },'index.php?c=Product&a=productList')

  },


  // 品牌列表
  getBrands:function(){
      var that = this
      app.API.getJSON({c:'Product',a:'brands'},function(res){
          if(res.data.code == 0){
            that.setData({
              brandList: res.data.rs
            })
          }
      })
  },


  // 筛选
  filterTap:function(e){
      var type = e.target.dataset.sort,
          priceSort = this.data.priceSort,
          brandDisplay = this.data.brandDisplay,
          priceClass = '',
          load = true

      // 加载中不允许操作
      if(this.data.loadmore == 'disply-block'){
          return false
      }

      switch(type){
          case 'price-asc':
              priceSort = 'price-desc'
              priceClass = 'filter-on sort-asc'
              brandDisplay = 'display-none'
              break
          case 'price-desc':
              priceSort = 'price-asc'
              priceClass = 'filter-on sort-desc'
              brandDisplay = 'display-none'
              break
          case 'brand':
              priceClass = ''
              brandDisplay = (brandDisplay=='display-flex'?'display-none':'display-flex')
              load = false
              break
          default:
              priceClass = ''
              brandDisplay = 'display-none'
              break
      }

      this.setData({
          type: type,
          priceSort: priceSort,
          priceClass: priceClass,
          brandDisplay: brandDisplay
      })

      if(load == true){
          // 重新加载列表
          this.setData({
              page: 0,
              reload: false
          })
          this.loadMore(true)
      }

  },

  // 显示搜索层
  showSearchLayer:function(){
      this.setData({
          searchFocus: true,
          isSearch:'isSearch'
      })
  },

  // 隐藏搜索层
  hideSearchLayer:function(){
      this.setData({
          isSearch:''
      })
  },

  // 提交搜索
  submitSearch:function(e){
      var key = e.detail.value
      console.log(key)
      if(key !== '' && key != undefined){
        wx.redirectTo({
            url:'list?keyword='+key
        })
      }
  },

  // 分享页面
  onShareAppMessage:function(){
      var title = '商品列表',
          desc = '25BOY本土原创潮牌，结合传统醒狮元素打造中国本土原创潮牌，Happy Easy Anyway!',
          path = '/pages/products/list?id='+this.data.category_id+'&type='+this.data.type+'&brand_id='+this.data.brand_id+'&keyword='+this.data.keyword

      if(this.data.user.promote_id != undefined && this.data.user.promote_id != ""){
          path = path+'&pid='+this.data.user.promote_id;
      }

      return {
        title: title,
        desc: desc,
        path: path
      }
  },

  // 进入首页按钮拖动事件
  homeBtnTouchMove:function(e){
    this.setData({
        footerData : app.homeBtnTouchMoveFun(e)
    })
  },
  // 拖动结束
  homeBtnTouchMoveEnd:function(e){
    this.setData({
        footerData : app.homeBtnTouchMoveEndFun(e)
    })
  },
  // 点击事件
  homeBtnClick:function(){
      wx.switchTab({
        url:'/pages/index/index'
      })
  }

})