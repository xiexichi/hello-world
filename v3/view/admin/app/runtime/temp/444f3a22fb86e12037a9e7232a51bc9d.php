<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"D:\project\v3\view\admin\app\public/../application//goods/view/categorys/add.html";i:1550629651;}*/ ?>
<style>
    .table-box{margin-top:20px;}
</style>
<link rel="stylesheet" href="/static/jwt/style/cj.css" media="all">
<form class="layui-form table-box" action="">
    <div class="layui-form-item">
        <label class="layui-form-label">分类名</label>
        <div class="layui-input-inline">
            <input type="text" name="cate_name" required  lay-verify="required" placeholder="分类名" autocomplete="off" class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">上级分类</label>
        <div class="layui-input-inline">
            <select name="pid" lay-search>
            </select>
        </div>
    </div>
    <div class="layui-form-item" id="upload_main" >
        <label class="layui-form-label">分类图片</label>
        <div class="layui-input-block">
            <div class="upload_box" style="width:180px; height:180px;">
                <input type="hidden" id="icon_input" name="cate_icon" class="hid-val-box" readonly />
                <div class="upload-view" id="icon" style="height:165px;" >
                    <img alt="" onerror="this.src='/static/jwt/images/upload_add.png'" >
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
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>
<script>
    var _thisObj;
    function checkSort(obj){
        var num = $(obj).val();
        if(num > 255){
            $(obj).val(255);
        }else if(num < 0){
            $(obj).val(0);
        }
    }
    $('#icon').click(function(){
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
            title : '选择图片',
            content: photoSpaceUrl,
            shadeClose: true,
            area: ['100%', '100%'],
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
    function loadCate(){
        // 获取分类信息
        request.setHost(SHOP_DATA).get('/goods/category/getCateAll/',{'showType':'tree'}, function(res){
            if( res.code == 0 ){
                res.data = setTreeList(res.data,'children');
                //分层标题处理
                res.data = setTreeGrid(res.data);
                $('select[name=pid]').append('<option value="0">为顶级分类</option>');
                for( var l = 0; l < res.data.length; l++ ){
                    $('select[name=pid]').append('<option value="'+res.data[l].id+'">'+res.data[l].cate_name+'</option>');
                }
                form.render();
            }
        });
    }

    $(document).ready(function(){
        loadCate(0);
        layui.use('form', function(){
            //监听提交
            form.on('submit(formDemo)', function(data){
                request.setHost(SHOP_DATA).post('/goods/category/checkRepeatCateName', data.field, function(res){
                    if (res.code == 0) {
                        if( res.data ){
                            layer.confirm("分类名已存在，是否继续添加", {
                                btn: ["确定","取消"] //按钮
                            }, function() {
                                add_submit(data.field);
                            });
                        }else{
                            add_submit(data.field);
                        }
                    } else {
                        layer.msg(res.msg);
                    }
                });
                return false;

                return false;
            });
        });
        form.render();
    });

    function add_submit(data){
        request.setHost(SHOP_DATA).post('/goods/category/add', data, function(res){
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
    }

    //树状结构多维数组转一维数组
    function setTreeList(cate_list,child,pNum,returnList){
        if( typeof(pNum) == 'undefined' ){
            pNum = 0;
        }
        if( typeof(returnList) == 'undefined' ){
            returnList = new Array();
        }
        for( var keys in cate_list ){
            cate_list[keys]['pNum'] = pNum;
            returnList.push(cate_list[keys]);
            if( typeof(cate_list[keys][child]) != 'undefined' && cate_list[keys][child].length > 0 ){
                returnList = setTreeList(cate_list[keys][child],child,pNum+1,returnList);
            }
        }
        return returnList;
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

