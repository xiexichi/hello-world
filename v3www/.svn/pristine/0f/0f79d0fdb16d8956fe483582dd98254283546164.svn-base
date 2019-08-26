<?php

namespace app\order\validate;

class OrderDeliveryPackage extends Base
{
    public function createPackage($data){
        $rule = [
            'order_id' => 'require|integer|>:0',
            'orderGoods' => 'require|array',
            'express_id' => 'require|integer|>:0',
            'express_sn' => 'max:255',
            'desc' => 'max:255',
        ];
        // 错误提示信息
        $message = [
            'order_id' => '参数错误',
            'orderGoods.require' => '缺少物流货物',
            'orderGoods.array' => '参数错误',
            'express_id' => '请选择正确的物流方式',
            'express_sn' => '单号错误',
            'desc' => '参数错误',
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }
}
