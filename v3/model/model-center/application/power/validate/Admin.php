<?php
namespace app\power\validate;

class Admin extends Base
{
    public function add($data){
        $rule = [
            'realname'      =>  'require|max:20',
            'loginname'     =>  'require|max:20',
            'password'      =>  'require|max:20',
            'repassword'    =>  'require|confirm:password',
            'code'          =>  'max:30',
            'status'        =>  'require|integer|in:1,2',
            'phone'         =>  'length:11',
            'merchant_id'   =>  'integer',
            //'shop_ids'      =>  'array',
            'role_id'       =>  'integer',
        ];
        $message = [
            'realname'      =>  '请输入管理员的真实姓名',
            'loginname'     =>  '请输入管理员的登录账号',
            'password'      =>  '请输入管理员的密码',
            'repassword'    =>  '两次输入的密码不一致',
            'code'          =>  '员工编号不能大于30个字符',
            'status'        =>  '状态错误',
            'phone'         =>  '手机号长度应该是11位',
            'merchant_id'   =>  '请选择商户主体',
            //'shop_ids'      =>  '店铺数据错误',
            'role_id'       =>  '选择角色组'
        ];
        return $this->validate($rule, $data, $message);
    }
    
    public function edit($data){
        $rule = [
            'id'            =>  'require|integer|gt:0',
            'password'      =>  'max:20',
            'repassword'    =>  'confirm:password',
            'status'        =>  'require|integer|in:1,2',
            'phone'         =>  'length:11',
            'merchant_id'   =>  'requireIf:account_type,3|requireIf:account_type,4|integer',
            //'shop_ids'      =>  'array',
            'role_id'       =>  'integer'
        ];
        $message = [
            'id'            =>  '参数错误',
            'password'      =>  '请输入管理员的密码',
            'repassword'    =>  '两次输入的密码不一致',
            'status'        =>  '状态错误',
            'phone'         =>  '手机号长度应该是11位',
            'merchant_id'   =>  '请选择商户主体',
            //'shop_ids'      =>  '店铺数据错误',
            'role_id'       =>  '选择角色组'
        ];
        return $this->validate($rule, $data, $message);
    }

    public function getAdminList($data){
        $rule = [
            'realname'      =>  'max:20',
            'status'        =>  'in:1,2',
            'merchant_id'   =>  'integer'
        ];
        $message = [
            'realname'      =>  '真实姓名过长',
            'status'        =>  '参数错误',
            'merchant_id'   =>  '参数错误'
        ];
        return $this->validate($rule, $data, $message);
    }
    
    public function getAdminEdit($data){
        $rule = ['id'=>'require|integer|gt:0'];
        $message = ['id'=>'参数错误'];
        return $this->validate($rule, $data, $message);
    }
    
    public function login($data){
        $rule = [
            'loginname'  =>  'require|max:20',
            'password'   =>  'require|max:20'
        ];
        $message = [
            'loginname'  =>  '请输入用户名',
            'password'   =>  '请输入密码'
        ];
        return $this->validate($rule, $data, $message);
    }
}
