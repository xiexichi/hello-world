<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:86:"D:\project\v3\view\admin\app\public/../application//goods/view/goods/sales_detail.html";i:1553657406;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/goods/view/goods/set_ship.html";i:1550126423;s:44:"../application/goods/view/goods/set_sku.html";i:1554691000;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
    .layui-table th,.layui-table td{border-color:#c6d1db; }
    [v-cloak]{display:none;}
</style>
<link rel="stylesheet" href="/static/jwt/style/cj.css" media="all">
<div class="layui-fluid">
    <form class="layui-form table-box" action="" id="form" v-cloak >
        <div class="layui-card" id="upload_main" >
            <div class="layui-card-body">
                <div class="layui-form-item">
                    <div class="layui-input-block" style="margin-left:10px;">
                        <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
                        <button type="button" class="layui-btn layui-btn-danger" id="openProp" onclick="$('#prop_box').show();$('#prop_val_box').show();$('#openProp').hide();$('#closeProp').show()" >开启属性管理</button>
                        <button type="button"  class="layui-btn layui-btn-danger" id="closeProp" onclick="$('#prop_box').hide();$('#prop_val_box').hide();$('#openProp').show();$('#closeProp').hide()">关闭属性管理</button>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">市场价<span class="star">*</span></label>
                    <div class="layui-input-inline">
                        <input type="text" name="market_price" v-model="set_sales.market_price" required  lay-verify="required" placeholder="请输入市场价" autocomplete="off" class="layui-input" v-on:blur="validateFloatEmpty(set_sales.market_price,'market')" v-on:keyup="validateFloat(set_sales.market_price,'market')" >
                    </div>
                    <div class="layui-form-mid layui-word-aux">RMB </div>
                    <label class="layui-form-label">基础售价<span class="star">*</span></label>
                    <div class="layui-input-inline">
                        <input type="text" name="sell_price" v-model="set_sales.sell_price" required  lay-verify="required" placeholder="请输入售价"  autocomplete="off" class="layui-input" v-on:blur="set_sales_price(set_sales.sell_price,'sell')" v-on:keyup="validateFloat(set_sales.sell_price,'sell')" >
                    </div>
                    <div class="layui-form-mid layui-word-aux">RMB （无sku 或 sku没有设置售价时的 售价）</div>
                </div>
                <div class="layui-form-item">
    <label class="layui-form-label">商品重量</label>
    <div class="layui-input-inline">
        <input type="text" name="weight" required  lay-verify="required" placeholder="请输入商品重量" v-model="set_ship.weight" onblur="validateFloatEmpty(this)" onkeyup="validateFloat(this)" autocomplete="off" class="layui-input" >
    </div>
    <div class="layui-form-mid layui-word-aux">kg</div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">是否免邮</label>
    <div class="layui-input-block">
        <input type="radio" name="ship_free" lay-filter="ship_free"  v-model="set_ship.ship_free" value="1" title="是" >
        <input type="radio" name="ship_free" lay-filter="ship_free"  v-model="set_ship.ship_free" value="0" title="否" checked>
    </div>
    <div class="layui-form-mid layui-word-aux">仅为此商品单独免邮</div>
    <!--<div class="layui-form-mid layui-word-aux">免邮运费模板将失效</div>-->
</div>
<!--<div class="layui-form-item">-->
    <!--<label class="layui-form-label">运费模板</label>-->
    <!--<div class="layui-input-inline">-->
        <!--<select name="delivery_id" lay-filter="delivery" lay-search v-model="set_ship.delivery_id" >-->
            <!--<option :value="delivery.id" v-for="(delivery,index) in show_list.delivery_list">{{delivery.delivery_name}}</option>-->
        <!--</select>-->
    <!--</div>-->
<!--</div>-->

                <div class="layui-form-item" id="prop_box" >
    <label class="layui-form-label">属性选择</label>
    <div class="layui-input-block" >
        <input type="checkbox" class="prop_list" :value="index" name="prop[]" v-for="(prop,index) in show_list.prop_list" lay-filter="prop" :checked="prop.checked" :title="prop.prop_name">
    </div>
</div>
<div id="prop_val_box" >
    <div class="layui-form-item" v-for="(prop,p_index) in show_list.prop_list" v-if="prop.checked">
        <label class="layui-form-label" ><a href="javascript:void(0);"  v-on:click="getPropValList(p_index,1)" >{{prop.prop_name}}</a></label>
        <div class="layui-input-block" >
            <input type="checkbox" class="prop_val" lay-filter="prop_val" name="prop_val[]" v-for="(prop_val,pv_index) in prop.val_list" :value="pv_index" :data-prop-index="p_index" v-model="prop_val.checked" :title="prop_val.pv_name+'('+prop_val.pv_erp_code+')'" lay-skin="primary">
        </div>
    </div>
</div>
<div id="sku_table" v-if="set_sku.sku_tab.length > 0" >
    <table class="layui-table">
        <colgroup>
            <col width="80px">
            <col>
            <col v-for="(prop,p_index) in show_list.prop_list" v-if="prop.checked" >
            <col width="150px">
            <col width="150px">
            <col width="150px">
            <col width="150px">
        </colgroup>
        <thead>
        <tr>
            <th>图片<br />(默认主图)</th>
            <th>商品名</th>
            <th v-for="(prop,p_index) in show_list.prop_list" v-if="prop.has_checked" style="padding:0 10px;">{{ prop.prop_name }}</th>
            <th>价格</th>
            <th>erp货号<br />(不填将自动生成)</th>
            <th v-if="id" >库存</th>
            <th>备注</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
            <tr :class="{'layui-bg-gray':sku.is_deleted,'layui-bg-gray':!sku.sales_status,}" v-for="(sku,s_index) in set_sku.sku_tab" >
                <td style="padding:5px;" >
                    <div v-if="sku.is_deleted == 0" >
                        <div class="upload_box" style="width:80px; height:80px; border:none;" >
                            <input type="hidden" name="sku_image[s_index]" v-model="sku.item_image" class="hid-val-box" readonly />
                            <div class="upload-view" id="goods_image" style="height:68px;" v-on:click="uploadImg('sku',s_index)" >
                                <img alt="" :src="sku.item_image" onerror="this.src='/static/jwt/images/upload_add.png'" >
                            </div>
                        </div>
                        <div v-if="!sku.sales_status" class="upload-del-box" style="width:80px; text-align:center;" ><a href="javascript:void(0);" v-on:click="uploadDel('sku',s_index)">删除</a></div>
                    </div>
                    <input v-else type="text" placeholder="该项已删除">
                    <div class="clear"></div>
                </td>
                <td :title="sku.item_id ? sku.item_id : set_info.goods_name">{{set_info.goods_name}}</td>
                <td v-for="(name,pv_name_index) in sku.pv_name" :rowspan="set_sku.sku_tab_rowspan[pv_name_index]" v-if="s_index%set_sku.sku_tab_rowspan[pv_name_index]==0" :title="name">{{ name }}</td>
                <td>
                    <input type="text" class="layui-input" :placeholder="'默认'+set_sales.sell_price" v-on:keyup="validateFloat(sku.price,s_index)" v-model="sku.price" v-if="sku.is_deleted == 0" >
                    <input v-else type="text" placeholder="该项已删除" disabled="disabled" >
                </td>
                <td>
                    <input type="text" class="layui-input" :placeholder="set_info.erp_code+','+sku.pv_erp_code" v-model="sku.erp_code"  v-if="sku.is_deleted == 0" >
                    <input v-else type="text" placeholder="该项已删除" disabled="disabled" >
                </td>
                <td v-if="id">{{ sku.stock }}</td>
                <td>
                    <input type="text" class="layui-input" v-model="sku.remark" v-if="sku.is_deleted == 0" >
                    <input v-else type="text" placeholder="该项已删除" disabled="disabled" >
                </td>
                <td>
                    <span v-if="sku.sales_status == 0">
                        <a href="javascript:void(0);" class="layui-btn layui-btn-danger layui-btn-sm" v-on:click="sku.is_deleted = 1" v-if="sku.is_deleted == 0" >删除</a>
                        <a href="javascript:void(0);" class="layui-btn layui-btn-danger layui-btn-sm" v-on:click="sku.is_deleted = 0" v-else >添加</a>
                    </span>
                    <span v-if="set_sku.control_sales && sku.is_deleted == 0" >
                        <a href="javascript:void(0);" class="layui-btn layui-btn-normal layui-btn-sm" v-on:click="sku.sales_status = 1" v-if="sku.sales_status == 0" >上架</a>
                        <a href="javascript:void(0);" class="layui-btn layui-btn-normal layui-btn-sm" v-on:click="sku.sales_status = 0" v-else >下架</a>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

            </div>
        </div>
    </form>
</div>
<script type="text/javascript" charset="utf-8" src="/static/js/vue.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/js/goods/sales_update.js"></script>
<script>
    const photoSpaceUrl = 'http://photo.25boy.com/?token='+photo_space_token+'&url='+photo_handle_url+'&showconfirm=1';
</script>

</body>
</html>