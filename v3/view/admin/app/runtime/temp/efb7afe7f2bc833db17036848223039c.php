<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:78:"D:\project\v3\view\admin\app\public/../application//system/view/index/add.html";i:1547107000;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
	<form class="layui-form layui-form-pane" id="openAddForm" lay-filter="layer">
		<div class="layui-form-item">
			<label class="layui-form-label">配置标题</label>
			<div class="layui-input-block">
				<input type="text" name="title" placeholder="显示名称" autocomplete="off" class="layui-input">
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">参数分组</label>
			<div class="layui-input-block">
				<?php $tabs = config('systemTab'); ?>
				<select name="group">
					<?php if(is_array($tabs) || $tabs instanceof \think\Collection || $tabs instanceof \think\Paginator): if( count($tabs)==0 ) : echo "" ;else: foreach($tabs as $k=>$vo): ?>
	        <option value="<?php echo $k; ?>"><?php echo $vo; ?></option>
	        <?php endforeach; endif; else: echo "" ;endif; ?>
	      </select>
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">类型</label>
			<div class="layui-input-block">
				<select name="type">
	        <option value="input">文本框 input</option>
	        <option value="select">下拉列表菜单 select</option>
	        <option value="radio">单选框组 radio</option>
	        <option value="checkbox">复选框组 checkbox</option>
	        <option value="textarea">多选文本框 textarea</option>
	      </select>
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">字段名</label>
			<div class="layui-input-block">
				<input type="text" name="name" placeholder="配置字段（大写），如 WEB_SITE_NAME" autocomplete="off" class="layui-input">
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">默认值</label>
			<div class="layui-input-block">
				<input type="text" name="value" placeholder="配置内容" autocomplete="off" class="layui-input">
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">备注内容</label>
			<div class="layui-input-block">
				<input type="text" name="note" placeholder="显示在后面的提示文字" autocomplete="off" class="layui-input">
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">扩展内容</label>
			<div class="layui-input-block">
				<textarea name="extra" placeholder="扩展内容" class="layui-textarea"></textarea>
				<div class="layui-form-mid layui-word-aux">类型为select,radio,checkbox时，这里填写选项，以英文,分隔</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-input-block">
				<button class="layui-btn" lay-submit lay-filter="edit-layer">立即提交</button>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
// layui
layui.use('form', function (){
  const form = layui.form
  form.render()

  //监听添加层提交
  form.on('submit(edit-layer)', function (data) {
  	const loadLayer = layer.load()
    request.setHost('shop_data').post('/system/system/add', data.field, function (json){
    	layer.close(loadLayer)
    	if (json.code === 0) {
    		layer.msg(json.msg, {
    			icon: 1,
    			time: 2000
    		}, function (){
    			// 刷新父页面
    			parent.location.reload()
    		})
    	}else{
    		layer.alert(json.msg, function (index){
    			layer.close(index)
    		})
    	}
    })
    return false
  })

})
</script>
</body>
</html>