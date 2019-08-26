<?php

namespace app\goods\validate;

class Goods extends Base
{
    public function index($data){
        $rule = [
            'status' => 'integer',
            'delete' => 'integer',
            'cate_id' => 'integer',
            'brand_id' => 'integer',
            'erp_code' => 'chsDash',
            'keyword' => 'chsDash',
            'prop_value_id' => 'integer',
            'attr_id' => 'integer',
            'attr_value' => 'min:0',
            'min_price' => '>=:0',
            'max_price' => '>=:0'
        ];
        // 错误提示信息
        $message = [
            'status' => '参数错误',
            'delete' => '参数错误',
            'cate_id' => '分类信息错误',
            'brand_id' => '品牌信息错误',
            'erp_code' => '货号不存在',
            'keyword' => '关键词只能是汉字、字母、数字和下划线及破折号',
            'prop_value_id' => '销售属性信息错误',
            'attr_id' => '规格信息错误',
            'attr_value' => '规格值信息错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function getGoodsTag($data){
        $rule = [
            'id' => 'require|integer|>:0'
        ];
        // 错误提示信息
        $message = [
            'id' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function bindTag($data){
        $rule = [
            'id' => 'require|integer|>:0',
            'tag' => 'array'
        ];
        // 错误提示信息
        $message = [
            'id' => '参数错误',
            'tag' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function edit($data){
        $rule = [
            'id' => 'require|integer|>:0',
            'is_hot' => 'integer',
            'is_new' => 'integer',
            'verify' => 'integer',
            'sales_status' => 'integer',
            'is_deleted' => 'integer'
        ];
        // 错误提示信息
        $message = [
            'id' => '参数错误',
            'is_hot' => '参数错误',
            'is_new' => '参数错误',
            'verify' => '参数错误',
            'sales_status' => '参数错误',
            'is_deleted' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function getGoodsInfo($data){
        $rule = [
            'id' => 'require|integer|>:0',
            'shop_id' => 'integer|>=:0',
            'sku_filter' => 'array'
        ];
        // 错误提示信息
        $message = [
            'id' => '参数错误',
            'shop_id' => '参数错误',
            'sku_filter' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function getInfo($data){
        $rule = [
            'id' => 'require|integer|>:0',
            'shop_id' => 'integer|>=:0',
            'user_id' => 'integer|>:0',
            'getType' => 'alpha',
        ];
        // 错误提示信息
        $message = [
            'id' => '参数错误',
            'shop_id' => '参数错误',
            'user_id' => '参数错误',
            'getType' => '参数错误',
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function resetItemInfo($data){
        $rule = [
            'item_id' => 'require|integer|>:0',
            'shop_id' => 'integer|>=:0',
            'user_id' => 'integer|>:0',
        ];
        // 错误提示信息
        $message = [
            'id' => '参数错误',
            'shop_id' => '参数错误',
            'user_id' => '参数错误',
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function getItemInfo($data){
        $rule = [
            'id' => 'require|integer|>:0',
        ];
        // 错误提示信息
        $message = [
            'id' => '参数错误',
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function goodsCreateData($data){
        $rule = [
            'goods_name' => 'require',
            'goods_image' => 'require|url',
            'goods_images_list' => 'array',
            'adv_desc' => 'max:255',
            'seo_keyword' => 'max:500',
            'seo_description' => 'max:255',
            'cate_id' => 'require|integer',
            'brand_id' => 'integer',
            'goods_code' => 'chsDash',
            'erp_code' => 'chsDash',
            'market_price' => 'require|float',
            'sell_price' => 'require|float',
            'is_shop_goods' => 'require|integer',
            'is_user_goods' => 'require|integer',
            'is_materials' => 'require|integer',
            'is_commission' => 'require|integer',
            'is_sell_goods' => 'require|integer',
            'ship_free' => 'require|integer',
            'delivery_id' => 'integer',
            'weight' => 'float',
            'attr' => 'array',
            'attr_list' => 'array',
            'sku_param' => 'array'
        ];
        $message = [
            'goods_name.require' => '商品名不能为空',
            'goods_image.require' => '商品主题必填',
            'goods_images_list' => '商品详情图参数错误',
            'adv_desc' => '商品广告最多255个字（含标点符号）',
            'seo_keyword' => 'SEO关键词最多500个字（含标点符号）',
            'seo_description' => 'SEO描述最多255个字（含标点符号）',
            'cate_id' => '商品分类必选',
            'brand_id' => '品牌信息错误',
            'goods_code' => '分类名只允许汉字、字母、数字和“_”及“-”',
            'erp_code' => 'erp货号只允许汉字、字母、数字和“_”及“-”',
            'market_price.require' => '市场价不能为空',
            'sell_price.require' => '基础售价不能为空',
            'is_shop_goods' => '是否店铺商品参数错误',
            'is_user_goods' => '是否会员商品参数错误',
            'is_materials' => '是否物料商品参数错误',
            'is_commission' => '是否分销商品参数错误',
            'is_sell_goods' => '是否可售商品参数错误',
            'ship_free' => '免邮参数错误',
            'attr.array' => '规格参数错误',
            'attr_list.array' => '规格参数错误',
            'sku_param.array' => 'sku参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function goodsUpdateData($data){
        $rule = [
            'id' => 'require|integer|>:0',
            'goods_name' => 'require',
            'goods_image' => 'require|url',
            'goods_images_list' => 'array',
            'adv_desc' => 'max:255',
            'seo_keyword' => 'max:500',
            'seo_description' => 'max:255',
            'cate_id' => 'require|integer',
            'brand_id' => 'integer',
            'goods_code' => 'chsDash',
            'erp_code' => 'chsDash',
            'market_price' => 'require|float',
            'sell_price' => 'require|float',
            'is_shop_goods' => 'require|integer',
            'is_user_goods' => 'require|integer',
            'is_materials' => 'require|integer',
            'is_commission' => 'require|integer',
            'is_sell_goods' => 'require|integer',
            'ship_free' => 'require|integer',
            'delivery_id' => 'integer',
            'weight' => 'float',
            'attr' => 'array',
            'attr_list' => 'array',
            'sku_param' => 'array'
        ];
        $message = [
            'goods_name.require' => '商品名不能为空',
            'goods_image.require' => '商品主题必填',
            'goods_images_list' => '商品详情图参数错误',
            'adv_desc' => '商品广告最多255个字（含标点符号）',
            'seo_keyword' => 'SEO关键词最多500个字（含标点符号）',
            'seo_description' => 'SEO描述最多255个字（含标点符号）',
            'cate_id' => '商品分类必选',
            'brand_id' => '品牌信息错误',
            'goods_code' => '分类名只允许汉字、字母、数字和“_”及“-”',
            'erp_code' => 'erp货号只允许汉字、字母、数字和“_”及“-”',
            'market_price.require' => '市场价不能为空',
            'sell_price.require' => '基础售价不能为空',
            'is_shop_goods' => '是否店铺商品参数错误',
            'is_user_goods' => '是否会员商品参数错误',
            'is_materials' => '是否物料商品参数错误',
            'is_commission' => '是否分销商品参数错误',
            'is_sell_goods' => '是否可售商品参数错误',
            'ship_free' => '免邮参数错误',
            'attr.array' => '规格参数错误',
            'attr_list.array' => '规格参数错误',
            'sku_param.array' => 'sku参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function saveSku($data){
        $rule = [
            'id' => 'require|integer|>:0',
            'erp_code' => 'chsDash',
            'market_price' => 'require|float',
            'sell_price' => 'require|float',
            'weight' => 'float',
            'ship_free' => 'integer|>=:0'
        ];
        // 错误提示信息
        $message = [
            'id' => '参数错误',
            'erp_code' => 'erp货号只允许汉字、字母、数字和“_”及“-”',
            'market_price.require' => '市场价不能为空',
            'sell_price.require' => '基础售价不能为空',
            'sku_param.array' => 'sku参数错误',
            'weight' => '商品重量数值错误',
            'ship_free' => '包邮参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function getGoodsShipPrice($data){
        $rule = [
            'item_id' => 'require|integer|>:0',
            'num' => 'require|integer|>:0',
            'delivery_id' => 'require|integer|>:0',
            'prov_id' => 'require|integer|>:0',
            'shop_id' => 'integer|>:0',
        ];
        // 错误提示信息
        $message = [
            'shop_id' => '商家参数错误',
            'item_id' => '缺少商品',
            'num.require' => '缺少商品数量',
            'num.integer' => '商品数量必须大于0',
            'delivery_id' => '缺少物流方式',
            'prov_id' => '缺少收货地区'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

}
