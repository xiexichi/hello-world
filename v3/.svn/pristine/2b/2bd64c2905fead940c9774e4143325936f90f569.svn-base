<?php

namespace app\document\controller;

use think\Exception;
use think\db;

class Seller extends Base
{	
	// 会员列表
	public function allAfter(){
		// 获取会员名
		if (!empty($this->data)) {
			$user = new \app\user\model\User();
			foreach ($this->data as $k => $v) {
				$user_name = $user->where('id',$this->data[$k]['user_id'])->field('user_name')->find();
				$this->data[$k]['user_name'] = $user_name['user_name'];
			}
		}
	}
}	