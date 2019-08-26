<?php

namespace app\merchant\validate;

class Merchant extends Base
{

	/**
	 * [add 添加]
	 * @param [type] $data [验证数据]
	 */
	public function add($data){

		$rule = [
			'merchant_type_id' => 'require|number',	// 商户类型id
			'account' => 'require|min:3|max:12', // 账号
			'passwd' => 'require|min:6|max:12', // 密码
			'name' => 'require|min:3', // 商户名称
			'desc' => 'min:1', // 商户简介
			'status' => 'number', // 状态
			'province_id' => 'require|number', // 省份id
			'city_id' => 'require|number', // 市id
			'region_id' => 'require|number', // 地区id
			'is_default' => 'number', // 默认主店（总店）
		];

		// 返回验证结果
		return $this->validate($rule, $data);
	}


	/**
	 * [edit 修改]
	 * @param [type] $data [验证数据]
	 */
	public function edit($data){

		$rule = [
			'id' => 'require|number',	// 商户id
			'merchant_type_id' => 'number',	// 商户类型id
			'account' => 'min:3|max:12', // 账号
			'passwd' => 'min:6|max:12', // 密码
			'name' => 'min:3', // 商户名称
			'desc' => 'min:1', // 商户简介
			'status' => 'number', // 状态
			'province_id' => 'number', // 省份id
			'city_id' => 'number', // 市id
			'region_id' => 'number', // 地区id
			'is_default' => 'number', // 默认主店（总店）
		];

		// 返回验证结果
		return $this->validate($rule, $data);
	}

}