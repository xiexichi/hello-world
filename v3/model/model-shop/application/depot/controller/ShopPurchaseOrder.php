<?php

/**
 *	进货单
 */

namespace app\depot\controller;

class ShopPurchaseOrder extends Base
{


	public function indexBefore(){
		$params = input();

		// 设置查找条件
		// 单号
		if (!empty($params['order_sn'])) {
			$this->model->where('a.order_sn', 'like', "%{$params['order_sn']}%");
		}

		// 进货类型
		if (!empty($params['type'])) {
			$this->model->where('a.type', $params['type']);
		}

		// 订单状态
		if (isset($params['status'])) {
			$this->model->where('a.status', $params['status']);
		}

		// 开始时间
		if (!empty($params['start_date'])) {
			$this->model->where("DATE(a.create_time) >= '{$params['start_date']}'");
		}

		// 结束时间
		if (!empty($params['end_date'])) {
			$this->model->where("DATE(a.create_time) <= '{$params['end_date']}'");
		}

		// 标记
		$joinItemFlag = false;

		// 商品代码
		if (!empty($params['item_code'])) {
			// 联表查询
			$this->model->join('shop_purchase_item spi', 'spi.shop_purchase_order_id = a.id')
					    ->join('stock s', 'spi.stock_id = s.id')
					    ->where('s.item_code', 'like', "%{$params['item_code']}%")
						->group('a.id');

			$joinItemFlag = true;
		}

		// 商品规格
		if (!empty($params['sku_code'])) {
			if (!$joinItemFlag) {
				// 联表查询
				$this->model->join('shop_purchase_item spi', 'spi.shop_purchase_order_id = a.id')
					    ->join('stock s', 'spi.stock_id = s.id')
						->group('a.id');
			}

			// 规格
			$this->model->where('s.sku_code', $params['sku_code']);
		}
		// $this->model->tempData['print_sql'] = true;

		

	}

}
