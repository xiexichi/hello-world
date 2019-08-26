<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:76:"D:\project\v3\view\admin\app\public/../application//test/view/test/test.html";i:1556863110;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
    .layui-table-body .layui-table-cell{
        height:auto;
        overflow:hidden;
        border:1px solid #ccc;
        padding:0;
    }
    .layui-table td, .layui-table th, .layui-table-col-set, .layui-table-fixed-r, .layui-table-grid-down, .layui-table-header, .layui-table-page, .layui-table-tips-main, .layui-table-tool, .layui-table-total, .layui-table-view,
    .layui-table[lay-skin=line], .layui-table[lay-skin=row]{border-color:#fff !important;}
    .layui-table-header{display:none;}
    .layui-table .layui-table-hover{background-color:transparent !important;}
    .layui-btn{min-width:80px;}
    .order-info{height:auto; line-height:35px; overflow:hidden;}
    .order-info div{ padding:0 0 0 20px; border-bottom:1px solid #ccc;}
    .order-info span{margin-right:10px;}
    .order-info .order-base{background-color:#f2f2f2;display:flex;align-items:center;justify-content:space-between;}
    .order-info .order-remark{color:#009688;font-size:12px;}
    .goods-list{float:left;padding:15px 0 15px 20px; height:190px; line-height:150px;}
    .goods-list .goods{float:left; height:140px; width:100px; line-height:140px; text-align:center; margin-right:10px;}
    .goods-list img{height:100px; width:100px;}
    .goods-list p{height:20px; width:100px; line-height:20px; font-size:12px;}
    .order-status-box,.order-content{float:right;padding:45px 0 15px 0; height:190px;}
    .order-status-box{width:200px; text-align:center; border-left:1px solid #ccc; border-right:1px solid #ccc;}
    .order-content{width:200px; text-align:center;}
</style>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="" id="form" >
                <div class="layui-form-item">
                    <label class="layui-form-label">风格</label>
                    <div class="layui-input-inline">
                    <select name="shop_id" id="shop_box"></select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    var form = layui.form;
    //已经封装好的request版ajax,,,,,,setHost里的参数,SHOP_DATA或者CENTER_DATA是两个不同的数据来源路径
    request.setHost(SHOP_DATA).get('/test/test/gettest',{}, function(res){
        // console.log(res);
        if( res.code == 0 ){
            var shop = res.data;
            var shop_box_html = '<option value="" >请选择风格</option>';
            for( var items in shop){
                shop_box_html += '<option value="'+shop[items].id+'" >'+shop[items].name+'</option>';
                // console.log(shop_box_html);
            }
            $('#shop_box').html(shop_box_html);
            form.render();
        }
    });
</script>

</body>
</html>