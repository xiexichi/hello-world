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
			 ->join('shop_depot sd', 'sd.shop_id = sdo.shop_id AND sd.stock_id = a.stock_id AND sd.type = sdo.type')
			 ->join('goods g', 's.item_code = g.erp_code', 'LEFT')
			 ->join('goods_item gi', 'CONCAT(s.item_code, ",",s.sku_code) = gi.erp_code', 'LEFT');
		
		// 打印sql
		// $this->tempData['print_sql'] = true;
	}

}