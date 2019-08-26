<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"D:\project\v3\view\admin\app\public/../application//goods/view/brands/detail.html";i:1548297175;}*/ ?>

<style>
    .table-box{margin-top:20px;}
</style>
<link rel="stylesheet" href="/static/jwt/style/cj.css" media="all">
<div class="layui-card">
    <div class="layui-card-body">
        <button class="layui-btn layui-btn-sm layui-btn-primary" onclick="parent.window.callback()">
            <i class="layui-icon"></i>返回
        </button>
        <form class="layui-form table-box" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">品牌名</label>
                <div class="layui-input-inline">
                    <input type="text" name="brand_name" required  lay-verify="required" placeholder="请输入品牌名" autocomplete="off" class="layui-input" >
                    <input type="hidden" name="id" id="ids" value="">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">开头字母</label>
                <div class="layui-input-inline">
                    <input type="text" name="brand_letter" required  lay-verify="required" placeholder="请输入品牌名开头字母" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">排序</label>
                <div class="layui-input-inline">
                    <input type="number" min="0" max="255" name="sort" required  lay-verify="required" placeholder="0-255" value="0" autocomplete="off" class="layui-input" onkeyup="checkSort(this)" >
                </div>
                <div class="layui-form-mid layui-word-aux">(排序由大都小排列)</div>
            </div>
            <div  id="upload_main" >
                <div class="layui-form-item">
                    <label class="layui-form-label">品牌LOGO<br/>(200 * 200)</label>
                    <div class="layui-input-block">
                        <div class="upload_box" style="width:130px; height:130px;" >
                            <input type="hidden" id="logo_input" name="brand_logo" class="hid-val-box" readonly />
                            <!--<div class="upload-title">品牌logo</div>-->
                            <!--<div class="upload-tips"><span>88</span> <span>*</span><span>33</span></div>-->
                            <div class="upload-view" id="logo" style="height:118px;" >
                                <img alt="" onerror="this.src='/static/jwt/images/upload_add.png'" >
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">品牌图片<br />(203 * 64)</label>
                    <div class="layui-input-block">
                        <div class="upload_box" style="width:130px; height:64px;">
                            <input type="hidden" id="img_input" name="brand_img" class="hid-val-box" readonly />
                            <div class="upload-view" id="img" style="height:50px;" >
                                <img alt="" onerror="this.src='/static/jwt/images/upload_add.png'" >
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">品牌描述</label>
                <div class="layui-input-block">
                    <textarea name="brand_desc" id="brand_desc" style="resize:none; width:300px;"  placeholder="请输入内容" class="layui-textarea"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    var id = getUrlParam('id');
    $(document).ready(function(){
        var param = {};
        param.id = id;
        request.setHost(SHOP_DATA).post('/goods/goods_brands/getBrandInfo',param, function(res){
            if (res.code == 0) {
                // 成功提示
                $('input[name=brand_name]').val(res.data.brand_name);
                $('input[name=brand_letter]').val(res.data.brand_letter);
                $('input[name=sort]').val(res.data.sort);
                $('input[name=id]').val(res.data.id);
                $('#logo').find('img').attr('src',res.data.brand_logo);
                $('#logo_input').val(res.data.brand_logo);
                $('#img').find('img').attr('src',res.data.brand_logo);
                $('#img_input').val(res.data.brand_logo);
                $('#brand_desc').val(res.data.brand_desc);
            } else {
                // 错误提示
                layer.msg(res.msg);
            }
        });
    });
    function checkSort(obj){
        var num = $(obj).val();
        if(num > 255){
            $(obj).val(255);
        }else if(num < 0){
            $(obj).val(0);
        }
    }

    let _thisObj;
    // 选择触发器
    $('#logo').click(function(){
        _thisObj = $(this);
        openPhotoSpace();
    });

    $('#img').click(function(){
        _thisObj = $(this);
        openPhotoSpace();
    });

    //打开图片空间
    function openPhotoSpace(){
        const url = "<?php echo url('/handlePhoto.html','','',true);?>"
        const token = "<?php echo session('photojwttoken');?>";
        const photoSpaceUrl = 'http://photo.25boy.com/?token='+token+'&url='+url+'&showconfirm=1';
        layer.open({
            type: 2,
            content: photoSpaceUrl,
            shadeClose: true,
            area: ['60%', '60%'],
            success: function(layero){
                layer.setTop(layero);
            }
        })
    }

    /**
     * 选择图片方法
     * 从handlePhoto.html文件自动发起调用
     * JSON content
     */
    function handlePhoto(content){
        if(content == 'close'){
            // 关闭窗口
            layer.closeAll();
        }else{
            var json = JSON.parse(content);
            // 业务处理
            if( json.length == 0 ){
                layer.msg('请选择图片');
                return false
            }
            if( json.length > 1 ){
                layer.msg('只能选择一张图片哦');
                return false
            }
            _thisObj.find('img').attr('src',json[0].image);
            $('#'+$(_thisObj).attr('id')+'_input').val(json[0].image);
            layer.closeAll();
        }
    }
    var form = layui.form;
    $(document).ready(function(){
        layui.use('form', function(){
            //监听提交
            form.on('submit(formDemo)', function(data){
                request.setHost(SHOP_DATA).post('/goods/goods_brands/edit', data.field, function(res){
                    if (res.code == 0) {
                        // 成功提示
                        layer.msg(res.msg);
                        setTimeout(function(){
                            parent.window.callback();
                        },1500);
                    } else {
                        // 错误提示
                        layer.msg(res.msg);
                    }
                });
                return false;
            });
        });
        form.render();
    });


</script>

