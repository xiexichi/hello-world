<?php

/**
 * 进货单模型
 */

namespace app\depot\model;

class ShopPurchaseOrder extends Base
{

	protected $alias = 'a';	// 默认表别名

	const STATUS_CANCEL = -1;			// 作废
	const STATUS_PENDING_REVIEW = 0;	// 待审核
	const STATUS_PASS = 1;				// 审核成功
	const STATUS_AUDIT_FAILURE = 2;		// 审核失败
	const STATUS_SHIP = 3;				// 已发货
	const STATUS_RECEIPT = 4;			// 已收货

	public $status = [
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
		$applyQuantity = "(SELECT SUM(apply_quantity) FROM shop_purchase_item WHERE shop_purchase_order_id = a.id) apply_quantity";
		// 实发数量
		$realQuantity = "(SELECT SUM(real_quantity) FROM shop_purchase_item WHERE shop_purchase_order_id = a.id) real_quantity";
		// 可用数量
		$availavleQuantity = "(SELECT SUM(availavle_quantity) FROM shop_purchase_item WHERE shop_purchase_order_id = a.id) availavle_quantity";

		// 获取状态名称和进货单类型
		$typeName = "(CASE a.shop_depot_type WHEN 1 THEN '商品' WHEN 2 THEN '物料' END)";

		// 状态名称
		$statusName = $this->getStatusSql('a');

		// 查找店铺与员工信息
		$this->alias('a')
			 ->field("a.*,ss.staff_name,hs.name shop_name,{$applyQuantity},{$realQuantity},{$availavleQuantity},{$typeName} type_name,{$statusName} status_name")
			 ->join('shop_staff ss', 'a.staff_id = ss.id')
			 ->join('hea_center.shop hs', 'hs.id = a.shop_id');
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

		// pe($sdpsData);
		// 创建进货单数据
		$order = [
			'order_sn' => $this->createOrderSn('P'),	// 订单号
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


		// 选择的商品项
		$selectItems = modelList2Array($items);

		// pe($selectItems);

		$addItemDatas = [];

		foreach ($selectItems as $k => $v) {
			$item = [];
			// 如果有size价
			if ($v['item_price'] > 0) {
				// 使用size价
				$item['sell_price'] = $v['item_price'];
			} else {
				// 使用商品价
				$item['sell_price'] = $v['sell_price'];
			}

			$item['shop_purchase_order_id'] = $this->data['id'];	// 进货单id
		    $item['stock_id'] = $v['stock_id'];	// 商品库存id
		    $item['apply_quantity'] = $v['quantity'];	// 申请数量
		    $item['market_price'] = $v['market_price'];	// 市场价

		    // 查找库存是否充足（提示信息也封装在方法里）
			if (!$this->checkStockQuantity($item['stock_id'], $item['apply_quantity'])) {
				$this->isExit = true;
				return false;
			}

		    // 添加商品项数据
		    $addItemDatas[] = $item;
		}

		// 进货项模型
		$spiModel = new ShopPurchaseItem();
		// 添加进货项
		if (!$spiModel->addAll($addItemDatas)) {
			$this->isExit = true;
			$this->error = '添加进货项失败！';
			return false;
		}

	}

	/**
	 * [editBefore 修改前置]
	 * @return [type] [description]
	 */
	public function editBefore(){
		// 获取进货单信息
		$order = $this->where('id', $this->data['id'])->find();
		if (!$order) {
			$this->isExit = true;
			$this->error = '订单信息不存在';
			return false;
		}

		// 判断是否非总后台请求修改
		// $order
		// pe($this->tempData['request']->header());

		$this->tempData['order'] = $order->getData();

		// pe($this->data);
	}

	/**
	 * [editAfter 修改后置]
	 * @return [type] [description]
	 */
	public function editAfter(){
		/**
		 * 一下的判断条件是防止状态修改后，多次触发调用
		 */

		// 判断订单状态是否被修改为：审核通过或已发货
		if (!empty($this->data['status']) && ($this->data['status'] == self::STATUS_PASS || $this->data['status'] == self::STATUS_SHIP) ) {
			// 订单状态修改为审核通过

			// 检测发货商品数量
			$spiModel = new ShopPurchaseItem();
			$infos = $spiModel->field('SUM(apply_quantity) apply_quantity, SUM(real_quantity) real_quantity')
					 ->where('shop_purchase_order_id', $this->data['id'])
					 ->group('shop_purchase_order_id')
					 ->find();

			if (!$infos) {
				$this->isExit = true;
				$this->error = '进货单不存在商品！';
				return false;
			}
			// 检测发货数量
			if ($infos['real_quantity'] <= 0) {
				$this->isExit = true;
				$this->error = '进货单发货数量为0，请检查是否已设置了发货数量！';
				return false;
			}


			// 如没同步
			if ($this->tempData['order']['is_sync'] == 0) {
				//★★★ 同步到ERP ★★★
				// if ($this->syncErp($this->data['id'])) {
				// 	// 同步成功，标记已同步
				// 	$this->is_sync = 1;
				// 	if (!$this->save()) {
				// 		$this->isExit = true;
				// 		$this->error = '更新收货状态失败';
				// 		return false;
				// 	}
				// }
			}
			
		}



		// 如果是确认收货
		if (!empty($this->data['status']) && $this->data['status'] == self::STATUS_RECEIPT) {

			// 如没有收货
			if ($this->tempData['order']['is_receipt'] == 0) {

				// ★★★ 店铺收货 ★★★
				if (!$this->receipt($this->data['id'])) {
					$this->isExit = true;
					return false;
				}

				// 收货成功，更新收货标记字段 is_receipt
				$this->is_receipt = 1;
				$this->receipt_time = date("Y-m-d H:i:s");
				if (!$this->save()) {
					$this->isExit = true;
					$this->error = '更新收货状态失败';
					return false;
				}
				
			}
		}


	}

	public function oneBefore(){
		// 获取状态名称和进货单类型
		$typeName = "(CASE a.shop_depot_type WHEN 1 THEN '销售' WHEN 2 THEN '物料' END)";
		// 状态名称
		$statusName = $this->getStatusSql('a');

		// 查找店铺与员工信息
		$this->alias('a')
			 ->field("a.*,ss.staff_name,hs.name shop_name,{$typeName} type_name,{$statusName} status_name,sdo.status differ_status")
			 ->join('shop_staff ss', 'a.staff_id = ss.id')
			 ->join('hea_center.shop hs', 'hs.id = ss.shop_id')
			 ->join('shop_differ_order sdo', 'a.id = sdo.shop_purchase_order_id', 'LEFT');

		// pe($this->fetchSql()->find());
	}

	public function oneAfter(){
		// pe($this->getLastSql());
	}

	/**
	 * [receipt 确认收货]
	 * @return [type] [description]
	 */
	public function receipt($id){

		// 查找商品项
		$spiModel = new ShopPurchaseItem();

		$items = $spiModel->where('shop_purchase_order_id', $id)->select();

		if (!$items) {
			$this->error = '没有商品项';
			return false;
		}

		// pe(modelList2Array($items));

		// 店铺库存
		$shopDepot = new ShopDepot();

		// 进货单类型
		$type = $this->tempData['order']['shop_depot_type'];

		// 入库
		foreach ($items as $k => $v) {

			// 实发数量为0的跳过
			if ($v->real_quantity == 0) {
				continue;
			}

			// 1.将实发数量，设置为可用数量
			$v->availavle_quantity = $v->real_quantity;

			if (!$v->save()) {
				$this->error = '设置可用数量失败';
				return false;
			}

			// 2. 入库操作
			$res = $shopDepot->inventoryOperation(
								$this->data['order_sn'],
								ShopDepotChange::TYPE_SHOP_PURCHASE ,
								$this->data['shop_id'],
								$v->stock_id,
								$v->real_quantity, 
								NULL, 
								$type);

			if (!$res) {
				// 回滚
				// $this->rollback();
				$this->error = '入库失败';
				return false;
			}

		}

		// 提交事务
		// $this->commit();
		
		return true;
	}


	/**
	 * [syncErp 同步订单到ERP]
	 * @param  [type] $order_id [订单id]
	 * @return [type]           [description]
	 */
	public function syncErp($order_id){
		// 获取订单信息
		if (!$order = $this->where('id', $order_id)->find()) {
			$this->error = '订单不存在';
			return false;
		}

		// 查询店铺
		$shop = new Shop();
		$shop->getOne($order['shop_id']);

		// 商品项
		$itemModel = new ShopPurchaseItem();
		$items = $itemModel->where('shop_purchase_order_id', $order_id)->select();
		// pe($itemModel->getLastSql());

		if (!$items) {
			$this->error = '当前订单没有商品项';
			return false;
		}

		// 模型转换为数组
		$items = modelList2Array($items);

		/**
		2019-02-14 还未完成
		商品项的real_quantity还未设置
		 */	
		$goods = array();
        foreach ($items as $v) {
        	// 查找商品sku
        	$stock = \think\Db::table('stock')->where('id', $v['stock_id'])->find();

        	if (!$stock) {
        		continue;
        	}

            $row['sku_sn'] = $stock['item_code'];
            $row['price'] = $v['sell_price'];
            $row['sku_prop'] = $stock['sku_code'];
            
            if (empty($v['real_quantity'])) {
            	continue;
            }

            // 此参数，待测试完成后，必须使用$v 的 real_quantity参数
            $row['num'] = $v['real_quantity'];

            $goods[] = $row;
        }


		pe($goods);
	}


}