<?php

/**
 * 差异单商品项
 */

namespace app\depot\model;

use think\Db;

class ShopDifferItem extends Base{


	public function indexBefore(){
		// 添加商品信息
		$this->alias('a')
			 ->field('a.*,sdo.status order_status,s.salable_qty,s.item_code,s.sku_code,s.sku_name,g.goods_name,g.market_price,g.sell_price,gi.item_price,gi.item_img,spi.apply_quantity  purchase_apply_quantity,spi.real_quantity purchase_real_quantity')
			 ->join('shop_differ_order sdo', 'sdo.id = a.shop_differ_order_id')
			 ->join('shop_purchase_item spi', 'spi.id = a.shop_purchase_item_id')
			 ->join('stock s', 's.id = spi.stock_id')
			 ->join('goods g', 's.item_code = g.erp_code', 'LEFT')
			 ->join('goods_item gi', 'CONCAT(s.item_code, ",",s.sku_code) = gi.erp_code', 'LEFT');
	}


	/**
	 * [saveDifferItem 保存差异项]
	 * @return [type] [description]
	 */
	public function saveDifferItem(){

		// 表对象
		$table = Db::table('shop_purchase_item');

		// 查找是否存在进货差异单
		$differOrder = $table->alias('spi')
							 ->field('sdo.*')
							 ->join('shop_purchase_order spo', 'spo.id = spi.shop_purchase_order_id')
							 ->join('shop_differ_order sdo', 'sdo.shop_purchase_order_id = spo.id')
							 ->where('spi.id', $this->data['shop_purchase_item_id'])
							 ->find();

		//
		// pe($table->getLastSql());

		// 开启事务
		$this->startTrans();

		// 判断是否有创建过差异单
		if ($differOrder) {
			// 有，则直接使用差异单的id
			$shopDifferOrderId = $differOrder['id'];

		} else {

			// 进货单
			$purchaseOrder = Db::table('shop_purchase_item')
				->alias('spi')
				->field('spo.*')
				->join('shop_purchase_order spo', 'spo.id = spi.shop_purchase_order_id')
				->where('spi.id', $this->data['shop_purchase_item_id'])
				->find();

			if (!$purchaseOrder) {
				$this->error = '进货单不存在';
				$this->rollback();	// 回滚
				return false;
			}

			// 设置进货单id
			$this->data['shop_purchase_order_id'] = $purchaseOrder['id'];

			// 没有，则创建差异单
			$sdoModel = new ShopDifferOrder();

			// 添加进货差异单
			$shopDiffer = $sdoModel->data($this->data)->add();

			if (!$shopDiffer) {
				$this->error = '创建差异单失败';
				$this->rollback();	// 回滚
				return false;
			}

			// 差异单数据
			$differOrder = $sdoModel->getData();

			// 设置
			$this->tempData['shop_differ_order'] = $differOrder;

			// 店铺差异单id
			$shopDifferOrderId = $differOrder['id'];
		}

		// 查找是否存在差异项
		$item = $this->where('shop_differ_order_id', $shopDifferOrderId)
			 		 ->where('shop_purchase_item_id', $this->data['shop_purchase_item_id'])
			 		 ->find();

		// 有则更新
		if ($item) {
			$item->apply_quantity = $this->data['apply_quantity'];
			// 更新
			$res = $item->save();

		} else {
			// 无则，新增
			$this->data['shop_differ_order_id'] = $shopDifferOrderId;
			// 新增
			$res = $this->save();
		}

		if (!$res) {
			$this->error = '新增差异项失败';
			$this->rollback();	// 回滚
			return false;
		}

		// 提交事务
		$this->commit();
		return true;
	}




	/**
	 * [batchEdit 批量修改]
	 * @return [type] [description]
	 */
	public function batchEdit(){
		// pe($this->data);

		// 判断订单状态是否可以修改
		$sdoModel = new ShopDifferOrder();
		// 进货单
		$order = $sdoModel->where('id', $this->data['shop_differ_order_id'])->find();

		if (!$order) {
			$this->error = '进货单不存在';
			return false;
		}

		// 进货单状态不是待审核，不能修改商品实发数量
		if ($order['status'] != 1) {	
			$this->error = '进货单状态不是待审核，不能修改商品实发数量';
			return false;
		}

		// 批量修改
		return $this->saveAll($this->data['items']);

	}

}