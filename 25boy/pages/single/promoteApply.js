var app = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        user: [],
        myProer: [],
        myProerNum: 0,
        myProerTotal: 0,
        ranking: [],
        rankingTotal: 0,
        tabStyle:{
            tabOn: ['on','',''],
            tabHidden: [false,true,true]
        }
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function (options) {
        // 记录来源
        app.markFromSource(options,'pages/single/promoteApply');
    },

    /**
     * 页面显示
     */
    onShow: function () {
        var that =this;

        // 检查登录
        app.checkLogin(function(res){
            if(res.sessionId != undefined && res.sessionId != ''){
                that.setData({
                    user: res
                });

                // 获取已邀请人数
                that.getProer(res.sessionId);

            }
        });

        // 佣金累计发放排行榜
        that.getRanking();
    },

    /*
    * 已邀请人数
    */
    getProer: function(sessionId){
        var that = this;

        if(sessionId == undefined || sessionId == ''){
            sessionId = this.data.user.sessionId;
        }

        app.API.getJSON({sessionId:sessionId},function(res){
            if(res.data.code == 0){
                that.setData({
                    myProer: res.data.rs.data,
                    myProerNum: res.data.rs.num,
                    myProerTotal: res.data.rs.total
                });
            }
        },'index.php?c=Promote&a=myProer');
    },

    /*
    * 佣金累计发放排行榜
    */
    getRanking: function(){
        var that = this;

        app.API.getJSON({},function(res){
            if(res.data.code == 0){
                that.setData({
                    ranking: res.data.rs.data,
                    rankingTotal: res.data.rs.total
                });
            }
        },'index.php?c=Promote&a=ranking');
    },

    /**
     * tab切换
     */
    tabSwitch: function (e) {
        var that = this,
            idx = e.currentTarget.dataset.idx;

        var tabStyle = {
            tabOn: ['','',''],
            tabHidden: [true,true,true]
        }

        tabStyle.tabOn[idx] = 'on';
        tabStyle.tabHidden[idx] = false;

        that.setData({
            tabStyle: tabStyle
        });
    },


    /**
     * 用户点击右上角分享
     */
    onShareAppMessage: function(){
        var path = '/pages/single/promoteApply';
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