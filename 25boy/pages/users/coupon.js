var app = getApp();
var sliderWidth = 100; // 需要设置slider的宽度，用于计算中间位置

Page({
  data:{
    user:[],
    titleName: '可用优惠券',
    statusItems: ['可用优惠券', '失效优惠券'],
    loadmore: 'display-none',
    loadmore_line: 'display-none',
    page: 0,
    dataList: [],
    status: 0,
    sliderOffset: 40
  },

  onLoad:function(options){
      var that = this,
          type = options.type ? options.type : '',
          subnav = options.subnav ? options.subnav : ''

      // 检查登录
      app.checkLogin(function(res){
          if(res.sessionId == undefined || res.sessionId == ''){
              wx.redirectTo({
                url:'/pages/public/login?gourl='+escape('/pages/users/coupon')
              })
              return false
          }else{
              that.setData({
                user:res
              })

              that.getShopCouponList();
          }
      });
  },
  

  // api加载数据，代金券
  getShopCouponList:function(reset){
      // 重置参数
      if(reset === true){
        this.setData({
          loadmore: 'display-none',
          loadmore_line: 'display-none',
          page: 0,
          reload: false,
          dataList:[]
        });
      }

      var that = this
      var page = that.data.page
      var dataList = that.data.dataList

      // 检查是否可以加载
      if(that.data.reload == true){
        return false
      }else{
        page+=1
      }

      // loading
      that.setData({
        loadmore:'disply-block',
        reload: true
      })

      // ajax获取列表
      app.API.getJSON({
          c:'Voucher',
          a:'getUserVouchers',
          sessionId:that.data.user.sessionId,
          pageNo:page,
          status: that.data.status
        },function(res){
          if(res.data.code == 0){
            var lists = res.data.rs.data
            for(var i in lists){
              if(parseInt(lists[i].voucher_type_id)==2 && parseInt(lists[i].remaining_quota)==0){
                lists[i].expired = 1
              }else if(lists[i].voucher_type_id==1 && lists[i].status == 1){
                lists[i].expired = 1
              }else{
                lists[i].expired = ''
              }
            }
            that.setData({
              reload: false,
              loadmore: 'display-none',
              dataList: dataList.concat(lists),
              page:page
            })
          }else{
            that.setData({
              loadmore_line: 'disply-block',
              loadmore: 'display-none',
              reload: true,
              page:page
            })
          }
      })
  },

  // 筛选
  filterCouponTap: function(e) {
      var that = this
      var itemList =  that.data.statusItems;

      wx.showActionSheet({
        itemList: itemList,
        success: function(res) {
          if (!res.cancel) {
            that.setData({
                status:res.tapIndex,
                titleName:itemList[res.tapIndex]
            })
            that.getShopCouponList(true)
          }
        }
      })
  },

  // 页面上拉触底事件的处理函数
  onReachBottom:function(){
      this.getShopCouponList();
  }

})