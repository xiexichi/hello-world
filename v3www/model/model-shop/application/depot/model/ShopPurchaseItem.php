<?php

/**
 * 进货商品项
 */

namespace app\depot\model;

class ShopPurchaseItem extends Base
{


	public function indexBefore(){
		// 添加商品信息
		$this->alias('a')
			 ->field('a.*,spo.status order_status,s.salable_qty,s.item_code,s.sku_code,s.sku_name,g.goods_name,g.market_price,g.sell_price,gi.item_price,gi.item_img,sdi.apply_quantity  differ_apply_quantity,sdi.real_quantity differ_real_quantity')
			 ->join('shop_purchase_order spo', 'spo.id = a.shop_purchase_order_id')
			 ->join('stock s', 's.id = a.stock_id')
			 ->join('goods g', 's.item_code = g.erp_code', 'LEFT')
			 ->join('goods_item gi', 'CONCAT(s.item_code, ",",s.sku_code) = gi.erp_code', 'LEFT')
			 ->join('shop_differ_item sdi', 'sdi.shop_purchase_item_id = a.id', 'LEFT');
	}


	public function editBefore(){
		// 查找库存id
		$item = $this->where('id', $this->data['id'])->find();

		if (!$item) {
			$this->isExit = true;
			$this->error = '不存在进货项';
			return false;
		}

		// 查找库存是否充足
		if (!$this->checkStockQuantity($item['stock_id'], $this->data['apply_quantity'])) {
			$this->isExit = true;
			return false;
		}

	}

	/**
	 * [batchEdit 批量修改]
	 * @return [type] [description]
	 */
	public function batchEdit(){
		// pe($this->data);

		// 判断订单状态是否可以修改
		$spoModel = new ShopPurchaseOrder();
		// 进货单
		$order = $spoModel->where('id', $this->data['shop_purchase_order_id'])->find();

		if (!$order) {
			$this->error = '进货单不存在';
			return false;
		}

		// 进货单状态不是待审核，不能修改商品实发数量
		if ($order['status'] != 0) {	
			$this->error = '进货单状态不是待审核，不能修改商品实发数量';
			return false;
		}

		// 库存验证
		foreach ($this->data['items'] as $k => $v) {
			$item = $this->alias('a')
						 ->field('a.*,s.salable_qty')
						 ->join('stock s', 's.id = a.stock_id')
						 ->where('a.id', $v['id'])
						 ->find();

			if (!$item) {
				$this->error = '商品不存在';
				return false;
			}

			if ($item['salable_qty'] < $v['real_quantity']) {
				$this->error = "总店库存只有{$item['salable_qty']},不够{$v['real_quantity']}";
				return false;
			}

		}


		// 批量修改
		return $this->saveAll($this->data['items']);

	}






}