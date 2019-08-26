<?php
namespace app\picshow\validate;

use app\common\validate\CommonValidate;

class Qrcode extends CommonValidate
{
	/**
	 * [add 添加]
	 * @param [type] $data [验证数据]
	 */
	public function add($data){

		$rule = [
			'third_app_id' => 'number',
			'url' => 'require',
			'logo' => 'accepted',
			'code_type' => 'min:1',
			'desc' => 'min:1',
			'savetolist' => 'accepted',
		];

		$msg = [
			'url' => '请输入二唯码内容',
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
			'third_app_id' => 'number',
			'url' => 'require',
			'logo' => 'accepted',
			'code_type' => 'min:1',
			'desc' => 'min:1',
		];

		$msg = [
			'id' => '参数错误，请返回刷新页面重试',
			'url' => '请输入二唯码内容',
		];

		// 返回验证结果
		return $this->validate($rule, $data, $msg);
	}
}