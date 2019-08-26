<?php
/**
 * 25boy v3 广告列表管理
 * 2019-02-26 张文杰
 */
namespace app\picshow\controller;
use app\apps\model\AppAuth;
use app\common\library\Wechat;

class Index extends Base
{
	public function indexBefore()
	{
		$data = clean(input());
		$this->model->with(['module', 'position'])->data($data);
		$this->model->order('sort asc, id desc');

		// 查询条件 
		if(!empty($data['module_id'])) {
			$this->model->setWhere('module_id', $data['module_id']);
		}
		if(!empty($data['position_id'])) {
			$this->model->setWhere('position_id', $data['position_id']);
		}
		if(!empty($data['title'])) {
			$this->model->setWhere('title', 'LIKE', "%{$data['title']}%");
		}
		if(!empty($data['start_time'])) {
			$this->model->setWhere('start_time', 'EGT', $data['start_time']);
		}
		if(!empty($data['end_time'])) {
			$this->model->setWhere('end_time', 'ELT', $data['end_time']);
		}
		$this->model->data($data);
	}
}
