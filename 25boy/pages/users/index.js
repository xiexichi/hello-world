var app = getApp();

Page({
	data:{
		user:{
			o2order_count: 0
		},
		canIUse: wx.canIUse('button.open-type.getUserInfo'),
		showLogin:false,
		isReady:false,
		displayMyCode:'hide',
		popBagLayerClass:'hide',
		mycode:[],
		autoCheckOrder:false,
		newOrder:[],
		displayPromoteTips:'hide',
		// 红点标记
		couponRedDot:0,
		promoteRedDot:0,
		// 关注公众号组件兼容判断
		canIUse: wx.canIUse('official-account')
	},

	onLoad:function(options){
		var that = this

		// 检查登录
	    app.checkLogin(function(res){
	    	wx.hideToast();
	        if(app.UTIL.isNull(res.sessionId) == true){
	        	that.setData({
	        		showLogin:true,
	        		user: {
	        			o2order_count: 0
	        		}
	        	})
	        	/*if(res != 'cancel'){
		            wx.navigateTo({
		              url:'/pages/public/login?gourl=close'
		            })
		        }*/
	        }else{
	            that.setData({
	            	showLogin:false,
	            	user:res
	            });
	            if(app.UTIL.isNull(res.promote_cash) == false && res.promote_cash > 0){
                    // 佣金提现提示
                    wx.getStorage({
                        key:"not_again",
                        complete: function(res){
                            if(app.UTIL.isNull(res.data)){
                                that.setData({
                                    displayPromoteTips: 'show'
                                });
                            }
                        }
                    });
                }
                // 查询导航栏是否有动态标记红点
                app.setNavBarStatus(res.sessionId, function(res){
            		that.setData({
            			couponRedDot: res[3].coupon,
            			promoteRedDot: res[3].promote
            		});
                });
	        }
	    },true)

	    // 记录来源
        app.markFromSource(options,'pages/users/index')
	},

	onShow:function(){
		if(this.data.isReady == true){
			var that = this
			// 检查登录
		    app.checkLogin(function(res){
		        if(app.UTIL.isNull(res.sessionId) == true){
		        	that.setData({
		        		showLogin:true,
		        		couponRedDot: 0,
        				promoteRedDot: 0,
		        		user: {
		        			o2order_count: 0
		        		}
		        	})
		        	// 取消红点标记
		        	app.setNavBarStatus();
		        }else{
		        	that.onPullDownRefresh();
		            that.setData({
		            	showLogin:false,
		            	user:res
		            });
		            // 查询导航栏是否有动态标记红点
	                app.setNavBarStatus(res.sessionId, function(res){
                		that.setData({
                			couponRedDot: res[3].coupon,
                			promoteRedDot: res[3].promote
                		});
	                });
		        }
		    },true)
		}
	},

	onReady:function(){
		this.setData({
			isReady:true
		})
	},

	// 跳到新页面
	gotoUrl:function(e){
	  var url = e.currentTarget.dataset.url
	  wx.navigateTo({
	      url:url
	  })
	},


	// 获取我的二唯码
	getMyQRcode:function(){
		var that = this


		wx.showToast({
			title: '生成二唯码',
			icon: 'loading',
			duration: 10000
		})
		app.API.getJSON({sessionId:this.data.user.sessionId},function(res){
			wx.hideToast()
			if(res.data.code == 0){
				that.setData({
					displayMyCode:'show',
					mycode:res.data.rs,
					autoCheckOrder:true
				})
				// ajax检测是否创建订单
				that.ajaxCheckOrder(res.data.rs.text)
						
			}else{
				wx.showModal({
					showCancel: false,
					title: '提示',
					content: '获取二唯码失败，请重试。'
				})
			}
		},'index.php?c=Qrcode&a=myCode')
	},

	// 隐藏二唯码层
	hideMyCodeLayer:function(e){
		if(e.target.dataset.tag != 'img'){
		    this.setData({
		        displayMyCode:'hide',
		        autoCheckOrder:false
		    })
		}
	},

	// 隐藏提现申请提示层
	hidePromoteTipsLayer:function(e){
		var tag = e.target.dataset.tag;
		if(tag != 'img'){
			// 不再提示
			if(tag == 'cancel'){
				wx.setStorage({
					key:"not_again",
					data:"promoteTips"
				});
			}
			this.setData({
				displayPromoteTips:'hide'
			})
		}
	},


	// ajax检测是否创建订单
	ajaxCheckOrder:function(code){
		var that = this

		if(code == ''){
			return false
		}

		if(that.data.autoCheckOrder == true){
	        var time = setTimeout(function(){
	            app.API.getJSON({code:code,sessionId:that.data.user.sessionId},function(res){
					if(res.data.code == 0 && res.data.rs.status == 'ok'){
						if(res.data.rs.type=='prepaid'){
							that.setData({
								displayMyCode: 'hide'
							})
							wx.navigateTo({
					            url: '/pages/users/wallet?type=prepaid'
					        })
						}else{
							// 2018-06-04 文杰修改，新o2o直接跳到订单详情支付
							that.setData({
								newOrder: res.data.rs,
								displayMyCode:'hide',
								// popBagLayerClass: 'show'
							})
							wx.navigateTo({
								url: '/pages/users/o2oOrderDetail?id='+res.data.rs.order_id
							})
						}
					}else{
						that.ajaxCheckOrder(code)
					}
				},'index.php?c=Synco2o&a=checkOrder')
	        },2500)
	    }
	},


	// 钱包支付方式
	checkBagSubmit:function(e){
		var that = this,
		  order_sn = this.data.newOrder.order_sn,
		  order_id = this.data.newOrder.order_id,
		  password = e.detail.value.password

		if(password == undefined || password == ""){
		  wx.showModal({
		    title: '请输入密码',
		    content: '支付密码与登录密码一样，忘记密码可通过微信登录进入我的二五->帐户设置，直接修改密码。',
		    showCancel: false
		  })
		  return false
		}

		wx.showToast({
		title: '正在付款',
		icon: 'loading',
		duration: 10000
		})

		// 获取签名sign
		app.API.getJSON({sn:order_sn,sessionId:that.data.user.sessionId},function(rs){

		  if(rs.data.code == 0){
		      var timeStamp = rs.data.rs.timeStamp
		      var salt = rs.data.rs.sign

		      // 发起支付
		      app.API.postDATA({sn:order_sn,password:password,timeStamp:timeStamp,salt:salt,sessionId:that.data.user.sessionId},function(res){
		          wx.hideToast()
		          if(res.data.code == 0){
					that.setData({
						popBagLayerClass: 'hide'
					})
					wx.navigateTo({
						url: '/pages/o2o/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
					})
		          }else if(res.data.code == "-2"){
		              that.setData({
						popBagLayerClass: 'hide'
					  })
		              wx.showModal({
		                title: '提示',
		                content: res.data.msg,
		                showCancel: false,
		                success:function(rs){
		                    if(rs.confirm){
		                        wx.navigateTo({
		                            url: '/pages/o2o/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
		                        })
		                    }
		                }
		              })
		          }else{
		              wx.showModal({
		                title: '提示',
		                content: res.data.msg,
		                showCancel: false
		              })
		          }
		      },'index.php/?c=Payment&a=payO2OrderByBag')

		  } //获取签名sign
		},'index.php?c=Payment&a=getSign')

	},


	// 隐藏层取消支付
	hideBagLayer: function(){
		this.setData({
			popBagLayerClass: 'hide'
		})
		wx.navigateTo({
            url: '/pages/users/o2oOrderDetail?id='+this.data.newOrder.order_id
        })
	},

	// 下拉刷新
	onPullDownRefresh: function() {
		var that = this,
			user = this.data.user;

		if( app.UTIL.isNull(user.sessionId) == true ){
			wx.stopPullDownRefresh();
			return false;
		}

	  // 更新本地登录缓存数据
	  app.updateUserInfo(user.sessionId,function(res){
	  		wx.stopPullDownRefresh();
	  		that.setData({
	  			user:res
	  		})
	  })
	},


	// 扫描二唯码
	scanCode:function(){
		wx.scanCode({
			success: (res) => {
				console.log(res);
				/*var strs = res.path.split("?");
				var path = '/'+strs[0];
				var parse = app.UTIL.parseURL(res.path);
				if(app.UTIL.isNull(parse.scene) == false){
					path = path + '?' + parse.scene;
				}*/
				var path = '/'+res.path;
				console.log("scanCode path: "+path);
				wx.navigateTo({
					url: path,
					fail: function(rs){
						console.log(rs);
					}
				})
			}
		});
	},


	// 获取微信用户信息
    authLogin:function(e) {
    	app.globalData.wxUserInfo = e.detail;
    	if( app.UTIL.isNull(e.detail.userInfo) == false ){
	        wx.navigateTo({
	            url: '/pages/public/login?gourl=close'
	        });
        }
    }

})