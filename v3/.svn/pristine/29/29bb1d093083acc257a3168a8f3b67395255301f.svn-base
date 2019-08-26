<?php

/**
 * 店铺库存预选项
 */

namespace app\depot\model;


class ShopDepotPreSelectItem extends Base
{

	public function getAllBefore(){
		// 如果是查找详情
		if ($this->tempData['one_detail']) {
			$this->alias('a')
				 ->field("a.*,gi.item_price,g.market_price,g.sell_price")
				 ->join('stock s', 'a.stock_id = s.id')
				 ->join('goods_item gi', 'CONCAT(s.item_code, ",",s.sku_code) = gi.erp_code')
				 ->join('goods g', 'gi.goods_id = g.id');
		}
	}


	public function indexBefore(){

		// pe($aaa);
		
		// 查找商品图片地址
		// $image = "(SELECT image FROM goods_images WHERE g.id = goods_id LIMIT 1) image";

		$image = "(SELECT item_img FROM goods_item WHERE erp_code = CONCAT(s.item_code,',',s.sku_code) LIMIT 1) image";

		// 查找商品
		$this->alias('a')
			 ->field("g.*,{$image},pc.color,a.shop_depot_pre_select_id,GROUP_CONCAT(a.stock_id) stock_ids")
			 ->join('stock s', 's.id = a.stock_id')
			 ->join('product_color pc', 's.product_color_id = pc.id')
			 ->join('goods g', 's.item_code = g.erp_code')
			 ->group('g.erp_code,s.product_color_id');

		// 打印sql
		// $this->tempData['print_sql'] = true;
	}

	/**
	 * [indexAfter ]
	 * @return [type] [description]
	 */
	public function indexAfter(){

		foreach ($this->data as $k => $v) {
			
			
			$preSelect = $this->alias('a')
							  ->field('a.*,s.*,a.id,sdps.shop_depot_type,IFNULL(sd.quantity,0) shop_quantity')
							  ->join('shop_depot_pre_select sdps', 'sdps.id = a.shop_depot_pre_select_id')
							  ->join('shop_staff ss', 'ss.id = sdps.staff_id')
							  ->join('stock s', 's.id = a.stock_id')
							  ->join('shop_depot sd', 'sd.stock_id = a.stock_id AND sd.shop_id = ss.shop_id AND sd.type = sdps.shop_depot_type', 'LEFT')
							  ->where('a.shop_depot_pre_select_id', $v['shop_depot_pre_select_id'])
				 			  ->where('a.stock_id', 'in', $v['stock_ids'])
				 			  ->group('a.stock_id')
				 			  ->select();


			// pe($this->getLastSql());

			$this->data[$k]['pre_select'] = $preSelect;
		}
	}

	
	public function add(){

		// 1.检查是否有预选标记 shop_depot_pre_select_id 是否存在
		$sdpModel = new ShopDepotPreSelect();
		if (!$sdpModel->find($this->data['shop_depot_pre_select_id'])) {
			$this->error = '预选标记不存在';
			$this->isExit = true;	// 标记退出，不继续向下执行
			return false;
		}

		// 设置查找条件
		$this->tempData['where'] = [
			'stock_id' => $this->data['stock_id'],
			'shop_depot_pre_select_id' => $this->data['shop_depot_pre_select_id']
		];
		
		// 2.修改或新增
		return $this->editOrAdd();
	}


}