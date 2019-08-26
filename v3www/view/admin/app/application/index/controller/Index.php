<?php
namespace app\index\controller;

use app\common\controller\Common;

class Index extends Common
{
    public function index()
    {
        return $this->fetch();
    }
    
    protected function checkLogin(){
        if ($this->request->controller() == 'Index' && $this->request->action() == 'login') {
            // 如果已登录，直接转跳到操作首页
            if (session('admin')) {
                // 直接跳转到登录页
                header('Location:/index/index/index');
                exit;
            }
            return true;
        }
        return parent::checkLogin();
    }
}
