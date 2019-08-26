var app = getApp();

Page({
  data:{
    user:[],
    address_id:'',
    address:{
      address : '',
      city : '',
      district : '',
      receiver_name : '',
      receiver_phone : '',
      state : ''
    },
    addressDefault:{
        state:'省份',
        city:'城市',
        district:'区/县'
    },
    addressValue:{
        state:0,
        city:0,
        district:0
    },
    addressData:{
        state:[],
        city:[],
        district:[]
    },
    addresskey:{
        state:[],
        city:[],
        district:[]
    },
    params:{
      state:0,
      city:0,
      district:0
    }
  },

  onLoad:function(options){
		var that = this,
        address_id = options.id ? options.id : ''

		// 检查登录
		app.checkLogin(function(res){
		  if(res.sessionId == undefined || res.sessionId == ''){
		      wx.redirectTo({
		        url:'/pages/public/login?gourl='+escape('/pages/users/address')
		      })
		      return false
		  }else{
		      that.setData({
		        user:res,
            address_id:address_id
		      })

          // 取省份
          that.getArea()
		  }
		})

  },


  getEditRow:function(address_id){
        var that = this,
            address_id = this.data.address_id

        // 编辑获取地址
        if(address_id != undefined && address_id != ''){
              app.API.getJSON({c:'Address',a:'getAddress',address_id:address_id,sessionId:that.data.user.sessionId},function(res){
                  if(res.data.code == 0){
                      // 取子地区
                      that.getChildrenArea('city',res.data.rs.state)
                      that.getChildrenArea('district',res.data.rs.city)

                      var addressValue = that.data.addressValue
                      var addressDefault = {
                          state: res.data.rs.state_name,
                          city: res.data.rs.city_name,
                          district: res.data.rs.district_name
                      }
                      var params = {
                          state: res.data.rs.state,
                          city: res.data.rs.city,
                          district: res.data.rs.district
                      }
                      // 查找addressKey
                      for (var i = 0; i < that.data.addresskey.state.length; i++) {
                          if(that.data.addresskey.state[i] == res.data.rs.state){
                              addressValue.state = i
                          }
                      }
                      for (var i = 0; i < that.data.addresskey.city.length; i++) {
                          if(that.data.addresskey.city[i] == res.data.rs.city){
                              addressValue.city = i
                          }
                      }
                      for (var i = 0; i < that.data.addresskey.district.length; i++) {
                          if(that.data.addresskey.district[i] == res.data.rs.district){
                              addressValue.district = i
                          }
                      }

                      that.setData({
                          params:params,
                          address:res.data.rs,
                          addressValue:addressValue,
                          addressDefault:addressDefault
                      })
                  }
              })
        }
  },

  // 取省份
  getArea:function(){
      var that = this,
          addressData = this.data.addressData,
          addresskey = this.data.addresskey,
          arrayValue = new Array(),
          arraykey = new Array()

      wx.showToast({
        title: '请稍候',
        icon: 'loading',
        duration: 10000
      })
      app.API.getJSON({c:'Area',a:'getArea',area_type:'state'},function(res){
          if(res.data.code == 0){
              for (var i = 0; i < res.data.rs.length; i++) {
                  arrayValue.push(res.data.rs[i].area_name)
                  arraykey.push(res.data.rs[i].area_id)
              }
              addresskey.state = arraykey
              addressData.state = arrayValue
              that.setData({
                  addressData:addressData
              })
              that.getEditRow()
          }
          wx.hideToast()
      })
  },

  // 获取子区域
  getChildrenArea:function(type,parent_id){
      var that = this,
          addressData = this.data.addressData,
          addresskey = this.data.addresskey,
          arrayValue = new Array(),
          arraykey = new Array()

      if(type != undefined && type != '' && parent_id != undefined && parent_id != ''){
          app.API.getJSON({c:'Area',a:'getChildrenArea',parent_id:parent_id},function(res){
              if(res.data.code == 0){
                  for (var i = 0; i < res.data.rs.length; i++) {
                      arrayValue.push(res.data.rs[i].area_name)
                      arraykey.push(res.data.rs[i].area_id)
                  }
                  addresskey[type] = arraykey
                  addressData[type] = arrayValue
                  that.setData({
                      addressData:addressData
                  })
              }
          })
      }
  },


  // picker选择地区
  bindPickerChange:function(e){
      var idx = e.detail.value,
          type = e.currentTarget.dataset.type,
          addressDefault = this.data.addressDefault,
          addressValue = this.data.addressValue,
          params = this.data.params,
          toType = ''

      if(isNaN(idx) == false){

          // 修改显示
          var showTxt = this.data.addressData[type][idx]
          if(showTxt != '' && showTxt != undefined){
              addressDefault[type] = showTxt
              addressValue[type] = idx
              this.setData({
                  addressValue:addressValue,
                  addressDefault:addressDefault
              })
          }

          // 决定子地区
          if(type == 'state'){
              toType = 'city'
          }else if(type == 'city'){
              toType = 'district'
          }

          var area_id = this.data.addresskey[type][idx]
          // 设置选择的params
          params[type] = area_id
          this.setData({
              params:params
          })
          this.getChildrenArea(toType,area_id)
      }
  },


  // 根据名称查询一条位置
  getAreaByName:function(name,type){
      if(name != undefined && name != ''){
          var that = this,
              addressDefault = this.data.addressDefault,
              addressValue = this.data.addressValue,
              params = this.data.params

              
          app.API.getJSON({c:'Area',a:'getAreaByName',name:name},function(res){
              console.log(res)
              if(res.data.code == 0){
                  // 决定子地区
                  var toType = '';
                  if(type == 'state'){
                      toType = 'city'
                  }else if(type == 'city'){
                      toType = 'district'
                  }
                  // 取子地区
                  that.getChildrenArea(toType,res.data.rs.area_id)

                  // 查找addressKey
                  for (var i = 0; i < that.data.addresskey[type].length; i++) {
                      if(that.data.addresskey[type][i] == res.data.rs.area_id){
                          addressValue[type] = i
                      }
                  }

                  addressDefault[type] = res.data.rs.area_name
                  params[type] = res.data.rs.area_id
                  that.setData({
                      addressDefault:addressDefault,
                      addressValue:addressValue,
                      params:params
                  })
              }
          })
      }
  },


  // 定位当前位置
  getMyPosition:function(){
      var that = this

      if(wx.getSetting){
          wx.getSetting({
              success:function(res){
                  if (!res.authSetting['scope.userLocation']) {
                      wx.authorize({
                          scope: 'scope.userLocation',
                          success(rs) {
                              // 用户已经同意小程序使用位置功能，后续调用 wx.getLocation 接口不会弹窗询问
                              that.getLocation();
                          },
                          fail(rs){
                              // 拒绝授权情况
                              wx.showModal({
                                title: '提示',
                                content: '请授权25BOY使用“地理位置”功能。',
                                cancelText: '不同意',
                                confirmText: '前往授权',
                                success: function(res){
                                  if (res.confirm) {
                                      if(wx.openSetting){
                                        wx.openSetting({
                                          complete:function(rs){
                                            // 设置完成重新加载
                                            that.getLocation();
                                          }
                                        });
                                      }else{
                                        wx.showModal({
                                            title: '',
                                            content: '当前微信基础库版本不支持此操作，请手动授权：点击右上角菜单->关于25BOY商城->点击右上角菜单->设置->勾选保存到相册',
                                            showCancel: false,
                                            confirmText: '关闭'
                                        });
                                      }
                                  }
                                }
                              });
                          }
                      });
                  }else{
                      that.getLocation();
                  }
              }
          });
      }else{
           wx.showModal({
              title: '版本过低',
              content: '当前微信版本过低，无法使用该功能，请升级到最新微信版本后重试。',
              showCancel: false
          });
      }
  },


  getLocation: function(){
      var that = this;
      wx.showToast({
          title: '定位中',
          icon: 'loading',
          duration: 10000
      });
      wx.getLocation({
          success: function(res) {
              app.API.getJSON({c:'Area',a:'Geocoder',latitude:res.latitude,longitude:res.longitude},function(res){
                  if(res.data.code == 0){
                      var province = res.data.rs.addressComponent.province
                      var city = res.data.rs.addressComponent.city
                      var district = res.data.rs.addressComponent.district
                      that.getAreaByName(province,'state')
                      that.getAreaByName(city,'city')
                      that.getAreaByName(district,'district')
                      var address = res.data.rs.addressComponent.street+res.data.rs.addressComponent.street_number
                      that.setData({
                          ['address.address']: address
                      })
                  }
              })
          },
          complete:function(res){
              wx.hideToast();
          }
      })
  },

  // 提交表单
  formSubmit:function(e){
      var that = this,
          params = this.data.params,
          receiver_name = e.detail.value.receiver_name,
          receiver_phone = e.detail.value.receiver_phone,
          address = e.detail.value.address
          params.receiver_name = receiver_name
          params.receiver_phone = receiver_phone
          params.address = address
          params.sessionId = this.data.user.sessionId

      if(params.receiver_name == '' || params.receiver_name == undefined){
          wx.showModal({
            showCancel: false,
            title: '提示',
            content: '请输入收货人姓名'
          })
          return false
      }

      /*if(app.UTIL.checkPhone(params.receiver_phone) == false){
          wx.showModal({
            showCancel: false,
            title: '提示',
            content: '手机号码格式不正确'
          })
          return false
      }*/

      if(!params.state || !params.city || !params.address){
          wx.showModal({
            showCancel: false,
            title: '提示',
            content: '请完善收货地址'
          })
          return false
      }

      var urlafter = 'index.php?c=Address&a=add'
      if(that.data.address_id){
          params.address_id = this.data.address_id
          urlafter = 'index.php?c=Address&a=edit'
      }

      app.API.postDATA(params,function(res){
          if(res.data.code == 0){
              wx.showToast({
                  title:'成功',
                  icon:'success',
                  duration:2000,
                  success:function(){
                      wx.navigateBack()
                  }
              })
          }else{
              wx.showModal({
                  title:'提示',
                  content:res.data.msg,
                  showCancel:false
              })
          }
      },urlafter)

  },


  // 调起用户编辑收货地址原生界面，并在编辑完成后返回用户选择的地址。
  getWxAddress:function(){
      var that = this;

      if(wx.getSetting){
          wx.getSetting({
              success:function(res){
                  if (!res.authSetting['scope.address']) {
                      wx.authorize({
                          scope: 'scope.address',
                          success(rs) {
                              // 用户已经同意授权
                              that.wxChooseAddress();
                          },
                          fail(rs){
                              // 拒绝授权情况
                              wx.showModal({
                                title: '提示',
                                content: '请授权25BOY使用微信“通讯地址”功能。',
                                cancelText: '不同意',
                                confirmText: '前往授权',
                                success: function(res){
                                  if (res.confirm) {
                                      if(wx.openSetting){
                                        wx.openSetting({
                                          complete:function(rs){
                                            // 设置完成重新加载
                                            that.wxChooseAddress();
                                          }
                                        });
                                      }else{
                                        wx.showModal({
                                            title: '',
                                            content: '当前微信基础库版本不支持此操作，请手动授权：点击右上角菜单->关于25BOY商城->点击右上角菜单->设置->勾选保存到相册',
                                            showCancel: false,
                                            confirmText: '关闭'
                                        });
                                      }
                                  }
                                }
                              });
                          }
                      })
                  }else{
                      that.wxChooseAddress();
                  }
              }
          });
      }else{
          wx.showModal({
              title: '版本过低',
              content: '当前微信版本过低，无法使用该功能，请升级到最新微信版本后重试。',
              showCancel: false
          });
      }
  },


  wxChooseAddress: function(){
      var that = this;
      wx.chooseAddress({
          success:function(res){
              var address = {
                    address : res.detailInfo,
                    city : res.cityName,
                    district : res.countyName,
                    receiver_name : res.userName,
                    receiver_phone : res.telNumber,
                    state : res.provinceName
                  }

              that.getAreaByName(res.provinceName,'state');
              that.getAreaByName(res.cityName,'city');
              that.getAreaByName(res.countyName,'district');

              that.setData({
                  address:address
              });
          }
      });
  }

})