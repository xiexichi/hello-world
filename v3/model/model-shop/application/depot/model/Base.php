<?php

namespace app\depot\model;

use app\common\model\CommonModel;

class Base extends CommonModel
{

	// 商品仓代码
	const PRODUCT_WAREHOUSE_CODE = '001';

	// center数据库名称
	const CENTER_DB_NAME = 'hea_center';
	
	/**
	 * [getStatus 获取订单状态]
	 * @return [type] [description]
	 */
	public function getStatus(){
		
		// 判断是否有订单状态属性
		if (objHasPropertie($this, 'status')) {
			return $this->status;
		}

		return [];
	}

	/**
	 * [createOrderSn 创建单号]
	 * @return [type] [description]
	 */
	public function createOrderSn($perfix){

		// 截取微秒
		$microtime = str_replace('0.', '', explode(' ',microtime())[0]);

		// 截取最后4位0
		$microtime = substr($microtime, 0 , strlen($microtime) - 4); 

		return $perfix.date('YmdHis').$microtime;
	}

	/**
	 * [checkStockQuantity 检测库存数量]
	 * @param  [type] $stock_id [库存id]
	 * @param  [type] $quantity [数量]
	 * @return [type]           [description]
	 */
	public function checkStockQuantity($stock_id, $quantity){
		$stockModel = new Stock();
		
		$stock = $stockModel->where('id', $stock_id)->find();
		
		if (!$stock) {
			$this->error = '库存不存在';
			return false;
		}

		// 判断ERP可用数量是否小于查找数量
		if ($stock['salable_qty'] < $quantity) {
			$this->error = "{$stock['item_code']} {$stock['sku_name']} 可用数量：{$stock['salable_qty']}小于{$quantity}";
			return false;
		}

		return true;
	}

	/**
	 * [checkShopQuantity 检测店铺库存是否足够]
	 * @param  [type] $shop_id  [店铺id]
	 * @param  [type] $stock_id [库存id]
	 * @param  [type] $quantity [数量]
	 * @param  [type] $type 	[库存类型：默认为1，则是商品类型]
	 * @return [type]           [description]
	 */
	public function checkShopQuantity($shop_id, $stock_id, $quantity, $type = 1){
		$sdModel = new ShopDepot();
		
		// 查找店铺库存	
		$item = $sdModel->alias('a')
						->field('a.*,s.item_code,s.sku_name')
						->join('stock s', 'a.stock_id = s.id')
					    ->where('a.shop_id', $shop_id)
					    ->where('a.stock_id', $stock_id)
					    ->where('a.type', $type)
					    ->find();

		if (!$item) {
			$this->error = '库存不存在';
			return false;
		}

		// 判断ERP可销售数是否小于查找数量
		if ($item['quantity'] < $quantity) {
			$this->error = "{$item['item_code']} {$item['sku_name']} 可销售数：{$item['quantity']}小于{$quantity}";
			return false;
		}

		return true;

	}

	/**
	 * 库存管理单据通用
	 * [cancel 公用作废方法]
	 * @return [type] [description]
	 */
	public function cancel(){
		// pe($this->getTableFields());
		// pe($this->data);

		// 判断当前模型是否有status字段
		$fields = $this->getTableFields();

		// 如果有status字段
		if (in_array('status', $fields)) {
			// 设置当前模型的status为-1
			$res = $this->where('id', $this->data['id'])->update(['status' => -1]);
			
			if ($res){
				return true;
			}

			$this->error = '作废订单失败';
			return false;
		}

		return true;
	}

	/**
	 * [getStatusSql 获取状态sql]
	 * @return [type] [description]
	 */
	protected function getStatusSql($tableAlias){
		$sql = "(CASE {$tableAlias}.status ";
		foreach ($this->status as $k => $v) {
			$sql .= " WHEN {$k} THEN '{$v}'";
		}

		$sql .= ' END)';
		
		return $sql;
	}


}