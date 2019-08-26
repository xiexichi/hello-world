<?php

namespace app\depot\controller;

class ShopReturnItem extends Base
{

	public function indexBefore(){

		$params = input();

		// 只获取对应进货单的商品项
		if (!empty($params['shop_return_order_id'])) {
			$this->model->where('shop_return_order_id', $params['shop_return_order_id']);
		}

		// 商品代码
		if (!empty($params['item_code'])) {
			$this->model->where('s.item_code', 'like', "%{$params['item_code']}%");
		}		

		// 规格
		if (!empty($params['sku_code'])) {
			$this->model->where('s.sku_code', $params['sku_code']);
		}

		// 打印查询sql
		// $this->model->tempData['print_sql'] = true;
	}


	/**
	 * [batchEdit 批量修改]
	 * @return [type] [description]
	 */
	public function batchEdit(){

		if (!$checkData = $this->validate->batchEdit(input())) {
			return errorJson(80001, $this->validate->getError());
		}

		// 获取订单信息
		// 店铺进货单模型
		$spoModel = new \app\depot\model\ShopPurchaseOrder();

		$order = $spoModel->where('id', $checkData['shop_return_order_id'])->find();

		if (!$order) {
			return errorJson(80001, '进货单不存在');
		}

		// 如果请求端的key不是25boy总后台，并且编辑商品项被锁定了
		if ( ($this->getHeader('key') != self::REQUEST_ADMIN_KEY) && $order['is_lock']) {
			return errorJson(80002, '编辑商品被锁定了，入需编辑请联系总公司相关人员！');
		}

		// 批量修改
		if ($this->model->data($checkData)->batchEdit()) {
			return successJson();
		}

		return errorJson(80001, $this->model->getError());
	}



}