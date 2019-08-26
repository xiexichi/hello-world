<?php

/**
 * 店铺调拨单模型
 */

namespace app\depot\model;

class ShopTransferOrder extends Base
{

	protected $status = [
		'-1' => '作废',
		'0'  => '待接收',
		'1'  => '已收货'
	];

	public function indexBefore(){
		// 表别名
		$alias = 'sto';

		// 调出数量
		$applyQuantity = "(SELECT SUM(apply_quantity) FROM shop_transfer_item WHERE shop_transfer_order_id = {$alias}.id) apply_quantity";
		// 调入数量
		$realQuantity = "(SELECT SUM(real_quantity) FROM shop_transfer_item WHERE shop_transfer_order_id = {$alias}.id) real_quantity";

		// 状态名称
		$statusName = $this->getStatusSql($alias);

		// 查找联表信息
		$this->alias($alias)
			 ->field("{$alias}.*,{$statusName} status_name,ocs.name out_shop_name,ics.name in_shop_name,{$applyQuantity},{$realQuantity},ss.staff_name")
			 ->join(self::CENTER_DB_NAME.'.shop ocs', 'sto.out_shop_id = ocs.id')
			 ->join(self::CENTER_DB_NAME.'.shop ics', 'sto.in_shop_id = ics.id')
			 ->join('shop_staff ss', "{$alias}.staff_id = ss.id");


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

	public function addBefore(){

		// pe($this->data);

		// 设置调出门店id
		if (!empty($this->data['shop_id'])) {
			$this->data['out_shop_id'] = $this->data['shop_id'];
		} else {
			$this->data['out_shop_id'] = '';
		}

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

		// pe($this->data);

		// pe($sdpsData);
		// 创建进货单数据
		$order = [
			'order_sn' => $this->createOrderSn('S'),	// 订单号
			'shop_id' => $this->data['shop_id'],
			'create_time' => date('Y-m-d H:i:s'),
			'in_shop_id' => $this->data['in_shop_id'],	
			'out_shop_id' => $this->data['out_shop_id'],
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

		// pe($order);

		$this->data = $order;
	}


	public function addAfter(){
		// pe($this->tempData['params']);

		// pe('addAfter');

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


		// 选择的商品项
		$selectItems = modelList2Array($items);

		// pe($selectItems);

		// 店铺库存模型
		$sdModel = new ShopDepot();

		$addItemDatas = [];

		// pe($this->data);


		foreach ($selectItems as $k => $v) {
			$item = [];
			$item['shop_transfer_order_id'] = $this->data['id'];	// 进货单id
		    $item['stock_id'] = $v['stock_id'];	// 商品库存id
		    $item['apply_quantity'] = $v['quantity'];	// 申请数量

			// 预扣库存
			$sdRes = $sdModel->inventoryOperation($this->data['order_sn'], 
							ShopDepotChange::TYPE_SHOP_TRANSFER_WITH_HOLD, 
							$this->data['shop_id'], 
							$item['stock_id'], 
							abs($item['apply_quantity']) * -1, // 乘以-1，因为是扣库存
							NULL,	// 可用数量
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
		$stfiModel = new ShopTransferItem();

		// pe($addItemDatas);

		// 添加进货项
		if (!$stfiModel->addAll($addItemDatas)) {
			$this->isExit = true;
			$this->error = '添加调货项失败！';
			return false;
		}

		
	}


}

