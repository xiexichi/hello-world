<?php
namespace app\user\validate;

class UserLevel extends Base
{

    public function add($data)
    {
        $rule = [
            'name' => 'require',
            'discount' => 'number|egt:1|elt:10',
            'level_limit' => 'integer',
            'auto_update' => 'require|integer|in:0,1',
            'if_free' => 'require|integer|in:0,1',
            'is_use_coupon' => 'require|integer|in:0,1'
        ];
        
        $message = [
            'name' => '名称必填',
            'discount' => '折扣范围1~10，允许小数',
            'level_limit' => '自动升级界限正整数',
            'auto_update' => '参数错误',
            'if_free' => '参数错误',
            'is_use_coupon' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data, $message);
    }

    public function delete($data)
    {
        $rule = [
            'ids' => 'require|gt:0|integer'
        ];
        $message = [
            'ids' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data, $message);
    }

    public function edit($data)
    {
        $rule = [
            'id'            => 'require|integer|gt:0',
            'name'          => 'require',
            'discount'      => 'number|egt:1|elt:10',
            'level_limit'   => 'integer',
            'auto_update'   => 'require|integer|in:0,1',
            'is_free'       => 'require|integer|in:0,1',
            'is_use_coupon' => 'require|integer|in:0,1'
        ];
        
        $message = [
            'id'            => '参数错误',
            'name'          => '名称必填',
            'discount'      => '折扣范围1~10，允许小数',
            'level_limit'   => '自动升级界限正整数',
            'auto_update'   => '参数错误',
            'is_free'       => '参数错误',
            'is_use_coupon' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data, $message);
    }
}
