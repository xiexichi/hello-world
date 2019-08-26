<?php

/**
 * 店铺差异单
 */

namespace app\depot\model;

class ShopDifferOrder extends Base{

	// 订单状态
	public $status = [
		'-1' => '作废',
		'0'  => '未提交',
		'1'  => '待审核',
		'2'  => '审核成功',
		'3'  => '审核失败',
	];

	public function indexBefore(){

		// 查找申请数量、实发数量、剩余数量
		// 申请数量
		$applyQuantity = "(SELECT SUM(apply_quantity) FROM shop_differ_item WHERE shop_differ_order_id = a.id) apply_quantity";
		// 实发数量
		$realQuantity = "(SELECT SUM(real_quantity) FROM shop_differ_item WHERE shop_differ_order_id = a.id) real_quantity";

		// 状态名称
		$statusName = $this->getStatusSql('a');

		// 查找店铺与员工信息
		$this->alias('a')
			 ->field("a.*,ss.staff_name,hs.name shop_name,{$applyQuantity},{$realQuantity},{$statusName} status_name,spo.order_sn purchase_order_sn")
			 ->join('shop_staff ss', 'a.staff_id = ss.id')
			 ->join('shop_purchase_order spo', 'spo.id = a.shop_purchase_order_id')
			 ->join('hea_center.shop hs', 'hs.id = a.shop_id');
	}

	public function addBefore(){

		// 创建进货单数据
		$order = [
			'order_sn' => $this->createOrderSn('Y'),	// 订单号
			'shop_purchase_order_id' => $this->data['shop_purchase_order_id'],	// 订单号
			'shop_id' => $this->data['shop_id'],
			'create_time' => date('Y-m-d H:i:s'),
		];

		// 如果有员工id
		if (!empty($this->data['staff_id'])) {
			$order['staff_id'] = $this->data['staff_id'];
		}

		// 获取进货单备注
		if (!empty($this->data['shop_remark'])) {
			$order['shop_remark'] = $this->data['shop_remark'];
		}

		$this->data = $order;

	}

	/**
	 * [editBefore 修改前置]
	 * @return [type] [description]
	 */
	public function editBefore(){

		$model = new ShopDifferOrder();
		// 查找订单信息
		$order = $model->where('id', $this->data['id'])->find();

		// 如果是审核通过，
		if ($this->data['status'] == 2 && $order['is_revise']) {
			// 调整数量
			if (!$this->revise($order)) {
				$this->isExit = true;
				return false;
			}

			// 设置已修正
			$this->data['is_revise'] = 1;
		}

	}

	/**
	 * [revise 修正]
	 * @param  [type] $order [订单信息]
	 * @return [type]        [description]
	 */
	protected function revise($order){

		// 查找差异商品项
		$itemModel = new ShopDifferItem();

		$items = $itemModel->where('shop_differ_order_id', $order['id'])->select();

		if (!$items) {
			$this->error = '没有差异商品项';
			return false;
		}

		// 店铺库存模型
		$shopDepot = new ShopDepot();

		foreach ($items as $k => $v) {

			// 1.判断是否需要返回预扣库存
			if ($v['apply_quantity'] < 0) {
				$res = $shopDepot->inventoryOperation(
								$this->data['order_sn'],
								ShopDepotChange::TYPE_SHOP_DIFFER_WITH_REVERSE,
								$this->data['shop_id'],
								$v->stock_id,
								abs($v->apply_quantity));
				if (!$res) {
					// 回滚
					// $this->rollback();
					$this->error = '返还预扣差异库存失败，'.$shopDepot->getError();
					return false;
				}
			}

			// 2. 修正操作
			$res = $shopDepot->inventoryOperation(
								$this->data['order_sn'],
								ShopDepotChange::TYPE_SHOP_DIFFER,
								$this->data['shop_id'],
								$v->stock_id,
								$v->real_quantity);

			if (!$res) {
				// 回滚
				// $this->rollback();
				$this->error = '修正进货数量失败，'.$shopDepot->getError();
				return false;
			}
		}

		return true;
	}



}