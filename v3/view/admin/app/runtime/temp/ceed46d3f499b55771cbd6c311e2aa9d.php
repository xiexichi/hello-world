<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:82:"D:\project\v3\view\admin\app\public/../application//picshow/view/index/detail.html";i:1551405380;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
      <label class="layui-form-label">广告标题</label>
      <div class="layui-input-block">
        <input type="text" name="title" placeholder="广告标题" class="layui-input" required lay-verify="required" v-model="row.title">
      </div>
    </div>
    <div class="layui-form-item layui-form" lay-filter="positionId">
      <label class="layui-form-label">所属广告位</label>
      <div class="layui-input-block">
        <select name="position_id" lay-verify="required">
          <option value="">选择广告位</option>
          <option v-for="(item, key) in positions" :key="key" :value="item.id" v-bind:selected="item.id==row.position_id">{{item.position_name}}</option>
        </select>
      </div>
    </div>
    <div class="layui-form-item layui-form" lay-filter="moduleId">
      <label class="layui-form-label">内容模块</label>
      <div class="layui-input-block">
        <select name="module_id" lay-verify="required">
          <option value="">选择内容模块</option>
          <option v-for="(item, key) in modules" :key="key" :value="item.id" v-bind:selected="item.id==row.module_id">{{item.module_name}}</option>
        </select>
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">访问参数</label>
      <div class="layui-input-block">
        <input type="text" name="parameter" placeholder="可选，如：id=1&tag=HEA" class="layui-input" v-model="row.parameter">
      </div>
    </div>
    <div class="layui-form-item" id="upload_main">
      <label class="layui-form-label">广告图片</label>
      <div class="layui-input-block">
        <div class="upload_box">
          <input type="hidden" name="imgurl" class="hid-val-box" v-model="row.imgurl" readonly />
          <div class="upload-view" @click="openPhotoSpace">
            <img alt="" :src="row.imgurl + '!w200'" >
          </div>
        </div>
        <div class="clear"></div>
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">开始时间</label>
      <div class="layui-input-block">
        <input type="text" name="start_time" id="start_time" autocomplete="off" placeholder="有效开始时间" class="layui-input" required lay-verify="required" v-model="row.start_time">
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">结束时间</label>
      <div class="layui-input-block">
        <input type="text" name="end_time" id="end_time" autocomplete="off" placeholder="有效结束时间" class="layui-input" v-model="row.end_time">
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

<link rel="stylesheet" href="/static/jwt/style/cj.css" media="all">
<script type="text/javascript" charset="utf-8" src="/static/js/vue.js"></script>
<script type="text/javascript">
const id = getUrlParam('id')
var app = new Vue({
  el: '#app',
  data: {
  	id: id,
    position_id: 0,
    module_id: 0,
    row: {
      imgurl: ''
    },
    positions: [],
    modules: []
  },
  mounted: function () {
    if (id && id != 0) this.getOne()
    this.getPositions()
    this.getModules()
  },
  /*watch: {
    positions: function (newQuestion, oldQuestion) {
      setTimeout(function () {
        // 刷新表单控件
        layui.form.render('select')
      }, 250)
    }
  },*/
  methods: {
  	// 获取数据
  	getOne: function() {
  		let _this = this
  		_this.id = id
  		request.setHost('center_data').get('/picshow/index/one?id='+id, function (json) {
  			if (json.code === 0) {
          _this.platform_code = json.data.platform_code
          _this.row = json.data
          _this.row.parameter = parseParams(json.data.parameter, false)
          _this.$nextTick().then (function () {
            layui.form.render('select')
          })
  			} else {
  				layer.alert(json.msg)
  			}
  		})
  	},
    // 获取apps列表
    getPositions: function () {
      let _this = this
      request.setHost('center_data').get('/picshow/position/all', function(json) {
        if (json.code === 0) {
          _this.positions = json.data
          _this.$nextTick().then (function () {
            layui.form.render('select', 'positionId')
          })
        }else{
          layer.alert(json.msg)
        }
      })
    },
    // 获取apps列表
    getModules: function () {
      let _this = this
      request.setHost('center_data').get('/picshow/module/all', function(json) {
        if (json.code === 0) {
          _this.modules = json.data
          _this.$nextTick().then (function () {
            layui.form.render('select', 'moduleId')
          })
        }else{
          layer.alert(json.msg)
        }
      })
    },
    // 选择图片触发器
    openPhotoSpace: function () {
      parent.openPhotoSpace()
    },
    // 设置值
    setValue: function (key, value) {
      this.row[key] = value
    }
  }
})

// layui
layui.use(['form', 'laydate'], function () {
  const form = layui.form
  const laydate = layui.laydate
  form.render()

  laydate.render({
    elem: '#start_time',
    type: 'datetime'
  })
  laydate.render({
    elem: '#end_time',
    type: 'datetime'
  })

  //监听添加层提交
  form.on('submit(edit-layer)', function (data) {
  	const loadLayer = layer.load()
  	let url = '/picshow/index/add'
  	if (id && id !=0){
  		url = '/picshow/index/edit'
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