<?php

/**
 * 店铺库存变动记录
 */

namespace app\depot\controller;

use \think\Db;

class ShopDepotChange extends Base
{

	/**
	 * [getChangeTypes 获取变动类型]
	 * @return [type] [description]
	 */
	public function getChangeTypes(){
		$changeTypes = Db::table('shop_depot_change_type')->select();
		return successJson($changeTypes);
	}


}