<?php
namespace app\power\validate;

class PowerGroup extends Base
{
    public function add($data){
        $rule = [
            'title'         =>  'require',
            'note'          =>  'max:255',
            'is_show'       =>  'require|integer|in:1,2',
            'sort'          =>  'require|integer',
            'module_id'     =>  'require|integer|gt:0',
            'controller_id' =>  'require|integer|gt:0',
            'action_ids'    =>  'array',
        ];
        
        $message = [
            'title'         =>  '名称必填',
            'note'          =>  '备注过长',
            'is_show'       =>  '选择是否显示',
            'sort'          =>  '排序必填',
            'module_id'      =>  '请选择模块',
            'controller_id' =>  '请选择控制器',
            'action_ids'    =>  '必须为数组'
        ];
        // 返回验证结果
        return $this->validate($rule, $data, $message);
    }
    
    public function getEditData($data){
        $rule = ['id' => 'require|integer|gt:0'];
        $message = ['id'=>'id错误'];
        return $this->validate($rule, $data, $message);
    }
}
