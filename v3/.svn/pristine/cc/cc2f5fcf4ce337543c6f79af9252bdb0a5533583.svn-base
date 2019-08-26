<?php

/**
 * 店铺权限验证器
 */

namespace app\staff\validate;

class ShopAuthRole extends Base
{

	/**
	 * [add description]
	 * @param [type] $data [description]
	 */
	public function add($data){
		$rule = [
            'shop_id' => 'require|number',				// 店铺id
            'role_name' => 'require|min:1|max:12',		// 员工代码
            'role_level' => 'require|number'	        // 店铺等级
        ];

        // 返回验证结果
        return $this->validate($rule, $data);
	}


}