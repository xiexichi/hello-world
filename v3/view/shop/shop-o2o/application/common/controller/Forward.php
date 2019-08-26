<?php

/**
 * 转发控制器
 */

namespace app\common\Controller;

class Forward extends Common
{

	// 转发地址类型
	protected $ctrl;

	// 请求地址
	protected $url;

	/**
	 * override
     * [checkLogin 检测登录]
     * @return [type] [description]
     */
    protected function checkLogin(){
    	// 跳过登录验证
    	return true;
    }

	// 设置转发地址类型
	public function setCtrl($ctrl){
		$this->ctrl = $ctrl;
		return $this;
	}

	// 设置请求地址
	public function setUrl($url){
		$this->url = $url;
		return $this;
	}

	/**
	 * [go 转发]
	 * @param  [type] $request [request对象]
	 * @return [type]          [description]
	 */
	public function go($request){

		// 获取请求url
		if ($this->url) {
			$url = $this->url;
		} else {
			$url = $request->url();
		}

		// 判断是否有设置请求地址
		if ($this->ctrl) {
			$ctrl = $this->ctrl;
		} else {
			// 获取请求地址
			$ctrl = $this->getHeader('ctrl');
		}

		if (!$ctrl) {
			// 设置默认为center
			$ctrl = 'center';
		}

		// 设置请求主机/域名
		$this->service->setHots($ctrl);

		// get转发
		if ($request->isGet()) {
			// pe(023);
			$res = $this->service->get($url);

		}

		// post转发
		if ($request->isPost()) {
			$res = $this->service->post($url, input());
		}

		// 转换一下json数据
		$data = json_decode($res, true);

		// 如果解析json数据失败，可能是报错
		if (!$data) {
			// 直接输出获取的数据
			echo "ctrl:{$ctrl}<br/>";
			exit($res);
		}

		return json($data);

	}

	/**
	 * [goResult 转发并返回结果]
	 * @param  [type] $request [request对象]
	 * @return [type]          [description]
	 */
	public function goResult($request){

		// 获取请求url
		if ($this->url) {
			$url = $this->url;
		} else {
			$url = $request->url();
		}

		// 判断是否有设置请求地址
		if ($this->ctrl) {
			$ctrl = $this->ctrl;
		} else {
			// 获取请求地址
			$ctrl = $this->getHeader('ctrl');
		}

		if (!$ctrl) {
			// 设置默认为center
			$ctrl = 'center';
		}

		// 设置请求主机/域名
		$this->service->setHots($ctrl);

		// get转发
		if ($request->isGet()) {
			// pe(023);
			$res = $this->service->get($url);

		}

		// post转发
		if ($request->isPost()) {
			$res = $this->service->post($url, input());
		}

		// 转换一下json数据
		$data = json_decode($res, true);

		// 解析json数据失败，可能是报错
		if (!$data) {
			// 直接返回
			return $res;
		}

		return $data;
	}



} 