<?php
/**
 * 25boy v3 广告位管理
 * 2019-02-26 张文杰
 */
namespace app\picshow\controller;
use app\apps\model\AppAuth;
use app\common\library\Wechat;

class Position extends Base
{
	public function indexBefore()
	{
		$data = input();
		// 条件
		$where = [];
		if (!empty($data['platform_code'])) {
			$where['platform_code'] = $data['platform_code'];
		}
		if (!empty($data['position_name'])) {
			$where['position_name'] = ['like', "%{$data['position_name']}%"];
		}
		$this->model->where($where);
	}
}
