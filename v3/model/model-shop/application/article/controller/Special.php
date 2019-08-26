<?php

namespace app\article\controller;

use think\Db;

class Special extends Base
{

	public function checkSpecialName(){
		$param = input('post.');
		if (empty($param['title'])) {
			return errorJson(020101,"参数错误");
		}
		if (isset($param['id'])) {
			$where['id'] = ['<>',$param['id']];
		}
		$where['title'] = ['=',$param['title']];
		// $checkRes = $this->model->checkSpecialName($where);

		return successJson(!$this->model->checkSpecialName($where));
	}

}
