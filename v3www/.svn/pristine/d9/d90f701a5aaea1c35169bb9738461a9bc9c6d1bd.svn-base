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



/*登录数据
Array
(
    [code] => 0
    [state] => SUCCESS
    [msg] =>
    [data] => Array
    (
        [id] => 17
        [realname] => 后台管理员A
        [loginname] => adminA
        [code] => 001
        [account_type] => 2
        [merchant_id] => 0
        [status] => 1
        [last_ip] =>
        [phone] => 15487454555
        [last_time] =>
        [create_time] => 2019-04-09 10:41:10
        [update_time] => 2019-04-10 18:16:51
        [power] => Array
        (
            [actions] => Array
            (
                [picshow] => Array
                (
                    [index] => Array
                    (
                        [xcvb] => 1
                        [sdaf] => 1
                        [asdfg] => 1
                        [zxcv] => 1
                        )
                    
                    )
                
                [goods] => Array
                (
                    [goods] => Array
                    (
                        [rt] => 1
                        [index] => 1
                        [dfg] => 1
                        [hgt] => 1
                        [kist.asd.sdfg] => 1
                        [a1234] => 1
                        [c1234] => 1
                        [fg] => 1
                        [d1234] => 1
                        [j] => 1
                        )
                    
                    [categorys] => Array
                    (
                        [h] => 1
                        [dsadfas] => 1
                        [fgh] => 1
                        [df] => 1
                        [list] => 1
                        [asdas] => 1
                        [sd] => 1
                        [add] => 1
                        )
                    
                    )
                
                )
            
            [menu] => Array
            (
                [picshow] => Array
                (
                    [title] => 广告管理
                    [children] => Array
                    (
                        [index] => Array
                        (
                            [title] => 广告列表
                            [link] => /picshow/index/zxcv.html
                            )
                        
                        )
                    
                    )
                
                [goods] => Array
                (
                    [title] => 商品管理
                    [children] => Array
                    (
                        [goods] => Array
                        (
                            [title] => 商品列表
                            [link] => /goods/goods/j.html
                            )
                        
                        [categorys] => Array
                        (
                            [title] => 商品分类
                            [link] =>
                            )
                        
                        )
                    
                    )
                
                )
            
            )
        
        [role] => Array
        (
            [id] => 16
            [pid] => 0
            [title] => 后台人员
            [note] =>
            [status] => 1
            [create_user_id] => 0
            [create_time] => 2019-04-08 15:27:19
            [update_time] => 2019-04-08 15:27:19
            )
        
        )
    
    )*/