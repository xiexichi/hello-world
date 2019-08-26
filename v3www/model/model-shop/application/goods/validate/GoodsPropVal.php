<?php

/**
 *  商品属性值
 */

namespace app\goods\validate;

class GoodsPropVal extends Base
{
    public function getPropValAll($data){
        $rule = [
            'prop_id' => 'require|integer|>:0',
            'page' => 'integer',
            'limit' => 'integer'
        ];
        // 错误提示信息
        $message = [
            'prop_id' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function getPropValInfo($data){
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
            'goods_prop_id' => 'require|integer',
            'pv_name' => 'require|chsDash',
            'pv_type_val' => 'max:500',
            'pv_desc' => 'max:255',
            'pv_erp_code' => 'chsDash'
        ];
        // 错误提示信息
        $message = [
            'goods_prop_id' => '参数错误',
            'pv_name.require' => '属性值名不能为空',
            'pv_name.chsDash' => '属性值名只允许汉字、字母、数字和“_”及“-”',
            'pv_desc' => '描述最多255个字（含标点符号）',
            'pv_erp_code.chsDash' => '属性值名只允许汉字、字母、数字和“_”及“-”'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function edit($data){
        $rule = [
            'id' => 'require|integer|>:0',
            'goods_prop_id' => 'require|integer',
            'pv_name' => 'require|chsDash',
            'pv_type_val' => 'max:500',
            'pv_desc' => 'max:255',
            'pv_erp_code' => 'chsDash'
        ];
        // 错误提示信息
        $message = [
            'id' => '参数错误',
            'goods_prop_id' => '参数错误',
            'pv_name.require' => '属性值名不能为空',
            'pv_name.chsDash' => '属性值名只允许汉字、字母、数字和“_”及“-”',
            'pv_desc' => '描述最多255个字（含标点符号）',
            'pv_erp_code.chsDash' => '属性值名只允许汉字、字母、数字和“_”及“-”'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }
}
