<?php

namespace app\system\validate;

use app\common\validate\CommonValidate;

class System extends CommonValidate
{

	/**
	 * [add 添加]
	 * @param [type] $data [验证数据]
	 */
	public function add($data){

		$rule = [
			'title' => 'require',
			'type' => 'require|alpha',
			'name' => 'require|regex:/^[A-Z][A-Z0-9_]+$/|unique:system_set',
			'note' => 'chsDash',
		];

		// 错误提示信息
		$message = [
			'name.regex' => '字段名需符合数据库字段规则，并全大写',
			'name.unique' => '字段名不是唯一，请换一个',
			'note.chsDash' => '扩展内容只能是汉字、字母、数字和下划线_及破折号-'
		];

		// 返回验证结果
		return $this->validate($rule, $data, $message);
	}

	/**
	 * [edit 修改]
	 * @param [type] $data [验证数据]
	 */
	public function edit($data){

		$rule = [
			'id' => 'require|number',
			'group' => 'number',
		];

		// 返回验证结果
		return $this->validate($rule, $data);
	}

}
