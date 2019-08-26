<?php
namespace app\apps\validate;

use app\common\validate\CommonValidate;

class AppAuth extends CommonValidate
{
	/**
	 * [add 添加]
	 * @param [type] $data [验证数据]
	 */
	public function add($data){

		$rule = [
			'app_name' => 'require',
			'app_id' => 'require|min:8|max:16|unique:app_auth',
			'app_key' => 'require|min:18|max:21',
			'platform_code' => 'require|min:2|unique.app_auth',
			'desc' => 'min:1',
			'status' => 'number',
		];

		$msg = [
			'app_name' => '应用名称不能为空',
			'app_id' => '应用ID要求8-16位',
			'app_id.unique' => '应用ID不是唯一，请重新填写',
			'app_key' => '应用密钥要求18-21位',
			'platform_code' => '平台代码至少2位',
			'platform_code.unique' => '平台代码不是唯一，请重新填写',
		];

		// 返回验证结果
		return $this->validate($rule, $data, $msg);
	}

	/**
	 * [edit 修改]
	 * @param [type] $data [验证数据]
	 */
	public function edit($data){

		$rule = [
			'id' => 'require|number',
			'app_name' => 'require',
			'app_id' => 'require|min:8|max:16|unique:app_auth,app_id,'.$data['id'],
			'app_key' => 'require|min:18|max:21',
			'platform_code' => 'require|min:2|unique:app_auth',
			'desc' => 'min:1',
			'status' => 'number',
		];

		$msg = [
			'id' => '参数错误，请刷新重试',
			'app_name' => '应用名称不能为空',
			'app_id' => '应用ID要求8-16位',
			'app_id.unique' => '应用ID不是唯一，请重新填写',
			'app_key' => '应用密钥要求18-21位',
			'platform_code' => '平台代码至少2位',
			'platform_code.unique' => '平台代码不是唯一，请重新填写',
		];

		// 返回验证结果
		return $this->validate($rule, $data, $msg);
	}
}