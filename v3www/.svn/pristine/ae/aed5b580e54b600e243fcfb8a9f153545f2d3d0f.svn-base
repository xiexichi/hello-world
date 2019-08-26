<?php
namespace app\power\validate;

class PowerRole extends Base
{
    public function add($data){
        $rule = [
            'id'        =>  'integer',
            'pid'       =>  'integer',
            'title'     =>  'require|max:20',
            'note'      =>  'max:255',
            'status'    =>  'require|integer|in:1,2',
            'authids'   =>  'array'
        ];
        // 错误提示信息
        $message = [
            'id'    =>  '参数错误',
            'pid'   =>  '请选择角色身份',
            'title' =>  '请填写角色名称',
            'note'  =>  '备注过长',
            'status'=>  '参数错误',
            'authids'=> '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }
    public function edit($data){
        $rule = [
            'id'        =>  'require|integer',
            'pid'       =>  'integer',
            'title'     =>  'require|max:20',
            'note'      =>  'max:255',
            'status'    =>  'require|integer|in:1,2',
            'authids'   =>  'array'
        ];
        // 错误提示信息
        $message = [
            'id'    =>  '参数错误',
            'pid'   =>  '请选择角色身份',
            'title' =>  '请填写角色名称',
            'note'  =>  '备注过长',
            'status'=>  '参数错误',
            'authids'=> '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }
    public function getEditData($data){
        $rule = ['id' => 'require|integer|gt:0'];
        $message = ['id'=>'id错误'];
        return $this->validate($rule, $data, $message);
    }
}
