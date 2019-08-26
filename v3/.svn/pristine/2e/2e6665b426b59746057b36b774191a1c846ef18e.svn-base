<?php
namespace app\index\controller;

use think\Controller;
use app\common\Controller\Forward;
use think\captcha\Captcha;

class Auth extends Controller
{

    /**
     * [login 登录]
     * 
     * @return [type] [description]
     */
    public function login()
    {
        $captcha = new Captcha();
        if(!$captcha->check(input('vercode'))){
            //return errorJson(10001,'验证码错误');
        }
       
        // 转发控制器
        $forward = new Forward();
        // 后台登录地址
        $adminLoginUrl = '/power/admin/login';
        
        $res = $forward->setCtrl('center_data')->setUrl($adminLoginUrl)->goResult($this->request); 

        // 解析失败
        if (! isset($res['code'])) {
            pe($res);
        }
        
        // 解析json数据
        // 判断是否验证成功
        if ($res['code'] == 0) {
            // 登录成功，保存session
            session('admin', $res['data']);
        }
        // 返回结果
        return $res;
    }

    /**
     * [logout 登出]
     * 
     * @return [type] [description]
     */
    public function logout()
    {
        // 清除session
        session('admin', null);
        
        return successJson([], '登出成功');
    }
}