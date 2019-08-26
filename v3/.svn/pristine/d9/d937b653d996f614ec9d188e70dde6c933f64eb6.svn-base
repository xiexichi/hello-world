<?php

namespace app\document\validate;

class SellerDocument extends Base
{
	public function delete($ids){
		$rule = [
			'ids'=> 'require|integer',
		];

		return $this->validate($rule,$ids);
	}

	public function addBefore($data){
		$rule = [
			'seller_id' => 'require|integer',
			'desc' => 'min:0',
			'document_path' => 'min:0',
		];

		return $this->validate($rule,$data);
	}

	public function editBefore($data){
		$rule = [
			'id' =>'require|integer',
			'seller_id' => 'require|integer',
			'desc' => 'min:0',
			'document_path' => 'min:0',
		];
		return $this->validate($rule,$data);
	}

}
