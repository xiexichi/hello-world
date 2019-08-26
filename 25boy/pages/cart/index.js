var app = getApp();

Page({
  data:{
    user: [],
    showEmpty: false,
    showLogin: false,
    allIconType: 'success',
    allIconColor: '#dc372f',
    total: 0,
    cart_ids: '',
    disabledSubmit: false,
    loadingSubmit: false,
    page: 0,
    cartList:[],
    reload: false,
    isReady:false
   
  },

  // 临时记住错误的数据，以便调用
  errData: {
      id: ''
  },

  onLoad: function(options){
    var that = this

    // 检查登录
    app.checkLogin(function(res){
        wx.hideToast();
        if(app.UTIL.isNull(res.sessionId) == true){
          that.setData({
            showLogin:true,
            showEmpty: false
          })
          if(res != 'cancel'){
              wx.navigateTo({
                url:'/pages/public/login?gourl=close'
              })
          }
          return false
        }else{
          that.setData({
            showLogin:false,
            user:res
          })

          // 初始化购物车
          that.loadMore(false);         
        }
    }, true)

    // 记录来源
    app.markFromSource(options,'pages/cart/index');
    
  },

    // 每次显示页面刷新登录状态
    onShow:function(){
        var that = this
        if(this.data.isReady == true){
            // 检查登录
            app.checkLogin(function(res){
                if(app.UTIL.isNull(res.sessionId) == true){
                    that.setData({
                        showLogin:true,
                        showEmpty: false
                    })
                    // 查询导航栏是否有动态
                    app.setNavBarStatus();
                }else{
                    that.setData({
                        showLogin:false,
                        user:res
                    })

                    // 刷新购物车
                    that.setData({
                        page: 0,
                        reload: false
                    })
                    that.loadMore(true)
                }
            },true)
        }
    },

    onReady:function(){
        this.setData({
            isReady:true
        })
    },


    // 页面上拉触底事件的处理函数
    /*onReachBottom:function(){
        this.loadMore()
    },*/


  // 加载更多数据
  loadMore: function(refresh) {
    var that = this
    var page = that.data.page
    if(refresh === true){
      var cartList = []
    }else{
      var cartList = that.data.cartList
    }

    // 检查是否可以加载
    if(that.data.reload == true){
      return false
    }else{
      page+=1
    }

    // loading
    wx.showToast({
      title: '加载中',
      icon: 'loading',
      duration: 10000,
      mask:true
    })
   // ajax获取产品列表
    app.API.getJSON({c:'Cart',a:'getCarts',sessionId:that.data.user.sessionId,rowNo:20,pageNo:page},function(res){
        wx.stopPullDownRefresh()
        wx.hideToast()
        if(res.data.code==0 && res.data.rs.data.length > 0){
          that.setData({
              showEmpty: false,
              reload: false,
              cartList: cartList.concat(res.data.rs.data),
              page: page
          })
        }else{
          if(page == 1){
            var showEmpty = true;
            cartList = [];
          }else{
            var showEmpty = false;
          }
          that.setData({
              showEmpty: showEmpty,
              reload: true,
              page: page,
              cartList: cartList
          })
        }
        that.resultData();
    })

  },

  // 选择单项
  selectItem: function(e){
  	var id=e.currentTarget.id
  	var newList = this.data.cartList
    	for (var i = 0; i < newList.length; i++) {
      	if(newList[i].cart_id == id){
      		if(newList[i].iconType == 'circle'){
      			newList[i].iconType = 'success'
  				  newList[i].iconColor = '#dc372f'
      		}else{
    				newList[i].iconType = 'circle'
    				newList[i].iconColor = '#ccc'
    			}
      	}
      }
      this.setData({
  		cartList: newList
  	})

  	this.resultData();
  },

  // 选择全部
  selectAll: function(e){
  	var iconType,iconColor
  	if(this.data.allIconType == 'circle'){
  		iconType = 'success'
    	iconColor = '#dc372f'
  	}else{
  		iconType = 'circle'
    	iconColor = '#ccc'
  	}

  	var newList = this.data.cartList
  	for (var i = 0; i < newList.length; i++) {
    	newList[i].iconType = iconType
		  newList[i].iconColor = iconColor
    }
    this.setData({
		cartList: newList,
	})

    this.resultData()
  },

  // 数量-
  cutNum: function(e){
      var id = e.currentTarget.id
      this.resultQuantity(id,'cut');
  },

  // 数量+
  plusNum: function(e){
      var id = e.currentTarget.id
      this.resultQuantity(id,'plus');
  },

  // 数量输入
  changeNum: function(e){
      var id = e.currentTarget.id,
          val = e.detail.value

      this.resultQuantity(id,val);
  },

  // 更新数量
  resultQuantity: function(id,act){
      var that = this
      var newList = that.data.cartList
      var theId = ''
      
      for (var i = 0; i < newList.length; i++) {
        // 转为数字类型
        newList[i].quantity = parseInt(newList[i].quantity)
        newList[i].siglequantity = parseInt(newList[i].siglequantity)

        if(newList[i].cart_id == id){
            theId = i
            switch(act){
              case 'cut':
                if(newList[i].quantity > 1){
                  newList[i].quantity -= 1
                }
                break;

              case 'plus':
                if(newList[i].quantity < newList[i].siglequantity){
                  newList[i].quantity += 1
                }
                break;

              default:
                act = parseInt(act)
                newList[i].quantity = act
                if(act <= 0 || act > newList[i].siglequantity){
                  that.errData.id = id
                  wx.showModal({
                      title: '输入有误',
                      content: '输入的数量不能为0，或者大于库存数',
                      showCancel: false,
                      complete: function(){
                          that.modalChange()
                      }
                  })
                }
                break;
           }
        }
      }

      // api更新数量
      app.API.getJSON({cart_id:id,quantity:newList[theId].quantity,sessionId:that.data.user.sessionId},function(res){
          console.log('updateQuantity:ok')
      },'index.php?c=Cart&a=updateQuantity')

      this.setData({
        cartList: newList
      })

      // 更新价格
      this.resultTotal()
  },

  /*
  * 整理数据
  * delid 需要删除的cart_id
  */
  resultData: function(delid){

    if(delid == undefined){
        delid = ''
    }

  	// 默认全选cart_ids
    var cart_ids=[],
        total = 0,
        allItem = 0,
        disabledSubmit = false,
        cartList = this.data.cartList,
        newList = new Array()

    for (var i = 0; i < cartList.length; i++) {
      if(delid == '' || delid != cartList[i].cart_id){
        if(cartList[i].siglequantity > 0 && cartList[i].stock==1){
          if(app.UTIL.isNull(cartList[i].iconType)){
              cartList[i].iconType = 'success';
          }
          allItem += 1;
          if(cartList[i].iconType != 'circle'){
            cart_ids.push(cartList[i].cart_id)
            total += cartList[i].price * cartList[i].quantity
          }
        }
        newList.push(cartList[i])
      }
    }

    var allIconType = 'circle',
    	  allIconColor = '#ccc'
    if(cart_ids.length == allItem){
    	allIconType = 'success'
    	allIconColor = '#dc372f'
    }

    if(cart_ids.length == 0){
      disabledSubmit = true;
    }

    if(newList.length == 0){
      // 隐藏导航栏红色点
      if( app.UTIL.compareVersion(app.systemInfo.SDKVersion,"1.9.0") >= 0){
          wx.hideTabBarRedDot({index: 2});
      }
    }else{
      // 显示导航栏红色点
      if( app.UTIL.compareVersion(app.systemInfo.SDKVersion,"1.9.0") >= 0){
          wx.showTabBarRedDot({index: 2});
      }
    }

    this.setData({
        cart_ids: cart_ids,
        allIconType: allIconType,
        allIconColor: allIconColor,
        total: total.toFixed(2),
        disabledSubmit: disabledSubmit,
        cartList: newList
    })
  },

  // 修改价格
  resultTotal: function(){
      var newList = this.data.cartList
      var total = 0
      for (var i = 0; i < newList.length; i++) {
          var subtotal = parseInt(newList[i].quantity)*parseFloat(newList[i].price)
          newList[i].subtotal = subtotal.toFixed(2);
          if(newList[i].iconType == 'success'){
            total += subtotal
          }
      }
      this.setData({
        total: total.toFixed(2),
        cartList: newList
    })
  },

  // 隐藏modal
  modalChange: function(e){
    var newList = this.data.cartList
    for (var i = 0; i < newList.length; i++) {
      if(newList[i].cart_id == this.errData.id){
        newList[i].quantity = 1
      }
    }

    this.setData({
      cartList: newList
    })

    // 更新价格
    this.resultTotal()
  },

  // 提交订单
  formSubmit: function(e){
      var param = this.data.cart_ids
      wx.navigateTo({
        url: '../order/order?cart_ids='+param
      })
  },

  // 删除购物车
  removeCart:function(e){
      var that = this,
          cart_id = e.currentTarget.id

      if(cart_id > 0 && cart_id != undefined){
        wx.showToast({
          title: '处理中',
          icon: 'loading',
          duration: 10000,
          mask:true
        })

        app.API.getJSON({c:'Cart',a:'del',cart_id:cart_id,sessionId:that.data.user.sessionId},function(res){
            wx.hideToast()
            if(res.data.code == 0){
                wx.showToast({
                  title: '已删除',
                  icon: 'success',
                  duration: 500
                })
                that.resultData(cart_id)
            }else{
                wx.showModal({
                  title: res.data.msg,
                  content: '',
                  showCancel: false,
                })
            }
        })
      }
  },


  // 跳到新页面
  gotoUrl:function(e){
      var url = e.currentTarget.dataset.url
      wx.navigateTo({
          url:url
      })
  },

  // 下拉刷新
  onPullDownRefresh: function() {
      this.setData({
          page: 0,
          reload: false
      })
      this.loadMore(true)
  }

})