<?php


namespace app\depot\validate;


class ShopDepotPreSelect extends Base
{

	public function add($data){
        // 如果有操作员工id
        if (input('ctrl_staff_id')) {
            // 设置员工id
            $data['staff_id'] = input('ctrl_staff_id');
        }

		$rule = [
            'shop_id' => 'require|number',// 店铺id
            'staff_id' => 'require|number',// 员工id
            'type' => 'require|number', // 操作类型
            'shop_depot_type' => 'require|number',	// 库存类型
            'tag' => 'require|min:3',	// 标记
            'remarks' => 'min:3',		// 备注
        ];
        
        // 返回验证结果
        return $this->validate($rule, $data);
	}

}