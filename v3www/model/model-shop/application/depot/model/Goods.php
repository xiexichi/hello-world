<?php

/**
 * 商品总库存
 */

namespace app\depot\model;

class Goods extends Base
{

	public function indexBefore(){

		// 将提交参数保存到临时数据中
		$this->tempData = $this->data;

		// 查找商品图片地址
		// $image = "(SELECT image FROM goods_images WHERE a.id = goods_id LIMIT 1) image";


		$image = "(SELECT item_img FROM goods_item WHERE erp_code = CONCAT(s.item_code,',',s.sku_code) LIMIT 1) image";

		// 查找stock表的颜色
		$this->alias('a')
			 ->field("a.*,s.product_color_id,pc.color,{$image}")
			 ->join('stock s', 'a.erp_code = s.item_code')
			 ->join('product_color pc', 's.product_color_id = pc.id')
			 ->where('s.warehouse_code', self::PRODUCT_WAREHOUSE_CODE)
			 ->group('a.erp_code, s.product_color_id');


		// $this->tempData['print_sql'] = true;
	}


	public function indexAfter(){

		// pe($this->tempData);
		$shopDepotPreSelectId = NULL;

		// 如果有
		if (!empty($this->tempData['shop_depot_pre_select_id'])) {
			$shopDepotPreSelectId = $this->tempData['shop_depot_pre_select_id'];
		}

		// 店铺id
		$shopId = NULL;

		if (!empty($this->tempData['shop_id'])) {
			$shopId = $this->tempData['shop_id'];
		}

		// 查找商品库存
		// pe($this->getLastSql());
		$stockModel = new Stock();

		// 设置查找采纳数
		$param = ['is_purchase' => true];

		foreach ($this->data as $k => $v) {

			$stocks = $stockModel->data($param)->getItems($v['erp_code'], $v['product_color_id'], $shopId, 'size', $shopDepotPreSelectId);

			// 查找商品库存
			$this->data[$k]['stocks'] = $stocks;
		}

	}




}
