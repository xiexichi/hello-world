<?php
/**
 * 25boy v3 运费模板模块
 * 2019-01-10 张文杰
 */
namespace app\system\controller;
use think\Db;

class Region extends Base
{
	public function allBefore()
	{
		$level = input('param.level', 'int', 0);
		$this->model->setWhere('level', $level);
	}
}