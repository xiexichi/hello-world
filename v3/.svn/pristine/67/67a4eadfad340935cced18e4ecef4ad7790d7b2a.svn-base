<?php

/**
 *	进货差异商品项 
 */

namespace app\depot\controller;

class ShopDifferItem extends Base
{

	/**
	 * 
	 * @return [type] [保存差异项]
	 */
	public function saveDifferItem(){
		// 验证数据
		if (!$checkData = $this->validate->saveDifferItem(input())) {
			return errorJson(10001, $this->validate->getError());
		}

		// 保存差异项
		$res = $this->model->data($checkData)->saveDifferItem();
		
		if (!$res) {
			return errorJson(10001, $this->model->getError());
		}

		// 返回数据
		$result = [];

		// 如果模型中存在店铺差异单数据
		if (!empty($this->model->tempData['shop_differ_order'])) {
			$result['shop_differ_order'] = $this->model->tempData['shop_differ_order'];
		}

		// 返回成功
		return successJson($result);
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
		// 店铺差异单模型
		$sdoModel = new \app\depot\model\ShopDifferOrder();

		$order = $sdoModel->where('id', $checkData['shop_differ_order_id'])->find();

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