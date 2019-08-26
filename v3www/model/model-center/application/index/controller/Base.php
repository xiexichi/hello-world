<?php

namespace app\index\controller;

use app\common\controller\Common;

class Base extends Common
{
	

	public function init(){
		// 创建验证器
		$this->validate = new \app\index\validate\Base();
	}
	

}