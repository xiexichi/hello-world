<?php

/**
 * 库存预选
 */

namespace app\depot\controller;

class ShopDepotPreSelect extends Base
{


	public function indexBefore(){
		$param = input();

		// 标签名称		
		if (!empty($param['tag'])) {
			$this->model->where('tag', 'like', "%{$param['tag']}%");
		}

		// 标签类型		
		if (!empty($param['type'])) {
			$this->model->where('type', $param['type']);
		}

		// 店铺库存类型		
		if (!empty($param['shop_depot_type'])) {
			$this->model->where('shop_depot_type', $param['shop_depot_type']);
		}

		// 店铺id		
		if (!empty($param['shop_id'])) {
			$this->model->where('shop_id', $param['shop_id']);
		}
		
		
	}


}


