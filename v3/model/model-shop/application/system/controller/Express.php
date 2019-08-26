<?php
/**
 * 25boy v3 快递公司代码
 * 2019-03-04 张文杰
 */
namespace app\system\controller;
use think\Db;

class Express extends Base
{
	public function indexBefore()
  {
    $data = clean(input());
    // 查询条件
    if( isset($data['status']) ){
      $this->model->setWhere('status', !$data['status']);
    }
    if( !empty($data['name']) ){
      $this->model->setWhere('name', 'LIKE', "%{$data['name']}%");
    }
    if( !empty($data['code']) ){
      $this->model->setWhere('code', $data['code']);
    }
    if( !empty($data['third_code']) ){
      $this->model->setWhere('third_code', $data['third_code']);
    }
    $this->model->order('id', 'asc');
    $this->model->data($data);
  }
}