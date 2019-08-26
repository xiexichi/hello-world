<?php
namespace app\power\validate;

class PowerController extends Base
{
    public function add($data){
        $rule = [
            'title'         =>  'require|max:20',
            'name'          =>  'require|max:20',
            'is_show'       =>  'require|integer|in:1,2',
            'module_id'     =>  'require|integer|gt:0',
            'sort'          =>  'require|integer'
        ];
        
        $message = [
            'title'         =>  '模块名称必填',
            'name'          =>  '模块代码必填',
            'is_show'       =>  '选择是否显示',
            'module_id'     =>  '选择上级模块',
            'sort'          =>  '排序必填'
        ];
        // 返回验证结果
        return $this->validate($rule, $data, $message);
    }
    
    public function all($data){
        $rule = [
            'type'      =>  'max:20',
            'module_id' =>  'integer'
        ];
        $message = [
            'type'         =>  '参数类型错误',
            'module_id'    =>  '模块id必须为正整数'
        ];
        return $this->validate($rule, $data, $message);
    }
    
}
