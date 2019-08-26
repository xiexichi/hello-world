<?php

/**
 * 店铺调货单（调拨单）
 */

namespace app\depot\validate;

class ShopTransferOrder extends Base
{

	public function add($data){

		// 如果有操作员工id
		if (input('ctrl_staff_id')) {
			// 设置员工id
			$data['staff_id'] = input('ctrl_staff_id');
		}

		$rule = [
			'shop_depot_pre_select_id' => 'require|number', // 店铺预选id:shop_depot_select_id
            'shop_id' => 'require|number', // 调出店铺预id
            'in_shop_id' => 'require|number', // 调入店铺id
            'shop_remark' => 'min:0', // 调出店铺备注
            'staff_id' 	  => 'number',	  // 员工id
        ];
        
        // 返回验证结果
        return $this->validate($rule, $data);
	}


}