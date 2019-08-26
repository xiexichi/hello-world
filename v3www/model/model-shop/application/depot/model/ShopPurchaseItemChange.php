<?php

/**
 * 批次变动记录
 */

namespace app\depot\model;

class ShopPurchaseItemChange extends Base
{


	public function addBefore(){
		// 变动时间
		$this->data['create_time'] = date('Y-m-d H:i:s');
	}



}