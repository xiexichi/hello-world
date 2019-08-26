<?php

/**
 * 员工验证器
 */

namespace app\staff\validate;

class Staff extends Base
{

	/**
	 * [add description]
	 * @param [type] $data [description]
	 */
	public function add($data){
		$rule = [
            'shop_id' => 'require|number',				// 店铺id
            'staff_code' => 'require|min:1|max:13',     // 员工代码
            'staff_account' => 'require|min:3|max:12',  // 员工账号（登录账号）
            'phone' => 'require|number',	// 员工手机
            'staff_name' => 'require|min:2|max:12',		// 员工名称
            'password' => 'require|min:6|max:12',		// 密码
            'shop_auth_role_id' => 'require|number',	// 店铺权限id
            'is_disable' => 'number'					// 是否禁用
        ];

        // 返回验证结果
        return $this->validate($rule, $data);
	}


    /**
     * [edit description]
     * @param [type] $data [description]
     */
    public function edit($data){
        $rule = [
            'id' => 'require|number',           // 员工id
            'shop_id' => 'number',              // 店铺id
            'staff_code' => 'min:1|max:13',     // 员工代码
            'staff_account' => 'min:3|max:12',  // 员工账号（登录账号）
            'phone' => 'number',    // 员工手机
            'staff_name' => 'min:2|max:12',     // 员工名称
            'password' => 'min:6|max:12',       // 密码
            'shop_auth_role_id' => 'number',    // 店铺权限id
            'is_disable' => 'number'            // 是否禁用
        ];

        // 返回验证结果
        return $this->validate($rule, $data);
    }

}