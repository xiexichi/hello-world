var app = getApp();

Page({

    /**
    * 页面的初始数据
    */
    data: {
        user: [],
        myCodeImg: '',
        helpImage: [
            'https://img.25miao.com/115/1501063991.gif',
            'https://img.25miao.com/115/1501063992.gif',
            'https://img.25miao.com/115/1501063993.gif',
            'https://img.25miao.com/115/1500363533.gif',
            'https://img.25miao.com/115/1501138255.jpg',
            'https://img.25miao.com/115/1501063994.gif'
        ]
    },

    /**
    * 生命周期函数--监听页面加载
    */
    onLoad: function (options) {
        // 记录来源
        app.markFromSource(options,'pages/single/promoteCourse');
    },


    /**
    * 生命周期函数--监听页面显示
    */
    onShow: function () {
        var that =this;

        // 检查登录
        app.checkLogin(function(res){
            if(res.sessionId != undefined && res.sessionId != ''){
                that.setData({
                    user: res
                });

                // 我的推广码
                // that.getPromoteCode();
            }
        });
    },


    /**
    * 我的推广码 - 领取红包页面
    **/
    getPromoteCode: function(){
        var that = this,
            pid = that.data.user.promote_id,
            myCodeImg = that.data.myCodeImg;

        if(pid == undefined || pid == ''){
            wx.showModal({
                title: '未登录',
                content: '请确保登录后再获取专属推广码'
            });
        }else{
            app.API.getJSON({pid: pid},function(res){
                if(res.data.code == 0){
                    that.setData({
                        myCodeImg: 'https://api.25boy.cn' + res.data.rs
                    });
                }
            },'index.php?c=qrcode&a=redpackCode');
        }
    },


    // 预览图片 
    previewImage: function(e){
        var idx = e.currentTarget.dataset.idx,
            imgurls = this.data.helpImage;
            imgurls[6] = this.data.myCodeImg;
        
        wx.previewImage({
            current: imgurls[idx],
            urls: imgurls
        })
    },


    /**
     * 用户点击右上角分享
     */
    onShareAppMessage: function(){
        var path = '/pages/single/promoteCourse';
        if(this.data.user.promote_id != undefined && this.data.user.promote_id != ""){
            path = path+'?pid='+this.data.user.promote_id;
        }

        return {
            title: '一次邀请，终身收益',
            desc: '25BOY本土原创潮牌，结合传统醒狮元素打造中国本土原创潮牌，Happy Easy Anyway!',
            path: path
        }
    }
})