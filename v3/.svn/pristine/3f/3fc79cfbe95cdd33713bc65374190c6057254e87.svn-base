<?php

namespace app\depot\controller;

class Purchase extends Base
{


	public function test(){

		$param = [
			'where' => ['id' =>  ['in', 1] ]
		];

		$url = '/merchant/shop/all';
		// $url = '/merchant/shop/index';
		$res = $this->service->setHots('center_data')->post($url, $param);

		pe($res);
	}



}