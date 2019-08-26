var app = getApp();

Page({
    data:{
        user:[],
        productList:[],
        tipHide:true,
        showHomeBtn:true,
        footerData:{
            homeBtnTop:'580rpx',
            homeBtnLeft:'660rpx',
        },
        // 红包广告图片
        redpic:[],
        windowWidth: 375,
        options:[]
    },


    onLoad:function(options)
    {
        var that = this;

        // 确定类型
        if (app.UTIL.isNull(options.type)){
            // 普通红包广告
            options.type = 'normal';
        }

        // 修改图片高度与宽度一致
        that.setData({
            options:options,
            windowWidth: app.systemInfo.windowWidth
        })

        // 获取红包广告图片
        that.getRed(options.type);

        // 获取商品
        // that.getProducts();

        // 记录来源
        app.markFromSource(options,'pages/single/redpack');
    },

    // 取红包广告图片及商品
    getRed : function(type) {
        var that = this;

        // 取来源标识
        var fromMark = app.getFromMark();

        // loading
        wx.showToast({
            title: '加载中',
            icon: 'loading',
            duration: 10000,
            mask:true
        });
        app.API.getJSON({
            type:type,
            rowNo:50,
            fr:fromMark.fr,
            ch:fromMark.ch
        },function(res){
            wx.hideToast();
            if(res.data.code == 0){
                that.setData({
                    redpic:res.data.rs.data.pic,
                    productList:res.data.rs.data.list
                })

                if(res.data.rs.data.pos != undefined && res.data.rs.data.pos.posname != undefined && res.data.rs.data.pos.posname != ''){
                    // 设置当前页面的标题
                    wx.setNavigationBarTitle({
                      title: res.data.rs.data.pos.posname
                    });
                }
            }
        },'index.php?c=Picshow&a=getRed')
    },

    // 取商品列表
    getProducts:function(){
        var that = this;

        // loading
        wx.showToast({
            title: '加载中',
            icon: 'loading',
            duration: 10000,
            mask:true
        })
        app.API.getJSON({c:'Picshow',a:'getlist',posid:59,rowNo:50},function(res){
            wx.hideToast();
            if(res.data.code == 0){
                that.setData({
                    productList:res.data.rs.data
                })
            }
        })
    },


    // 领取红包
    getRedpack: function(e)
    {
        var that = this;

        // loading
        wx.showToast({
            title: '正在领取',
            icon: 'loading',
            duration: 10000,
            mask:true
        })

        // 检查登录
        app.checkLogin(function(res){
            if(res.sessionId == undefined || res.sessionId == ''){
                wx.hideToast();
                wx.showModal({
                    title: '未登录',
                    content: '您还没登录帐户，请登录后领取。',
                    confirmText: '马上登录',
                    showCancel: false,
                    success: function(res) {
                        if (res.confirm) {
                            wx.navigateTo({
                                url:'/pages/public/login?gourl=close'
                            })
                        }
                    }
                })
            }else{
                that.setData({
                    user: res
                });

                app.API.postDATA({sessionId:that.data.user.sessionId},function(res){
                    wx.hideToast();
                    if(res.data.code == 0){
                        that.setData({
                            tipHide:false
                        })
                    }else{
                        wx.showModal({
                            title: '领取失败',
                            content: res.data.msg,
                            showCancel: false
                        })
                    }
                },'index.php?c=Recharge&a=redpack');
            }
        });
    },

    confirmTip:function(){
        this.setData({
            tipHide:true
        })
        wx.navigateTo({
            url:'/pages/users/wallet?type=prepaid'
        })
    },
    hideTip:function(){
        this.setData({
            tipHide:true
        })
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


    resetImage: function(e){
        var that = this;
        var idx = e.currentTarget.dataset.idx;
        var zoom = e.detail.width/(this.data.windowWidth);    // 计算缩放比例
        var redpic = that.data.redpic;

        var array = []
        for (var i = 0; i < redpic.length; i++) {
            array[i] = redpic[i]
            if(i == idx){
                array[i].width = (e.detail.width/zoom)+'px';
                array[i].height = (e.detail.height/zoom)+'px';
            }
        }
        that.setData({
            redpic: array
        })
      },


    // 分享页面
    onShareAppMessage:function(){
        var path = '/pages/single/redpack';
        var options = this.data.options;
        if (app.UTIL.isNull(options.type)){
            options.type = 'normal';
        }
        path = path+'?type='+options.type;
        if(this.data.user.promote_id != undefined && this.data.user.promote_id != ""){
            path = path+'&pid='+this.data.user.promote_id;
        }

        // console.log(path);

        return {
            title: '送你50元现金红包',
            desc: '25BOY潮牌新国货，原创设计品牌。',
            path: path
        }
    },


    // 下拉刷新
    onPullDownRefresh: function(){
        this.onLoad(this.data.options)
        wx.stopPullDownRefresh()
    }

})