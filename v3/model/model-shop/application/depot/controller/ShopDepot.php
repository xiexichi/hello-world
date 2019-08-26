<?php

/**
 * 店铺库存
 */

namespace app\depot\controller;

use app\depot\model\ShopDepotChange;

use think\Db;

class ShopDepot extends Base
{

	/**
	 * [test 测试方法]
	 * @return [type] [description]
	 */
	public function test(){

		// 1.测试销售卖出
		// $res = $this->model->inventoryOperation('219022211475536' , ShopDepotChange::TYPE_SALES_SELL, 2, 6697, -28);

		// $this->model->startTrans();

		// 2.测试销售退货
		$res = $this->model->inventoryOperation('219022816124240' , ShopDepotChange::TYPE_SALES_RETURN, 2, 6698, 2);


		if ($res) {
			return successJson();
		}

		return errorJson(10001, $this->model->getError());
		// $this->model->batchDepotOperating(2, 4355, -9);

	}


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
			$this->model->where('g.cate_id', 'IN', $cids);
		}

		// 品牌ids
		if (!empty($params['brand_ids'])) {
			// 链接商品表
			$this->model->where('g.brand_id', 'IN', $params['brand_ids']);
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


		// 库存类型
		if (!empty($params['type'])) {
			$this->model->where('a.type', $params['type']);
		}

		// 设置查找参数
		$this->model->data($params);
	}

	/**
	 * [getStockInfo 获取库存信息]
	 * @return [type] [description]
	 */
	public function getStockInfo(){

		// 验证数据
		if (!$checkData = $this->validate->getStockInfo(input())) {
			return errorJson(10001, $this->validate->getError());
		}

		// 获取商品库存信息
		$data = $this->model->data($checkData)->getStockInfo();

		if (!$data) {
			return errorJson(10002, $this->model->getError('获取库存信息失败'));
		}

		// 返回库存信息
		return successJson($data);
	}


}