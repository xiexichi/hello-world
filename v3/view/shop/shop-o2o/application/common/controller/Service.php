<?php

/**
 * 服务接口对象
 */

namespace app\common\controller;

use think\Controller;

class Service extends Controller{


    protected $hots = [
        // 'user' => 'http://user.25boy.cn/', // 线上用户模块主机地址
        // 'user' => 'http://user.25boy.com/', // 本地用户模块主机地址
    ];

    // 使用主机地址
    protected $useHots = '';

    protected $fiexdParams = [];

    // curl错误码
    protected $errorCode;

    // 初始化方法
    public function _initialize(){
        parent::_initialize();

        if (IS_WIN) {
            $this->hots['center'] = 'http://control-center.25boy.com/';
            $this->hots['shop'] = 'http://control-shop.25boy.com/';
            // $this->hots['user'] = 'http://user.25boy.cn/';

            // 中心数据层接口地址
            $this->hots['center_data'] = 'http://data-center.25boy.com/';
            $this->hots['shop_data'] = 'http://data-shop.25boy.com/';
        } else {
            $this->hots['center'] = 'http://control-center.25boy.cn/'; // 线上用户模块主机地址
        }

        // 添加session固定参数
        if (session('shop')) {
            // 添加店铺id
            $this->fiexdParams['shop_id'] = session('shop')['id'];
        }

        // 如果是员工登录
        if (session('staff')) {
            // 操作员工id
            $this->fiexdParams['ctrl_staff_id'] = session('staff')['id'];

            //角色id
            $this->fiexdParams['role_id'] = session('staff.shop_auth_role_id');
            //身份
            $this->fiexdParams['account_type'] = 2;//员工
        }

        // 如果是商户大佬获取商户经理之类的登录
        if(session('admin')){
            // 操作员工id
            $this->fiexdParams['ctrl_admin_id'] = session('admin.id');

            //角色id
            $this->fiexdParams['role_id'] = 0;
            //身份
            $this->fiexdParams['account_type'] = 1;//商户大佬/经理等
        }

    }

    /**
     * [addFiexdParams 添加固定参数]
     * @param array $params [必须是关联数组]
     */
    public function addFiexdParams($params = []){
        // 添加固定参数
        foreach ($params as $k => $v) {
            $this->fiexdParams[$k] = $v;
        }
    }


    /**
     * [setHots 设置使用主机]
     * @param [type] $hots_label [description]
     */
    public function setHots($hots_label) {
        if (!in_array($hots_label, array_keys($this->hots))) {
            pe('主机地址不存在');
        }
        $this->useHots = $this->hots[$hots_label];
        return $this;
    }


    /**
     * [get GET方式的请求方法]
     * @param  [type] $url    [API地址]
     * @param  [type] $params [提交参数]
     * @return [type]         [description]
     */
    public function get($url, $params = []){
        // 组合参数
        foreach ($params as $k => $v) {
            if ($k == 0) {
                $url .= '?';
            }
            $url .= '&'.$k.'='.$v;
        }

        // 如果url没有？
        if ($this->fiexdParams && strpos($url, '?') == FALSE) {
            $url .= '?';
        }

        // 添加固定参数
        foreach ($this->fiexdParams as $k => $v) {
            $url .= '&'.$k.'='.$v;
        }

        // 组合完整url
        $url = $this->useHots . trim($url,'/');

        // pe($url);

        return $this->curl_get($url);
    }

    /**
     * [post POST方式的请求方法]
     * @param  [type] $url    [description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function post($url, $params){
        // 组合完整url
        $url = $this->useHots . trim($url,'/');

        // pe($url);

        // 添加固定参数
        foreach ($this->fiexdParams as $k => $v) {
            $params[$k] =$v;
        }

        // p($url);
        // pe($params);

        return $this->curl_post($url, $params);
    }

    /**
     * 自定义curl发送post数据方法
     * @param string 上传的地址
     * @param [array] [$data] [发送的数据]
     * @return [type] [description]
     */
    protected function curl_post($url,$data){
        //开启curl
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //绕过权限验证
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch,CURLOPT_TIMEOUT,5);  //定义超时5秒钟
         // POST数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // 把post的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));    //所需传的数组用http_bulid_query()函数处理一下，就ok了

        //执行并获取url地址的内容
        $output = curl_exec($ch);
        $errorCode = curl_errno($ch);

        $this->errorCode = $errorCode;

        //释放curl句柄
        curl_close($ch);
        if(0 !== $errorCode) {
            // 记录curl错误日志
            $info = [
                'error' => 'curl请求错误：'.$errorCode,
                'url'   => $url,
                'data'  => $data
            ];
            \think\Log::write($info, 'error');
            return false;
        }

        return $output;
    }

    /**
     * 自定义curl发送get数据方法
     * @param string 上传的地址
     * @return [type] [description]
     */
    protected function curl_get($url){
        //开启curl
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        //捕获内容，但不输出
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //绕过权限验证
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        //设置get请求
        //curl_setopt($ch,CURLOPT_GET, 1);

        //执行并获取url地址的内容
        $output = curl_exec($ch);
        $errorCode = curl_errno($ch);

        $this->errorCode = $errorCode;

        //释放curl句柄
        curl_close($ch);
        if(0 !== $errorCode) {
            return false;
        }

        return $output;
    }

    /**
     * [getErrorCode 获取错误码]
     * @return [type] [description]
     */
    public function getErrorCode(){
        return $this->errorCode;
    }

}
