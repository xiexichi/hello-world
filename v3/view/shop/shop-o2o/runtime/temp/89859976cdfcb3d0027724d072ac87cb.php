<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"D:\project\v3\view\shop\shop-o2o\public/../application//index/view/index/login.html";i:1556010537;}*/ ?>
    <link rel="stylesheet" href="/static/layui/css/login/admin.css" media="all">
    <link rel="stylesheet" href="/static/layui/css/login/login.css" media="all">
    <div class="layadmin-user-login layadmin-user-display-show" id="LAY-user-login" style="display: none;">
        <div class="layadmin-user-login-main">
            <div class="layadmin-user-login-box layadmin-user-login-header">
                <h2>登录</h2>
            </div>
            <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
                <div class="layui-form-item">
                    <label class="layadmin-user-login-icon layui-icon layui-icon-username" for="LAY-user-login-username"></label>
                    <input type="text" name="username" lay-verify="required" placeholder="员工账号" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-password"></label>
                    <input type="password" name="password" lay-verify="required" placeholder="登录密码" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <div class="layui-row">
                        <div class="layui-col-xs7">
                            <label class="layadmin-user-login-icon layui-icon layui-icon-vercode" for="LAY-user-login-vercode"></label>
                            <input type="text" name="vercode" id="LAY-user-login-vercode" lay-verify="required" placeholder="图形验证码" class="layui-input">
                        </div>
                        <div class="layui-col-xs5">
                            <div style="margin-left: 10px;">
                                <img onclick="this.src='<?php echo captcha_src(); ?>?'+Math.random()" src="<?php echo captcha_src(); ?>" class="layadmin-user-login-codeimg" id="LAY-user-get-vercode" style="max-height:47px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <button class="layui-btn layui-btn-normal layui-btn-fluid" lay-submit lay-filter="formDemo" style="height:47px">登 入</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    layui.config({
        base: '/static/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['jquery', 'form', 'layer', 'element'], function() {
        var $ = layui.$,
            form = layui.form,
            layer = layui.layer,
            element = layui.element;

        form.render()

        // 监听用户名输入
        $("input[name='username']").bind("input propertychange", function(event) {
            // 如果是dpl，则是店铺登录
            if ($(this).val() == 'dpl') {
                location.href = '/index/index/shop_login';
                return;
            }
        });


        //提交
        form.on('submit(formDemo)', function(obj) {
            $.post('/index/auth/staffLogin', obj.field, function(res) {
                if (res.code == 0) {
                    // 将登录地址存入本地储存
                    localStorage.setItem('loginPage', '/index/index/login')

                    // 登录成功
                    location.href = '/index/index/index'
                } else {
                    layer.msg(res.msg)
                }
            })
        })
    })
    </script>
