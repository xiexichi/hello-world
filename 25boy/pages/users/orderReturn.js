var app = getApp();

Page({

    /**
    * 页面的初始数据
    */
    data: {
        order_id:'',
        user:[],
        orderItem:[],
        orderType:'',
        photos:[],
        showUpbtn: true,
        maxNum:3,    // 最多上传图片
        params:{
            condition:1,     // 退换原因 1天七理由，2错发、漏发, 3质量问题
            items:[]
        },
        refund_price:0      // 应退金额
    },

    /**
    * 生命周期函数--监听页面加载
    */
    onLoad: function (options) {
        var that = this,
            order_id = options.id ? options.id : '';

        // 检查登录
        app.checkLogin(function(res){
            if(res.sessionId != undefined && res.sessionId != ''){
                var user = res
                if(order_id != undefined && order_id > 0){
                    wx.showToast({
                      title: '读取中',
                      icon: 'loading',
                      duration: 10000,
                      mask:true
                    })

                    // 设置数据
                    that.setData({
                        user: user,
                        order_id: order_id
                    })

                    // 获取订单信息
                    that.getOrderInfo(order_id);
                    
                }
            }
        })
    },

    // 获取订单商品
    getOrderInfo:function(order_id){
        var that = this;

        app.API.getJSON({order_id:order_id,sessionId:that.data.user.sessionId},function(res){
            // wx.hideToast();
            if(res.data.code == 0){
                that.setData({
                    orderType: res.data.rs.order_type
                });
            }else{
                wx.showModal({
                    title:'找不到订单',
                    content:'参数错误，找不到相关订单信息',
                    success:function(){
                        wx.navigateBack();
                    }
                })
            }

            // 获取订单信息
            that.getOrderitem(order_id);
        },'index.php?c=Order&a=getOrderSimple')
    },

    // 获取订单商品
    getOrderitem:function(order_id){
        var that = this;

        app.API.getJSON({order_id:order_id,sessionId:that.data.user.sessionId},function(res){
            wx.hideToast();
            if(res.data.code == 0 && res.data.rs.length > 0){
                that.setData({
                    orderItem: res.data.rs
                });
            }else{
                wx.showModal({
                    title:'找不到订单',
                    content:'参数错误，找不到相关订单信息',
                    success:function(){
                        wx.navigateBack();
                    }
                })
            }
        },'index.php?c=Order&a=getOrderitem')
    },

    // 添加图片
    addImg: function(){
        var that = this,
            photos = that.data.photos,
            maxNum = parseInt(that.data.maxNum);

          if(photos.length >= maxNum){
              wx.showModal({
                title: '提示',
                content: '最多允许上传'+maxNum+'张图片。',
                showCancel:false
              })
          }else{
              wx.chooseImage({
                  count:maxNum,
                  sizeType:['compressed'],
                  sourceType:['album','camera'],
                  success: function(res) {
                      var tempFilePaths = res.tempFilePaths
                      if(tempFilePaths.length > maxNum || (photos.length+tempFilePaths.length)>maxNum){
                          wx.showModal({
                            title: '提示',
                            content: '最多允许上传'+maxNum+'张图片。',
                            showCancel:false
                          })
                          return false
                      }

                      that.syncUpload(tempFilePaths);
                  }
              })
          }
    },


    // 串行上传图片
    syncUpload:function(tempFilePaths){
        var that = this;
        var photos = that.data.photos;

        if(tempFilePaths.length == 0){
            wx.hideToast()
            return false;
        }

        wx.showToast({
            title: '上传中',
            icon: 'loading',
            duration: 10000,
            mask:true
        })

        wx.uploadFile({
          url: app.API.BASE_URL+'index.php?c=Upload&a=upyun',
          filePath: tempFilePaths[0],
          name: 'images',
          formData:{
              sessionId:that.data.user.sessionId,
              tree_id:'1226',
              connport:'weapp'
          },
          success: function(res){
            var json = JSON.parse(res.data)
            if(json.code == 0){
                for (var j = 0; j < json.rs.length; j++) {
                  var images = {
                      url:json.rs[j].url,
                      file_id:json.rs[j].file_id
                  }
                  photos.push(images)
                }
                var showUpbtn = false
                if(photos.length < that.data.maxNum){
                    showUpbtn = true
                }
                that.setData({
                    showUpbtn:showUpbtn,
                    photos:photos
                })
                tempFilePaths.splice(0,1)
                that.syncUpload(tempFilePaths);
            }else{
                wx.showModal({
                    title:'提示',
                    content:json.msg,
                    showCancel:false
                })
            }
          }
        })
    },


    // 移除图片
    delImg:function(e){
        var that = this,
            idx = e.currentTarget.dataset.idx,
            images = new Array()

        for (var i = 0; i < that.data.photos.length; i++) {
          if(i == idx){
              app.API.postDATA({file_id:that.data.photos[i].file_id,tree_id:1226,sessionId:that.data.user.sessionId},function(res){
                  console.log('success');
              },'index.php?c=Upload&a=delFileByFileId')
          }else{
              images.push(that.data.photos[i])
          }
        }

        var showUpbtn = false
        if(images.length < that.data.maxNum){
          showUpbtn = true
        }
        that.setData({
          showUpbtn:showUpbtn,
          photos:images
        })
    },


    // 设置参数
    setParams:function(e){
        var that = this,
            orderItem = that.data.orderItem,
            key = e.currentTarget.dataset.key,
            val = e.detail.value,
            params = that.data.params;

        if(key && val){
            switch(key){
                case 'condition':
                    params[key] = val;
                    break;

                case 'items':
                    params[key] = val;
                    break;
            }

            // 排除不允许七天无理由退换商品
            var items = new Array();
            if(key == 'condition' && val == 1){
                for (var i=0; i < params.items.length; i++) {
                    for (var j=0; j < orderItem.length; j++) {
                        if(orderItem[j].free_return==1 && params.items[i]==orderItem[j].items_id){
                            items.push(orderItem[j].items_id);
                        }
                    };
                };
                params.items = items;
            }

            that.setData({
                params:params
            })

            that.getRefundPrice();
        }
    },

    // 查询应退金额
    getRefundPrice:function(){
        var that = this,
            orderItem = that.data.orderItem,
            params = that.data.params,
            items = params.items;

        if( items.length > 0 )
        {
            // 整理需要提交的数据
            var refunditem = '';
            var refundnum = '';
            for (var i = 0; i < items.length; i++) {
                for (var j = 0; j < orderItem.length; j++) {
                    if( orderItem[j].items_id == items[i] ){
                        if( orderItem[j].free_return == 1 || params.condition > 1 )
                        refunditem += orderItem[j].items_id+'='+orderItem[j].num+';';
                        // refundnum += orderItem[j].num + ',';
                    }
                };
            };

            // 获取退款金额
            wx.showToast({
                title: '计算应退款',
                icon: 'loading',
                duration: 10000,
                mask:true
            });
            app.API.postDATA({order_id:that.data.order_id,condition:params.condition,refunditem:refunditem,sessionId:that.data.user.sessionId},function(res){
                wx.hideToast();
                if( res.data.code == 0 ){
                    that.setData({
                        refund_price: parseFloat(res.data.refund_price)
                    })
                }
            },'index.php?c=Order&a=refund_price');
        }
    },


    // 提交申请
    submitForm: function(e) {
        var that = this,
            params = that.data.params,
            photos = that.data.photos,
            orderType = that.data.orderType,
            orderItem = that.data.orderItem

        // 非七天无理由需要上传图片
        if(params.condition != 1 &&  photos.length <= 0){
            wx.showModal({
                title: '提示',
                content: '非七天无理由需要上传图片凭证。',
                showCancel: false
            });
            return false;
        }
        // 选择退换货商品
        if(params.items.length <= 0){
            wx.showModal({
                title: '提示',
                content: '请勾选退换货商品。',
                showCancel: false
            });
            return false;
        }

        // 选择退换货商品
        if( ! app.UTIL.isNull(orderType) && orderType == 'combomeal' && params.items.length != orderItem.length){
            wx.showModal({
                title: '提示',
                content: '套餐订单，退货需要全数退还。',
                showCancel: false
            });
            return false;
        }

        // return false;
        var images = new Array();
        for (var i=0; i < photos.length; i++) {
            images.push(photos[i].url);
        };


        // 添加退换货记录
        wx.showToast({
            title: '提交中',
            icon: 'loading',
            duration: 10000,
            mask:true
        })
        var params = {
            order_id:that.data.order_id,
            sessionId:that.data.user.sessionId,
            images:images,
            exchange:e.detail.value.exchange,
            note:e.detail.value.note,
            condition:params.condition,
            items:params.items

        };
        app.API.postDATA(params,function(res){
            wx.hideToast();
            wx.showModal({
                title: '操作提示',
                content: res.data.msg,
                showCancel: false,
                success : function(re) {
                    if (re.confirm && res.data.code == 0) {
                        wx.redirectTo({
                            url: '/pages/users/orderDetail?id=' + res.data.id
                        });
                    }
                }
            });
        },'index.php?c=Order&a=exchangeReturn');
    },

    // 帮助提示
    showTips : function(e) {
        console.log(e);
        wx.showModal({
            title: '',
            content: e.currentTarget.dataset.msg,
            showCancel: false,
            confirmText: '知道了'
        });
    },

    // 取消并返回
    cancelBack : function() {
        wx.redirectTo({
            url: '/pages/users/orderDetail?id=' + this.data.order_id
        });
    }
     
})