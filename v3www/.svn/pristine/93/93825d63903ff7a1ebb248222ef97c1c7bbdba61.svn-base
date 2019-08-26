<?php

namespace app\merchant\controller;

use app\common\controller\Common;

use think\Db;

class Base extends Common
{

	


	/**
	 * [getMerchantTypes 获取商户类型]
	 * @return [type] [description]
	 */
	public function getMerchantTypes(){
		$types = Db::table('merchant_type')->select();
		return successJson($types);
	}


}