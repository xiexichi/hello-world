<?php

/**
 * 店铺库存预选项
 */

namespace app\depot\controller;


class ShopDepotPreSelectItem extends Base
{


	public function indexBefore(){
		// 添加查找参数
		$params = input();
		
		// pe($params);

		if (!empty($params['shop_depot_pre_select_id'])) {
			$this->model->where('shop_depot_pre_select_id', $params['shop_depot_pre_select_id']);
		}

		
	}


}