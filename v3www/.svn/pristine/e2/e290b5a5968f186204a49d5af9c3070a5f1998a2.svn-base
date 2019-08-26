<?php

/**
 * 店铺调整单模型
 */

namespace app\depot\model;

class ShopAdjustOrder extends Base
{

	// 订单状态
	public $status = [
		'-1' => '作废',
		'0'  => '待审核',
		'1'  => '调整成功',
		'2'  => '不同意调整',
	];


	public function indexBefore(){
		// 查找申请数量、实发数量、剩余数量
		// 申请数量
		$applyQuantity = "(SELECT SUM(apply_quantity) FROM shop_adjust_item WHERE shop_adjust_order_id = a.id) apply_quantity";
		// 实发数量
		$realQuantity = "(SELECT SUM(real_quantity) FROM shop_adjust_item WHERE shop_adjust_order_id = a.id) real_quantity";

		// 获取状态名称和进货单类型
		$typeName = "(CASE a.type WHEN 1 THEN '商品' WHEN 2 THEN '物料' END)";

		// 状态名称
		$statusName = $this->getStatusSql('a');

		// 查找店铺与员工信息
		$this->alias('a')
			 ->field("a.*,ss.staff_name,hs.name shop_name,{$applyQuantity},{$realQuantity},{$typeName} type_name,{$statusName} status_name")
			 ->join('shop_staff ss', 'a.staff_id = ss.id')
			 ->join('hea_center.shop hs', 'hs.id = a.shop_id');

		// 打印sql
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
			'order_sn' => $this->createOrderSn('T'),	// 订单号
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


		// 选择的商品项
		$selectItems = modelList2Array($items);

		// pe($selectItems);

		// 店铺库存模型
		$sdModel = new ShopDepot();

		$addItemDatas = [];

		foreach ($selectItems as $k => $v) {

			// 计算调整差
			// 如果调整后小于现在库存
		    $spItem = $spModel->where('shop_id', $this->data['shop_id'])
				    		  ->where('stock_id', $v['stock_id'])
				    		  ->find();
			if (!$spItem) {
				$this->isExit = true;
				$this->error = '店铺不存在此商品！';
				return false;
			}

			// 设置item数据
			$item = [];
			$item['shop_adjust_order_id'] = $this->data['id'];	// 进货单id
		    $item['stock_id'] = $v['stock_id'];	// 商品库存id
		    $item['apply_quantity'] = $v['quantity'] - $spItem['quantity'];	// 申请数量

		  
		    // 如果调整数量是负数，则先预扣
		    if ($item['apply_quantity'] < 0) {
				// 预扣库存
				$sdRes = $sdModel->inventoryOperation($this->data['order_sn'], 
							ShopDepotChange::TYPE_SHOP_ADJUST_WITH_HOLD, 
							$this->data['shop_id'], 
							$item['stock_id'], 
							abs($item['apply_quantity']));
		    }
		    
		    // 添加商品项数据
		    $addItemDatas[] = $item;
		}

		// 进货项模型
		$itemModel = new ShopAdjustItem();
		// 添加进货项
		if (!$itemModel->addAll($addItemDatas)) {
			$this->isExit = true;
			$this->error = '添加进货项失败！';
			return false;
		}

	}

}