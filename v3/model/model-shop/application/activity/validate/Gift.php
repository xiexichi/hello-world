<?php

/**
 *  商品标签
 */

namespace app\activity\validate;

class Gift extends Base
{
    public function index($data){
        $rule = [
            'id' => 'integer',
            'type' => 'integer',
            'status' => 'integer'
        ];
        // 错误提示信息
        $message = [
            'id' => '缺少id',
            'type' => '券类型参数错误',
            'status' => '状态参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function detailGift($data){
        $rule = [
            'gift_id' => 'require|integer',
            'title' => 'require',
            'description' => 'max:255',
            'goods_id' => 'require|integer|>:0',
            'type' => 'require|integer',
            'use_goods_type' => 'require|integer',
            'use_shop_type' => 'require|integer',
            'order_group' =>  'integer',
            'order_o2o' =>  'integer',
            'order_online' =>  'integer',
            'condition' => 'require|float|>:0',
            'start_time' => 'require|date',
            'end_time' => 'require|date',
            'goods_list' => 'array',
            'shop_list' => 'array'
        ];
        // 错误提示信息
        $message = [
            'gift_id' => '缺少id',
            'title' => '标题不能为空',
            'goods_id' => '赠送商品必填',
            'type' => '券类型参数错误',
            'use_goods_type' => '商品类型参数错误',
            'use_shop_type' => '店铺类型参数错误',
            'order_group' =>  '订单类型参数错误',
            'order_o2o' =>  '订单类型参数错误',
            'order_online' =>  '订单类型参数错误',
            'condition' => '条件必填',
            'use_max_qty' => '叠加数必须为整数',
            'start_time.require' => '开始时间必填',
            'start_time.date' => '请填写时间格式',
            'end_time.require' => '结束时间必填',
            'end_time.date' => '请填写时间格式',
            'goods_list' => '商品参数格式错误',
            'shop_list' => '店铺参数格式错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function edit($data){
        $rule = [
            'id' => 'require|integer',
            'status' => 'integer',
            'is_invalid' => 'integer',
            'is_deleted' => 'integer',
            'qty' => 'integer',
        ];
        // 错误提示信息
        $message = [
            'status' => '参数错误',
            'is_invalid' => '参数错误',
            'is_deleted' => '参数错误',
            'qty' => '参数错误',
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function getCoupon($data){
        $rule = [
            'coupon_id' => 'require|integer',
            'user_id' => 'require|integer',
        ];
        // 错误提示信息
        $message = [
            'coupon_id' => '参数错误',
            'user_id' => '参数错误',
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function getGiftList($data){
        $rule = [
            'goods_id' => 'require|integer',
            'shop_id' => 'require|integer',
        ];
        // 错误提示信息
        $message = [
            'goods_id' => '参数错误',
            'shop_id' => '参数错误',
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

}
