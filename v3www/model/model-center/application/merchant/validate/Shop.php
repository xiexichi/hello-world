<?php

namespace app\merchant\validate;

class Shop extends Base
{

	/**
	 * [add 添加]
	 * @param [type] $data [验证数据]
	 */
	public function add($data){

		$rule = [
			'merchant_id' => 'require|number',	// 所属商户id
			'account' => 'require|min:3|max:12', // 店铺账号
			'passwd' => 'require|min:6|max:12', // 密码
			'name' => 'require|min:3', // 店铺名称
			'desc' => 'min:1', // 店铺简介
			'status' => 'number', // 状态
			'province_id' => 'require|number', // 省份id
			'city_id' => 'require|number', // 市id
			'region_id' => 'require|number', // 地区id
			'address' => 'require|min:1', // 详细地址
			'phone' => 'require', // 电话
			'shop_type_id' => 'require|number', // 店铺类型id
			'shop_sale_type_id' => 'require|number', // 店铺销售类型id
			'sell_proxy_auth' => 'number', // 销售代发权限: 0=不开启，1=开启（暂时不用）
		];

		// 返回验证结果
		return $this->validate($rule, $data);
	}

	/**
	 * [verifyAccount 验证账号]
	 * @return [type] [description]
	 */
	public function verifyAccount($data){
		$rule = [
			'username' => 'require|min:3', // 店铺账号
			'password' => 'require|min:3', // 店铺密码
		];

		// 返回验证结果
		return $this->validate($rule, $data);
	}


}