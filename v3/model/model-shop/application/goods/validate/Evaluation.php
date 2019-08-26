<?php

namespace app\goods\validate;

class Evaluation extends Base
{
    public function index($data){
        $rule = [
            'order_id' => 'integer|>:0',
            'order_sn' => 'alphaNum',
            'user_id' => 'integer|>:0',
            'has_img' => 'integer',
            'verify' => 'integer',
        ];
        // 错误提示信息
        $message = [
            'order_id' => '参数错误',
            'order_sn' => '单号格式错误',
            'user_id' => '用户id格式错误',
            'has_img' => '参数错误',
            'verify' => '参数错误',
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

}
