<?php

/**
 *  商品标签
 */

namespace app\goods\validate;

class GoodsTag extends Base
{

    public function add($data){
        $rule = [
            'tag_name' => 'require',
            'sort' => 'integer|>=:0|<=:255',
        ];
        // 错误提示信息
        $message = [
            'tag_name' => '标签名不能为空',
            'sort' => '排序数值范围0-255整数'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function edit($data){
        $rule = [
            'id' => 'require|integer|>:0',
            'tag_name' => 'require',
            'sort' => 'integer|>=:0|<=:255',
        ];
        // 错误提示信息
        $message = [
            'tag_name' => '标签名不能为空',
            'sort' => '排序数值范围0-255整数'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function deleted($data){
        $rule = [
            'id' => 'require|integer|>:0',
        ];
        // 错误提示信息
        $message = [
            'id' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }


}
