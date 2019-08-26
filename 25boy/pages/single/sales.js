/* 活动页面 */

var app = getApp();
var WxParse = require('../../wxParse/wxParse.js');

Page({
    data:{
        user:[],
        sales_id:'',
        wxParseData:'',
        info:{},
        showHomeBtn:true,
        footerData:[]
    },
    onLoad:function(options){
        var that = this;
        var sales_id = options.id ? options.id : '';

        // 检查登录
        app.checkLogin(function(res){
            if(res.sessionId != undefined && res.sessionId != ''){
                that.setData({
                    user: res
                })
            }
        })

        this.setData({
            sales_id:sales_id
        })
        this.getDetail()

        // 记录来源
        app.markFromSource(options,'pages/single/sales')
    },

    //  api获取数据
    getDetail:function(){
        var that = this,
            sales_id = this.data.sales_id

        wx.showToast({
            title: '加载中',
            icon: 'loading',
            duration: 10000
        })
        app.API.getJSON({id:sales_id},function(res){
            wx.hideToast()
            if(res.data.code == 0){
                // 设置当前页面的标题
                wx.setNavigationBarTitle({
                  title: res.data.rs.title
                })
                // wxParse富文本转xwml
                WxParse.wxParse('article', 'html', res.data.rs.content, that,5);
                that.setData({
                    info:res.data.rs
                })
            }else{
                wx.showModal({
                  title:'提示',
                  content:res.data.msg,
                  showCancel:false,
                  success:function(rs){
                      if (rs.confirm == true || rs.confirm == "true") {
                          wx.navigateBack()
                      }
                  }
              })
            }
        },'index.php?c=Sales&a=informationSingle')

    },


    // 分享页面
    onShareAppMessage:function(){
        var title = this.data.info.title + ' - 25BOY',
            desc = this.data.info.desc,
            path = '/pages/single/sales?id='+this.data.sales_id;

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