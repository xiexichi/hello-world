<?php

/**
 * 商品总库存
 */

namespace app\depot\controller;

use think\Db;

class Goods extends Base
{

	public function indexBefore(){
		$params = input();

		// 如果有分类ids
		if (!empty($params['category_ids'])) {
			// pe($params['category_ids']);

			$cids = $params['category_ids'];

			// 查找下级分类
			$childrens = Db::table('goods_categorys')
								->field('GROUP_CONCAT(id) cids')
								->where('pid', 'IN', $params['category_ids'])
								->group('id > 0')
								->find();

			if ($childrens) {
				// 拼接查找的分类id
				$cids .= ',' . $childrens['cids'];
			}

			// 链接商品表
			$this->model->where('a.cate_id', 'IN', $cids);
		}

		// 品牌ids
		if (!empty($params['brand_ids'])) {
			// 链接商品表
			$this->model->where('a.brand_id', 'IN', $params['brand_ids']);
		}

		// sku_sn
		if (!empty($params['sku_sn'])) {
			$this->model->where('s.item_code', 'like', "%{$params['sku_sn']}%");
		}

		// 规格sku_code
		if (!empty($params['sku_code'])) {

			$skuCode = str_replace('，', ',', $params['sku_code']);

 			$this->model->where('s.sku_code', 'IN', $skuCode);
		}


		// 设置查找参数
		$this->model->data($params);
	}


}