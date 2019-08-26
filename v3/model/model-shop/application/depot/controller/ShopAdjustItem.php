<?php

/**
 * 店铺库存调整商品项
 */

namespace app\depot\controller;


class ShopAdjustItem extends Base
{

	public function indexBefore(){

		$params = input();


		// 只获取对应进货单的商品项
		if (!empty($params['shop_adjust_order_id'])) {
			$this->model->where('a.shop_adjust_order_id', $params['shop_adjust_order_id']);
		}

		// 商品代码
		if (!empty($params['item_code'])) {
			$this->model->where('s.item_code', $params['item_code']);
		}		

		// 规格
		if (!empty($params['sku_code'])) {
			$this->model->where('s.sku_code', $params['sku_code']);
		}

	}


}