// 产品详情页

var app = getApp();

Page({
  data:{
    user:[],
    product_id: 0,
    windowWidth: 375,
    popLayerClass: 'hide',
    // 详情图尺寸
    imageSize: [],
    // 尺码 
    size: [],
    // 已选择参数
    param: {
        size: '',
        color: '',
        quantity: 1,
        tiptext: '请选择：颜色/尺码'
    },
    // 总库存
    sotcknum: 0,
    // 默认图片
    defaultimg: '',
    // 产品信息
    product: [],
    // 预售时间
    presale_date:'',
    // 是否关注
    is_favorites:0,
    // 详情图
    productdetail: [],
    // 商品规格
    stockprops: [],
    // 推广素材
    promoteInfo: [],
    displayPromote: 'hide',
    // 购物车数量
    cartTotal: 0,

    showHomeBtn:true,
    footerData:[]
  },

  onLoad: function(options){
    var that = this,
        product_id = options.id;


    if(product_id != undefined && product_id > 0){
        // 设置product_id
        that.setData({
            product_id: product_id
        });

        wx.showToast({
            title: '加载中',
            icon: 'loading',
            duration: 10000,
            mask: true
        });

        // 检查登录
        app.checkLogin(function(res){
            if(res.sessionId != undefined && res.sessionId != ''){
                that.setData({
                    user: res
                });
            }
            // 获取产品信息
            that.getProduct();

            // 购物车数量
            app.API.getJSON({sessionId:that.data.user.sessionId}, function(res){
                if(res.data.code == 0){
                    that.setData({
                        cartTotal: res.data.total
                    })
                }
            },'index.php?c=cart&a=getTotal');
        });
    }

    // 记录来源
    app.markFromSource(options,'pages/products/index')
  },

  // 获取产品信息
  getProduct: function(){
      var that = this,
          product_id= this.data.product_id;

      
      app.API.postDATA({product_id:product_id,sessionId:that.data.user.sessionId},function(res){
          // 隐藏loading
          wx.hideToast();

          if(res.data.code == 0){

              var productdetail = app.UTIL.getImagesSrc(res.data.rs.content);
              // 判断是否有效预售时间
              var presale = res.data.rs.presale_date;
              var oDate1 = new Date(presale);
              var oDate2 = new Date();
              var presale_date =  '';
              if(oDate1.getTime() > oDate2.getTime()){
                  var year = oDate1.getFullYear()
                  var month = oDate1.getMonth() + 1
                  var day = oDate1.getDate()
                  var presale_date =  year + '-' + month + '-' + day;
              }
              var product = res.data.rs;
              // 分销价
              if(product.seller_discount > 0 && product.seller_discount < 1){
                  product.seller_price = parseFloat(product.price*product.seller_discount).toFixed(2);
              }
              that.setData({
                  presale_date: presale_date,
                  product: product,
                  is_favorites: res.data.rs.is_favorites,
                  stockprops: res.data.rs.stockprops,
                  productdetail: productdetail,
                  ['promoteInfo.product_name']: res.data.rs.product_name
              })

              /*if(res.data.rs.stock == 1){
                that.setOther()
              }*/

          }else{

              wx.showModal({
                title: '提示',
                content: '参数有误，找不到该商品，或者不存在。',
                confirmText: '上一页',
                showCancel: false,
                success: function(res) {
                  if (res.confirm == true || res.confirm == "true") {
                    wx.navigateBack()
                  }
                }
              })

          }
      },'index.php?c=Product&a=single')

  },


  /*// 设置其它参数
  setOther: function(){
      var that = this

      // 修改图片高度与宽度一致
      that.setData({
          windowWidth: app.systemInfo.windowWidth
      })

      // 产品详情图尺寸
      var imageSize = []
      for (var i = 0; i < this.data.productdetail.length; i++) {
          imageSize[i] = {
              width: '',
              height: ''
          }
      }
      
      var defaultimg = that.data.product.images[0];
      var defaultSize = that.data.stockprops[0].size;
      that.setData({
          imageSize: imageSize,
          size: defaultSize,
          sotcknum: that.data.product.total_quantity,
          defaultimg: defaultimg
      })
  },

  // 重新设置图片尺寸
  resetImage: function(e){
    var idx = e.currentTarget.dataset.idx
    var zoom = e.detail.width/(this.data.windowWidth-20)    // 计算缩放比例

    var array = []
    for (var i = 0; i < this.data.imageSize.length; i++) {
        array[i] = this.data.imageSize[i]
        if(i == idx){
            array[i] = {
                width: (e.detail.width/zoom)+'px',
                height: (e.detail.height/zoom)+'px'
            }
        }
    }
    this.setData({
        imageSize: array
    })
  },*/


  // 弹出层
  showLayer: function(){
    this.setData({
        popLayerClass: 'show'
    })
  },
  hideLayer: function(){
    this.setData({
        popLayerClass: 'hide'
    })
  },

  // 选择尺码
  selectSize: function(e){
      var num = e.currentTarget.dataset.num,
          val = e.currentTarget.dataset.val

      if(val != this.data.param.size){
          var array = []
          for (var i = 0; i < this.data.size.length; i++) {
              array[i] = this.data.size[i]
              if(array[i].sku == val){
                  array[i]['class'] = 'text-hover'
              }else{
                  array[i]['class'] = 'text-none'
              }
          }

          this.setData({
              sotcknum: num,
              size: array
          })
          this.setParams('size',val)
      }
  },

  // 选择颜色
  selectColor: function(e){
      var val = e.currentTarget.dataset.val,
          stockprops = this.data.stockprops,
          currentSize = this.data.size,
          defaultimg = this.data.defaultimg

      if(val != this.data.param.color){
          var array = []
          for (var i = 0; i < stockprops.length; i++) {
              array[i] = stockprops[i]
              if(array[i].name == val){
                  array[i]['class'] = 'text-hover'
                  currentSize = array[i].size
                  defaultimg = array[i].img
              }else{
                  array[i]['class'] = 'text-none'
              }
          }

          this.setData({
              stockprops: stockprops,
              size: currentSize,
              defaultimg: defaultimg
          })
          this.setParams('color',val)
      }
  },

  // 选择数量
  setNember: function(e){
      var that = this
      var act = e.currentTarget.dataset.act,
          quantity = parseInt(this.data.param.quantity),
          sotcknum = parseInt(this.data.sotcknum)

      // 数量-
      if(act == 'cut'){
          if(quantity > 1){
              quantity -= 1
          }

      // 数量+
      }else if(act == 'plus'){
          if(quantity < sotcknum){
              quantity += 1
          }

      // 输入值
      }else{
          var value = parseInt(e.detail.value)
          if(value <= 0 || value > sotcknum){
              wx.showModal({
                  title: '输入有误',
                  content: '输入的数量不能为0，或者大于库存数',
                  showCancel: false,
                  complete: function(){
                    that.setParams('quantity',1)
                    return 1
                  }
              })
              return 1;
          }else{
              quantity = value
          }
      }

      if(quantity > sotcknum){
          quantity = sotcknum
      }
      if(quantity < 1){
          quantity = 1
      }

      this.setParams('quantity',quantity)
  },

  // 设置已选参数
  setParams: function(key,val){
      var param = this.data.param
      switch(key){
          case 'size':
              param['size'] = val
              break;
          case 'color':
              param['color'] = val
              param['size'] = ''
              break;
          case 'quantity':
              param['quantity'] = val
              break;
      }

      if(param.size==''){
          param.tiptext = "请选择：尺码"
      }
      if(param.color==''){
          param.tiptext = "请选择：颜色"
      }
      if(param.size=='' && param.color==''){
          param.tiptext = "请选择：颜色/尺码"
      }
      if(param.size!='' && param.color!=''){
          param.tiptext = "已选："+param.color+'，'+param.size
      }

      this.setData({
          param: param
      })

  },

  // 添加到购物车
  addCart: function(e){

      var param = this.data.param,
          error = '',
          that = this

      if(that.data.user.sessionId == undefined || that.data.user.sessionId == ''){
          wx.showModal({
              title: '请登录后操作。',
              content: '此操作需要登录25BOY帐号。',
              confirmText:'绑定登录',
              cancelText:'再看看',
              success:function(res){
                if (res.confirm == true || res.confirm == "true") {
                  var gourl = escape('/pages/products/index?id='+that.data.product_id)
                  wx.redirectTo({
                    url:'/pages/public/login?gourl='+gourl
                  })
                }
              }
          })
          return false
      }

      if(param.size=='' || param.color==''){
          error = param.tiptext
      }
      if(param.quantity <= 0 || param.quantity > this.data.sotcknum){
          error = '输入的数量不能为0，或者大于库存数'
      }

      if(error != ''){
          wx.showModal({
              title: '添加失败',
              content: error,
              showCancel: false
          })
      }else{

          wx.showToast({
              title: '处理中',
              icon: 'loading',
              duration: 10000,
              mask: true
          });

          // 加入请求参数
          var sessionId = that.data.user.sessionId
          var params = {
              sessionId:sessionId,
              product_id:that.data.product_id,
              size_prop:param.size,
              color_prop:param.color,
              quantity:param.quantity,
              sku_sn:that.data.product.sku_sn
          }

          // 提交到购物车
          app.API.postDATA(params,function(res){
              wx.hideToast();
              if(res.data.code == 0){
                    wx.showToast({
                        title: '添加成功',
                        icon: 'success',
                        duration: 2000
                    });
                    that.setData({
                        cartTotal: res.data.rs.total,
                        popLayerClass:'hide'
                    })
              }else{
                  wx.showModal({
                      title: '提示',
                      content: res.data.msg,
                      showCancel: true
                  })
              }
          },'index.php?c=Cart&a=add')
      }
  },

  // 预览图片
  previewImage: function(e){
      var type = e.currentTarget.dataset.type,
          img = e.currentTarget.dataset.img,
          imgurls = null;

      if(type == 'swiper'){
          imgurls = this.data.product.images;
      }else if(type == 'promoteProductImg'){
          imgurls = [this.data.promoteInfo.productImg];
      }else if(type == 'promoteRedpackImg'){
          imgurls = [this.data.promoteInfo.redpackImg];
      }else{
          imgurls = this.data.productdetail;
      }

      wx.previewImage({
          current: img ? img : '',
          urls: imgurls
      })
  },

  // 关注产品
  addFavoritesTap:function(e){
      var that = this,
          product_id = this.data.product_id


      if(that.data.user.sessionId == undefined || that.data.user.sessionId == ''){
          wx.showModal({
              title: '请登录后操作。',
              content: '此操作需要登录25BOY帐号。',
              confirmText:'绑定登录',
              cancelText:'再看看',
              success:function(res){
                if (res.confirm == true || res.confirm == "true") {
                  var gourl = escape('/pages/products/index?id='+that.data.product_id)
                  wx.redirectTo({
                    url:'/pages/public/login?gourl='+gourl
                  })
                }
              }
          })
          return false
      }

      wx.showToast({
          title: '关注中',
          icon: 'loading',
          duration: 10000
      });
      app.API.getJSON({c:'Favorites',a:'add',product_id:product_id,sessionId:that.data.user.sessionId},function(res){
          wx.hideToast();
          if(res.data.code == 0){
              wx.showToast({
                  title: '关注成功',
                  icon: 'success',
                  duration: 500
              })
              that.setData({
                  is_favorites:1
              })
          }else{
              wx.showModal({
                  title: '提示',
                  content: res.data.msg,
                  showCancel: true
              })
          }
      })
  },


  // 取消关注产品
  cancelFavoritesTap:function(e){
      var that = this,
          product_id = this.data.product_id

      wx.showToast({
          title: '处理中',
          icon: 'loading',
          duration: 10000
      });
      app.API.getJSON({c:'Favorites',a:'del',product_id:product_id,sessionId:that.data.user.sessionId},function(res){
          wx.hideToast();
          if(res.data.code == 0){
              wx.showToast({
                  title: '已取消关注',
                  icon: 'success',
                  duration: 500
              })
              that.setData({
                  is_favorites:0
              })
          }
      })
  },

  // 显示推广层
  showPromoteLayer: function(){
      var that = this,
          pid = that.data.user.promote_id;

      if(that.data.promoteInfo.productImg != undefined && that.data.promoteInfo.productImg != ''){
          that.setData({
              displayPromote: 'show'
          });
          return true;
      }

      if(pid == undefined || pid == ''){
          wx.showToast({
              title: '未登录',
              image: '/images/toast-info.png',
              duration: 2000,
              complete: function(){
                  wx.navigateTo({
                      url: '/pages/single/promoteApply'
                  });
              }
          });
      }else{
          wx.showToast({
              title: '获取中',
              icon: 'loading',
              duration: 10000
          });
          // 商品推广码
          app.API.getJSON({id:that.data.product_id, pid:pid},function(res){
              if(res.data.code == 0){
                  var productImg = 'https://api.25boy.cn' + res.data.rs;
                  that.setData({
                      ['promoteInfo.productImg']: productImg,
                      displayPromote: 'show'
                  });
                  // 红包推广码
                  that.getPromoteCode();
              }
          },'index.php?c=qrcode&a=productDetail');
      }
  },

  /**
  * 我的推广码 - 领取红包页面
  **/
  getPromoteCode: function(){
      var that = this,
          pid = that.data.user.promote_id,
          myCodeImg = that.data.myCodeImg;

      app.API.getJSON({pid: pid},function(res){
          wx.hideToast();
          if(res.data.code == 0){
              that.setData({
                  ['promoteInfo.redpackImg']: 'https://api.25boy.cn' + res.data.rs
              });
          }
      },'index.php?c=qrcode&a=redpackCode');
  },

  // 隐藏推广层
  hidePromoteLayer: function(){
      this.setData({
          displayPromote: 'hide'
      });
  },

  // 保存图片到手机
  saveImagesToLocal: function(e){
      var that = this,
          img = e.currentTarget.dataset.img;

      if(wx.getSetting){
          wx.getSetting({
              success:function(res){
                  if (!res.authSetting['scope.writePhotosAlbum']) {
                      wx.authorize({
                          scope: 'scope.writePhotosAlbum',
                          success(rs) {
                              // 用户已经同意小程序使用保存图片功能，后续调用 wx.saveImageToPhotosAlbum 接口不会弹窗询问
                              that.saveImageToPhotosAlbum(img);
                          },
                          fail(rs){
                              // 拒绝授权情况
                              wx.showModal({
                                title: '提示',
                                content: '请授权25BOY使用“保存到相册”功能。',
                                cancelText: '不同意',
                                confirmText: '前往授权',
                                success: function(res){
                                  if (res.confirm) {
                                      if(wx.openSetting){
                                        wx.openSetting({
                                          complete:function(rs){
                                            // 设置完成重新加载
                                            that.saveImageToPhotosAlbum(img);
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
                      that.saveImageToPhotosAlbum(img);
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

  // 授权后调用保存到相册功能
  saveImageToPhotosAlbum:function(img){
      wx.getImageInfo({
          src: img,
          success:function(res){
              wx.saveImageToPhotosAlbum({
                  filePath: res.path,
                  success:function(rs){
                      wx.showToast({
                          title: '保存成功',
                          icon: 'success',
                          duration: 1000
                      })
                  },
                  fail: function(rs){
                      console.log(rs);
                      wx.showModal({
                          title: '',
                          content: '保存图片失败，请点击打开图片动保存。',
                          showCancel: false
                      });
                  }
              })
          },
          fail: function(rs){
              console.log(rs);
              wx.showModal({
                  title: '',
                  content: '保存失败，请点击打开图片动保存。',
                  showCancel: false
              });
          }
      });
  },


  // 分享页面
  onShareAppMessage:function(){
      var title = '【'+this.data.product.brand_name+'】' + this.data.product.product_name,
          desc = '25BOY本土原创潮牌，结合传统醒狮元素打造中国本土原创潮牌，Happy Easy Anyway!', 
          path = '/pages/products/index?id='+this.data.product_id;

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