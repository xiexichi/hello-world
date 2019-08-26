<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:88:"D:\project\v3\view\admin\app\public/../application//merchant/view/meregion/page_add.html";i:1556963455;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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

<div class="layui-fluid">
    <form class="layui-form table-box" action="" id="form" lay-filter="form">
        <div class="layui-card">
            <div class="layui-card-body">
                <div class="layui-form-item" pane>
                    <div class="layui-inline">
                        <button class="layui-btn" lay-submit lay-filter="submitBtn">立即提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        <button class="layui-btn layui-btn-primary" onclick="parent.window.callback()">返回</button>
                    </div>
                </div>
                <div class="layui-tab layui-tab-card">
                    <div class="layui-tab-content" >
                        <div class="layui-tab-item layui-show">
                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">区域名</label>
                                <div class="layui-input-block">
                                    <input class="layui-input" lay-verify="required" type="text" name="region_name" placeholder="请输入登录名(必填)" />
                                </div>
                            </div>
                            <!--<div class="layui-form-item">-->
                                <!--<label class="layui-form-label">是否启用</label>-->
                                <!--<div class="layui-input-block">-->
                                    <!--<input type="radio" name="status" value="1" title="启动" checked="">-->
                                    <!--<input type="radio" name="status" value="2" title="禁用">-->
                                <!--</div>-->
                            <!--</div>-->
                           <div class="layui-form-item">
                                <label class="layui-form-label">管理员</label>
                                <div class="layui-input-block">
                                    <select name="admin_id" lay-verify="required" lay-filter="selectAdmin"  xm-select="select1" xm-select-skin="primary">


                                            <!--<option value="1" disabled="disabled">北京</option>-->
                                            <!--<option value="2" selected="selected">上海</option>-->
                                            <!--<option value="3">广州</option>-->
                                            <!--<option value="4" selected="selected">深圳</option>-->
                                            <!--<option value="5">天津</option>-->


                                    </select>
                                </div>
                            </div>
                            <!--<div class="layui-form-item">-->
                                <!--<label class="layui-form-label">选择权限</label>-->
                                <!--<div class="layui-input-block">-->
                                    <!--<div id="LAY-auth-tree-index"></div>-->
                                <!--</div>-->
                            <!--</div>-->
                            <div class="layui-form-item">
                                <label class="layui-form-label">店铺</label>
                                <div class="layui-input-block">
                                    <select name="shop_id" lay-verify="required" lay-filter="selectShop"  xm-select="select2" xm-select-skin="primary">
                                    </select>
                                </div>
                            </div>
                            <div id="merchant_id_selecter_view"></div>
                            <div id="shop_id_selecter_view"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/html" id="merchant_id_selecter_tpl">
    <div class="layui-form-item">
        <label class="layui-form-label">商户主体</label>
        <div class="layui-input-block">
            <select name="merchant_id" lay-filter="selectMerchantID" lay-verify="required">
                <option></option>
                {{# layui.each(d, function(index,item){ }}
                <option value="{{ item.id }}">{{ item.name }}</option>
                {{# }); }}
            </select>
        </div>
    </div>
</script>
<script type="text/html" id="shop_id_selecter_tpl">
    <div class="layui-form-item">
        <label class="layui-form-label">管核店铺</label>
        <div class="layui-input-block">
            {{# layui.each(d, function(index,item){ }}
            <input type="checkbox" value="{{ item.id }}" name="shop_ids[]" title="{{ item.name }}">
            {{# }); }}
        </div>
    </div>
</script>

<link rel="stylesheet" href="/static/layui/module/formSelects/formSelects-v4.css" media="all">
<script src="/static/layui/layui.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    layui.config({
        base:'/static/layui/'
    }).extend({
        formSelects:'formSelects',
    });

    var roleType = 0;
    var merchantID = 0;
    $(document).ready(function(){
        layui.use(['jquery','form', 'layer', 'laytpl','formSelects'], function(){
            var $ = layui.jquery;
            var form = layui.form;

            var formSelects = layui.formSelects;

            var layer = layui.layer;
            var laytpl = layui.laytpl;
            var getTplMerchant = merchant_id_selecter_tpl.innerHTML;
            var viewMerchant = document.getElementById('merchant_id_selecter_view');

            // var getTplShop = shop_id_selecter_tpl.innerHTML;
            // var viewShop = document.getElementById('shop_id_selecter_view');
            form.render();

            // var get_admin = [];
            // request.setHost(CENTER_DATA).get('/merchant/meregion/getAdmins',{}, function(res){
            //     if( res.code == 0 ){
            //         var selectDom = $('select[name=admin_id]');
            //         selectDom.append('<option value="">请选择</option>');
            //         var list = res.data;
            //         for(var i in list){
            //             console.log(list[i]);
            //         }
            //         form.render();
            //     }else{
            //         layer.msg(res.msg);
            //     }
            // });


           request.setHost(CENTER_DATA).get('/merchant/admin/all',{}, function(res){
                if( res.code == 0 ){
                    var data = [];
                    var list = res.data;
                    for(var i in list) {
                        res = {"name":list[i].realname,"admin_id":list[i].id};
                        data[data.length]=res;
                    }
                    // for (var i in data){
                    //     console.log(data[i].admin_id);
                    // }

                    formSelects.data('select1','local',{
                        arr:data });
                }else{
                    layer.msg(res.msg);
                }

            });




            request.setHost(CENTER_DATA).get('/merchant/shop/all',{}, function(res){
                if( res.code == 0 ){
                    // console.log(res.data);
                    var data =[];
                    var list = res.data;
                    for(var i in list){
                        res = {"name":list[i].name,"va":list[i].id,};
                        data[data.length] = res;
                    }
                    formSelects.data('select2','local',{
                        arr:data });
                }else{
                    layer.msg(res.msg);
                }
            });


            //监听提交
            form.on('submit(submitBtn)', function(data){
                // request.setHost(CENTER_DATA).post('/merchant/meregion/add', data.field, function(res){
                //     if (res.code == 0) {
                //         layer.msg(res.msg);
                //         setTimeout(function(){
                //             parent.window.callback();
                //         },1000);
                //     } else {
                //         layer.msg(res.msg);
                //     }
                // });//入库merchant_region表
                // request.setHost(CENTER_DATA).post('/merchant/merchantregionadmin/add', data.field, function(res){
                //     if (res.code == 0) {
                //         layer.msg(res.msg);
                //         setTimeout(function(){
                //             parent.window.callback();
                //         },1000);
                //     } else {
                //         layer.msg(res.msg);
                //     }
                // });//入库merchant_region_admin联合表
                // request.setHost(CENTER_DATA).post('/merchant/merchantregionshop/add', data.field, function(res){
                //     if (res.code == 0) {
                //         layer.msg(res.msg);
                //         setTimeout(function(){
                //             parent.window.callback();
                //         },1000);
                //     } else {
                //         layer.msg(res.msg);
                //     }
                // });//入库merchant_region_shop联合表
                // return false;
            });

            form.on('select(selectRole)',function(data){
                roleType = $(data.elem).find("option:selected").data('type');
                merchantID = $(data.elem).find("option:selected").data('merchant_id');

                if(roleType == 2){
                    //商户角色//显示商户选择器
                    request.setHost(CENTER_DATA).get('/merchant/Merchant/getMerchantList',{}, function(res){
                        if( res.code == 0 ){
                            laytpl(getTplMerchant).render(res.data, function(html){
                                viewMerchant.innerHTML = html;
                            });
                            form.render();
                        }
                    });
                }else{
                    //清空商户选择器
                    laytpl(getTplMerchant).render([],function(html){
                        viewMerchant.innerHTML = '';
                    });
                    form.render();
                    //清空店铺选择器
                    // laytpl(getTplShop).render([],function(html){
                    //     viewShop.innerHTML = '';
                    // });
                    form.render();
                }
            });

            // form.on('select(selectMerchantID)',function(data){
            //     //显示商户下的店铺//商户经理，要选店铺
            //     if(merchantID > 0){
            //         request.setHost(CENTER_DATA).get('/merchant/Shop/getShopsByMerchantID',{merchant_id:data.value}, function(res){
            //             if( res.code == 0 ){
            //                 laytpl(getTplShop).render(res.data, function(html){
            //                     viewShop.innerHTML = html;
            //                 });
            //                 form.render();
            //             }
            //         });
            //     }else{
            //         //清空店铺选择器
            //         laytpl(getTplShop).render([],function(html){
            //             viewShop.innerHTML = '';
            //         });
            //         form.render();
            //     }
            // });



        });
    });

</script>


</body>
</html>