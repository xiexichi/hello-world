<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:90:"D:\project\v3\view\admin\app\public/../application//user/view/user_integral/page_list.html";i:1556332579;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
    <div class="layui-card">
        <form class="layui-form searchForm">
            <div class="layui-card-header" style="padding: 10px 20px">
                <button class="layui-btn" lay-submit>搜索</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
            <div class="layui-card-body">
                <div class="layui-form-item layui-inline">
                    <label class="layui-form-label">会员ID</label>
                    <div class="layui-input-inline">
                        <input type="text" name="user_id" placeholder="" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item layui-inline">
                    <label class="layui-form-label">会员名称</label>
                    <div class="layui-input-inline">
                        <input type="text" name="user_name" placeholder="" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item layui-inline">
                    <label class="layui-form-label">积分类型</label>
                    <div class="layui-input-inline">
                        <select name="integral_type_id">
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="layui-card">
        <div class="layui-card-body">
            <table id="table" lay-filter="table"></table>
        </div>
    </div>
</div>
<!-- 头部操作模板 -->
<script type="text/html" id="toolbarTpl"></script>
<!-- 表内操作模板 -->
<script type="text/html" id="ctrlbarTpl"></script>
<script type="text/javascript">
var table = layui.table;
var form = layui.form;
var layer = layui.layer;
var laydate = layui.laydate;
var $ = layui.jquery;

$(document).ready(function() {
    layui.use(['form', 'table', 'layer','laydate','jquery'], function() {
        laydate.render({
            elem: '#create_time'
        });

        request.setHost(CENTER_DATA).get('/user/user_integral_type/all',{}, function(res){
            if( res.code == 0 ){
                var selectDom = $('select[name=integral_type_id]');
                selectDom.append('<option value=""></option>');
                for(var i in res.data){
                    selectDom.append('<option value="'+res.data[i].id+'">'+res.data[i].name+'</option>');
                }
                form.render();
            }
        });

        layer.load(2);
        //执行渲染
        table.render({
            id: 'table',
            elem: '#table',
            height: 'auto', //容器高度
            url: '/user/user_integral/getList',
            page: true,
            toolbar: '#toolbarTpl',
            headers: {
                ctrl: CENTER_DATA
            },
            cols: [
                [
                    { field: 'id', title: 'ID' },
                    { field: 'user_id', title: '会员ID'},
                    { field: 'avatar', title: '会员头像', templet: d => {
                        return '<img src="' + d.avatar + '" alt="" width="50">'
                    }},
                    { field: 'user_name', title: '会员名称' },
                    { field: 'integral_type_name', title: '积分类型' },
                    { field: 'integral', title: '积分变动' },
                    { field: 'integral_total', title: '积分余量' },
                    { field: 'pay_sn', title: '积分来源' },
                    { field: 'note', title: '备注描述' },
                    { field: 'create_time', title: '创建时间'}
                    //{ field: 'id', title: '操作', templet: '#ctrlbarTpl' }
                ]
            ],
            done: function() {
                layer.closeAll();
            }
        });


        //监听工具栏事件
        table.on('toolbar(table)', function(obj) {
            const data = obj.data
            const layEvent = obj.event
            switch (layEvent) {
                default:
                    // statements_def
                    break;
            }
        });

        //监听行工具事件
        table.on('tool(table)', function(obj) {
            const data = obj.data
            const layEvent = obj.event
            const tr = obj.tr

            switch (layEvent) {
                default:
                    // statements_def
                    break;
            }
        });

        form.on('submit', function(data){
            table.reload('table', {
                where: data.field,
                page: {
                    curr: 1, //重新从第 1 页开始
                }
            });
            return false;
        });
    });
    form.render();
});

function callback() {
    table.reload('table');
    layer.closeAll();
}
</script>
</body>
</html>