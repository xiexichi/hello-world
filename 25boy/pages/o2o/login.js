var app = getApp();

Page({
    data:{
        wxuserinfo:[],
        gourl:'/pages/users/o2oOrder?type=nopay&ws=1',
        sessionKey:'',
        business:{},
        formParams: {
            password: '',
            nickname: '',
            phone: ''
        }
    },

    onLoad: function(options){
        var that = this,
            sessionKey = that.data.sessionKey;

        wx.showToast({
            title: '检查登录',
            icon: 'loading',
            duration: 10000
        });

        // 记录来源
        app.markFromSource(options,'pages/o2o/login');

        // 检查登录
        app.checkLogin(function(res){
            if( app.UTIL.isNull(res.sessionId) == false ){
                // 已经登录，跳转
                wx.redirectTo({
                    url:that.data.gourl
                })
            }else{
                // 未登录，获取sessionKey
                var sessionKey = wx.getStorageSync('sessionKey');
                if( app.UTIL.isNull(sessionKey) == true ){
                    sessionKey = res.sessionKey || '';
                }
                that.setData({
                    sessionKey: sessionKey
                });

                // 获取商户信息
                app.getBusinessInfo(function(res){
                    if(res.data.code == 0){
                        that.setData({
                            business : res.data.rs
                        });    
                    }
                });
                wx.hideToast();
            }
        });       
    },

    // 设置字段值
    setValue:function(e){
        const name = e.currentTarget.dataset.name;
        this.setData({
            [ 'formParams.'+name ]: e.detail.value
        })
    },

    // 快速登录－新用户注册
    registerSubmit:function(formParams){
        var that = this,
        password = formParams.password,
        nickname = formParams.nickname,
        phone = formParams.phone,
        error = '',
        wxuserinfo = app.globalData.wxUserInfo;


        if(app.UTIL.checkPassword(password) == false){
            error = '密码6-16位，只能输入数字、字母，横杠和下划线'
        }
        if(phone == ''){
            error = '请填写正确的手机号码'
        }
        if(error != ''){
            wx.showModal({
                title: '提示',
                content: error,
                showCancel: false
            })
            return false
        }

        // 推广者id
        var pid = wx.getStorageSync('pid');

        // 取来源标识
        var fromMark = app.getFromMark();

        wx.showToast({
            title: '请稍等',
            icon: 'loading',
            duration: 10000
        });
        var params = {
            phone:phone,
            password:password,
            fr:fromMark.fr,
            ch:fromMark.ch,
            pid:pid,
            type:'weapp',
            o2oreg:1,
            sessionKey:that.data.sessionKey,
            image_url:wxuserinfo.userInfo.avatarUrl,
            social_name:wxuserinfo.userInfo.nickName
        }
        app.API.postDATA(params,function(res){
            wx.hideToast();
            if(res.data.code==0){
                // 保存登录缓存
                wx.setStorageSync('userInfo', res.data)
                app.globalData.userInfo = res.data.rs;
                wx.showModal({
                    title: '成功',
                    content: '欢迎您加入25BOY!',
                    showCancel: false,
                    success:function(rs){
                        if(rs.confirm){
                            if(that.data.gourl == 'close'){
                                wx.navigateBack()
                            }else{
                                wx.redirectTo({
                                    url: that.data.gourl
                                })
                            }
                        }
                    }
                })
                return false;
            }else{
                wx.showModal({
                    title: '失败',
                    content: res.data.msg,
                    showCancel: false
                })
            }
        },'index.php?c=User&a=fastRegister')
    },


    /**
     * 2018-05-29 小程序更新弃用wx:getUserInfo
     * 获取微信用户信息
     */
    getUserInfo: function(e) {
        if( app.UTIL.isNull(e.detail.userInfo) ){
            wx.showModal({
                title: '登录失败',
                content: '请允许25boy获取微信授权',
                showCancel: false
            })
            return false;
        }
        app.globalData.wxUserInfo = e.detail;
        this.checkParams();
    },


    /**
     * 2018-05-29 弃用wx:getUserInfo后提交表单
     */
    checkParams: function() {
        this.registerSubmit(this.data.formParams);
    }

})