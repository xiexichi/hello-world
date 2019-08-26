<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:86:"D:\project\v3\view\admin\app\public/../application//merchant/view/merchant/detail.html";i:1554802462;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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


<div class="p-1">
  
  <form id="merchant-form" class="layui-form" action="">
    <input type="hidden" name="id">

    <div class="layui-form-item">
      <label class="layui-form-label">商户名称</label>
      <div class="layui-input-inline">
        <input type="text" name="name" required  lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
      </div>
      
      <label class="layui-form-label">账号</label>
      <div class="layui-input-inline">
        <input type="text" name="account" required  lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
      </div>
  
      <label class="layui-form-label">密码</label>
      <div class="layui-input-inline">
        <input type="password" name="passwd" placeholder="不修改请留空" class="layui-input">
      </div>
    </div>


    <div class="layui-form-item">
      <label class="layui-form-label">商户类型</label>
      <div class="layui-input-inline">
        <select id="merchant_types" name="merchant_type_id" lay-verify="required">
        </select>
      </div>
  
      <label class="layui-form-label">商户状态</label>
      <div class="layui-input-inline">
        <input type="radio" name="status" value="1" title="正常">
        <input type="radio" name="status" value="0" title="禁用">
      </div>

    </div>


    <div class="layui-form-item">
      <label class="layui-form-label">省</label>
      <div class="layui-input-inline">
        <select id="province" name="province_id" lay-verify="required" lay-filter="province">
          <option value=""></option>
        </select>
      </div>

      <label class="layui-form-label">市</label>
      <div class="layui-input-inline">
        <select id="city" name="city_id" lay-verify="required" lay-filter="city">
          <option value=""></option>
        </select>
      </div>
      
      <label class="layui-form-label">区</label>
      <div class="layui-input-inline">
        <select id="region" name="region_id" lay-verify="required">
          <option value=""></option>
        </select>
      </div>
    </div>

    <div class="layui-form-item layui-form-text">
      <label class="layui-form-label">商户简介</label>
      <div class="layui-input-block">
        <textarea name="desc" placeholder="" class="layui-textarea"></textarea>
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


<script>
  // 获取商户id
  var id = getUrlParam('id');

  /**
   * [setAreas 设置区域下拉数据]
   */
  function setAreas(params, $obj, callback){

    // 清空原有数据
    $obj.empty();

    // 获取地区信息
    request.get('/index/area/getAreas',params, function(res){
      console.info(res);

      for (var i = 0; i < res.data.length; i++) {
        var area = res.data[i];
        $obj.append('<option value="'+area.id+'">'+area.area_name+'</option>');
      }

      // 如果有回调函数，则执行
      if (undefined != callback) {
        callback();
      }

      // 渲染表单
      form.render();
    })
  }

//Demo
  
  var form = layui.form;

  // 渲染表单
  form.render();

  // 设置省份信息
  setAreas({type: 1}, $('#province'));

  // 获取商户类型数据
  request.setAsync(true).setHost(CENTER_DATA).get('/merchant/merchant/getMerchantTypes', function(res){
    for (let i = 0; i < res.data.length; i++) {
      var type = res.data[i];
      $('#merchant_types').append('<option value="'+type.id+'">'+type.type+'</option>');
    }
    // 渲染表单
    form.render();
  })


  // 获取商户详情
  request.setHost(CENTER_DATA).get('/merchant/merchant/one?id='+id, function(res){
    console.info(res);

    // 设置获取城市数据
    setAreas({type: 2, pid: res.data['province_id']}, $('#city'), function
      (){
        $('#merchant-form').find('select[name="city_id"]').val(res.data['city_id']);
      });

    // 设置获取地区数据
    setAreas({type: 3, pid: res.data['city_id']}, $('#region'), function
      (){
        $('#merchant-form').find('select[name="region_id"]').val(res.data['region_id']);
      });

    // 填入数据
    for (i in res.data) {
      // 跳过状态和密码
      if (i == 'status' || i == 'passwd') {
        continue;
      }
      $('#merchant-form').find('input[name="'+i+'"]').val(res.data[i]);
      $('#merchant-form').find('textarea[name="'+i+'"]').val(res.data[i]);
      $('#merchant-form').find('select[name="'+i+'"]').val(res.data[i]);
    }

    // 设置商户状态
    $('#merchant-form').find('input [name="status"],[value="'+res.data['status']+'"]').attr('checked', 'true');

    // 渲染表单
    form.render();
  })


  // 省份选择
  form.on('select(province)', function(data){
    // 设置城市信息
    setAreas({type: 2, pid: data.value}, $('#city'));
    // 区域也清空
    $('#region').empty();
  }); 

  // 城市选择
  form.on('select(city)', function(data){
    // 设置城市信息
    setAreas({type: 3, pid: data.value}, $('#region'));
  }); 
  
  //监听提交表单
  form.on('submit(formDemo)', function(data){
    console.info(data.field);
    // layer.msg(JSON.stringify(data.field));

    // 修改
    request.setHost(CENTER_DATA).post('/merchant/merchant/edit', data.field, function(res){
      console.info(res);

      if (res.code == 0) {

        // 成功提示
        layer.open({
          icon: 1,
          content: '修改成功',
          yes: function(index, layero){
            //do something
            layer.close(index); //如果设定了yes回调，需进行手工关闭
            // 返回上一页
            location.href = '/merchant/merchant/list.html';
          }
        });

      } else {
        // 错误提示
        layer.alert(res.msg, {icon: 1});
      }

    })

    return false;
  });


</script>
</body>
</html>