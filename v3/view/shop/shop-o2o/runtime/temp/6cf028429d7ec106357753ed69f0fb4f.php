<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:92:"D:\project\v3\view\shop\shop-o2o\public/../application//power/view/power_role/page_list.html";i:1555997870;}*/ ?>
<div class="role" id="app">
    <div class="layui-card">
        <div class="layui-card-body">
            <table id="myDataTable" lay-filter="myDataTable"></table>
        </div>
    </div>
</div>
<!-- 表格工具栏模板 -->
<script id="toolbarTpl" type="text/html">
    <div class="layui-btn-group">
		<button class="layui-btn layui-btn-primary layui-btn-sm" lay-event="add"><i class="layui-icon layui-icon-add-1"></i>添加</button>
		<button class="layui-btn layui-btn-primary layui-btn-sm" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>修改</button>
		<button class="layui-btn layui-btn-primary layui-btn-sm" lay-event="delete"><i class="layui-icon layui-icon-delete"></i>删除</button>
	</div>
</script>
<script type="text/javascript">
// layui
layui.use(['table', 'form'], function() {
    const table = layui.table
    const form = layui.form

    form.render()

    // 数据表格
    table.render({
        elem: '#myDataTable',
        height: 'auto',
        url: '/power/power_role/getList',
        toolbar: '#toolbarTpl',
        limit: 30,
        defaultToolbar: false,
        headers: {
            ctrl: SHOP_DATA
        },
        page: true,
        cols: [
            [
                { field: 'id', title: '', type: 'radio' },
                { field: 'title', title: '角色名称' },
                { field: 'note', title: '备注' }
            ]
        ]
    })

    //监听工具栏事件
    table.on('toolbar(myDataTable)', function(obj) {
        const rows = table.checkStatus(obj.config.id);
        const data = rows.data[0] || undefined
        if (obj.event != 'add' && !data) {
            layer.msg('请选择要操作的行')
            return false
        }
        switch (obj.event) {
            case 'add':
                layer.open({
                    title: '添加角色',
                    type: 2,
                    content: '/power/power_role/page_add',
                    area: ['800px', '85%'],
                    btn: false
                })
                break;
            case 'edit':
                layer.open({
                    title: '修改角色',
                    type: 2,
                    content: '/power/power_role/page_edit?id=' + data.id,
                    area: ['800px', '85%'],
                    btn: false
                })
                break;
            case 'delete':
                layer.confirm("是否确认删除！", {
                    btn: ["确定","取消"] //按钮
                }, function(){
                    request.setHost(SHOP_DATA).post('/power/power_role/delete',{ids:data.id}, function(res){
                        if (res.code == 0) {
                            // 成功提示
                            layer.msg(res.msg);
                            setTimeout(function(){
                                table.reload('myDataTable');
                            },1000);
                        } else {
                            // 错误提示
                            layer.msg(res.msg);
                        }
                    });
                });
                return false;
                break;
        }
    })

})
</script>