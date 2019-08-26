<?php

namespace app\article\validate;

class Categorys extends Base
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


	public function add($data){
        $rule = [
            'desc' =>'require',
            'name' => 'require|chsDash',
            'sort' => 'integer|>=:0|<=:255',
            'parent_id' => 'require|integer|>=:0'
        ];
        $message = [
            'desc.require' => '描述不能为空',
            'name.require' => '分类名不能为空',
            'name.chsDash' => '分类名只允许汉字、字母、数字和“_”及“-”',
            'sort' => '排序数值范围0-255整数',
            'parent_id' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }


    public function edit($data){
        $rule = [
            'id' => 'require|integer|>:0',
            'name' => 'require|chsDash',
            'sort' => 'integer|>=:0|<=:255',
            'desc' => 'require',
            'parent_id' => 'require',
        ];
        $message = [
            'id.require' => '参数错误', 
            'name.require' => '分类名不能为空',
            'name.chsDash' => '分类名只允许汉字、字母、数字和“_”及“-”',
            'sort' => '排序数值范围0-255整数',
            'desc' => '请填写描述',
            'parent_id'=> '参数错误',
        ];

        return $this->validate($rule, $data,$message);
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



    public function cateDel($ids){
        $rule = ['ids' => 'require|integer|>:0'];
        return $this->validate($rule,$ids);
    }

}