<?php
namespace app\power\validate;

class PowerAction extends Base
{
    public function addAll($data){
        $rule = [
            'name'          =>  'require',
            'controller_id' =>  'require|integer|gt:0'
        ];
        
        $message = [
            'name'          =>  '模块代码必填',
            'controller_id' =>  '请选择上级控制器'
        ];
        // 返回验证结果
        return $this->validate($rule, $data, $message);
    }
    
    public function all($data){
        $rule = [
            'type'          =>  'max:20',
            'controller_id' =>  'integer'
        ];
        $message = [
            'type'          =>  '参数类型错误',
            'controller_id' =>  '控制器id必须为正整数'
        ];
        return $this->validate($rule, $data, $message);
    }
    
    public function setDefault($data){
        $rule = [
            'action_id'     =>  'require|gt:0|integer',
            'controller_id' =>  'require|gt:0|integer'
        ];
        $message = [
            'action_id'     =>  '参数错误',
            'controller_id' =>  '参数错误'
        ];
        return $this->validate($rule, $data, $message);
    }
}
