<?php

/**
 * 店铺退货商品项
 */

namespace app\depot\model;

class ShopReturnItem extends Base
{

	public function indexBefore(){
		// 添加商品信息
		$this->alias('a')
			 ->field('a.*,sro.status order_status,s.salable_qty,s.item_code,s.sku_code,s.sku_name,g.goods_name,g.market_price,g.sell_price,gi.item_price,gi.item_img')
			 ->join('shop_return_order sro', 'sro.id = a.shop_return_order_id')
			 ->join('stock s', 's.id = a.stock_id')
			 ->join('goods g', 's.item_code = g.erp_code', 'LEFT')
			 ->join('goods_item gi', 'CONCAT(s.item_code, ",",s.sku_code) = gi.erp_code', 'LEFT');
	}

	/**
	 * [batchEdit 批量修改]
	 * @return [type] [description]
	 */
	public function batchEdit(){
		// pe($this->data);

		// 判断订单状态是否可以修改
		$spoModel = new ShopReturnOrder();
		// 进货单
		$order = $spoModel->where('id', $this->data['shop_return_order_id'])->find();

		if (!$order) {
			$this->error = '退货单不存在';
			return false;
		}

		// pe($order);

		// 进货单状态不是待审核，不能修改商品实发数量
		if ($order['status'] != 0) {	
			$this->error = '退货单状态不是待审核，不能修改商品实发数量';
			return false;
		}

		// 批量修改
		return $this->saveAll($this->data['items']);

	}

}