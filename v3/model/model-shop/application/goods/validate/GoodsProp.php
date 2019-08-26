<?php

/**
 *  商品属性
 */

namespace app\goods\validate;

class GoodsProp extends Base
{

    public function getPropAll($data){
        $rule = [
            'page' => 'integer',
            'limit' => 'integer'
        ];
        // 返回验证结果
        return $this->validate($rule, $data);
    }

    /**
     * [getPropInfo 获取单个属性信息
     * @param  [type] $data [验证数据]
     * @return [type]       [description]
     */
    public function getPropInfo($data){
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
            'prop_name' => 'require',
            'sort'      => 'integer|>=:0|<=:255',
            'show_type' => 'integer',
            'is_changed' => 'integer',
            'prop_desc' => 'max:255'
        ];
        // 错误提示信息
        $message = [
            'prop_name' => '属性类型名不能为空',
            'sort' => '排序数值范围0-255整数',
            'prop_desc' => '描述最多255个字（含标点符号）'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function edit($data){
        $rule = [
            'id'        => 'require|integer|>:0',
            'prop_name' => 'require',
            'sort'      => 'integer|>=:0|<=:255',
            'show_type' => 'integer',
            'is_changed' => 'integer',
            'prop_desc' => 'chsDash'
        ];
        // 错误提示信息
        $message = [
            'prop_name' => '属性类型名不能为空',
            'sort' => '排序数值范围0-255整数',
            'prop_desc' => '描述最多255个字（含标点符号）'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

}
