<?php

/**
 * 员工
 */

namespace app\staff\controller;

class Staff extends Base
{

	/**
	 * [login 员工登录]
	 * @return [type] [description]
	 */
	public function login(){
		// 
	    $params = input();

		if (empty($params['username'])) {
			return errorJson(10001, '缺失参数username');
		}

		if (empty($params['password'])) {
			return errorJson(10001, '缺失参数password');
		}

		$staff = $this->model->data($params)->verifyAccount();

		if ($staff) {
			return successJson($staff);
		}

		return errorJson(10001, $this->model->getError('登录失败'));
	}


	/**
	 * [indexBefore]
	 * @return [type] [description]
	 */
	public function indexBefore(){
		$params = input();

		if (!empty($params['staff_code'])) {
			$this->model->where('a.staff_code', 'like', "%{$params['staff_code']}%");
		}

		if (!empty($params['phone'])) {
			$this->model->where('a.phone', 'like', "%{$params['phone']}%");
		}

		if (!empty($params['staff_name'])) {
			$this->model->where('a.staff_name', 'like', "%{$params['staff_name']}%");
		}

		if (isset($params['is_disable']) && $params['is_disable'] != '') {
			$this->model->where('a.is_disable', $params['is_disable']);
		}
		
		if(!empty($params['shop_id'])){
		    $this->model->where('a.shop_id', $params['shop_id']);
		}
	}


	/**
	 * [getNewStaffCode 获取员工编号]
	 * @return [type] [description]
	 */
	public function getNewStaffCode(){
		//  接收参数
		$params = input();

		if (empty($params['shop_id'])) {
			return errorJson(10001, '缺失参数shop_id');
		}

		$staffCode = $this->model->data($params)->getNewStaffCode();

		// 返回员工编号
		return successJson(['staff_code' => $staffCode]);
	}

	/**
	 * 获取店铺代码
	 * @return [type] [description]
	 */
	public function getShopCode(){

		//  接收参数
		$params = input();

		if (empty($params['shop_id'])) {
			return errorJson(10001, '缺失参数shop_id');
		}

		// 获取店铺代码
		$shopCode = $this->model->data($params)->getShopCode();

		// 返回员工编号
		return successJson(['shop_code' => $shopCode]);
	}

	
}
