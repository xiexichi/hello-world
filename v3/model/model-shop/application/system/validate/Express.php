<?php

namespace app\system\validate;

use app\common\validate\CommonValidate;

class Express extends CommonValidate
{

	/**
	 * [add 添加]
	 * @param [type] $data [验证数据]
	 */
	public function add($data)
	{
		$rule = [
			'name' => 'require',
			'code' => 'require|alpha',
			'third_code' => 'alpha',
			'desc' => 'min:1',
			'status' => 'number',
		];

		// 错误提示信息
		$message = [
			'code.alpha' => '快递代码只允许是英文字母',
			'third_code.alpha' => '第三方快递代码只允许是英文字母',
		];

		// 返回验证结果
		return $this->validate($rule, $data, $message);
	}

	/**
	 * [edit 修改]
	 * @param [type] $data [验证数据]
	 */
	public function edit($data)
	{
		$rule = [
			'id' => 'require|number',
			'name' => 'min:1',
			'code' => 'alpha',
			'third_code' => 'alpha',
			'desc' => 'min:1',
			'status' => 'number',
		];

		// 错误提示信息
		$message = [
			'code.alpha' => '快递代码只允许是英文字母',
			'third_code.alpha' => '第三方快递代码只允许是英文字母',
		];

		// 返回验证结果
		return $this->validate($rule, $data, $message);
	}

}
