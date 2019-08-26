<?php

/**
 * 店铺调整单商品项模型
 */

namespace app\depot\model;

class ShopAdjustItem extends Base
{

	public function indexBefore(){

		//  查找店铺库存数量


		// 添加商品信息
		$this->alias('a')
			 ->field('a.*,sdo.status order_status,s.salable_qty,s.item_code,s.sku_code,s.sku_name,g.goods_name,g.market_price,g.sell_price,gi.item_price,gi.item_img')
			 ->join('shop_adjust_order sdo', 'sdo.id = a.shop_adjust_order_id')
			 ->join('stock s', 's.id = a.stock_id')
			 ->join('shop_depot sd', 'sd.shop_id = sdo.shop_id AND sd.stock_id = a.stock_id AND sd.type = sdo.shop_depot_type')
			 ->join('goods g', 's.item_code = g.erp_code', 'LEFT')
			 ->join('goods_item gi', 'CONCAT(s.item_code, ",",s.sku_code) = gi.erp_code', 'LEFT');
		
		// 打印sql
		// $this->tempData['print_sql'] = true;
	}


	public function editBefore(){

		$itemModel = new ShopAdjustItem();

		$item = $itemModel->alias('a')
						  ->field('a.*,sao.shop_id,sao.order_sn,sao.shop_depot_type,sd.quantity shop_quantity')
						  ->join('shop_adjust_order sao', 'a.shop_adjust_order_id = sao.id')
						  ->join('shop_depot sd', 'sd.stock_id = a.stock_id AND sd.type = sao.shop_depot_type')
						  ->where('a.id', $this->data['id'])->find();
		// 
		if (!$item) {
			$this->isExit = true;
			$this->error = '调整项不存在';
			return false;
		}

		// 计算实际调整数量
		$adjustQuantity = $this->data['apply_quantity'] - $item['shop_quantity'];

		// 设置回修改数据中（用于保存数据）
		$this->data['apply_quantity'] = $adjustQuantity;

		// 变动数量
		$changeQuantity = 0;

		/**
		 *	修改的4种情况
		 *  1.由负数修改为负数
		 *  2.由负数修改为正数
		 *  3.由正数修改为负数
		 *  4.由正数修改为正数，这种情况不用修改库存（因为正数只记录，负数才操作）
		 */

		// 改变数量
		$changeQuantity = NULL;

		if ($item) {
			// 有item则是修改
			
			// 1.由负数修改为负数
			if ($item['apply_quantity'] < 0 && $adjustQuantity < 0) {
				$changeQuantity = $adjustQuantity - $item['apply_quantity'];
			}

			// 2.由负数修改为正数
			if ($item['apply_quantity'] < 0 && $adjustQuantity > 0) {
				// 这个位置要转换为正数
				$changeQuantity = abs($item['apply_quantity']);
			}

			// 2.由正数修改为负数
			if ($item['apply_quantity'] > 0 && $adjustQuantity < 0) {
				$changeQuantity = $adjustQuantity;
			}

		}

		// p($item->getData());
		// pe($changeQuantity);

		// 如果有变动数量
		if ($changeQuantity != 0) {
			// 
			if ($changeQuantity > 0) {
				// 正数，则是要返回库存
				$type = ShopDepotChange::TYPE_SHOP_ADJUST_WITH_HOLD;
			} else {
				// 负数，则是要再次扣除库存
				$type = ShopDepotChange::TYPE_SHOP_ADJUST_WITH_REVERSE;
			}

			// 店铺库存模型
			$sdModel = new ShopDepot();

			// 预扣库存
			$sdRes = $sdModel->inventoryOperation($item['order_sn'], 
							$type, 
							$item['shop_id'], 
							$item['stock_id'], 
							$changeQuantity,
							NULL,
							$item['shop_depot_type']);

			if (!$sdRes) {
				$this->isExit = true;
				$this->error = '预处理库存失败:' . $sdModel->getError();
				return false;
			}
		}

		// pe($item);
		// pe($this->data);	
	}


}