<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:85:"D:\project\v3\view\admin\app\public/../application//picshow/view/position/detail.html";i:1551405380;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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

<div class="p-1" id="app">
	<form class="layui-form layui-form-pane" id="openAddForm" lay-filter="layer">
    <div class="layui-form-item">
      <label class="layui-form-label">广告位名称</label>
      <div class="layui-input-block">
        <input type="text" name="position_name" placeholder="便于快速识别所在位置的名称" class="layui-input" required lay-verify="required" v-model="row.position_name">
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">所属平台</label>
      <div class="layui-input-block">
        <select name="platform_code" lay-verify="required" lay-filter="platform_code">
          <option value="">选择关联应用</option>
          <option v-for="(item, key) in apps" :key="key" :value="item.platform_code" v-bind:selected="item.platform_code==platform_code">{{item.app_name}} [{{item.platform_code}}]</option>
        </select>
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">宽度</label>
      <div class="layui-input-block">
        <input type="text" name="width" placeholder="建议图片设计尺寸，单位px" class="layui-input" required lay-verify="required" v-model="row.width">
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">高度</label>
      <div class="layui-input-block">
        <input type="text" name="height" placeholder="建议图片设计尺寸，单位px" class="layui-input" required lay-verify="required" v-model="row.height">
      </div>
    </div>
		<div class="layui-form-item">
			<label class="layui-form-label">描述</label>
			<div class="layui-input-block">
				<textarea name="desc" placeholder="后台描述内容" class="layui-textarea" v-model="row.desc"></textarea>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-input-block">
				<input type="hidden" name="id" v-if="id" v-model="id">
				<button class="layui-btn" lay-submit lay-filter="edit-layer">提交</button>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript" charset="utf-8" src="/static/js/vue.js"></script>
<script type="text/javascript">
const id = getUrlParam('id')
var app = new Vue({
  el: '#app',
  data: {
  	id: id,
    platform_code: '',
    row: {},
    apps: []
  },
  mounted: function () {
    if (id && id != 0) this.getOne()
    this.getApps()
  },
  watch: {
    apps: function (newQuestion, oldQuestion) {
      setTimeout(function () {
        // 刷新表单控件
        layui.form.render('select')
      }, 250)
    }
  },
  methods: {
  	// 获取数据
  	getOne: function() {
  		let _this = this
  		_this.id = id
  		request.setHost('center_data').get('/picshow/position/one?id='+id, function (json) {
  			if (json.code === 0) {
          _this.platform_code = json.data.platform_code
          _this.row = json.data
  			} else {
  				layer.alert(json.msg)
  			}
  		})
  	},
    // 获取apps列表
    getApps: function () {
      let _this = this
      request.setHost('center_data').get('/apps/app_auth/all', function(json) {
        if (json.code === 0) {
          _this.apps = json.data
          _this.$nextTick().then (function () {
            layui.form.render('select')
          })
        }else{
          layer.alert(json.msg)
        }
      })
    }
  }
})

// layui
layui.use('form', function () {
  const form = layui.form
  form.render()

  //监听添加层提交
  form.on('submit(edit-layer)', function (data) {
  	const loadLayer = layer.load()
  	let url = '/picshow/position/add'
  	if (id && id !=0){
  		url = '/picshow/position/edit'
  	}
    request.setHost('center_data').post(url, data.field, function (json){
      layer.close(loadLayer)
    	if (json.code === 0) {
        layer.msg(json.msg, {
          icon: 1,
          time: 2000
        }, function () {
          // 刷新父页面
          parent.location.reload()
        })
      }else{
        layer.close(loadLayer)
        layer.alert(json.msg, function (index) {
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