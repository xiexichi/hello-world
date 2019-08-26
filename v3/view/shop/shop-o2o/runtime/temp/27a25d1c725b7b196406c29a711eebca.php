<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:93:"D:\project\v3\view\shop\shop-o2o\public/../application//depot/view/depot_pre_select/list.html";i:1556004449;}*/ ?>

<div id="search-div" class="layui-collapse" lay-filter="search-filter">
  <div class="layui-colla-item">
    <h2 class="layui-colla-title">
      <strong>查找条件</strong>
    </h2>
    <div class="layui-colla-content layui-show">
    
      <div class="site-text site-block">

        <form class="layui-form" action="">
          <div class="layui-form-item fled-space-between">
            <div class="layui-input-inline">
              <button class="layui-btn" lay-submit lay-filter="formDemo">查找</button>
              <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
          </div>

          <div class="layui-form-item mt-1">
            <label class="layui-form-label">标记名称</label>
            <div class="layui-input-inline">
              <input type="text" name="tag" placeholder="" autocomplete="off" class="layui-input">
            </div>

            <label class="layui-form-label ctrl_type_ele">操作类型</label>
            <div class="layui-input-inline ctrl_type_ele">
              <select name="type" id="type" lay-filter="type">
                  <option value="">全部</option>
                  <option value="1">店铺进货</option>
                  <option value="2">店铺退货</option>
                  <option value="3">店铺调整</option>
                  <option value="4">店铺调拨</option>
              </select>
            </div>
  
            <label class="layui-form-label" id="depot_type_select_label">库存类型</label>
            <div class="layui-input-inline">
              <select name="shop_depot_type" id="depot_type_select" lay-filter="depot_type_select">
                  <option value="">全部</option>
                  <option value="1">商品</option>
                  <option value="2">物料</option>
              </select>
            </div>
  
          </div>
  
        </form>

      </div>
    
    </div>

  </div>
</div>


<!-- 商品表格 -->
<table id="demo" lay-filter="test">
</table>

<!-- 控制模板 -->
<script type="text/html" id="ctrlTpl">
  <div>
    <a href="/depot/depot_pre_select/detail.html?id={{ d.id }}" class="layui-table-link">编辑</a>
    &nbsp;&nbsp;
    <a href="/depot/depot_pre_select/select_list.html?id={{ d.id }}" class="layui-table-link">预选详情</a>
  </div>
</script>

<!-- 表格模板 -->
<script type="text/html" id="toolbarTpl">
  <div>
    <div id="add-select" class="layui-inline" lay-event="add"><i class="layui-icon layui-icon-add-1"></i></div>
    <div id="del-select" class="layui-inline" lay-event="delete"><i class="layui-icon layui-icon-delete"></i></div>
  </div>
</script>

<script type="text/javascript">

//  外部引用标签类型，默认为1（进货类型）
var type = getUrlParam('type');

if (type) {
  // 隐藏选择操作类型元素
  $('.ctrl_type_ele').hide();
}
	
//注意：折叠面板 依赖 element 模块，否则无法进行功能性操作
var element = layui.element;  
var table = layui.table;
var form = layui.form;

form.render();

//监听提交
form.on('submit(formDemo)', function(data){

  // 重新初始化表格数据
  init(data.field);
 
  return false;
});

  
// 初始化
init();

// 初始化方法
function init(params){

  // 请求地址
  url = '/depot/shop_depot_pre_select?';

  // 判断是否有外部引用
  if (type) {
    url += 'type=' + type;
  } else {

  }

  // 拼接参数
  if (params) {
    for (let i in params) {
      if (params[i]) {
        url += '&' + i + '=' + params[i];
      }      
    }
  }

  //第一个实例
  table.render({
  elem: '#demo'
  ,height: 422
  ,url: url      //数据接口
  ,page: true    //开启分页
  ,toolbar: '#toolbarTpl'
  // ,toolbar: 'default'
  ,headers: {
  	ctrl: SHOP_DATA
  }
  ,done: function(res, curr, count){
    tableDoneCallback(res, curr, count);
  }
  ,cols: [[ //表头
    {type: 'radio', fixed: 'left'}
    ,{field: 'tag', title: '标记', width:120}
    ,{field: 'type', title: '预选类型', width: 120, templet: function(row){
      if (row.type == 1) {
        return '进货';
      }
      if (row.type == 2) {
        return '退货';
      }
      if (row.type == 3) {
        return '调整';
      }
      if (row.type == 4) {
        return '调拨';
      }
      return '未知';
    }}
    ,{field: 'shop_depot_type', title: '库存类型', width: 120, templet: function(row){
      if (row.shop_depot_type == 1) {
        return '商品';
      }
      if (row.shop_depot_type == 2) {
        return '物料';
      }
      return '未知';
    }}
    ,{field: 'create_time', title: '创建时间', width: 180}
    ,{field: 'remarks', title: '备注', width: 280}
    ,{field: 'id', title: '操作', width: 280, templet: '#ctrlTpl'}
  ]]
  });

  table.on('radio(test)', function(obj){
    console.log(obj.checked); //当前是否选中状态
    console.log(obj.data); //选中行的相关数据

    if (obj.checked) {
      // 调用父页面的方法
      window.parent.setPreSelect(obj.data);
    }

  });

}

/**
 * [tableDoneCallback 表格初始化完成回调方法]
 * @param  {[type]} res   [description]
 * @param  {[type]} curr  [description]
 * @param  {[type]} count [description]
 * @return {[type]}       [description]
 */
function tableDoneCallback(res, curr, count){
  $('#add-select').on('click', function(){
    // 打开创建预选窗口
    openLayer('/depot/depot_pre_select/add_select');
  })
}


/**
 * [closeLayer 关闭弹窗]
 * @param  {[type]} tipType [提示类型]
 * @return {[type]}         [description]
 */
function closeLayer(tipType){
  // 关闭全部弹窗
  layer.closeAll();
  if (tipType == 'add') {
    layer.alert('添加成功');
  }

  if (tipType == 'edit') {
    layer.alert('修改成功');
  }

  // 重新初始化
  init();
}

</script>