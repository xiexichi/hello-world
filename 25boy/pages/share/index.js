var app = getApp();

Page({
  data:{
    user:[],
    loadmore: 'display-none',
    loadmore_line: 'display-none',
    page: 0,
    datalist:[],
    reload: false,
    filterList: ['按时间','按票数'],
    filterKey:['time','zan'],
    filterName:'按时间',
    filter:'time'
  },

  onLoad: function(options){
    var that = this;

    that.setData({
      footerData:app.footerData
    })

    // 检查登录
    app.checkLogin(function(res){
        if(res.sessionId != undefined && res.sessionId != ''){
            that.setData({
                user: res
            })
        }
    })

    // ajax加载第一页
    that.loadMore();

    // 记录来源
    app.markFromSource(options,'pages/share/index')

  },

  // 页面上拉触底事件的处理函数
  onReachBottom:function(){
      this.loadMore()
  },

  // 加载更多数据
  loadMore: function(refresh) {
    var that = this,
        page = that.data.page,
        filter = that.data.filter;

    if(refresh === true){
      var datalist = []
    }else{
      var datalist = that.data.datalist
    }

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

   // ajax获取产品列表
    app.API.getJSON({c:'Share',a:'shareList',pageNo:page,sort:filter},function(res){
        wx.stopPullDownRefresh()
        if(res.data.code == 0){
          that.setData({
            reload: false,
            loadmore: 'display-none',
            datalist: datalist.concat(res.data.rs.data),
            page: page
          })
        }else{
          that.setData({
            loadmore_line: 'disply-block',
            loadmore: 'display-none',
            reload: true,
            page: page
          })
        }
    })

  },


  // 转到添加晒图页面
  gotoAddShare:function(){
      wx.navigateTo({
        url:'/pages/share/add'
      })
  },


  // 排序
  headerFilter: function(){
      var that = this,
          filterList = this.data.filterList;

      wx.showActionSheet({
          itemList: filterList,
          success: function(res) {
              var filterName = filterList[res.tapIndex];
              var filter = that.data.filterKey[res.tapIndex];
              that.setData({
                  filterName:filterName,
                  filter:filter,
                  page: 0,
                  reload: false
              });
              that.loadMore(true);
          },
          fail: function(res) {
              console.log(res.errMsg)
          }
      })
  },

  // 投票
  likeShareTap:function(e){
      var that = this,
          share_id = e.currentTarget.dataset.id,
          datalist = this.data.datalist;

      // 检查登录
      if(that.data.user.sessionId == undefined || that.data.user.sessionId == ''){
          wx.showModal({
              title: '请登录后操作。',
              content: '您还没登录25BOY呢',
              confirmText:'绑定登录',
              cancelText:'再看看',
              success:function(res){
                if (res.confirm == true || res.confirm == "true") {
                  var gourl = escape('/pages/share/index')
                  wx.redirectTo({
                    url:'/pages/public/login?gourl='+gourl
                  })
                }
              }
          })
          return false
      }

      app.API.postDATA({id:share_id,type:'share',sessionId:that.data.user.sessionId},function(res){
          if(res.data.code == 0){
              wx.showToast({
                title: '投票成功',
                icon: 'success',
                duration: 2000
              });
              // 更新点击数显示
              for (var i = 0; i < datalist.length; i++) {
                  if(datalist[i].share_id == share_id){
                      datalist[i].vote = (parseInt(datalist[i].vote)+1);
                  }
              };
              that.setData({
                  datalist: datalist
              })
          }else{
              wx.showModal({
                  title:'提示',
                  content:res.data.msg,
                  showCancel:false
              })
          }
      },'index.php?c=vote&a=addVote')
  },

  // 下拉刷新
  onPullDownRefresh: function() {
      this.setData({
          page: 0,
          reload: false
      })
      this.loadMore(true)
  },


  // 分享页面
  onShareAppMessage:function(){
      var path = '/pages/share/index';

      if(this.data.user.promote_id != undefined && this.data.user.promote_id != ""){
          path = path+'?pid='+this.data.user.promote_id;
      }

      return {
        title: '潮人穿衣打扮',
        desc: '来25BOY，看看潮人是怎样穿衣打扮的吧！',
        path: path
      }
  },

  

})