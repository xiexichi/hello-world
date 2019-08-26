<?php

namespace app\index\controller;

use think\Db;

class Area extends Base
{

	/**
	 * [init 自定义控制器初始化方法]
	 * @return [type] [description]
	 */
	public function init(){
		// 创建验证器
		$this->validate = new \app\index\validate\Area();
	}

	public function getAreas(){
		// 接收提交参数
		$param = input();

		// 验证参数
		if (!$checkData = $this->validate->getAreas($param)) {
			// 返回失败结果
			return errorJson(10001, $this->validate->getError());
		}

		// 表对象
		$t = Db::table('area');

		// 如果有上级id
		if (!empty($checkData['pid'])) {
			$t->where('pid', $checkData['pid']);
		}

		// 查找数据
		$data = $t->where('area_type', $checkData['type'])->select();

		// 返回正确结果
		return successJson($data);
	}


}