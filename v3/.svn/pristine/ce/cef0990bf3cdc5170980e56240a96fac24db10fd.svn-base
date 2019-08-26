<?php

/**
 *  商品销售参数规格
 */

namespace app\goods\validate;

class AttributeCate extends Base
{

    /**
     * [getGoodsTagSingle 获取单个信息]
     * @param  [type] $data [验证数据]
     * @return [type]       [description]
     */
    public function getAttrCateList($data){
        $rule = [
            'page' => 'integer',
            'limit' => 'integer'
        ];
        // 返回验证结果
        return $this->validate($rule, $data);
    }

    /**
     * [getGoodsTagSingle 获取单个信息]
     * @param  [type] $data [验证数据]
     * @return [type]       [description]
     */
    public function getGoodsAttrCateInfo($data){
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
            'attr_name' => 'require'
        ];
        // 错误提示信息
        $message = [
            'attr_name.require' => '参数名不能为空'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function edit($data){
        $rule = [
            'id' => 'require|integer|>:0',
            'attr_name' => 'require'
        ];
        // 错误提示信息
        $message = [
            'id' => '参数错误',
            'attr_name' => '参数名不能为空'

        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function deleted($data){
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

}
