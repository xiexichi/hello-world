<?php

/**
 * 
 */

namespace app\index\validate;

class Area extends Base
{

	/**
	 * [getAreas 获取区域类型]
	 * @param  [type] $data [验证数据]
	 * @return [type]       [description]
	 */
	public function getAreas($data){
		$rule = [
			'type' => 'require|number',
			'pid' => 'number', // 地区上级id
		];

		// 返回验证结果
		return $this->validate($rule, $data);
	}


}