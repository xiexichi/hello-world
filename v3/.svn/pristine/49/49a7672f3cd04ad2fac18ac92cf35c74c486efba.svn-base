<?php
namespace app\user\controller;

use app\common\Controller\Forward;

class User extends Base{

	public function login(){
		// 转发控制器
		$forward = new Forward();
		// 会员登录地址
		$loginUrl = '/user/user/login';
		
		$res = $forward->setCtrl('center_data')->setUrl($loginUrl)->goResult($this->request);
		
		// 解析失败
		if(! isset($res['code'])){
			pe($res);
		}
		
		// 解析json数据
		// 判断是否验证成功
		if($res['code'] == 0){
			// 登录成功，保存session
			session('user', $res['data']['user']);
			return successJson([], '登录成功');
		}
		
		// 返回结果
		return json($res);
	}

	public function logout(){
		// 清除session
		session(null);
		return successJson([], '登出成功');
	}
}