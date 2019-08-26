<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:85:"D:\project\v3\view\admin\app\public/../application//goods/view/evaluation/detail.html";i:1553073076;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>25BOY 新零售系统v3</title>
<link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
<link rel="stylesheet" href="/static/style/common.css" media="all">
<link rel="stylesheet" href="/static/style/admin.css" media="all">
<script src="/static/js/jquery-3.1.1.min.js"></script>

<!-- 百度echarts -->
<script src="/static/js/echarts.min.js"></script>

<!-- 自定义js -->
<script src="/static/js/common.js"></script>
<script src="/static/js/request.js"></script>

<!-- layui组件js -->
<!-- <script src="/static/layui/layui.js"></script> -->
<script src="/static/layui/layui.all.js"></script>

<script src="/static/js/layui-common.js"></script>
<!-- 全局参数 -->
<script type="text/javascript">
const photo_space_token = "<?php echo \think\Session::get('photojwttoken'); ?>"
const photo_handle_url = "<?php echo url('/handlePhoto.html','','',true);?>"
</script>
</head>

<style>
    .body-tab th{ min-width:100px; line-height:35px; text-align:right; }
    .body-tab td{ min-width:100px; }
    .layui-card-header{font-weight:600;}
    .img-list{float:left; width:150px; border:1px solid #ccc; margin-right:10px; }
</style>
<script src="/static/ueditor/ueditor.config.js"></script>
<script src="/static/ueditor/ueditor.all.js"></script>
<script src="/static/ueditor/lang/zh-cn/zh-cn.js"></script>
<div class="layui-fluid" id="vue_main">
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-form-item" v-if="eval_info">
                <button class="layui-btn" v-if="eval_info.verify == 0" v-on:click="verify(1)" >审核通过</button>
                <button class="layui-btn layui-btn-danger" v-if="eval_info.verify == 0" v-on:click="verify(2)" >审核不通过</button>
                <button class="layui-btn" v-if="eval_info.verify == 1 && !share_box" v-on:click="openShare()" >开启晒单贴编辑</button>
                <button class="layui-btn" v-if="eval_info.verify == 1 && share_box" v-on:click="closeShare()" >退出晒单贴编辑</button>
                <button class="layui-btn layui-btn-danger" v-if="eval_info.verify == 1 && share_box" v-on:click="shareSubmit()" >提交发帖</button>
            </div>
            <div class="layui-tab layui-tab-card">
                <ul class="layui-tab-title">
                    <li class="layui-this"  v-if="!share_box" >评论内容</li>
                    <li class="layui-this" v-if="share_box" >设置晒图内容</li>
                </ul>
                <div class="layui-tab-content" >
                    <div class="layui-tab-item layui-show" v-if="!share_box" >
                        <table class="body-tab">
                            <tr>
                                <th>评论星级：</th>
                                <td>{{eval_info.star}}</td>
                                <th>会员名：</th>
                                <td>{{user_info.user_name}}</td>
                                <th>评价时间：</th>
                                <td>{{eval_info.create_time}}</td>
                            </tr>
                            <tr>
                                <th>商品编号：</th>
                                <td colspan="5">{{order_goods.erp_code}}</td>
                            </tr>
                            <tr>
                                <th>下单商品图：</th>
                                <td colspan="5"> <div class="img-list"><img :src="order_goods.item_images" v-on:click="showPic(order_goods.item_images)" ></div></td>
                            </tr>
                            <tr>
                                <th>评论内容：</th>
                                <td colspan="5">{{eval_info.content}}</td>
                            </tr>
                            <tr>
                                <th>晒图内容：</th>
                                <td colspan="5">
                                    <div class="img-list" v-for="(img,index) in eval_info.images_list"><img :src="img" v-on:click="showPic(img)" ></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="layui-tab-item layui-show" v-if="share_box" >
                        <div class="layui-form-item">
                            <label class="layui-form-label">分享标题</label>
                            <div class="layui-input-block">
                                <input type="text" name="share_title" v-model="share_title" id="share_title" autocomplete="off" class="layui-input" placeholder="请填写标题" >
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">描述内容</label>
                            <div class="layui-input-block">
                                <textarea placeholder="请输入内容" class="layui-textarea">{{share_description}}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">图片内容</label>
                            <div class="layui-input-block">
                                <div class="img-list" v-for="(img,index) in share_images"><img :src="img" v-on:click="showPic(img)" ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" charset="utf-8" src="/static/js/vue.js"></script>
<script>
    var ue;
    var id = getUrlParam('id');
    var Vue = new Vue({
        el: '#vue_main',
        data:{
            'id' : id,
            'eval_info' : [],
            'order_goods' : [],
            'user_info': [],
            'share_box' : true,
            'share_title' : '',
            'share_description' : '',
            'share_images' : '',
        },
        mounted:function(){
            var that = this;
            that.share_box = false;
            that.getEvalInfo();
        },
        methods:{
            getEvalInfo:function(){
                var that = this;
                request.setHost(SHOP_DATA).get('/goods/evaluation/one?id='+that.id, function(res){
                    if( res.code == 0 ){
                        that.eval_info = res.data.eval_info;
                        that.order_goods = res.data.order_goods;
                        that.user_info = res.data.user_info;
                    }
                });
            },
            showPic:function(img){
                layer.open({
                    type: 1,
                    title: false,
                    closeBtn: 0,
                    shadeClose: true,
                    skin: 'yourclass',
                    content: '<img src="'+img+'">'
                });
            },
            verify:function(verify){
                var that = this;
                layer.confirm('是否确认操作', {
                    btn: ["确定","取消"] //按钮
                }, function(){
                    var param = {};
                    param.id = that.id;
                    param.verify = verify;
                    request.setHost(SHOP_DATA).post('/goods/evaluation/edit',param,function(res){
                        if( res.code == 0 ){
                            // 成功提示
                            layer.msg(res.msg);
                            setTimeout(function(){
                                location=location;
                            },1000);
                        } else {
                            // 错误提示
                            layer.msg(res.msg);
                        }
                    });
                });
            },
            openShare:function(){
                var that = this;
                that.share_box = true;
                that.share_description = that.eval_info.content;
                that.share_images = that.eval_info.images_list;
            },
            closeShare:function(){
                layer.confirm('是否确认退出编辑，退出不会保存草稿', {
                    btn: ["确定","取消"] //按钮
                }, function() {
                    location = location;
                });
            },
            shareSubmit:function(){
                var that = this;
                var param = {};
                param.user_id = that.eval_info.user_id;
                param.title = that.share_title;
                param.description = that.share_description;
                param.images_list = that.share_images;
                request.setHost(SHOP_DATA).post('/share/share/add',param,function(res){
                    if( res.code == 0 ){
                        // 成功提示
                        layer.msg(res.msg);
                        setTimeout(function(){
                            location=location;
                        },1000);
                    } else {
                        // 错误提示
                        layer.msg(res.msg);
                    }
                });
            }
        }
    });
</script>


</body>
</html>