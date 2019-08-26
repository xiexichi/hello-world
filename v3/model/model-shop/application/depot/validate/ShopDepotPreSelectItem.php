<?php


namespace app\depot\validate;


class ShopDepotPreSelectItem extends Base
{

	public function add($data){
		$rule = [
            'shop_depot_pre_select_id' => 'require|number',// 预选标记id
            'stock_id' => 'require|number',	// 商品库存id
            'quantity' => 'require|number',	// 预选数量
        ];
        // 返回验证结果
        return $this->validate($rule, $data);
	}



}