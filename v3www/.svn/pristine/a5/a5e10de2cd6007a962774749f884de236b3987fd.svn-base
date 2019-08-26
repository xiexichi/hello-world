<?php
namespace app\apps\controller;
use app\common\controller\Common;

class Base extends Common
{
	/**
	 * 添加控制器后置方法
	 */
	public function addAfter()
	{
		// 添加操作日志
		actionLogs('添加新记录', $this->model);
	}

	/**
	 * 修改控制器后置方法
	 */
	public function editAfter()
	{
		// 添加操作日志
		actionLogs('修改记录', $this->model);
	}

	/**
	 * 删除控制器后置方法
	 */
	public function deleteAfter()
	{
		// 添加操作日志
		actionLogs('删除记录', $this->model);
	}
}