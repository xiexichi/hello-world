<?php

namespace app\depot\model;


class ShopDepotPreSelect extends Base
{

	protected $alias = 'a';

	

	public function oneBefore(){
		$a = $this->alias;
		// 查找店员信息
		$this->alias('a')
			 ->field('a.*,ss.shop_id,ss.staff_name')
			 ->join('shop_staff ss', 'ss.id = a.staff_id');
	}


	public function addBefore(){

		// 检测员工是否有重复的标签
		$tag = $this->where('staff_id', $this->data['staff_id'])->where('tag', $this->data['tag'])->find();

		if ($tag) {
			$this->isExit = true;
			$this->error = "预选标签：{$this->data['tag']} 已存在，标签名不能重复！";
		}

		// 添加创建时间
		$this->data['create_time'] = date('Y-m-d H:i:s');

	}


}