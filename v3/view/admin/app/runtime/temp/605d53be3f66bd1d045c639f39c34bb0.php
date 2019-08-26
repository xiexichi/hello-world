<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:83:"D:\project\v3\view\admin\app\public/../application//power/view/admin/page_edit.html";i:1556335338;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
        <input type="hidden" name="id" value="0">
        <div class="layui-card">
            <div class="layui-card-body">
                <div class="layui-form-item">
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
                                <label class="layui-form-label">真实姓名</label>
                                <div class="layui-input-block">
                                    <input class="layui-input" disabled="" lay-verify="required" type="text" name="realname" placeholder="请输入真实姓名(必填)" />
                                </div>
                            </div>
                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">登录名</label>
                                <div class="layui-input-block">
                                    <input class="layui-input" disabled="" type="text" name="loginname" placeholder="请输入登录名(必填)" />
                                </div>
                            </div>
                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">密码</label>
                                <div class="layui-input-block">
                                    <input class="layui-input" type="text" name="password" placeholder="请输入新的密码(必填)" />
                                </div>
                            </div>
                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">重复密码</label>
                                <div class="layui-input-block">
                                    <input class="layui-input" type="text" name="repassword" placeholder="请再次输入新的密码(必填)" />
                                </div>
                            </div>
                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">员工编号</label>
                                <div class="layui-input-block">
                                    <input class="layui-input" disabled="" type="text" name="code" placeholder="请输入员工编号" />
                                </div>
                            </div>
                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">手机号</label>
                                <div class="layui-input-block">
                                    <input class="layui-input" type="text" name="phone" placeholder="请输入手机号" />
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">是否启用</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="status" value="1" title="启动" checked="">
                                    <input type="radio" name="status" value="2" title="禁用">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">角色组</label>
                                <div class="layui-input-block">
                                    <select name="role_id" lay-filter="selectRole">
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
            <input {{# if(item.is_selected == 1){}}checked{{# } }} type="checkbox" value="{{ item.id }}" name="shop_ids[]" title="{{ item.name }}">
            {{# }); }}
        </div>
    </div>
</script>

<script type="text/javascript">
    var adminID = getUrlParam('id');
    var roleType = 0;
    var merchantID = 0;

    $(document).ready(function(){
        layui.use(['jquery', 'form', 'layer', 'laytpl'], function(){
            var $ = layui.jquery;
            var form = layui.form;
            var layer = layui.layer;
            var laytpl = layui.laytpl;
            var getTplMerchant = merchant_id_selecter_tpl.innerHTML;
            var viewMerchant = document.getElementById('merchant_id_selecter_view');

            //var getTplShop = shop_id_selecter_tpl.innerHTML;
            //var viewShop = document.getElementById('shop_id_selecter_view');

            form.render();

            request.setHost(CENTER_DATA).get('/power/admin/getAdminEdit',{id:adminID}, function(res){
                if( res.code == 0 ){
                    var adminData = res.data.adminData;
                    var roleList =res.data.roleList;
                    var merchantList = res.data.merchantList;
                    var shopList = res.data.shopList;
                    var roleInfo = res.data.roleInfo;

                    var selectDom = $('select[name=role_id]');
                    selectDom.append('<option value="">请选择</option>');
                    for(var i in roleList){
                        selectDom.append('<option data-merchant_id="'+roleList[i].merchant_id+'" data-type="'+roleList[i].type+'" value="'+roleList[i].id+'">'+roleList[i].title+'</option>');
                    }

                    //商户主体列表
                    if(merchantList.length > 0){
                        laytpl(getTplMerchant).render(merchantList, function(html){
                            viewMerchant.innerHTML = html;
                        });
                    }

                    //某商户下的所有店铺
                    // if(shopList.length > 0){
                    //     laytpl(getTplShop).render(shopList, function(html){
                    //         viewShop.innerHTML = html;
                    //     });
                    // }

                    //已选择的角色
                    if(roleInfo){
                        roleType = roleInfo.type;
                        merchantID = roleInfo.merchant_id;
                    }

                    form.val('form', {
                        'id':adminData.id,
                        'realname': adminData.realname,
                        'loginname': adminData.loginname,
                        'code': adminData.code,
                        'phone': adminData.phone,
                        'status': String(adminData.status),
                        'merchant_id': adminData.merchant_id,
                        'role_id':adminData.role_id
                    });

                    form.render();

                }else{
                    layer.msg(res.msg);
                }
            });

            //监听提交
            form.on('submit(submitBtn)', function(data){
                request.setHost(CENTER_DATA).post('/power/admin/edit', data.field, function(res){
                    if (res.code == 0) {
                        layer.msg(res.msg);
                        setTimeout(function(){
                            parent.window.callback();
                        },1000);
                    } else {
                        layer.msg(res.msg);
                    }
                });
                return false;
            });

            form.on('select(selectRole)',function(data){
                roleType = $(data.elem).find("option:selected").data('type');
                merchantID = $(data.elem).find("option:selected").data('merchant_id');

                if(roleType == 2){
                    //商户角色//显示商户选择器
                    request.setHost(CENTER_DATA).get('/merchant/Merchant/all',{}, function(res){
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