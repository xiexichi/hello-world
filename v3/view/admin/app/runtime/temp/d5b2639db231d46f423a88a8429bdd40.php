<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:84:"D:\project\v3\view\admin\app\public/../application//goods/view/goods/sku_detail.html";i:1548647658;s:46:"../application/goods/view/goods/set_sales.html";i:1548209908;s:44:"../application/goods/view/goods/set_sku.html";i:1548393216;}*/ ?>
<link rel="stylesheet" href="/static/jwt/style/cj.css" media="all">
<script src="/static/ueditor/ueditor.config.js"></script>
<script src="/static/ueditor/ueditor.all.js"></script>
<script src="/static/ueditor/lang/zh-cn/zh-cn.js"></script>
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-form-item">
    <label class="layui-form-label">市场价<span class="star">*</span></label>
    <div class="layui-input-inline">
        <input type="text" name="market_price" v-model="set_sales.market_price" required  lay-verify="required" placeholder="请输入市场价" autocomplete="off" class="layui-input" onblur="validateFloatEmpty(this)" onkeyup="validateFloat(this)" >
    </div>
    <div class="layui-form-mid layui-word-aux">RMB </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">基础售价<span class="star">*</span></label>
    <div class="layui-input-inline">
        <input type="text" name="sell_price" v-model="set_sales.sell_price" required  lay-verify="required" placeholder="请输入售价"  autocomplete="off" class="layui-input" onblur="validateFloatEmpty(this)" onkeyup="validateFloat(this)"  >
    </div>
    <div class="layui-form-mid layui-word-aux">RMB （无sku 或 sku没有设置售价时的 售价）</div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">是否店铺商品</label>
    <div class="layui-input-inline">
        <input type="radio" name="is_shop_goods" v-model="set_sales.is_shop_goods" value="1" lay-filter="is_shop_goods" title="是">
        <input type="radio" lay-filter="is_shop_goods" name="is_shop_goods" v-model="set_sales.is_shop_goods" value="0" title="否">
    </div>
    <label class="layui-form-label">是否会员商品</label>
    <div class="layui-input-inline">
        <input type="radio" name="is_user_goods" lay-filter="is_user_goods" v-model="set_sales.is_user_goods" value="1" title="是" >
        <input type="radio" name="is_user_goods" lay-filter="is_user_goods" v-model="set_sales.is_user_goods" value="0" title="否" >
    </div>
    <label class="layui-form-label">是否物料</label>
    <div class="layui-input-inline">
        <input type="radio" name="is_materials" lay-filter="is_materials" v-model="set_sales.is_materials" value="1" title="是" >
        <input type="radio" name="is_materials" lay-filter="is_materials" v-model="set_sales.is_materials" value="0" title="否" >
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">是否分销</label>
    <div class="layui-input-inline">
        <input type="radio" name="is_commission" lay-filter="is_commission" v-model="set_sales.is_commission" value="1" title="是" >
        <input type="radio" name="is_commission" lay-filter="is_commission" v-model="set_sales.is_commission" value="0" title="否" >
    </div>
    <label class="layui-form-label">是否可销售</label>
    <div class="layui-input-inline">
        <input type="radio" name="is_sell_goods" lay-filter="is_sell_goods"  v-model="set_sales.is_sell_goods" value="1" title="是" >
        <input type="radio" name="is_sell_goods" lay-filter="is_sell_goods"  v-model="set_sales.is_sell_goods" value="0" title="否" >
    </div>
</div>

        <div class="layui-form-item">
    <label class="layui-form-label">属性选择</label>
    <div class="layui-input-block" id="prop_box">
        <input type="checkbox" class="prop_list" :value="index" name="prop[]" v-for="(prop,index) in show_list.prop_list" lay-filter="prop" :checked="prop.checked" :title="prop.prop_name">
    </div>
</div>
<div id="prop_val_box" >
    <div class="layui-form-item" v-for="(prop,p_index) in show_list.prop_list" v-if="prop.checked">
        <label class="layui-form-label">{{prop.prop_name}}</label>
        <div class="layui-input-block">
            <input type="checkbox" class="prop_val" lay-filter="prop_val" name="prop_val[]" v-for="(prop_val,pv_index) in prop.val_list" :value="pv_index" :data-prop-index="p_index" v-model="prop_val.checked" :title="prop_val.pv_name" lay-skin="primary">
        </div>
    </div>
</div>
<div id="sku_table" v-if="set_sku.sku_tab.length > 0" >
    <table class="layui-table">
        <colgroup>
            <col width="110px">
            <col>
            <col v-for="(prop,p_index) in show_list.prop_list" v-if="prop.checked" >
            <col width="150px">
            <col width="150px">
            <col width="150px">
            <col width="80px">
        </colgroup>
        <thead>
        <tr>
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
            <tr :class="{'layui-bg-cyan':sku.is_deleted}" v-for="(sku,s_index) in set_sku.sku_tab" >
                <td>
                    <div v-if="sku.is_deleted == 0" >
                        <div class="upload_box" style="width:100px; height:100px;" >
                            <input type="hidden" name="sku_image[s_index]" v-model="sku.item_image" class="hid-val-box" readonly />
                            <div class="upload-view" id="goods_image" style="height:88px;" v-on:click="uploadImg('sku',s_index)" >
                                <img alt="" :src="sku.item_image" onerror="this.src='/static/jwt/images/upload_add.png'" >
                            </div>
                        </div>
                        <div class="upload-del-box" ><a href="javascript:void(0);" v-on:click="uploadDel('sku',s_index)">删除</a></div>
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
                        <i class="layui-icon layui-icon-delete"  v-on:click="sku.is_deleted = 1" v-if="sku.is_deleted == 0 " ></i>
                        <i class="layui-icon layui-icon-add-1"  v-on:click="sku.is_deleted = 0" v-else ></i>
                </td>
            </tr>
        </tbody>
    </table>
</div>

    </div>
</div>
<script type="text/javascript" charset="utf-8" src="/static/js/vue.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/js/goods/edit_update.js"></script>
<script>
    const url = "<?php echo url('/handlePhoto.html','','',true);?>";
    const token = "<?php echo session('photojwttoken');?>";
    const photoSpaceUrl = 'http://photo.25boy.com/?token='+token+'&url='+url+'&showconfirm=1';
</script>
