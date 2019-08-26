<?php

/**
 *  商品标签
 */

namespace app\activity\validate;

class CouponUser extends Base
{
    public function index($data)
    {
        $rule = [
            'coupon_id' => 'integer|>:0',
            'user_id' => 'integer|>:0',
            'coupon_sn' => 'max:255',
            'use_status' => 'integer|>=:0',
        ];
        $message = [
            'coupon_id' => '参数错误',
            'user_id' => '参数错误',
            'coupon_sn' => '参数错误',
            'use_status' => '参数错误',
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function getUserList($data)
    {
        $rule = [
            'user_id' => 'require|integer|>:0',
        ];
        $message = [
            'user_id' => '参数错误',
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }
}
