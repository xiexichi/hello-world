<?php

namespace app\merchant\model;

class Shop extends Base
{

	public function indexBefore(){
		// 表别名
		$this->alias('a');

		// 如果是显示详细信息
		if (!empty($this->data['is_detail'])) {
			$this->field('a.*,st.type shop_type,a1.area_name province,a2.area_name city,a3.area_name region')
				 ->join('area a1', 'a.province_id = a1.id')
				 ->join('area a2', 'a.city_id = a2.id')
				 ->join('area a3', 'a.region_id = a3.id')
				 ->join('shop_type st', 'a.shop_type_id = st.id', 'LEFT');
		}

		// 如果有商户id
		if (!empty($this->data['merchant_id'])) {
			// 商户id
			$this->where('a.merchant_id', $this->data['merchant_id']);
		}

		// pe($aaa);
	}



	public function addBefore(){
		// 设置添加时间
		$this->data['create_time'] = date('Y-m-d H:i:s');

		// 检测唯一字段是否重复
		if (!$this->checkRepeatFields()) {
			return false;
		}

		// 密码加密
		$this->data['passwd'] = $this->passCrypt($this->data['passwd']);
	}


	public function editBefore(){
		
		// 检测唯一字段是否重复
		if (!$this->checkRepeatFields($this->data['id'])) {
			return false;
		}

		// 密码加密
		$this->data['passwd'] = $this->passCrypt($this->data['passwd']);
	}


	/**
	 * [checkRepeatFields 检测唯一字段是否存在]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	protected function checkRepeatFields($id = NULL){
		// name
		if (!empty($this->data['name'])) {
			if ($id) {
				$this->where('id', '<>', $id);
			}

			$res = $this->where("name", $this->data['name'])->find();
			if ($res) {
				$this->isExit = true;
				$this->error = "店铺名称：{$this->data['name']} 已存在，不能重复！";
				return false;
			}
		}

		// account
		if (!empty($this->data['account'])) {
			
			if ($id) {
				$this->where('id', '<>', $id);
			}

			$res = $this->where("account", $this->data['account'])->find();
			if ($res) {
				$this->isExit = true;
				$this->error = "账号：{$this->data['account']} 已存在，不能重复！";
				return false;
			}
		}

		return true;
	}


	/**
	 * [verifyAccount 验证账号]
	 * @return [type] [description]
	 */
	public function verifyAccount(){

		// 查找店铺账号
		$shop = $this->where('account', $this->data['username'])->find();

		if (!$shop) {
			$this->error = '账号不存在！';
			return false;
		}

		// 验证密码
		if ($this->passCrypt($this->data['password']) != $shop['passwd']) {
			$this->error = '账号或密码错误！';
			return false;
		}

		return $shop;
	}

	/**
	 * 查看一堆shop_id是不是都属于某商户下的
	 * @param unknown $shopIDArr [1,2,3]
	 * @param unknown $merchantID
	 * @return boolean
	 */
	public function isShopsAllInMerchant($shopIDArr,$merchantID){
	    $shopCount = count($shopIDArr);
	    $shopIDs = implode(',', $shopIDArr);
	    
	    $count = $this->where('merchant_id',$merchantID)->where('id','IN',$shopIDs)->value('COUNT(id)');
	    return $shopCount == $count ? true : false;
	}
	
	public function getShopsByMerchantID($merchantID){
	    return $this->field('id,name')->where(['merchant_id'=>$merchantID,'status'=>1])->select();
	}
	public function getShopsByAdminID($adminID){
	    return $this->field('id,name')->where('status',1)->where('id','IN',function($q) use($adminID){
                	        $q->table('admin_shop')->where('admin_id',$adminID)->field('shop_id');
                	    })->select();
            
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

}