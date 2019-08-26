<?php

/**
 * 店铺库存基类
 */

namespace app\depot\controller;

use app\common\controller\Common;

class Base extends Common
{

	public function getStock(){

		// $data = file_get_contents("http://o2o.25boy.com/business/BusinessStock/eslist?page=2&rows=10");

		// file_put_contents("stock.json", $data);

		$stock = file_get_contents("stock.json");

		$data = json_decode($stock, true);

		// pe($data);
	
		$result = [
			'code'  => 0,
			'count' => $data['total'],
			'data'  => $data['rows']
		]; 

		return json($result);                                                                                    
	}

}