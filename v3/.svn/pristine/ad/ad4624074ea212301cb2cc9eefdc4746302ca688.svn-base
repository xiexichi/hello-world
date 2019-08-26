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

    protected $isExit = false;

    // 允许访问控制器固定方法
    protected $allowCtrl = [
        'user' => ['add','edit']
    ];

    /**
     * [$denyCtrl 禁止访问控制器]
     * 例子： merchant => '*'
     *       表示这个模块禁止访问
     *
     *       merchant => ['shop']
     *       表示这个模块的shop控制器禁止访问
     * @var [type]
     */
    protected $denyCtrl = [
        'merchant' => '*'
    ];

    /**
     * [checkRequestMethod 检测访问方法]
     * @return [type] [description]
     */
    protected function checkRequestMethod(){

        // 获取当前url
        $url = $_SERVER['REQUEST_URI'];

        // 获取当前请求方法名称
        $action = $this->request->action();

        // 如果控制器是orders         
        $controller = strtolower($this->request->controller());

        // 模型名称
        $module = strtolower($this->request->module());

        // 验证禁止访问模块和控制器
        if (!empty($this->denyCtrl[$module])) {
            if (is_array($this->denyCtrl[$module])) {
                $denyCtrls = $this->denyCtrl[$module];

                if (in_array($controller, $denyCtrls)) {
                    $this->isExit = true;
                    return errorJson(10000,'给我滚！');
                }

            } else {
                if ($this->denyCtrl[$module] == '*') {
                    $this->isExit = true;
                    return errorJson(10000,'给我滚！');
                }
            }
        }


        // 一般固定禁止外界访问转发方法
        $denyActions = ['add','edit','all','del'];


        if (empty($this->allowCtrl[$controller])) {
            
            if (in_array($action, $denyActions)) {

                $this->isExit = true;
                return errorJson(10000,'给我滚！');
            }

        } else {
            $allowActions = $this->allowCtrl[$controller];

            if (in_array($action, $denyActions)) {
                if (!in_array($action, $allowActions)) {
                    $this->isExit = true;
                    return errorJson(10000,'给我滚！');
                }
            }
        }

    }


    
    /**
     * [go 转发]
     * @param  [type] $request [request对象]
     * @return [type]          [description]
     */
    public function go($request){

        // 检测访问方法
        $this->checkRequestMethod();

        if ($this->isExit) {
            return errorJson(10000,'给我滚！');
        }


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
            $ctrl = 'center_data';
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
    
    public function checkLogin(){
        return true;
    }
    public function checkPower(){
        return true;
    }
    
} 