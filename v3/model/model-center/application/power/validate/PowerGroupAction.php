<?php
namespace app\power\validate;

class PowerGroupAction extends Base
{
    public function add($data){
        $rule = [
            'group_id'     =>  'require|integer|gt:0',
            'action_id'    =>  'require|integer|gt:0'
        ];
        
        $message = [
            'group_id'     =>  'group_id错误',
            'action_id'    =>  'action_id错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data, $message);
    }
}
