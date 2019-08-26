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

		// 退货单状态不是待审核，不能修改商品实发数量
		if ($order['status'] != 0) {	
			$this->error = '退货单状态不是待审核，不能修改商品实发数量';
			return false;
		}

		// 批量修改
		return $this->saveAll($this->data['items']);

	}

	/**
	 * [editAfter 修改后置]
	 * @return [type] [description]
	 */
	public function editAfter(){
		// p($this->tempData['changeBeforeData']);
		// pe($this->data);

		// 检测是有获取修改前数据
		if (empty($this->tempData['changeBeforeData'])) {
			$this->isExit = true;
			$this->error = '请先获取修改前数据';
			return false;
		}

		// 检测修改数量
		$beforeData = $this->tempData['changeBeforeData'];

		// 计算修改后的差值
		$differ = $beforeData['apply_quantity'] - $this->data['apply_quantity'];

		// 店铺库存模型
		$sdModel = new ShopDepot();

		// 变动类型
		$changeType;

		// pe($differ);

		// 设置变动类型
		if ($differ < 0) {
			// 继续扣减
			$changeType = ShopDepotChange::TYPE_SHOP_RETURN_WITH_HOLD;
		} else {
			// 返还预扣库存
			$changeType = ShopDepotChange::TYPE_SHOP_RETURN_WITH_REVERSE;
		}


		// 查找订单信息
		$sroModel = new ShopReturnOrder();
		$order = $sroModel->where('id', $beforeData['shop_return_order_id'])->find();

		if (!$order) {
			$this->isExit = true;
			$this->error = '退货单不存在';
			return false;
		}

		// 预扣库存
		$sdRes = $sdModel->inventoryOperation($order['order_sn'], 
						$changeType, 
						$order['shop_id'], 
						$beforeData['stock_id'], 
						$differ,// 乘以-1，因为是扣库存
						NULL,
						$order['shop_depot_type']);

		if (!$sdRes) {
			$this->isExit = true;
			$this->error = '预扣库存失败:' . $sdModel->getError();
			return false;
		}

	}


}