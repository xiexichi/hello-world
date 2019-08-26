<?php
namespace app\power\validate;

class ShopStaff extends Base
{
    public function getPower($data){
        $rule = [
            'merchant_id'  =>  'require|gt:0'
        ];
        $message = [
            'merchant_id'  =>  '参数错误'
        ];
        return $this->validate($rule, $data, $message);
    }
}
