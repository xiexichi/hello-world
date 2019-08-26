<?php

/**
 * 店铺退货
 */

namespace app\depot\model;

class ShopReturnOrder extends Base
{

	const STATUS_CANCEL = -1;			// 作废
	const STATUS_PENDING_REVIEW = 0;	// 待审核
	const STATUS_PASS = 1;				// 审核成功
	const STATUS_AUDIT_FAILURE = 2;		// 审核失败
	const STATUS_SHIP = 3;				// 已发货
	const STATUS_RECEIPT = 4;			// 已收货

	protected $status = [
		'-1' => '作废',
		'0'  => '待审核',
		'1'  => '审核成功',
		'2'  => '审核失败',
		'3'  => '已发货',
		'4'  => '已收货'
	];

	public function indexBefore(){

		// 查找申请数量、实发数量、剩余数量
		// 申请数量
		$applyQuantity = "(SELECT SUM(apply_quantity) FROM shop_return_item WHERE shop_return_order_id = a.id) apply_quantity";
		// 实发数量
		$realQuantity = "(SELECT SUM(real_quantity) FROM shop_return_item WHERE shop_return_order_id = a.id) real_quantity";

		// 获取状态名称和进货单类型
		$typeName = "(CASE a.shop_depot_type WHEN 1 THEN '销售' WHEN 2 THEN '物料' END)";

		// 状态名称
		$statusName = $this->getStatusSql('a');

		// 查找店铺与员工信息
		$this->alias('a')
			 ->field("a.*,ss.staff_name,hs.name shop_name,{$applyQuantity},{$realQuantity},{$typeName} type_name,{$statusName} status_name")
			 ->join('shop_staff ss', 'a.staff_id = ss.id', 'LEFT')
			 ->join('hea_center.shop hs', 'hs.id = a.shop_id');


		// $this->tempData['print_sql'] = true;
	}

	/**
	 * [getStatusSql 获取订单状态sql]
	 * @param  [type] $tablePrefix [表前缀]
	 * @return [type]              [description]
	 */
	protected function getStatusSql($tableAlias){
		$sql = "(CASE {$tableAlias}.status ";
		foreach ($this->status as $k => $v) {
			$sql .= " WHEN {$k} THEN '{$v}'";
		}

		$sql .= ' END)';
		
		return $sql;
	}

	/**
	 * [oneAfter 查找单条数据后置方法]
	 * @return [type] [description]
	 */
	public function oneAfter(){
		// 获取商品申请数量和实退数量
		// pe($this->data);

		// 商品项模型
		$itemModel = new ShopReturnItem();

		// 申请数量
		$totalApplyQuantity = $itemModel->where('shop_return_order_id', $this->data['id'])->sum('apply_quantity');

		// 实退数量
		$totalRealQuantity = $itemModel->where('shop_return_order_id', $this->data['id'])->sum('real_quantity');

		// 设置
		$this->data['total_apply_quantity'] = $totalApplyQuantity;
		$this->data['total_real_quantity'] = $totalRealQuantity;

	}


	public function addBefore(){

		// 将提交参数，存入临时数据中
		$this->tempData['params'] = $this->data;

		// 1.创建订单数据

		// 预选标签模型
		$sdpsModel = new ShopDepotPreSelect();

		$sdpsData = $sdpsModel->setWhere('a.id', $this->data['shop_depot_pre_select_id'])->one();

		if (!$sdpsData) {
			$this->isExit = true;
			$this->error = '没有选择商品';
			return false;
		}

		// pe($sdpsData);
		// 创建进货单数据
		$order = [
			'order_sn' => $this->createOrderSn('C'),	// 订单号
			'shop_id' => $this->data['shop_id'],
			'create_time' => date('Y-m-d H:i:s'),
			'shop_depot_type' => $sdpsData['shop_depot_type']
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
	

	public function addAfter(){
		// pe($this->tempData['params']);

		// 查找选择商品项
		$sdpiModel = new ShopDepotPreSelectItem();

		// 设置查找详情
		$sdpiModel->tempData['one_detail'] = true;

		$items = $sdpiModel->setWhere('shop_depot_pre_select_id', $this->tempData['params']['shop_depot_pre_select_id'])->getAll();

		if (!$items) {
			$this->isExit = true;
			$this->error = '没有选择商品';
			return false;
		}

		// pe($this->data);

		// 选择的商品项
		$selectItems = modelList2Array($items);

		// pe($selectItems);

		// 店铺库存模型
		$sdModel = new ShopDepot();

		$addItemDatas = [];

		foreach ($selectItems as $k => $v) {
			$item = [];
			$item['shop_return_order_id'] = $this->data['id'];	// 进货单id
		    $item['stock_id'] = $v['stock_id'];	// 商品库存id
		    $item['apply_quantity'] = $v['quantity'];	// 申请数量

			// 预扣库存
			$sdRes = $sdModel->inventoryOperation($this->data['order_sn'], 
							ShopDepotChange::TYPE_SHOP_RETURN_WITH_HOLD, 
							$this->data['shop_id'], 
							$item['stock_id'], 
							abs($item['apply_quantity']) * -1,// 乘以-1，因为是扣库存
							NULL,
							$this->data['shop_depot_type']);

			if (!$sdRes) {
				$this->isExit = true;
				$this->error = '预扣库存失败:' . $sdModel->getError();
				return false;
			}

		    // 添加商品项数据
		    $addItemDatas[] = $item;
		}

		// 进货项模型
		$sriModel = new ShopReturnItem();

		// 添加进货项
		if (!$sriModel->addAll($addItemDatas)) {
			$this->isExit = true;
			$this->error = '添加退货项失败！';
			return false;
		}

		
	}

	/**
	 * [editBefore 修改前置]
	 * @return [type] [description]
	 */
	public function editBefore(){

		$model = new ShopReturnOrder();
		$order = $model->where('id', $this->data['id'])->find();

		// 判断订单状态是否为收货，并且未收过货
		if ($this->data['status'] == self::STATUS_RECEIPT && !$order['is_receipt']) {
			/**
			 * 处理收货
			 * 1.返回预扣退货数量
			 * 2.扣除真实退货数量
			 */
			// 商品项模型
			$itemModel = new ShopReturnItem();

			$items = $itemModel->where('shop_return_order_id', $this->data['id'])->select();
			if (!$items) {
				$this->error = '没有退货商品项';
				$this->isExit = true;
				return false;
			}

			// 商品对象数组转为数组
			$itemArr = modelList2Array($items);

			// 店铺库存模型
			$sdModel = new ShopDepot();

			// 释放预扣库存
			foreach ($itemArr as $k => $v) {
				// 1.返回预扣库存
				$sdRes = $sdModel->inventoryOperation($this->data['order_sn'], 
								ShopDepotChange::TYPE_SHOP_RETURN_WITH_REVERSE, 
								$this->data['shop_id'], 
								$v['stock_id'], 
								abs($v['apply_quantity']));

				if (!$sdRes) {
					$this->isExit = true;
					$this->error = $sdModel->getError();
					return false;
				}

				// 2.真正扣除库存
				$sdRes = $sdModel->inventoryOperation($this->data['order_sn'], 
								ShopDepotChange::TYPE_SHOP_RETURN, 
								$this->data['shop_id'], 
								$v['stock_id'], 
								abs($v['apply_quantity']) * -1); // 因为是扣库存，所以必须为负数
				
				if (!$sdRes) {
					$this->isExit = true;
					$this->error = $sdModel->getError();
					return false;
				}
			}


			// pe($itemArr);

			// 设置为已收货
			$this->data['is_receipt'] = 1;
		}

		// pe($order->getData());
	}

	/**
	 * [editAfter 修改后置]
	 * @return [type] [description]
	 */
	public function editAfter(){
		
	}

}