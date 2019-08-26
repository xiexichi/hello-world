<?php

/**
 * 店铺
 */

namespace app\merchant\controller;

use think\Db;

class Shop extends Base
{

	public function indexBefore(){
		$this->model->data(input());
	}

	/**
	 * [getShopTypes 获取店铺类型]
	 * @return [type] [description]
	 */
	public function getShopTypes(){
		$data = Db::table('shop_type')->order('id')->select();
		return successJson($data);
	}

	/**
	 * [getShopSaleTypes 获取店铺销售类型]
	 * @return [type] [description]
	 */
	public function getShopSaleTypes(){
		$data = Db::table('shop_sale_type')->order('id')->select();
		return successJson($data);
	}

	/**
	 * [verifyAccount 验证账号]
	 * @return [type] [description]
	 */
	public function verifyAccount(){
		// 验证数据
		if (!$checkData = $this->validate->verifyAccount(input())) {
			return errorJson(10001, $this->validate->getError());
		}

		if (!$shopData = $this->model->data($checkData)->verifyAccount()) {
			return errorJson(10001, $this->model->getError());
		}

		// 返回店铺信息
		return successJson($shopData);
	}



}
