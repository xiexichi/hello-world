var app = getApp();

Page({
  data:{
      user:[],
      share_id:'',
      share:[],
      // 详情图尺寸
      imageSize: [],
      windowWidth:375,
      windowHeight:600,
      btntxt:'投Ta一票',
      showHomeBtn:true,
      showFxTap:'display-none',
      footerData:[],
      options:[],

      // 评论
      share_comment_id: 0,
      loadmore: 'display-none',
      loadmore_line: 'display-none',
      page:0,
      reload: false,
      shareCommentList:[],
      commentOrderTxt:'按热度',
      commentBtnLoading: false,
      commentBtnDis: false,
      commentBtnTxt: '发送',
      commentOrder: 'hot',
      replyText:'',
      scrollToView:0,
      replyPlaceholder:'添加评论',
      replyFocus:false
  },

  onLoad:function(options){
      var that = this,
          share_id = options.id ? options.id : '';

      // 修改图片高度与宽度一致
      that.setData({
          windowWidth: app.systemInfo.windowWidth,
          windowHeight: app.systemInfo.windowHeight
      });

      if(share_id != undefined && share_id != ''){
          this.setData({
              share_id:share_id,
              footerData:app.footerData,
              options:options
          })
          this.getShareDetail();
      }else{
          wx.showModal({
              title:'错误',
              content:'缺失必要参数',
              showCancel:false,
              success:function(rs){
                  if (rs.confirm) {
                      wx.navigateBack()
                  }
              }
          });
          return false;
      }

      // 记录来源
      app.markFromSource(options,'pages/share/detail');

      // 评论列表
      that.getComment();
  },

  onShow:function(){
      var that = this;

      // 检查登录
      app.checkLogin(function(res){
          if(res.sessionId != undefined && res.sessionId != ''){
              that.setData({
                  user: res
              })
          }
      })
  },

  getShareDetail:function(){
      var that = this,
          share_id = this.data.share_id

      wx.showToast({
        title: '加载中',
        icon: 'loading',
        duration: 10000
      })
      app.API.getJSON({c:'Share',a:'shareDetails',share_id:share_id},function(res){
          wx.hideToast()
          if(res.data.code == 0){
              that.setData({
                  share:res.data.rs
              })
              that.setOther(res.data.rs.photos)
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
      })
  },

  setOther:function(photos){
      var that = this
     

      // 产品详情图尺寸
      var imageSize = []
      for (var i = 0; i < photos.length; i++) {
          imageSize[i] = {
              width: '',
              height: ''
          }
      }
      that.setData({
          imageSize: imageSize
      })
  },

  // 重新设置图片尺寸
  resetImage: function(e){
    var idx = e.currentTarget.dataset.idx
    var zoom = e.detail.width/(this.data.windowWidth);    // 计算缩放比例
    var imageSize = this.data.imageSize;

    for (var i = 0; i < imageSize.length; i++) {
        if(i == idx){
            if(e.detail.width > (this.data.windowWidth*0.5)){
                imageSize[i].width = (e.detail.width/zoom)+'px';
                imageSize[i].height = (e.detail.height/zoom)+'px';
            }else{
                imageSize[i].width = e.detail.width+'px';
                imageSize[i].height = e.detail.height+'px';
            }
        }
    }
    this.setData({
        imageSize: imageSize
    })
  },

  // 预览图片
  previewImage: function(e){
      var idx = e.currentTarget.dataset.idx,
          photos = this.data.share.photos;
      wx.previewImage({
          current: photos[idx],
          urls: photos
      })
  },


  // 投票
  likeShareTap:function(){
      var that = this

      // 检查登录
      if(that.data.user.sessionId == undefined || that.data.user.sessionId == ''){
          wx.showModal({
              title: '请登录后操作。',
              content: '您还没登录25BOY呢',
              confirmText:'绑定登录',
              cancelText:'再看看',
              success:function(res){
                if (res.confirm == true || res.confirm == "true") {
                  var gourl = escape('/pages/share/detail?id='+that.data.share_id)
                  wx.redirectTo({
                    url:'/pages/public/login?gourl='+gourl
                  })
                }
              }
          })
          return false
      }

      app.API.postDATA({id:that.data.share_id,type:'share',sessionId:that.data.user.sessionId},function(res){
          if(res.data.code == 0){
              wx.showToast({
                title: '投票成功',
                icon: 'success',
                duration: 2000
              })
              that.setData({
                  btntxt:'投票成功'
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

  // 分享提示
  fxTap:function(){
      var that = this;

      if(that.data.showFxTap == 'display-none'){
          that.setData({
              showFxTap:'display-block'
          })
      }else{
          that.setData({
              showFxTap:'display-none'
          })
      }
      // 10秒后自动隐藏提示
      setTimeout(function(){
        that.setData({
            showFxTap:'display-none'
        })
      },10000)
  },


  // 删除自己的晒图
  delShareTap:function(e){
      var share_id = this.data.share_id

      // ajax获取产品列表
      app.API.getJSON({c:'Share',a:'delShare',share_id:share_id,sessionId:this.data.user.sessionId},function(res){
          if(res.data.code == 0){
              wx.showModal({
                title: '删除成功',
                content:'晒图已经删除，返回列表请手动刷新页面。',
                showCancel:false,
                success:function(rs){
                    if (rs.confirm) {
                        wx.navigateBack()
                    }
                }
              })
          }else{
              wx.showModal({
                  title:'提示',
                  content:res.data.msg,
                  showCancel:false
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


  // 分享页面
  onShareAppMessage:function(){
      var title = "#25BOY晒图# 来自"+this.data.share.username+"的分享！",
          decs = this.data.share.content, 
          path = '/pages/share/detail?id='+this.data.share_id;

      if(this.data.user.promote_id != undefined && this.data.user.promote_id != ""){
          path = path+'&pid='+this.data.user.promote_id;
      }

      return {
        title: title,
        desc: decs,
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
  },


  // 评论
  getComment: function() {
    var that = this;
    var page = that.data.page;
    var commentList = that.data.shareCommentList;


    // 检查是否可以加载
    if(that.data.reload == true){
        return false;
    }else{
        page+=1;
    }

    // loading
    that.setData({
        loadmore:'disply-block',
        reload: true
    })

    // ajax获取产品列表
    app.API.getJSON({share_id:that.data.share_id,order:that.data.commentOrder,pageNo:page,rowNo:30},function(res){
        if(res.data.code == 0 && res.data.rs.data.length>0){
            that.setData({
                reload: false,
                loadmore: 'display-none',
                shareCommentList: commentList.concat(res.data.rs.data),
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
    },'index.php?c=Share&a=comment')
  },


  // 添加评论
  commentSubmit: function(e){
      var that = this,
          commentList = that.data.shareCommentList,
          share_comment_id = that.data.share_comment_id,
          scrollToView = that.data.scrollToView;

      if(this.data.user.sessionId == undefined || this.data.user.sessionId == ''){
          wx.showModal({
              title: '请登录后操作。',
              content: '此操作需要登录25BOY帐号。',
              confirmText: '绑定登录',
              cancelText: '取消',
              success:function(res){
                  if (res.confirm == true || res.confirm == "true") {
                      wx.navigateTo({
                        url:'/pages/public/login?gourl=close'
                      })
                  }
              }
          });
          return false;
      }

      if(e.detail.value.comment == undefined || e.detail.value.comment == ''){
          wx.showModal({
            title: '请输入评论内容',
            content: '',
            showCancel: false
          })
      }else{

          that.setData({
              commentBtnLoading: true,
              commentBtnDis: true,
              commentBtnTxt: ''
          })

          var params = {
              sessionId: that.data.user.sessionId,
              share_id: that.data.share_id,
              comment: e.detail.value.comment,
              share_comment_id: share_comment_id
          };
          app.API.postDATA(params,function(res){
              that.setData({
                commentBtnLoading: false,
                commentBtnDis: false,
                commentBtnTxt: '发送'
              });
              if(res.data.code == 0){
                  wx.showToast({
                      title: '评论成功',
                      icon: 'success',
                      duration: 1500
                  });

                  // 回复评论
                  if(share_comment_id > 0){
                      for (var i = 0; i < commentList.length; i++) {
                          var replys = commentList[i].replys;
                          if(commentList[i].id == share_comment_id){
                              // 在评论处插入新回复
                              if(replys.length == 0){
                                  var replys = new Array();
                              }
                              replys.push(res.data.rs);
                              commentList[i].replys = replys;
                          }
                      };
                  // 新评论
                  }else{
                      // 更新新评论到开头
                      commentList.unshift(res.data.rs);
                      scrollToView = 'replyView';
                  }
                  // 更新数据
                  that.setData({
                      share_comment_id: 0,
                      replyText: '',
                      replyPlaceholder: '添加评论',
                      shareCommentList: commentList,
                      scrollToView:scrollToView
                  });
              }else{
                  wx.showModal({
                    title: res.data.msg,
                    content: '',
                    showCancel: false
                  })
              }
          },'index.php?c=Share&a=addComment');



      }
  },


  // 评论排序方式
  commentOrder: function(){
      var that = this,
          commentOrder = that.data.commentOrder,
          commentOrderTxt = that.data.commentOrderTxt;

      if(commentOrder == 'time'){
          commentOrder = 'hot';
          commentOrderTxt = '按热度';
      }else{
          commentOrder = 'time';
          commentOrderTxt = '按时间';
      }

      that.setData({
          commentOrder: commentOrder,
          shareCommentList: [],
          page:0,
          reload: false,
          commentOrderTxt:commentOrderTxt
      });

      // 刷新评论列表
      that.getComment();
  },


  // 评论点赞行为
  zanTap: function(e){
      var that = this,
          id = e.currentTarget.dataset.id,
          commentList = that.data.shareCommentList;

      var params = {
          sessionId: that.data.user.sessionId,
          share_id: that.data.share_id,
          share_comment_id: id
      };
      app.API.postDATA(params, function(res){
          if(res.data.code == 0){
              wx.showToast({
                  title: '点赞成功',
                  icon: 'success',
                  duration: 1500
              });
              // 更新点击数显示
              for (var i = 0; i < commentList.length; i++) {
                  if(commentList[i].id == id){
                      commentList[i].zan = (parseInt(commentList[i].zan)+1);
                  }
              };
              that.setData({
                  shareCommentList: commentList
              }); 
          }else{
              wx.showModal({
                title: res.data.msg,
                content: '',
                showCancel: false
              })
          }
      },'index.php?c=Share&a=zan');
  },


  // 回复评论行为
  replyTap: function(e){
      var that = this,
          id = e.currentTarget.dataset.id,
          nickname = e.currentTarget.dataset.nickname;

      that.setData({
          replyPlaceholder: '@'+nickname,
          replyFocus: true,
          share_comment_id: id
      });
  },

  // 回复框失去焦点时，记录输入的内容
  markReplyText:function(e){
      this.setData({
          replyText: e.detail.value
      })
  },


  // 取消回复行为
  cancelReply: function(){

      if(this.data.replyText == ''){
          this.setData({
              replyPlaceholder: '添加评论',
              replyFocus: false,
              share_comment_id: 0
          });
      }
  },


  // 下拉刷新
  onPullDownRefresh: function() {
      this.onLoad(this.data.options)
  }

})