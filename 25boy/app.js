//app.js
var UTIL = require('./utils/util.js');
var API = require('./utils/api.js');
var ENCRYPT = require('./utils/encrypt.js');

App({
    version: 'v2.0.0',
    UTIL: UTIL,
    API: API,
    ENCRYPT: ENCRYPT,
    footerData:[],
    windowWidth:375,
    systemInfo:{
        windowWidth:375,
        windowHeight:675,
        SDKVersion: ''
    },
    // 全局数据
    globalData: {
      userInfo: [],
      wxUserInfo: [],
      extraData: {
        thirdapp: ''
      }
    },


    getVersion: function(){
        return this.version;
    },

    // 当小程序初始化完成时，会触发 onLaunch（全局只触发一次）
    onLaunch: function(options) {
        var that = this;

        UTIL.removeUserCache();

        wx.getNetworkType({
          success: function(res) {
            if(res.networkType == 'none' || res.networkType == 'unknown'){
                wx.showModal({
                  title: '网络异常',
                  content: '手机无法连接互联网，不能正常使用服务，请检查您的网络连接设置。',
                  showCancel: false
                })
            }
          }
        });

        // 获取系统信息-窗口宽度
        wx.getSystemInfo({
            success: function (res) {
                res.SDKVersion = res.SDKVersion ? res.SDKVersion : '';
                that.systemInfo = res;
                that.windowWidth = res.windowWidth;
            }
        });

        // 第三方自动登录userToken
        if(options.query.userToken){
            wx.setStorageSync('userToken', options.query.userToken);
        }

        that.resetData();

    },


    // 生命周期函数--监听小程序显示
    onShow: function(options) {
        if(options.referrerInfo && options.referrerInfo.extraData){
            var extraData = {};
            if(( typeof  options.referrerInfo.extraData) ==  "string" ){
                extraData = JSON.parse(options.referrerInfo.extraData);
            }else{
                extraData = options.referrerInfo.extraData;
            }
            this.globalData.extraData = extraData;
        }
    },

    /*
    * 临时小程序码扫描转换路径
    * pages/index/index?scene=p_product-id_1097-pid_7
    * 得到scene，先用 - 分割，再用 _ 分割得到集合{p:product, id:1097, pid:7}
    * 用 p 判断跳转页面，其它的为传递的参数
    */
    queryScene: function(options){
        if(this.UTIL.isNull(options.scene) == false){
            var scene = options.scene;
            var temp1 = scene.split('-');
            var param = {};
            for(var i=0; i<temp1.length; i++){
                var val = temp1[i].split('_');
                param[val[0]] = val[1];
            }
            var pagesUrl = 'pages/index/index';
            if(this.UTIL.isNull(param.p) == false){
                var p = param.p;
                delete param.p;
                switch(p){
                    case 'redpack':
                        pagesUrl = 'pages/single/redpack';
                        break;
                    case 'product':
                        pagesUrl = 'pages/products/index';
                        break;
                    case 'category':
                        pagesUrl = 'pages/products/index';
                        break;
                }
            }
            pagesUrl = '/' + pagesUrl + '?' + this.UTIL.parseParam(param);
            console.log('navigateTo: ' + pagesUrl);
            if(pagesUrl.indexOf('pages/') >= 0){
                wx.navigateTo({
                    url: pagesUrl,
                    fail:function(res){
                        console.log('----------------navigateTo fail--------------------')
                        console.log(res)
                    }
                });
            }
        }
    },


    /**
    * 获取用户登录信息
    * @param function [cb] 回调
    * @param bool [showToast] 是否显示加载中以及未授权提示
    **/
    checkLogin: function(cb, showToast){
        var that = this;

        if(showToast == undefined){
            showToast = false;
        }

        // 检查是否已经登录
        // 20170823，取消app.globalData.userInfo
        // if(that.globalData.userInfo.length > 0 && that.UTIL.isNull(that.globalData.userInfo.sessionId) == false){
        //     // 有全局数据，直接返回登录结果
        //     typeof cb == "function" && cb(that.globalData.userInfo)
        // }else{
            // 已经绑定微信，自动登录
            var user = wx.getStorageSync('userInfo')
            if (user.code == 0) {
                that.globalData.userInfo = user.rs
                // 有缓存，直接返回登录结果
                typeof cb == "function" && cb(user.rs);
            }else{
                // 未登录无缓存
                console.log('checkLogin:无缓存用户缓存');

                // var sessionKeyTime = wx.getStorageSync('sessionKeyTime');
                // var nowTime = new Date().getTime();
                // if(sessionKeyTime>nowTime){
                //     console.log('sessionKeyTime:'+(sessionKeyTime-nowTime)/1000);
                //     typeof cb == "function" && cb('hasSession');
                // }else{
                    // 清除用户相关缓存
                    that.globalData.userInfo = [];
                    that.UTIL.removeUserCache();
                    // 尝试登录
                    that.wxlogin(cb, showToast);
                // }
            }
        // }
    },


    /**
    * 检查微信登录状态是否有效
    * 有效不处理，无效尝试重新登录
    **/
    wxCheckSession: function(cb, showToast){
        var that = this;

        console.log("++++++++++++++++app.wxCheckSession");

        var sessionKey = wx.getStorageSync('sessionKey');
        var nowTime = new Date().getTime();
        wx.checkSession({
            complete: function(res){
                if(res.errMsg == 'checkSession:ok' && that.UTIL.isNull(sessionKey) == false){
                    typeof cb == "function" && cb('hasSession');
                }else{
                    // 清除用户相关缓存
                    that.globalData.userInfo = [];
                    that.UTIL.removeUserCache();
                    // 尝试登录
                    that.wxlogin(cb, showToast);
                }
            }
        });
    },


    /**
    * wx.login(OBJECT)
    * 调用微信登录接口，不需要授权
    * @param function [cb] 回调
    * @param bool [showToast] 是否显示加载中
    **/
    wxlogin: function(cb, showToast){
        var that = this;
        console.log("++++++++++++++++app.wxlogin");
        // loading
        if(showToast === true){
            wx.showToast({
                title: '检查登录',
                icon: 'loading',
                duration: 10000,
                mask: true
            });
        }

        wx.login({
            success: function(loginRes) {
                if (loginRes.code) {
                    //发起网络请求，登录网站
                    that.API.postDATA({code:loginRes.code},function(res){
                        console.log('api.oneLogin',res);
                        if(showToast === true){
                            wx.hideToast();
                        }
                        if(res.data.code == 0){
                            // 有绑定微信用户登录成功
                            that.globalData.userInfo = res.data.rs;
                            wx.setStorageSync('userInfo', res.data);
                            // userToken用完登录后可删除
                            wx.removeStorage({
                                key: 'userToken'
                            });
                            typeof cb == "function" && cb(res.data.rs);
                        }else{
                            // 未绑定登录失败，返回微信登录信息
                            var sessionKey = '';
                            var expires_in = 0;
                            if(that.UTIL.isNull(res.data.rs) == false){
                                if(that.UTIL.isNull(res.data.rs.sessionKey) == false){
                                    sessionKey = res.data.rs.sessionKey;
                                }
                                if(that.UTIL.isNull(res.data.rs.expires_in) == false){
                                    expires_in = res.data.rs.expires_in;
                                    expires_in = new Date().getTime()+1000*expires_in;
                                }
                            }
                            wx.setStorage({key:'sessionKey', data:sessionKey});
                            wx.setStorage({key:'sessionKeyTime', data:expires_in});
                            typeof cb == "function" && cb(res.data);
                        }
                    },'index.php?c=weapp&a=oneLogin');
                } else {
                    console.log('获取用户登录态失败！' + loginRes.errMsg)
                    wx.showModal({
                        title: '登录失败',
                        content: loginRes.errMsg,
                        showCancel: false
                    });
                    typeof cb == "function" && cb('fail');
                }
            }
        });
    },


    /**
    * * wx.getUserInfo(OBJECT)
    * 调用微信 getUserInfo 接口，获取用户头像昵称等资料
    * 缓存数据
    **/
    wxGetUserInfo: function(cb){
        var that = this;

        // 读取缓存
        var wxGetUserInfo = this.globalData.wxUserInfo;

        if(that.UTIL.isNull(wxGetUserInfo) == false){
            // 缓存有效，直接返回
            typeof cb == "function" && cb(wxGetUserInfo);
        }else{
            // 无缓存数据，重新拉取
            wx.getUserInfo({
                withCredentials: false,  //是否带上登录态信息
                // 允许授权
                success:function(res){
                    // wx.setStorageSync('wxGetUserInfo', res);
                    that.globalData.wxUserInfo = res;
                    typeof cb == "function" && cb(res);
                },
                // 拒绝授权
                cancel: function(res){
                    console.log('wx.wxGetUserInfo: cancel 用户拒绝授权拉取资料');
                    typeof cb == "function" && cb('cancel');
                },

                fail:function(rs){
                    console.log('wx.wxGetUserInfo: fail 拉取用户资料失败');
                    typeof cb == "function" && cb('cancel');
                }
            });
        }
    },


    // 更新用户资料
    updateUserInfo:function(sessionId,callback){
        var that = this
        if(sessionId == undefined || sessionId == ''){
            return false;
        }

        that.API.getJSON({c:'Member',a:'getInfo',sessionId:sessionId},function(res){
            if(res.data.code == 0){
                // 返回有绑定保存登录会话
                that.globalData.userInfo = res.data.rs
                wx.setStorageSync('userInfo', res.data)
                typeof callback == "function" && callback(res.data.rs)
            }
        })

    },

    // 取来源标识
    getFromMark: function(n){
        // fr&ch 来源标识自动过期
        var fromMark = wx.getStorageSync('fromMark');
        var nowTime = new Date().getTime()
        if(this.UTIL.isNull(fromMark) == true){
            fromMark = {
                fr:'',
                ch:'',
                timeout:0
            };
        }

        if(fromMark.timeout-nowTime != NaN && fromMark.timeout-nowTime <= 0){
            fromMark = {
                fr:'',
                ch:''
            };
        }

        if( this.UTIL.isNull(n) == false ){
            return fromMark[n];
        }else{
           return fromMark;
        }
    },


    // 记录来源 ?fr=xxx&ch=xxx
    markFromSource:function(options,page){

        // 解决扫码 & 变成  &amp; 导致无法识别二级参数问题
        var data = {};
        for(var key in options){
            var val = options[key];
            if( key.indexOf('amp;') >= 0 ){
                key = key.replace('amp;', '');
            }
            data[key] = val;
        }

        // 缓存推广者pid
        if(options.pid != undefined && options.pid != ''){
            wx.setStorage({key:'pid', data:options.pid})
        }


        var fr = data.fr ? data.fr : '',
            ch = '';

        // 子标识
        if(data.ch != undefined && data.ch != '' && data.ch != 'undefined'){
            ch = data.ch;
        }

        // 一级来路标识
        if(fr != undefined && fr != ''){
            // 缓存来路标识，本地缓存有效期1天
            var formCode = {fr:fr,ch:ch,timeout:new Date().getTime()+86400*1000}
            wx.setStorage({
              key:'fromMark',
              data:formCode
            })
        }

        // 用户信息
        var user = wx.getStorageSync('userInfo')
        var sessionId = '';
        if(user.code == 0 && user.rs.sessionId != undefined && user.rs.sessionId != ''){
            sessionId = user.rs.sessionId
        }

        // url参数
        var paramStr = this.UTIL.parseParam(data);
        if(paramStr != ''){
            page += '?' + paramStr;
        }

        var prarms = {
            fr:fr,
            ch:ch,
            client:'weapp',
            u:this.ENCRYPT.encode(page),  // 受访页面
            sessionId:sessionId
        }
        this.API.postDATA(prarms,function(res){
            console.log('-------------受访页面：'+page)
        },'index.php?c=Origin&a=mark')

    },

    // 进入首页按钮拖动事件
    homeBtnTouchMoveFun:function(e){
        var touchs = e.touches[0];
        var clientX = touchs.clientX;
        var clientY = touchs.clientY;
        var zoom = this.systemInfo.windowWidth/750;
        var scale = Math.ceil(90*zoom/2);

        //防止坐标越界,view宽高的一般
        if (clientX < scale) clientX=scale;
        if (clientX > (this.systemInfo.windowWidth-scale)) clientX=this.systemInfo.windowWidth-scale;
        if (clientY < scale) clientY=scale;

        var footer = {
            homeBtnTop:clientY-scale+'px',
            homeBtnLeft:clientX-scale+'px',
            homeBtnOpacity:1
        }
        this.footerData = footer;
        return footer;
    },
    // 拖动结束
    homeBtnTouchMoveEndFun:function(e){
        var footer = this.footerData;
        footer.homeBtnOpacity = 0.45;
        return footer;
    },


    // 每月1号重置参数
    resetData: function(){
        var myDate = new Date();
        var today = myDate.getDate();
        if(today == 1){
            // 删除不再提示记录
            wx.removeStorage({
                key: 'not_again'
            });
        }
    },

    // 获取商户信息
    getBusinessInfo: function(cb){
        // 取来源标识
        var that = this,
        fromMark = this.getFromMark();

        if(fromMark.fr == 'business'){
            that.API.getJSON({code: fromMark.ch},function(res){
                typeof cb == "function" && cb(res);
            },'index.php?c=Origin&a=getBusiness');
        }
    },


    // 查询导航栏是否有动态标记红点
    setNavBarStatus: function(sessionId, cb){
        var that = this;

        // wx.showTabBarRedDot 要求SKD基础库1.9.0或以上
        if( that.UTIL.compareVersion(that.systemInfo.SDKVersion,"1.9.0") >= 0)
        {
            that.API.getJSON({sessionId: sessionId}, function(res){
                // console.log(res);
                if(res.data.code == 0){
                    var jsonData = res.data.rs.data;
                    for(var i=0; i<jsonData.length; i++){
                        if(jsonData[i].status == 1){
                            wx.showTabBarRedDot({index: i});
                        }else{
                            wx.hideTabBarRedDot({index: i});
                        }
                    }
                    typeof cb == "function" && cb(jsonData);
                }
            },'index.php?c=index&a=navbarStatus');
        }
    },

    // 检查待付款的o2o订单
    checkPayingOrder: function(sessionId, business_code){
        var that = this;
        var time = setTimeout(function() {
            console.log(sessionId)
            that.ajaxCheckOrder(sessionId, business_code);
        }, 3000);
    },

    // ajax检测是否有未付款的o2o订单
    ajaxCheckOrder:function(sessionId, business_code){
        var that = this
        if(sessionId == ''){
            return false
        }

        API.getJSON({sessionId:sessionId, code:business_code},function(res){
            if(res.data.code == 0){
                wx.navigateTo({
                    url: '/pages/users/o2oOrderDetail?id='+res.data.rs.order_id
                })
            }else{
                that.checkPayingOrder(sessionId, business_code)
            }
        },'index.php?c=Synco2o&a=checkPayingOrder')
    },

    // 保存模板消息表单凭证
    saveTemplateSign(form_id, sessionId, type){
        API.postDATA({form_id:form_id, sessionId:sessionId, type:type},function(res){
            console.log('saveTemplateSign',res);
        }, 'index.php?c=wxtemplate&a=saveTemplateSign')
    }

})
