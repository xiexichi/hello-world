<?php
namespace app\picshow\validate;

use app\common\validate\CommonValidate;

class Index extends CommonValidate
{
	/**
	 * [add 添加]
	 * @param [type] $data [验证数据]
	 */
	public function add($data){

		$rule = [
			'title' => 'require',
			'position_id' => 'require|number',
			'module_id' => 'require|number',
			'type' => 'min:1|alphaDash',
			'imgurl' => 'require|url',
			'parameter' => 'min:1',
			'start_time' => 'dateFormat:Y-m-d H:i:s',
			'end_time' => 'dateFormat:Y-m-d H:i:s',
			'sort' => 'number',
			'status' => 'number',
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
			'title' => 'require',
			'position_id' => 'require|number',
			'module_id' => 'require|number',
			'type' => 'min:1|alphaDash',
			'imgurl' => 'require|url',
			'parameter' => 'min:1',
			'start_time' => 'dateFormat:Y-m-d H:i:s',
			'end_time' => 'dateFormat:Y-m-d H:i:s',
			'sort' => 'number',
			'status' => 'number',
			'desc' => 'min:1',
		];

		// 返回验证结果
		return $this->validate($rule, $data);
	}
}