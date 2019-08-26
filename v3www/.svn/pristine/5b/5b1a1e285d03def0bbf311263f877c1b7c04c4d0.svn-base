<?php

/**
 * 店铺库存变动记录
 */

namespace app\depot\model;


class ShopDepotChange extends Base
{

	

	// 销售操作类型
	const TYPE_SALES_SELL = 1;	    // 销售卖出
	const TYPE_SALES_RETURN = 2;	// 销售退货
	const TYPE_SALES_SWAP_IN = 3;	// 销售换入
	const TYPE_SALES_SWAP_OUT = 4;	// 销售换出


	// 实体店铺操作类型

	// 进货
	const TYPE_SHOP_PURCHASE = 5;	// 店铺进货
	
	// 退货
	const TYPE_SHOP_RETURN = 6;	    // 店铺退货
	const TYPE_SHOP_RETURN_WITH_HOLD = 61;   // 店铺退货预扣
	const TYPE_SHOP_RETURN_WITH_REVERSE = 62;   // 店铺退货返还

	// 调货
	const TYPE_SHOP_TRANSFER = 7;   // 店铺调货
	const TYPE_SHOP_TRANSFER_WITH_HOLD = 71;   // 店铺调货预扣
	const TYPE_SHOP_TRANSFER_WITH_REVERSE = 72;   // 店铺调货返还

	// 调整
	const TYPE_SHOP_ADJUST = 8;   // 店铺调整单
	const TYPE_SHOP_ADJUST_WITH_HOLD = 81;   // 店铺调整预扣
	const TYPE_SHOP_ADJUST_WITH_REVERSE = 82;   // 店铺预扣返还

	// 差异
	const TYPE_SHOP_DIFFER = 9;   // 店铺进货差异单
	const TYPE_SHOP_DIFFER_WITH_HOLD = 91;   // 店铺进货差异预扣
	const TYPE_SHOP_DIFFER_WITH_REVERSE = 92;   // 店铺进货预扣返还


	/**
	 * [getWithAndReverse 获取预扣和预扣返还库存变动类型]
	 * @return [type] [description]
	 */
	static public function getWithAndReverse(){
		return [
			self::TYPE_SHOP_RETURN_WITH_HOLD,
			self::TYPE_SHOP_RETURN_WITH_REVERSE,
			self::TYPE_SHOP_TRANSFER_WITH_HOLD,
			self::TYPE_SHOP_TRANSFER_WITH_REVERSE,
			self::TYPE_SHOP_ADJUST_WITH_HOLD,
			self::TYPE_SHOP_ADJUST_WITH_REVERSE,
			self::TYPE_SHOP_DIFFER_WITH_HOLD,
			self::TYPE_SHOP_DIFFER_WITH_REVERSE,
		];
	}


	public function indexBefore(){

		// 商品代码
		if (!empty($this->data['sku_sn'])) {
			$this->where('s.item_code', 'like', "%{$this->data['sku_sn']}%");
		}

		// 规格
		if (!empty($this->data['sku_code'])) {
			$this->where('s.sku_code', 'in', $this->data['sku_code']);
		}

		// 店铺商品类型
		if (!empty($this->data['type'])) {
			$this->where('sd.type', $this->data['type']);
		}		

		// 订单号
		if (!empty($this->data['order_sn'])) {
			$this->where('sdc.order_sn', $this->data['order_sn']);
		}		

		// 开始时间
		if (!empty($this->data['start_date'])) {
			$this->where("DATE(sdc.create_time) >= '{$this->data['start_date']}'");
		}

		// 变动类型
		if (!empty($this->data['shop_depot_change_type_id'])) {
			$this->where('sdc.shop_depot_change_type_id', $this->data['shop_depot_change_type_id']);
		}

		// 店铺商品类型
		$shopDepotType = "(CASE sd.type = 1 WHEN TRUE THEN '商品' ELSE '物料' END)";

		// 联表
		$this->alias('sdc')
			 ->field("sdc.*,sdct.type change_type,hcs.name shop_name,s.item_code,s.sku_code,{$shopDepotType} shop_depot_type")
			 ->join('shop_depot_change_type sdct', 'sdc.shop_depot_change_type_id = sdct.id')
			 ->join('shop_depot sd', 'sd.id = sdc.shop_depot_id')
			 ->join('stock s', 's.id = sd.stock_id')
			 ->join('hea_center.shop hcs', 'hcs.id = sdc.shop_id');

		// 打印sql
		// $this->tempData['print_sql'] = true;

	}





}


