<?php

/**
 *  商品标签
 */

namespace app\goods\validate;

class Category extends Base
{

    public function getCateAll($data){
        $data['showType'] = isset($data['showType']) ? $data['showType'] : 'list';
        $rule = [
            'showType' => 'alphaDash',
            'pid' => 'integer'
        ];
        // 返回验证结果
        return $this->validate($rule, $data);
    }

    public function getCateInfo($data){
        $rule = [
            'id' => 'require|integer|>:0'
        ];
        // 错误提示信息
        $message = [
            'id' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function add($data){
        $rule = [
            'cate_name' => 'require|chsDash',
            'cate_icon' => 'url',
            'sort' => 'integer|>=:0|<=:255',
            'pid' => 'integer|>=:0'
        ];
        $message = [
            'cate_name.require' => '分类名不能为空',
            'cate_name.chsDash' => '分类名只允许汉字、字母、数字和“_”及“-”',
            'sort' => '排序数值范围0-255整数',
            'pid' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function edit($data){
        $rule = [
            'id' => 'require|integer|>:0',
            'cate_name' => 'chsDash',
            'cate_icon' => 'url',
            'sort' => 'integer|>=:0|<=:255',
            'pid' => 'integer|>=:0',
            'is_deleted' => 'integer'
        ];
        $message = [
            'id' => '参数错误',
            'cate_name.require' => '分类名不能为空',
            'cate_name.chsDash' => '分类名只允许汉字、字母、数字和“_”及“-”',
            'cate_icon' => '图片路径不正确',
            'sort' => '排序数值范围0-255整数',
            'pid' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

}
