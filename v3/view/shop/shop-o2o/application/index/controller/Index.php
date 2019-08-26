<?php
namespace app\index\controller;

use app\common\controller\Common;

class Index extends Common
{

    /**
     * [checkLogin 检测登录]
     *
     * @return [type] [description]
     */
    protected function checkLogin(){
        if ($this->request->url() == '/index/index/login' || $this->request->url() == '/index/index/shop_login'){

            // 如果已登录，直接转跳到操作首页
            if (session('shop')) {
                // 直接跳转到登录页
                header('Location:/index/index/index');
                exit();
            }

            return true;
        }

        return parent::checkLogin();
    }

    public function index(){
        $userInfo = [];
        if(!empty(session('admin'))){//商户大佬/经理登录
            $userInfo['name'] = session('admin.realname');
        }else{
            $userInfo['name'] = session('staff.staff_name');
        }

        // 获取菜单列表
        $menu = session('power.menu');
        if (empty($menu)) {
            header('Location:/index/index/login');
            exit;
            //return $this->fetch('index/no_power');
        }

        $this->assign('menu', $menu);
        $this->assign('userInfo', $userInfo);
        return $this->fetch('index', input());
    }

    public function selectShop(){
        if(empty(session('admin'))){//不是商户大佬/经理登录
            return errorJson(10001, '非商户登录');
        }
        $shopID = input('shop_id');
        if(empty($shopID)){
            return errorJson(10001, '参数错误');
        }
        session('shop.id',$shopID);
        //直接跳转
        return successJson('操作成功');
    }
}
