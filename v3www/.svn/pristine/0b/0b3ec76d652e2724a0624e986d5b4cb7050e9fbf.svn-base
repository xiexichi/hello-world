<?php

namespace app\document\validate;

class ShopDocument extends Base
{
	public function delete($ids){
		$rule = [
			'ids'=> 'require|integer',
		];

		return $this->validate($rule,$ids);
	}

	public function addBefore($data){
		$rule = [
			'shop_id' => 'require|integer',
			'desc' => 'min:0',
			'document_path' => 'min:0',
		];

		return $this->validate($rule,$data);
	}

	public function editBefore($data){
		$rule = [
			'id' =>'require|integer',
			'shop_id' => 'require|integer',
			'desc' => 'min:0',
			'document_path' => 'min:0',
		];
		return $this->validate($rule,$data);
	}

}
