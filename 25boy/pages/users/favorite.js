var app = getApp();

Page({
	data:{
		user:[],
    loadmore: 'display-none',
    loadmore_line: 'display-none',
    page: 0,
    reload: false,
    type:'',
		dataList:[]
	},

	onLoad:function(options){
			var that = this,
					type = options.type ? options.type : ''

			// 检查登录
	    app.checkLogin(function(res){
	        if(res.sessionId == undefined || res.sessionId == ''){
	            wx.redirectTo({
	              url:'/pages/public/login?gourl='+escape('/pages/users/favorite')
	            })
	            return false
	        }else{
	            that.setData({
	              user:res
	            })

	            that.loadMore(type);
	        }
	    })

	    // 类型
	    that.setData({
          type:type
      });
	    if(type == 'history'){
	    		wx.setNavigationBarTitle({
					  title: '我浏览过的商品'
					})
	    }
	},

  // 页面上拉触底事件的处理函数
  onReachBottom:function(){
      this.loadMore()
  },

	// api加载数据
  loadMore:function(type){
      var that = this
      var page = that.data.page
      var dataList = that.data.dataList
      var params = new Object()

      if( app.UTIL.isNull(that.data.type) == false ){
        type = that.data.type
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

      // 浏览历史
      if(type == 'history'){
      		params = {c:'Member',a:'browseHistory',sessionId:that.data.user.sessionId,pageNo:page}
      }else{
      		params = {c:'Member',a:'favorites',sessionId:that.data.user.sessionId,pageNo:page}
      }

      // ajax获取列表
      app.API.getJSON(params,function(res){
          if(res.data.code == 0){
            that.setData({
              reload: false,
              loadmore: 'display-none',
              dataList: dataList.concat(res.data.rs.data),
              page:page
            })
          }else{
            that.setData({
              loadmore_line: 'disply-block',
              loadmore: 'display-none',
              reload: true,
              page:page
            })
          }
      })
  }

})