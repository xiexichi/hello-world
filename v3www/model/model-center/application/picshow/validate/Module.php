<?php
namespace app\picshow\validate;

use app\common\validate\CommonValidate;

class Module extends CommonValidate
{
	/**
	 * [add 添加]
	 * @param [type] $data [验证数据]
	 */
	public function add($data){

		$rule = [
			'module_name' => 'require',
			'module_code' => 'require|alphaDash|unique:ad_module',
			'desc' => 'min:1',
		];

		$msg = [
			'module_name' => '内容模块名称不能为空',
			'module_code.alphaDash' => '内容模块代码不能有特殊字符',
			'module_code.unique' => '内容模块代码不是唯一，请重新填写',
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
			'module_name' => 'require',
			'module_code' => 'require|alphaDash|unique:ad_module',
			'desc' => 'min:1',
		];

		$msg = [
			'id' => '参数错误，请返回刷新页面重试',
			'module_name' => '内容模块名称不能为空',
			'module_code.alphaDash' => '内容模块代码不能有特殊字符',
			'module_code.unique' => '内容模块代码不是唯一，请重新填写',
		];

		// 返回验证结果
		return $this->validate($rule, $data, $msg);
	}
}