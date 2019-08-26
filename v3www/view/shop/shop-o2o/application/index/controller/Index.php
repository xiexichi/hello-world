<?php
namespace app\index\controller;

use app\common\controller\Common;

// use think\Controller;

class Index extends Common
{
	
	/**
	 * [checkLogin 检测登录]
	 * @return [type] [description]
	 */
	protected function checkLogin(){

		if ($this->request->controller() == 'Index' && $this->request->action() == 'login') {

			// 如果已登录，直接转跳到操作首页
			if (session('shop')) {
	    		// 直接跳转到登录页
	    		header('Location:/index/index/index');
	    		exit;
	    	}

			return true;
		}

		return parent::checkLogin();
	}


    public function index()
    {	
        return $this->fetch('index', input());
    }

    /**
     * [login 登录]
     * @return [type] [description]
     */
    public function login(){
    	return $this->fetch();
    }

}
