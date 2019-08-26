<?php

namespace app\depot\controller;

use app\common\controller\Common;

class Base extends Common
{
	/**
	 * [getStatus 获取订单状态]
	 * @return [type] [description]
	 */
	public function getStatus(){
		return successJson($this->model->getStatus());
	}

	/**
	 * [cancel 作废进货单]
	 * @return [type] [description]
	 */
	public function cancel(){

		// 验证数据
		if (!$checkData = $this->validate->cancel(input())) {
			return errorJson(10001, $this->validate->getError());
		}

		// 作废订单
		$res = $this->model->data($checkData)->cancel();
		
		if ($res) {
			return successJson();
		}

		// 返回错误结果
		return errorJson(10001, $this->model->getError());
	}

}