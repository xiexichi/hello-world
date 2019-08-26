<?php

namespace app\index\controller;

use think\Controller;
use app\common\Controller\Forward;
use think\captcha\Captcha;

class Auth extends Controller
{

	/**
	 * [login 登录]
	 * @return [type] [description]
	 */
	public function login(){
		// 转发控制器
		$forward = new \app\common\Controller\Forward();

		// 登录
		// pe(input());

		// 店铺登录地址
		$shopLoginUrl = '/merchant/shop/verifyAccount';

		// 1. 店铺登录
		$res = $forward->setCtrl('center_data')->setUrl($shopLoginUrl)->goResult($this->request);

		// 解析失败
		if (!isset($res['code'])) {
			pe($res);
		}

		// 解析json数据
		// 判断是否验证成功
		if ($res['code'] == 0) {
			// 登录成功，保存session
			session('shop', $res['data']);
		}

		// 删除店铺数据
		unset($res['data']);

		// 返回结果
		return $res;
	}

	/**
	 * [logout 登出]
	 * @return [type] [description]
	 */
	public function logout(){
		// 清除session
		//session('shop', null);
		
		$path = empty(session('admin')) ? 'login' : 'shop_login';
		session(null);
		return successJson(['path'=>$path], '登出成功');
	}


	/**
	 * [staffLogin 员工登录]
	 * @return [type] [description]
	 */
	public function staffLogin(){
	    $captcha = new Captcha();
	    if(!$captcha->check(input('vercode'))){
	        //return errorJson(10001,'验证码错误');
	    }
		// 转发控制器
		$forward = new Forward();

		// 登录
		// pe(input());

		// 店铺登录地址
		$loginUrl = '/staff/staff/login';

		// 1. 店铺登录
		$res = $forward->setCtrl('shop_data')->setUrl($loginUrl)->goResult($this->request);

		// 解析失败
		if (!isset($res['code'])) {
			pe($res);
		}

		// 解析json数据
		// 判断是否验证成功
		if ($res['code'] == 0) {
			// 登录成功，保存session
			session('power', $res['data']['power']);
			unset($res['data']['power']);
			session('staff', $res['data']);
			session('shop.id',$res['data']['shop_id']);
		}

		// 删除员工数据
		unset($res['data']);

		// 返回结果
		return $res;
	}
	
	public function shopLogin(){
	    $captcha = new Captcha();
	    if(!$captcha->check(input('vercode'))){
	        //return errorJson(10001,'验证码错误');
	    }
	    // 转发控制器
	    $forward = new Forward();
	    
	    // 商户登录地址
	    $loginUrl = '/power/admin/shopLogin';
	    //商户登录
	    $res = $forward->setCtrl('center_data')->setUrl($loginUrl)->goResult($this->request);
	    
	    // 解析失败
	    if (!isset($res['code'])) {
	        pe($res);
	    }
	    
	    //商户全权
	    $powerUrl = '/power/shop_staff/getAllPower';
	    $power = $forward->setCtrl('shop_data')->setUrl($powerUrl)->goResult($this->request);

	    // 解析json数据
	    // 判断是否验证成功
	    //商户信息
	    if ($res['code'] == 0) {
	        // 登录成功，保存session
	        session('admin', $res['data']);
	    }
	    //商户权限
	    if($power['code'] == 0){
	        session('power', $power['data']);
	    }
	    

	    // 返回结果
	    return $res;
	}
}