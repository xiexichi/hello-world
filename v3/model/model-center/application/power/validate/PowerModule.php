<?php
namespace app\power\validate;

class PowerModule extends Base
{
    public function add($data){
        $rule = [
            'title'         =>  'require|max:20',
            'name'          =>  'require|max:20',
            'is_show'       =>  'require|integer|in:1,2',
            'sort'          =>  'require|integer',
            'icon_code'     =>  'max:20'
        ];
        
        $message = [
            'title'         =>  '模块名称必填',
            'name'          =>  '模块代码必填',
            'is_show'       =>  '选择是否显示',
            'sort'          =>  '排序必填',
            'icon_code'     =>  '图标代码过长'
        ];
        // 返回验证结果
        return $this->validate($rule, $data, $message);
    }
    
    public function edit($data){
        $rule = [
            'id'            =>  'require|integer|gt:0',
            'title'         =>  'require|max:20',
            'name'          =>  'require|max:20',
            'is_show'       =>  'require|integer|in:1,2',
            'sort'          =>  'require|integer',
            'icon_code'     =>  'max:20'
        ];
        
        $message = [
            'id'            =>  'id错误',
            'title'         =>  '模块名称必填',
            'name'          =>  '模块代码必填',
            'is_show'       =>  '选择是否显示',
            'sort'          =>  '排序必填',
            'icon_code'     =>  '图标代码过长'
        ];
        // 返回验证结果
        return $this->validate($rule, $data, $message);
    }
    
    
}
