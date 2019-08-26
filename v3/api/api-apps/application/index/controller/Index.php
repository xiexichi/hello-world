<?php
namespace app\index\controller;

use app\common\controller\Common;

class Index extends Common
{
    public function index()
    {

//        //获取菜单列表
        $menu = session('admin.power')['menu'];
        $adminInfo = session('admin');
        if(empty($menu)){
            return $this->fetch('index/no_power');
        }

        $this->assign('menu',$menu);
        $this->assign('adminInfo',$adminInfo);
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
