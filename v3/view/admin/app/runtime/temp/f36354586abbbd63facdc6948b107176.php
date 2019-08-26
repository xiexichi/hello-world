<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:86:"D:\project\v3\view\admin\app\public/../application//goods/view/goods/sales_detail.html";i:1551001129;s:44:"../application/goods/view/goods/set_sku.html";i:1551342920;}*/ ?>
<style>
    .table-box{margin-top:20px;}
    .layui-form-label{width:100px;}
    .star{color:#FB5A5C;}
</style>
<link rel="stylesheet" href="/static/jwt/style/cj.css" media="all">
<form class="layui-form table-box" action="" id="form"  >
    <div class="layui-card" id="upload_main" >
        <div class="layui-card-body">
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">市场价<span class="star">*</span></label>
                <div class="layui-input-inline">
                    <input type="text" name="market_price" v-model="set_sales.market_price" required  lay-verify="required" placeholder="请输入市场价" autocomplete="off" class="layui-input" onblur="validateFloatEmpty(this)" onkeyup="validateFloat(this)" >
                </div>
                <div class="layui-form-mid layui-word-aux">RMB </div>
                <label class="layui-form-label">基础售价<span class="star">*</span></label>
                <div class="layui-input-inline">
                    <input type="text" name="sell_price" v-model="set_sales.sell_price" required  lay-verify="required" placeholder="请输入售价"  autocomplete="off" class="layui-input" v-on:blur="set_sales_price" onkeyup="validateFloat(this)"  >
                </div>
                <div class="layui-form-mid layui-word-aux">RMB （无sku 或 sku没有设置售价时的 售价）</div>
            </div>
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
            <th>id</th>
            <th>图片</th>
            <th>商品名</th>
            <th v-for="(prop,p_index) in show_list.prop_list" v-if="prop.has_checked" style="padding:0 10px;">{{ prop.prop_name }}</th>
            <th>价格</th>
            <th>erp货号<br />(不填将自动生成)</th>
            <th>备注</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
            <tr :class="{'layui-bg-gray':sku.is_deleted,'layui-bg-gray':!sku.sales_status,}" v-for="(sku,s_index) in set_sku.sku_tab" >
                <td>{{sku.item_id}}</td>
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
                <td>{{set_info.goods_name}}</td>
                <td v-for="(name,pv_name_index) in sku.pv_name" colspan="1">{{ name }}</td>
                <td>
                    <input type="text" class="layui-input" :placeholder="'默认'+set_sales.sell_price" onkeyup="validateFloat(this)" v-model="sku.price" v-if="sku.is_deleted == 0" >
                    <input v-else type="text" placeholder="该项已删除" disabled="disabled" >
                </td>
                <td>
                    <input type="text" class="layui-input" :placeholder="set_info.erp_code+','+sku.pv_erp_code" v-model="sku.erp_code"  v-if="sku.is_deleted == 0" >
                    <input v-else type="text" placeholder="该项已删除" disabled="disabled" >
                </td>
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
<script type="text/javascript" charset="utf-8" src="/static/js/vue.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/js/goods/sales_update.js"></script>
<script>
    const url = "<?php echo url('/handlePhoto.html','','',true);?>";
    const token = "<?php echo session('photojwttoken');?>";
    const photoSpaceUrl = 'http://photo.25boy.com/?token='+token+'&url='+url+'&showconfirm=1';
</script>
