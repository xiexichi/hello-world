<?php

/**
 * 店铺退货商品项
 */

namespace app\depot\model;

class ShopTransferItem extends Base
{

	public function indexBefore(){	

		// 调整单id
		if (!empty($this->data['shop_transfer_order_id'])) {
			$this->where('a.shop_transfer_order_id', $this->data['shop_transfer_order_id']);
		}

		// 添加商品信息
		$this->alias('a')
			 ->field('a.*,sto.status order_status,s.salable_qty,s.item_code,s.sku_code,s.sku_name,g.goods_name,g.market_price,g.sell_price,gi.item_price,gi.item_img')
			 ->join('shop_transfer_order sto', 'sto.id = a.shop_transfer_order_id')
			 ->join('stock s', 's.id = a.stock_id')
			 ->join('goods g', 's.item_code = g.erp_code', 'LEFT')
			 ->join('goods_item gi', 'CONCAT(s.item_code, ",",s.sku_code) = gi.erp_code', 'LEFT');
	}



	public function editBefore(){

		// pe($this->data);
		// 检测库存是否足够

		$item = $this->alias('a')
					 ->field('a.*,sto.order_sn,sto.out_shop_id,sd.availavle_quantity')
					 ->join('shop_depot sd', 'sd.stock_id = a.stock_id')
					 ->join('shop_transfer_order sto', 'sto.id = a.shop_transfer_order_id')
					 ->where('a.id', $this->data['id'])
					 ->find();

		if (!$item) {
			$this->isExit = true;
			$this->error = '店铺不存在的产品库存';
			return false;
		}

		// 判断可用库存
		if ($item['availavle_quantity'] < ($this->data['apply_quantity'] - $item['apply_quantity'])) {
			$this->isExit = true;
			$this->error = '可操作库存只有'.$item['availavle_quantity'];
			return false;
		}

		// 查找订单信息
		$stfoModel = new ShopTransferOrder();
		$order = $stfoModel->where('id', $item['shop_transfer_order_id'])->find();
		if (!$order) {
			$this->isExit = true;
			$this->error = '调拨单不存在';
			return false;
		}

		// pe($order);

		// 预扣库存
		/**
		 * 因为是修改，所以是追加记录
		 */

		if ($this->data['apply_quantity'] != $item['apply_quantity']) {

			// 店铺库存模型
			$sdModel = new ShopDepot();

			// 计算差值：原申请库存 - 新申请库存
			$differ = $item['apply_quantity'] - $this->data['apply_quantity'];

			// 
			if ($differ > 0) {
				// 正数，则是要返回库存
				$type = ShopDepotChange::TYPE_SHOP_TRANSFER_WITH_REVERSE;
			} else {
				// 负数，则是要再次扣除库存
				$type = ShopDepotChange::TYPE_SHOP_TRANSFER_WITH_HOLD;
			}

			// 预扣库存
			$sdRes = $sdModel->inventoryOperation($item['order_sn'], 
							$type, 
							$item['out_shop_id'], 
							$item['stock_id'], 
							$differ,
							NULL,
							$order['shop_depot_type']);

			if (!$sdRes) {
				$this->isExit = true;
				$this->error = '预处理库存失败:' . $sdModel->getError();
				return false;
			}

			// pe($this->data);
		}

	}


}