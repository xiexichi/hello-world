var app = getApp();

Page({
    data:{
        user:[],
        voucher: {},
        voucher_code: '',
        invalid: false, //失效
        lave_quantity: 0,
        showHomeBtn:true,
        footerData:{}
    },


    onLoad:function(options)
    {
        var voucher_id = options.id || '';
        this.voucher_code = options.code || '';
        console.log(this.voucher_code)
        this.getVoucherInfo(voucher_id);
        this.setData({
            footerData: app.footerData
        })
        // 记录来源
        app.markFromSource(options,'pages/activity/voucher');
    },

    // 获取优惠券信息
    getVoucherInfo: function(voucher_id){
        var that = this;
        app.API.postDATA({ voucher_id: voucher_id},function(res){
            if(res.data.code == 0){
                var voucher = res.data.rs;
                if(!voucher || voucher.length == 0){
                    that.setData({
                        invalid: true
                    });
                }else{
                    var lave_quantity = parseInt(voucher.quantity)-parseInt(voucher.received_quantity);
                    that.setData({
                        lave_quantity: lave_quantity,
                        voucher: voucher
                    });
                }
            }else{
                wx.showToast({
                    title: res.data.msg,
                    icon: 'none'
                });
            }
        },'index.php?c=voucher&a=getVouchers');
    },

    // 领取优惠券
    getVoucher: function(){
        var that = this;
        var voucher_id = this.data.voucher.voucher_id;

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
                app.API.postDATA({
                    sessionId:res.sessionId,
                    voucher_id: voucher_id,
                    code: that.voucher_code
                },function(rs){
                    if(rs.data.code == 0){
                        wx.showToast({
                            title: '领取成功',
                            icon: 'success',
                            mask: true,
                            duration: 1500
                        });
                        setTimeout(function() {
                            wx.redirectTo({
                                url: '/pages/users/coupon'
                            })
                        }, 1500);
                    }else{
                        wx.showToast({
                            title: rs.data.msg,
                            icon: 'none',
                            duration: 3000
                        });
                    }
                },'index.php?c=voucher&a=getVoucher');
            }
        }, true);
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
    },

    // 分享页面
    onShareAppMessage:function(){
        var path = '/pages/activity/voucher?id='+this.data.voucher.voucher_id;
        if(this.data.user.promote_id != undefined && this.data.user.promote_id != ""){
            path = path+'&pid='+this.data.user.promote_id;
        }
        return {
            title: '25BOY优惠券',
            desc: this.data.voucher.title,
            path: path
        }
    }

})