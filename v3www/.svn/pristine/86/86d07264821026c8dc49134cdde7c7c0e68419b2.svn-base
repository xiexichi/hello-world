<?php

namespace app\system\validate;

use app\common\validate\CommonValidate;

class DeliveryArea extends CommonValidate
{

	/**
	 * [add 添加]
	 * @param [type] $data [验证数据]
	 */
	public function add($data)
	{
		$rule = [
			'area_name' => 'require',
			'delivery_id' => 'require|number',
			'base_fee' => 'number',
			'base_num' => 'number',
			'base_num_fee' => 'number',
			'step_fee' => 'number',
			'step_num' => 'number',
			'step_num_fee' => 'number',
			'fee_mode' => 'require|number',
			'free_money' => 'number',
			'regions' => 'min:1'
		];

		// 返回验证结果
		return $this->validate($rule, $data);
	}

	/**
	 * [edit 修改]
	 * @param [type] $data [验证数据]
	 */
	public function edit($data)
	{
		$rule = [
			'id' => 'require|number',
			'area_name' => 'require',
			'delivery_id' => 'require|number',
			'base_fee' => 'number',
			'base_num' => 'number',
			'base_num_fee' => 'number',
			'step_fee' => 'number',
			'step_num' => 'number',
			'step_num_fee' => 'number',
			'fee_mode' => 'require|number',
			'free_money' => 'number',
			'regions' => 'min:1'
		];

		// 返回验证结果
		return $this->validate($rule, $data);
	}

}
