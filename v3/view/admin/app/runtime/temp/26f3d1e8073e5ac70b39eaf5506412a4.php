<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:89:"D:\project\v3\view\admin\app\public/../application//article/view/article/article_add.html";i:1553853809;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:49:"../application/article/view/article/add_info.html";i:1553853809;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
    .layui-form-label{width:100px;}
    .star{color:#FB5A5C;}
</style>
<link rel="stylesheet" href="/static/jwt/style/cj.css" media="all">
<script src="/static/ueditor/ueditor.config.js"></script>
<script src="/static/ueditor/ueditor.all.js"></script>
<script src="/static/ueditor/lang/zh-cn/zh-cn.js"></script>
<div class="layui-fluid">
    <form class="layui-form table-box" action="" id="form" lay-filter="layer">
        <div class="layui-card">
            <div class="layui-card-body" id="upload_main" >
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <button class="layui-btn" lay-submit lay-filter="formAdd">立即提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        <button class="layui-btn layui-btn-primary" onclick="parent.window.callback()">返回</button>
                    </div>

                </div>
                <div class="layui-tab layui-tab-card">
                    <ul class="layui-tab-title">
                        <li class="layui-this">基础信息</li>
                    </ul>
                    <div class="layui-tab-content" >
                        <div class="layui-tab-item layui-show">
                                <div class="layui-form-item">
        <label class="layui-form-label">文章名<span class="star">*</span></label>
        <div class="layui-input-inline">
            <input type="text" name="title"  lay-verify="required" placeholder="请输入文章名" autocomplete="off" class="layui-input" style="width:300px;" >
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">文章分类<span class="star">*</span></label>
        <div class="layui-input-inline">
            <select name="categorys_id" lay-search>
            </select>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">文章图片<span class="star">*</span></label>
        <div class="layui-input-inline">
            <div class="upload_box" style="width:180px; height:180px;">
                <input type="hidden" id="article_image_input" name="image" class="hid-val-box" readonly />
                <div class="upload-title">主图<span class="star">*</span></div>
                <div class="upload-view" id="image" style="height:150px;" onClick="openPhotoSpace()" >
                    <img alt="" id="imageBack" src="" onerror="this.src='/static/jwt/images/upload_add.png'" >
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">排序</label>
        <div class="layui-input-inline">
            <input type="number" min="0" max="255" name="sort" placeholder="0-255" value="0" autocomplete="off" class="layui-input" onkeyup="checkSort(this)" >
        </div>
        <div class="layui-form-mid layui-word-aux">(排序由大都小排列)</div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">描述</label>
        <div class="layui-input-inline">
            <textarea name="desc" style="resize:none; width:300px;"  placeholder="请输入描述" class="layui-textarea"></textarea>
        </div>
    </div>


    <div class="layui-form-item">
        <label class="layui-form-label">文章详情</label>
        <div class="layui-input-block">
            <div id="article_content" name="content" style="width:75%; height:300px; margin-left:20px;"></div>
        </div>
    </div>

    <script>
    var urls = "<?php echo url('/handlePhoto.html','','',true);?>";
    var tokens = "<?php echo session('photojwttoken');?>";
    var photoSpaceUrl = 'http://photo.25boy.com/?token='+tokens+'&url='+urls+'&showconfirm=1';
        var ue = UE.getEditor('article_content');
        //获取文章内容内容
        function getArticleContent() {
            var html = '';
            ue.ready(function () {
                html = ue.getContent();
            });
            return html;
        }
    </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- <script type="text/javascript" charset="utf-8" src="/static/js/vue.js"></script> -->
<script type="text/javascript" charset="utf-8" src="/static/js/article/article/article_common.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/js/article/article/article_add.js"></script>





</body>
</html>