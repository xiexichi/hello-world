<?php

/**
 * 登录
 */

namespace app\index\controller;

use app\common\controller\Common;

class Auth extends Common
{

	/**
	 * [login 登录]
	 * @return [type] [description]
	 */
	public function login(){
		pe(input());
	}



}