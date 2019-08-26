<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:88:"D:\project\v3\view\admin\app\public/../application//article/view/special/goods_list.html";i:1553853809;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
    .layui-table-cell{
        height: 100px;
    }

    .layui-table-body .layui-table-cell{
        height:105px;
        line-height: 105px;
    }

</style>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="" id="form" >
                <input type="hidden" name="state" value="<?=isset($_GET['state']) ? $_GET['state'] : '';?>">
                <input type="hidden" name="delete" value="<?=isset($_GET['delete']) ? $_GET['delete'] : '';?>">
                <div class="layui-form-item">
                    <label class="layui-form-label">关键词</label>
                    <div class="layui-input-inline">
                        <input type="text" name="keyword" value="<?=isset($_GET['keyword']) ? $_GET['keyword'] : '';?>" placeholder="商品名关键词" autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label">货号</label>
                    <div class="layui-input-inline">
                        <input type="text" name="erp_code" value="<?=isset($_GET['erp_code']) ? $_GET['erp_code'] : '';?>" placeholder="商品货号" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">分类</label>
                    <div class="layui-input-inline">
                        <select name="cate_id" id="cate_box" lay-search >
                        </select>
                    </div>
                    <label class="layui-form-label">品牌</label>
                    <div class="layui-input-inline">
                        <select name="brand_id" id="brand_box" lay-search >
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <button class="layui-btn" lay-submit lay-filter="formDemo">确认</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <table id="goods" lay-filter="goods_list"></table>
        </div>
    </div>
</div>

<!-- 操作模板 -->
<script type="text/html" id="toolbar">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm" lay-event="select">选择商品</button>
    </div>
</script>

<script src="/static/js/article/article/article_common.js"></script>
<script>
    $('.nav_list').eq(0).addClass('layui-this');
    var param = '/?';
    var state = getUrlParam('state');
    if( state != null ){
        param += '&status='+state;
        $('.nav_list').removeClass('layui-this');
        if( state == 1 ){
            $('.nav_list').eq(1).addClass('layui-this');
        }else{
            $('.nav_list').eq(2).addClass('laformyui-this');
        }
    }
    var _del = getUrlParam('delete');
    if( _del != null && _del == 1 ){
        $('.nav_list').removeClass('layui-this');
        param += '&delete='+_del;
        $('.nav_list').eq(3).addClass('layui-this');
    }

    var table = layui.table;
    var form = layui.form;
    var param = '/?';
    //执行渲染
    table.render({
        id:'goods',
        elem: '#goods',
        height: 'auto', //容器高度
        url: '/goods/goods/index'+param,
        page: true,
        toolbar: '#toolbar',
        headers: {
            ctrl: SHOP_DATA
        },
        cols: [[
            {field:'id',type:'checkbox',width:50},
            {field: 'id', title: 'ID', width:80},
            {field: 'image', title: '主图', width:125,templet:function (d) { return '<div><img src='+d.image+' width="100" height="100"></div>' }},
            {field: 'goods_name', title: '商品名',},
            {field: 'sku_sn', title: '货号', width:120},
            {field: 'sort', title: '排序', width:60},
            {field: 'update_time', title: '更新时间', width:170 },
            // {field: 'id', title: '操作', width:260,templet: '#ctrlTpl'}
        ]]
    });

    layui.use('form', function(){
        request.setHost(SHOP_DATA).get('/goods/category/getCateAll/',{'showType':'tree_list'}, function(res){
            if( res.code == 0 ){
                var cate_list = setTreeGrid(res.data);
                var cate_html = '<option value="" >选择分类</option>';
                for( var c = 0; c < cate_list.length; c++ ){
                    cate_html += '<option value="'+cate_list[c].id+'" >'+cate_list[c].cate_name+'</option>';
                }
                $('#cate_box').html(cate_html);
                form.render();
            }
        });
        request.setHost(SHOP_DATA).get('/goods/goods_brands/all/',{'limit':'0'}, function(res){
            if( res.code == 0 ){
                var brand_list = res.data;
                var brand_html = '<option value="" >选择品牌</option>';
                for( var b = 0; b < brand_list.length; b++ ){
                    brand_html += '<option value="'+brand_list[b].id+'" >'+brand_list[b].brand_name+'</option>';
                }
                $('#brand_box').html(brand_html);
                form.render();
            }
        });
        form.on('submit', function(data){
            searchData = data.field;
            //上述方法等价于
            table.reload('goods', {
                where: searchData,
                page: {
                    curr: 1 //重新从第 1 页开始
                }
            });
            return false;
        });
        form.render();
    });

    //监听工具栏事件
    table.on('toolbar(goods_list)', function (obj) {
        var checkStatus = table.checkStatus(obj.config.id);
        
        const layEvent = obj.event;
        if (layEvent === 'select') {
            var data = checkStatus.data;
            selectGoods(data);
        }
        
    });


    function selectGoods(data) {
        parent.getSelectGoods(data);
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
    }

    function setTreeGrid(cate_list){
        var nbsp = '';
        var tree = '';
        for( var i = 0; i < cate_list.length; i++ ){
            tree = '';
            nbsp = '';
            if( cate_list[i].pNum > 0 ){
                tree += '├';
                for( var t = 0; t < cate_list[i].pNum; t++ ){
                    // nbsp += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    // tree += '─';
                    tree += '──';
                }
                cate_list[i].cate_name = nbsp+tree+cate_list[i].cate_name;
            }
        }
        return cate_list;
    }

</script>



</body>
</html>