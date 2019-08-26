<?php
namespace app\apps\validate;

use app\common\validate\CommonValidate;

class AppThird extends CommonValidate
{
	/**
	 * [add 添加]
	 * @param [type] $data [验证数据]
	 */
	public function add($data){

		$rule = [
			'type' => 'require',
			'name' => 'require',
			'appid' => 'require',
			'secret' => 'min:1',
			'mch_id' => 'min:1',
			'key' => 'min:1',
			'desc' => 'min:1',
			'sign_type' => 'min:1',
			'trade_type' => 'min:1',
			'cert_pem_path' => 'min:1',
			'key_pem_path' => 'min:1',
			'desc' => 'min:1',
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
			'id' => 'require|number',
			'type' => 'require',
			'name' => 'require',
			'appid' => 'require',
			'secret' => 'min:1',
			'mch_id' => 'min:1',
			'key' => 'min:1',
			'desc' => 'min:1',
			'sign_type' => 'min:1',
			'trade_type' => 'min:1',
			'cert_pem_path' => 'min:1',
			'key_pem_path' => 'min:1',
			'desc' => 'min:1',
		];

		$msg = [
			'id' => '参数错误，请刷新重试',
		];

		// 返回验证结果
		return $this->validate($rule, $data, $msg);
	}
}