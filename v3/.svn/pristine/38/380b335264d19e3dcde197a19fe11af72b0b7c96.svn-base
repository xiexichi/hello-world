<?php
namespace app\user\validate;

class UserIntegral extends Base
{
    public function getList($data){
        $rule = [
            'user_id' => 'integer',
            'user_name' => 'max:50',
            'integral_type_id' => 'integer'
        ];
        
        $message = [
            'user_id' => '用户id为正整数',
            'user_name' => '用户名少于50字',
            'integral_type_id' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data, $message);
    }
}