<?php

/**
 * 验证器基类
 */

namespace app\depot\validate;

use app\common\validate\CommonValidate;

class Base extends CommonValidate
{


	/**
	 * [cancel 作废]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function cancel($data){
		$rule = [
            'id' => 'require|number',// 店铺库存管理单据id
        ];
        // 返回验证结果
        return $this->validate($rule, $data);
	}


}