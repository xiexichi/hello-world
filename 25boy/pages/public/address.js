var app = getApp();

Page({
  data:{
  	type:'',
  	user:[],
    addressList:[],
    loadmore_line:'display-none'
  },

  onLoad:function(options){
    var that = this

    // 检查登录
    app.checkLogin(function(res){
        if(res.sessionId == undefined || res.sessionId == ''){
            wx.redirectTo({
              url:'/pages/public/login?gourl='+escape('/pages/public/address')
            })
            return false
        }else{
            that.setData({
            	user:res
            })
            that.getAddress()
        }
    })

    var type = options.type
    if(type != '' && type != undefined){
    	that.setData({
    		type:type
    	})
    }

  },

  onShow:function(){
      this.getAddress()
  },

  // api获取收货地址
  getAddress:function(){
  	var that = this,
        addressList = this.data.addressList;

    if(that.data.user.sessionId == undefined || that.data.user.sessionId == ''){
        return false
    }

    // 初始化
    // wx.setStorageSync('userDefaultAddress', null)

  	var params = {
  		c:'Member',
  		a:'getAddress',
  		sessionId:that.data.user.sessionId
  	}
  	app.API.getJSON(params,function(res){
      wx.stopPullDownRefresh()
  		if(res.data.code == 0){
        addressList = res.data.rs;
  			that.setData({
  				addressList: addressList,
          loadmore_line:'display-none'
  			})
  		}else{
  			that.setData({
            loadmore_line:'display-block'
        })
  		}
  	})
  },

  // 选择地址
  selectItem:function(e){
  		var that = this
      if(that.data.type=='select'){
        wx.setStorageSync('userDefaultAddress', e.currentTarget.dataset)
  			wx.navigateBack()
  		}
  },

  // 设为默认地址
  setDefault:function(e){
  	var that = this,
  		address_id = e.currentTarget.dataset.id

  	if(address_id != '' && address_id != undefined){
  		var params = {
	  		c:'Member',
	  		a:'setDefaultAddress',
	  		sessionId:that.data.user.sessionId,
	  		address_id:address_id
	  	}
  		app.API.getJSON(params,function(res){
  			if(res.data.code == 0){
  				// 更新本地会员缓存信息
  				var user = wx.getStorageSync('userInfo')
		  		if(user.code == 0){
  					app.globalData.userInfo.address_id = address_id
		  			user.rs.address_id = address_id
			  		wx.setStorage({key:"userInfo",data:user})
	  				that.setData({
	  					user:user.rs
	  				})
		  		}
  				wx.showToast({
  					title:'设置成功',
  					icon:'success',
  					duration:1500
  				})
  			}else{
  				wx.showModal({
  					title:'提示',
  					content:res.data.msg,
  					showCancel:false
  				})
  			}
  		})
  	}

  },

  // 删除地址
  delAddress:function(e){
  	var that = this,
  		address_id = e.currentTarget.dataset.id

  	if(address_id != '' && address_id != undefined){
  		var params = {
	  		c:'Member',
	  		a:'delAddress',
	  		sessionId:that.data.user.sessionId,
	  		address_id:address_id
	  	}
  		app.API.getJSON(params,function(res){
  			if(res.data.code == 0){
          that.getAddress()
  				wx.showToast({
  					title:'已删除',
  					icon:'success',
  					duration:1500
  				})
  			}else{
  				wx.showModal({
  					title:'提示',
  					content:res.data.msg,
  					showCancel:false
  				})
  			}
  		})
  	}
  },


  // 编辑地址，跳转
  editAddress: function(e){
    wx.navigateTo({
      url: '/pages/public/addressModify?id='+e.currentTarget.dataset.id
    })
  },

  // 下拉刷新
  onPullDownRefresh: function() {
      this.getAddress()
  }

})