<?php
/**
 * 25boy v3 日志模块
 * 2019-01-19 张文杰
 */
namespace app\system\controller;
use app\common\library\Action;

class Logs extends Base
{
	public function indexBefore()
	{
		$data = clean(input());
		if(!empty($data['module'])) {
			$this->model->setWhere('module', $data['module']);
		}
		if(!empty($data['controller'])) {
			$this->model->setWhere('controller', $data['controller']);
		}
		if(!empty($data['action'])) {
			$this->model->setWhere('action', $data['action']);
		}
		if(!empty($data['admin_id'])) {
			$this->model->setWhere('admin_id', $data['admin_id']);
		}
		if(!empty($data['start_date'])) {
			$this->model->setWhere('create_time', 'GT', $data['start_date']);
		}
		if(!empty($data['end_date'])) {
			$this->model->setWhere('create_time', 'LT', $data['end_date']);
		}
		$this->model->data($data);
		$this->model->order('id', 'desc');
	}
}