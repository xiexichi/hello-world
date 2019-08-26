<?php

namespace app\merchant\model;

class Merchant extends Base
{
	public function indexBefore(){
		// 如果是显示详细信息
		if (!empty($this->data['is_detail'])) {
			$this->alias('a')
				 ->field('a.*,mt.type merchant_type,a1.area_name province,a2.area_name city,a3.area_name region')
				 ->join('area a1', 'a.province_id = a1.id')
				 ->join('area a2', 'a.city_id = a2.id')
				 ->join('area a3', 'a.region_id = a3.id')
				 ->join('merchant_type mt', 'a.merchant_type_id = mt.id');
		}
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
				$this->error = "商户名称：{$this->data['name']} 已存在，不能重复！";
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


}