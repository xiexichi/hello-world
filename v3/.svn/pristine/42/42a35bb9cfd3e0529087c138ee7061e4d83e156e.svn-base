<?php

namespace app\staff\model;

use app\power\model\ShopStaff;

class Staff extends Base{

	// 表名
	protected $name = 'shop_staff';


	public function indexBefore(){
		// 查找角色名称
		$this->alias('a')
			 ->field('a.*,pr.title')
			 ->join('power_role pr', 'a.shop_auth_role_id = pr.id');
	}

	
	public function addBefore(){

		// 检测员工编号是否存在
		$staffModel = new Staff();
		$satff = $staffModel->where('staff_code', $this->data['staff_code'])->find();
		if ($satff) {
			// 重新获取员工代码
			$this->data['staff_code'] = $this->getNewStaffCode();
		}

		// 检测员工账号是否存在
		if (!$this->checkAccountUnique()) {
			$this->isExit = true;
			return false;
		}

		// 创建时间
		$this->data['create_time'] = date('Y-m-d H:i:s');

		// 密码加密
		$this->data['password'] = $this->passCrypt($this->data['password']);
	}


	public function editBefore(){

		// 检测员工账号是否存在
		if (!$this->checkAccountUnique($this->data['id'])) {
			$this->isExit = true;
			return false;
		}

		if (isset($this->data['password']) && $this->data['password'] == '') {
			unset($this->data['password']);
		} else {
			// 密码加密
			$this->data['password'] = $this->passCrypt($this->data['password']);
		}
		
		// 删除员工代码
		if (isset($this->data['staff_code'])) {
			unset($this->data['staff_code']);
		}

	}


	public function oneAfter(){
		// 员工账号
		$this->data['staff_account'] = str_replace($this->getShopCode(), '', $this->data['staff_account']);
	}


	/**
	 * [checkAccountUnique 检测员工账号是否唯一]
	 * @return [type] [description]
	 */
	protected function checkAccountUnique($exclude_id = NULL){
		// 检测员工账号是否存在
		$staffModel = new Staff();
		
		// 如果有排除id
		if ($exclude_id) {
			$staffModel->where('id', 'not in', $exclude_id);
		}

		$satffAccount = $staffModel->where('staff_account', $this->data['staff_account'])->find();
		if ($satffAccount) {
			
			$this->error = '员工账号已存在，请修更换新的账号';
			return false;
		}
		return true;
	}


	/**
	 * [getShopStaffCode 获取店员工编号]
	 * @return [type] [description]
	 */
	public function getShopStaffCode(){
		// 店铺最新员工
		$staffCount = $this->where('shop_id', $this->data['shop_id'])->count();

		// 店员工编号
		$shopStaffCode = $staffCount + 1;

		// 店铺id补0
		for ($i = 3; $i > strlen($staffCount); $i--) {
			$shopStaffCode = '0' . $shopStaffCode;
		}

		return $shopStaffCode;
	}

	/**
	 * [getShopCode 获取店铺代码]
	 * @return [type] [description]
	 */
	public function getShopCode(){
		// 店铺id取4位
		$shopCode = $this->data['shop_id'];
		// 店铺id补0
		for ($i = 4; $i > strlen($this->data['shop_id']); $i--) {
			$shopCode = '0' . $shopCode;
		}

		return $shopCode;
	}

	/**
	 * [getNewStaffCode 获取新员工编号]
	 * @return [type] [description]
	 */
	public function getNewStaffCode(){
		// 店铺最新员工
		
		/**
		 * 创建8位数的员工编号
		 * 年月日 + 店铺id + 员工顺序
		 */

		// 获取员工顺序
		$staffNum = $this->getShopStaffCode();

		$date = date('ymd');

		// 店铺id取4位
		$shopCode = $this->getShopCode();

		// 组合员工编号
		$staffCode = $date . $shopCode . $staffNum;
			
		// 返回员工编号
		return $staffCode;
	}

	/**
	 * [verifyAccount 验证账号]
	 */
	public function verifyAccount(){

		// pe($this->data);

		$staff = $this->where('staff_account', $this->data['username'])->find();
		
		
		if (!$staff) {
			$this->error = '账号不存在';
			return false;
		}

		// 验证密码
		if ($this->passCrypt($this->data['password']) != $staff['password']) {
			$this->error = '账号或密码错误';
			return false;
		}
		unset($staff['password']);

		//获取权限和菜单列表
		$power = (new ShopStaff())->getAdminPower($staff['shop_auth_role_id']);
		$staff['power'] = $power;
		
		return $staff;
	}
	


}