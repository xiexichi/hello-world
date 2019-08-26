<?php

/**
 *  商品品牌
 */

namespace app\goods\validate;

class GoodsBrands extends Base
{

    public function getBrandsAll($data){
        if( !isset($data['limit']) ){
            $data['limit'] = 10;
        }
        if( !isset($data['page']) ){
            $data['page'] = 1;
        }
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
    public function getBrandInfo($data){
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
            'brand_name'    => 'require|chsDash',
            'brand_letter'  => 'require|alpha',
            'sort'          => 'integer|>=:0|<=:255',
            'brand_logo'    => 'url',
            'brand_img'     => 'url',
            'brand_url'     => 'url',
            'brand_desc'    => 'max:255'
        ];
        // 错误提示信息
        $message = [
            'brand_name.require' => '品牌名不能为空',
            'brand_name.chsDash' => '品牌名只允许汉字、字母、数字和“_”及“-”',
            'brand_letter.require' => '开头字母不能为空',
            'brand_letter.alpha' => '品开头字母只允许字母',
            'sort' => '排序数值范围0-255整数',
            'brand_desc' => '品牌描述最多255个字（含标点符号）',
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function edit($data){
        $rule = [
            'id'            => 'require|integer|>:0',
            'brand_name'    => 'require',
            'brand_letter'  => 'require|alpha',
            'sort'          => 'integer|>=:0|<=:255',
            'brand_logo'    => 'url',
            'brand_img'     => 'url',
            'brand_url'     => 'url',
            'brand_desc'    => 'max:255'
        ];
        // 错误提示信息
        $message = [
            'brand_name.require' => '品牌名不能为空',
            'brand_name.chsDash' => '品牌名只允许汉字、字母、数字和“_”及“-”',
            'brand_letter.require' => '开头字母不能为空',
            'brand_letter.alpha' => '品开头字母只允许字母',
            'sort' => '排序数值范围0-255整数',
            'brand_desc' => '品牌描述最多255个字（含标点符号）',
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
