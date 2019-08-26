<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:80:"D:\project\v3\view\admin\app\public/../application//goods/view/goods/detail.html";i:1548663039;s:45:"../application/goods/view/goods/set_info.html";i:1548577845;s:46:"../application/goods/view/goods/set_sales.html";i:1551001135;s:44:"../application/goods/view/goods/set_sku.html";i:1548752869;s:48:"../application/goods/view/goods/set_content.html";i:1548308393;s:45:"../application/goods/view/goods/set_ship.html";i:1550126423;}*/ ?>
<style>
    .table-box{margin-top:20px;}
    .layui-form-label{width:100px;}
    .star{color:#FB5A5C;}
</style>
<link rel="stylesheet" href="/static/jwt/style/cj.css" media="all">
<script src="/static/ueditor/ueditor.config.js"></script>
<script src="/static/ueditor/ueditor.all.js"></script>
<script src="/static/ueditor/lang/zh-cn/zh-cn.js"></script>
<form class="layui-form table-box" action="" id="form">
    <div class="layui-card">
        <div class="layui-card-body" id="upload_main" >
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
                </div>
            </div>
            <div class="layui-tab layui-tab-card">
                <ul class="layui-tab-title">
                    <li class="layui-this">基础信息</li>
                    <li>销售信息</li>
                    <li>sku管理</li>
                    <li>详情内容</li>
                    <li>运费信息</li>
                </ul>
                <div class="layui-tab-content" >
                    <div class="layui-tab-item layui-show">
                        <div class="layui-form-item">
    <label class="layui-form-label">商品名<span class="star">*</span></label>
    <div class="layui-input-inline">
        <input type="text" name="goods_name" required  lay-verify="required" placeholder="请输入标题" v-model="set_info.goods_name" autocomplete="off" class="layui-input" style="width:300px;" >
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">商品图片<span class="star">*</span></label>
    <div class="layui-input-inline">
        <div class="upload_box" style="width:180px; height:180px;">
            <input type="hidden" id="goods_image_input" name="goods_image" v-model="set_info.goods_image" class="hid-val-box" readonly />
            <div class="upload-title">主图<span class="star">*</span></div>
            <!--<div class="upload-tips"><span>宽</span> <span>*</span><span>高</span></div>-->
            <div class="upload-view" id="goods_image" style="height:150px;" v-on:click="uploadImg('goods_image')" >
                <img alt="" :src="set_info.goods_image" onerror="this.src='/static/jwt/images/upload_add.png'" >
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="layui-input-inline" v-for="(img,img_index) in set_info.goods_image_list" v-if="img_index < 4">
        <div class="upload_box" style="width:180px; height:180px;">
            <input type="hidden" :id="'goods_img'+img_index+'_input'" :name="'goods_images_list['+img_index+']'" class="hid-val-box" readonly v-model="img.img" />
            <div class="upload-title">细节图</div>
            <div class="upload-view" :id="'goods_img'+img_index" style="height:150px;" v-on:click="uploadImg(img_index)" >
                <img alt="" :src="img.img" onerror="this.src='/static/jwt/images/upload_add.png'" >
            </div>
        </div>
        <div class="upload-del-box" v-if="img_index == 0 || img_index+1 < set_info.goods_image_list.length"><a href="javascript:void(0);" v-on:click="uploadDel('image_list',img_index)">删除</a></div>
        <div class="clear"></div>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">广告语</label>
    <div class="layui-input-inline">
        <textarea name="adv_desc" v-model="set_info.goods_desc" style="resize:none; width:300px;"  placeholder="请输入商品广告语" class="layui-textarea"></textarea>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">SEO关键词</label>
    <div class="layui-input-inline">
        <input type="text" name="seo_keyword" v-model="set_info.seo_keyword" placeholder="SEO关键词" autocomplete="off" class="layui-input" style="width:300px;" >
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">SEO描述</label>
    <div class="layui-input-inline">
        <textarea name="seo_description" v-model="set_info.seo_desc" style="resize:none; width:300px;"  placeholder="请输入商品SEO描述" class="layui-textarea"></textarea>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">商品品牌</label>
    <div class="layui-input-inline">
        <select name="brand_id" lay-filter="brands" lay-search v-model="set_info.brand_id" >
            <option value="" >请选择分类</option>
            <option :value="brand.id" v-for="(brand,index) in show_list.brand_list">{{brand.brand_name}}</option>
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">商品分类<span class="star">*</span></label>
    <div class="layui-input-inline">
        <select name="cate_id" lay-filter="cates" required lay-verify="required" lay-search v-model="set_info.cate_id" >
            <option value="" >请选择分类</option>
            <option :value="cate.id" v-for="(cate,index) in show_list.cate_list">{{cate.cate_name}}</option>
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">商品条码号</label>
    <div class="layui-input-inline">
        <input type="text" name="goods_code" placeholder="输入条码号" v-model="set_info.goods_code" autocomplete="off" class="layui-input" style="width:300px;" >
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">erp货号<span class="star">*</span></label>
    <div class="layui-input-inline">
        <input type="text" name="erp_code" v-model="set_info.erp_code" required  lay-verify="required" placeholder="erp商品编码" autocomplete="off" class="layui-input" style="width:300px;" >
    </div>
</div>

                    </div>
                    <div class="layui-tab-item">
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

                    </div>
                    <div class="layui-tab-item">
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
                    <div class="layui-tab-item">
                        <div class="layui-form-item">
    <label class="layui-form-label"></label>
    <div class="layui-input-inline" >
        <select lay-filter="attrGroup"  lay-search>
            <option value="0" >使用参数组</option>
            <option :value="group.id" v-for="(group,index) in show_list.attr_group_list" >{{group.group_name}}</option>
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">商品参数项</label>
    <div class="layui-input-block" >
        <input type="checkbox" class="attr_list" :value="index" name="attr[]" v-for="(attr,index) in set_content.attr_list" lay-filter="attr" :checked="attr.checked" :title="attr.attr_name">
    </div>
</div>
<div class="layui-form-item">
    <div style="float:left; overflow:hidden; margin:5px 0;" v-for="(attr,index) in set_content.attr_list" v-if="attr.checked">
        <label class="layui-form-label" >{{attr.attr_name}}</label>
        <div class="layui-input-inline">
            <input type="text" :name="'attr_list['+attr.id+']'" required  lay-verify="required" v-model="attr.content" autocomplete="off" class="layui-input" >
        </div>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">商品详情</label>
    <div class="layui-input-block">
        <div id="goods_content" style="width:75%; height:300px; margin-left:20px;"></div>
    </div>
</div>


                    </div>
                    <div class="layui-tab-item">
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

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" charset="utf-8" src="/static/js/vue.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/js/goods/goods_update.js"></script>
<script>
    const url = "<?php echo url('/handlePhoto.html','','',true);?>";
    const token = "<?php echo session('photojwttoken');?>";
    const photoSpaceUrl = 'http://photo.25boy.com/?token='+token+'&url='+url+'&showconfirm=1';
</script>
