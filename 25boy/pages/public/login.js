var app = getApp();

Page({
	data:{
		wxuserinfo:[],
		hidden_fastLogin:false,
		hidden_login:true,
		hidden_register:true,
		gourl:'/pages/users/index',
		codeDisabled:false,
		codeTxt:'发送验证码',
		isReady:false,
		second:60,
		sessionKey:'',
		// 表单数据
		formParams: {
			phone: '',
			msgcode: '',
			password: '',
			nickname: '',
		}
	},

	onLoad:function(options){
		console.log('login',options);
		this.setData({
			options: options
		});
	},

	onReady:function(){
		this.setData({
			isReady:true
		})
	},

	onShow:function(){
		var that = this,
			sessionKey = that.data.sessionKey,
			options = that.data.options,
			showToast = false,
			delta = options.delta ? options.delta : 1;


		if(that.data.isReady == false && app.UTIL.isNull(options.gourl) == false){
			that.setData({
				gourl:unescape(options.gourl)
			});
		}

		// 显示loading
		if(options.infun == 'invalid'){
			showToast = true;
			var currentPages = getCurrentPages();
			delta = currentPages.length;
		}

		// 检查登录态
		console.log("login onShow");
		if(app.UTIL.isNull(sessionKey) == true){
			wx.getStorage({
				key: 'sessionKey',
				success: function(rs){
					that.setData({
						sessionKey: rs.data
					});
				},
				fail: function(rs){
					app.wxlogin(function(res){
						if(app.UTIL.isNull(res.rs) == false && app.UTIL.isNull(res.rs.sessionKey) == false){
							sessionKey = res.rs.sessionKey;
							// #wxlogin已经设置sesseionKey
							that.setData({
								sessionKey: sessionKey
							});
						}
						if(app.UTIL.isNull(res.sessionId) == false){
							if(that.data.gourl == 'close'){
				        		wx.navigateBack({
				        			delta:delta
				        		})
				        	}else{
					        	// 已登录跳到我的二五
					            wx.redirectTo({
					              url:that.data.gourl
					            })
					        }
						}
					},true);
				}
			});
		}
	},


	// 显示隐藏面板
	showPanel:function(e){
		var type = e.currentTarget.dataset.type;
		var data = {
			hidden_fastLogin:true,
			hidden_login:true,
			hidden_register:true
		}
		data['hidden_'+type] = false;
		this.setData(data);
	},

	// 设置字段值
	setValue:function(e){
		const name = e.currentTarget.dataset.name;
		this.setData({
			[ 'formParams.'+name ]: e.detail.value
		})
	},

	// 获取短信验证码
	getPhoneCode:function(e){
		var that = this,
			phone = this.data.formParams.phone,
			type = e.currentTarget.dataset.type

		if(phone == ''){
			wx.showModal({
			  title: '提示',
			  content: '请输入手机号码',
			  showCancel: false
			})
			return false
		}

		that.setData({
			codeDisabled:true,
			second:60
		})
		var timestamp = new Date().getTime();
		// 是否验证手机号码唯一性
		var checkPhone = 0;
		if(type == 'reg'){
			// 注册
			checkPhone = 1;
		}

		app.API.postDATA({phone:phone,timestamp:timestamp,checkPhone:checkPhone},function(res){
			if(res.data.code == 0){
				that.regetCodeTime();
			}else{
				wx.showModal({
				  title: '提示',
				  content: res.data.msg,
				  showCancel: false
				})
				that.setData({
						codeDisabled:false
				})
			}
		},'index.php?c=Message&a=phoneCode')
	},


	// 快速登录－已有帐号登录
	fastLoginSubmit: function(formParams){
		var that = this,
			phone = formParams.phone,
			msgcode = formParams.msgcode,
			wxuserinfo = this.data.wxuserinfo;

		if(phone == ''){
			wx.showModal({
			  title: '提示',
			  content: '请输入手机号码',
			  showCancel: false
			});
			return false;
		}
		if(msgcode == ''){
			wx.showModal({
			  title: '提示',
			  content: '请输入短信验证码',
			  showCancel: false
			});
			return false;
		}else{
			that.setData({
				msgcode: msgcode
			})
		}

		wx.showToast({
			title: '请稍等',
			icon: 'loading',
			duration: 10000
		});
		var params = {
			phone:phone,
			msgcode:msgcode,
			type:'weapp',
			sessionKey:that.data.sessionKey,
			image_url:wxuserinfo.userInfo.avatarUrl,
			social_name:wxuserinfo.userInfo.nickName
		}
		app.API.postDATA(params,function(res){
			wx.hideToast();
			if(res.data.code==0){
				// 登录成功－保存登录缓存
				wx.setStorageSync('userInfo', res.data)
				
				if(that.data.gourl == 'close'){
					wx.navigateBack()
				}else{
					wx.redirectTo({
						url: that.data.gourl
					})
				}
			}else if(res.data.code == 2){
				// 未注册－记录手机号到下一步
				if(app.UTIL.isNull(wxuserinfo.userInfo) == true || app.UTIL.isNull(wxuserinfo.userInfo.nickName) == true){
					wxuserinfo.userInfo.nickName = 'boy_' + app.UTIL.randomNum(6);
				}
				that.setData({
					wxuserinfo:wxuserinfo,
					hidden_fastLogin:true,
					hidden_login:true,
					hidden_register:false
				});
			}else{
				wx.showModal({
				  title: '登录失败',
				  content: res.data.msg,
				  showCancel: false
				})
			}
		},'index.php?c=User&a=fastLogin')


	},

	// 快速登录－新用户注册
	registerSubmit:function(formParams){
		var that = this,
			phone = formParams.phone,
			msgcode = formParams.msgcode,
			password = formParams.password,
			nickname = formParams.nickname,
			error = '',
			wxuserinfo = this.data.wxuserinfo;

		// 验证合法性
		if(app.UTIL.checkPassword(password) == false){
			error = '密码6-16位，只能输入数字、字母，横杠和下划线'
		}
		if(app.UTIL.getByteLen(nickname)<2 || app.UTIL.getByteLen(nickname)>16 || app.UTIL.illegalChar(nickname) == false){
			error = '昵称2-16位，不能包含特殊字符'
		}
		if(phone == ''){
			error = '手机号码为空，请返回上一步重新填写'
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
			msgcode:msgcode,
			password:password,
			nickname:nickname,
			fr:fromMark.fr,
			ch:fromMark.ch,
			pid:pid,
			type:'weapp',
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
					  title: '注册成功',
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
				  title: '注册失败',
				  content: res.data.msg,
				  showCancel: false
				})
			}
		},'index.php?c=User&a=fastRegister')
	},


	// 用户帐号密码登录
	loginSubmit:function(formParams){
		var that = this
		var account = formParams.phone,
			password = formParams.password,
			wxuserinfo = this.data.wxuserinfo

		if(account == ''){
			wx.showModal({
			  title: '提示',
			  content: '请输入25BOY登录帐号',
			  showCancel: false
			})
			return false
		}

		if(password == '' || password == undefined){
			wx.showModal({
			  title: '提示',
			  content: '密码6-16位，只能输入数字、字母，横杠和下划线',
			  showCancel: false
			})
			return false
		}

		wx.showToast({
			title: '登录中',
			icon: 'loading',
			duration: 10000
		});
		var params = {
			account:account,
			password:password,
			type:'weapp',
			sessionKey:that.data.sessionKey,
			image_url:wxuserinfo.userInfo.avatarUrl,
			social_name:wxuserinfo.userInfo.nickName
		}
		app.API.postDATA(params,function(res){
			wx.hideToast();
			if(res.data.code==0){
				// 保存登录缓存
				wx.setStorageSync('userInfo', res.data)
				
				if(that.data.gourl == 'close'){
					wx.navigateBack()
				}else{
					wx.redirectTo({
						url: that.data.gourl
					})
				}
			}else{
				wx.showModal({
				  title: '登录失败',
				  content: res.data.msg,
				  showCancel: false
				})
			}
		},'index.php?c=User&a=login')
	},


	goFindPass:function(){
		wx.redirectTo({
			url: '/pages/public/findpass?url='+escape(this.data.gourl)
		})
	},

	// 重新获取验证码倒计时
    regetCodeTime: function(){
        var that = this,
            second = that.data.second

        if (second == 0) {
            that.setData({
                codeTxt: "发送验证码",
                codeDisabled: false
            });
            return ;
        }
        var time = setTimeout(function(){
            that.setData({
                second: second-1,
                codeTxt: second+'秒后重发'
            });
            that.regetCodeTime();
        },1000)
    },

    /**
     * 2018-05-29 小程序更新弃用wx:getUserInfo
     * 获取微信用户信息
     */
    getUserInfo: function(e) {
    	const action = e.currentTarget.dataset.action;
        if( app.UTIL.isNull(e.detail.userInfo) ){
        	wx.showModal({
        		title: '登录失败',
        		content: '请允许25boy获取微信授权',
        		showCancel: false
        	})
            return false;
        }
        app.globalData.wxUserInfo = e.detail;
        this.setData({
        	wxuserinfo: e.detail
        })
        this.checkParams(action);
    },


    /**
     * 2018-05-29 弃用wx:getUserInfo后提交表单
     */
    checkParams: function(action) {
    	this[action](this.data.formParams);
    }


})