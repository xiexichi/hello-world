<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:79:"D:\project\v3\view\admin\app\public/../application/system\view\index\index.html";i:1551857230;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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


<div class="layui-fluid" id="app">
	<div class="layui-card">
		<div class="layui-card-body">
			<div class="layui-tab layui-tab-brief" lay-filter="systemTab">
				<ul class="layui-tab-title">
					<?php if(is_array($tabs) || $tabs instanceof \think\Collection || $tabs instanceof \think\Paginator): if( count($tabs)==0 ) : echo "" ;else: foreach($tabs as $k=>$vo): ?>
						<li class="<?php echo $k==0?'layui-this':''; ?>" lay-id="<?php echo $k; ?>"><?php echo $vo; ?></li>
					<?php endforeach; endif; else: echo "" ;endif; ?>
					<span class="float-right">
						<button class="layui-btn layui-btn-normal layui-btn-sm" id="J-add">添加参数</button>
					</span>
				</ul>
				<div class="layui-tab-content" id="systemTabContent">
					<?php if(is_array($formHtmlGroup) || $formHtmlGroup instanceof \think\Collection || $formHtmlGroup instanceof \think\Paginator): if( count($formHtmlGroup)==0 ) : echo "" ;else: foreach($formHtmlGroup as $k=>$vo): ?>
					<div class="layui-tab-item <?php echo $k==0?'layui-show':''; ?>">
						<?php if(!(empty($vo) || (($vo instanceof \think\Collection || $vo instanceof \think\Paginator ) && $vo->isEmpty()))): ?>
							<form class="layui-form">
								<?php echo $vo; ?>
								<div class="layui-form-item">
									<div class="layui-input-block">
										<input type="hidden" name="group" value="<?php echo $k; ?>">
										<button class="layui-btn" lay-submit lay-filter="submit-system">立即提交</button>
									</div>
								</div>
							</form>
						<?php endif; ?>
					</div>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<style type="text/css">
.layui-form-label{width:150px;}
.layui-input-block{margin-left:180px;}
</style>
<script type="text/javascript" charset="utf-8" src="/static/js/system/index.js"></script>
</body>
</html>