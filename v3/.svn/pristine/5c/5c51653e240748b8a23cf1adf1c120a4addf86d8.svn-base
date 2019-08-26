<?php

/**
 * 店铺权限角色
 */

namespace app\staff\controller;

class ShopAuthRole extends Base
{
	

	public function allBefore(){
		
		// 参数
		$params = input();

		// 店铺id
		if (!empty($params['shop_id'])) {
			$this->model->where('shop_id', $params['shop_id']);
		}
		
	}

	
}