<?php

/**
 *
 * 公共模型中缺失参数统一以1开头的5位数状态码
 * 
 * 公告控制器
 */

namespace app\common\controller;

use think\Controller;
use \think\Db;
use \think\Config;
use think\Request;

class Common extends Controller
{
	// 请求对象
	// protected $request;
	protected $model;

	// 是否退出
	protected $isExit;

	protected $data;

	// 助手类
	protected $helper;

	// 服务类
    protected $service;

	// 状态码
	protected $code;
	// 错误信息
	protected $error;

	// 是否允许获取全部数据（默认禁止，如某些控制器需要操作，则设置此属性为true）
	protected $allowAll = true;
	// 是否允许删除数据（默认禁止，如某些控制器需要操作，则设置此属性为true）
	protected $allowDel = true;
	protected $allowAdd = true;
	protected $allowEdit = true;

	// 图片根目录
	// const PHOTO_ROOT = 'http://192.168.2.230:8870/upload/';
	const PHOTO_ROOT = '';

	// 客栈api请求名称
	const HOTEL_API = 'hotel-api';

	// 25后台请求的名称
	const BOY_API = '25admin-api';


	/**
	 * override
     * 构造方法
     * @access public
     * @param Request $request Request 对象
     */
    public function __construct(Request $request = null)
    {	
    	// 调用父级构造方法
    	parent::__construct();

        $this->request = is_null($request) ? Request::instance() : $request;

        // 检测登录
        //$this->checkLogin();
        //$this->checkPower();
    }

    /**
     * [checkLogin 检测登录]
     * @return [type] [description]
     */
    protected function checkLogin(){
    	// 判断是否ajax的请求
    	if ($this->request->isAjax()) {
    		if ( !session('shop') && !session('admin') && !session('staff')) {
				// 返回json数据
	    		$data = [
	    			'code' => 10000,
	    			'status' => 'ERROR',
	    			'msg' => '请先登录.'
	    		];
	    		echoJson($data);
	    		exit;
    		}
    	} else {
    		// 不阻止访问店铺登录
    		if(!session('shop')){
    		    if(strtolower($this->request->module()) != 'index' && strtolower($this->request->controller()) != 'index'){
    		        // 直接跳转到登录页
    		        header('Location:/index/index/login');
    		        exit;
    		    }
    		}
    	}
    	// 如果是员工登录
    	if (session('staff')) {
    		$staff = session('staff');
    		$this->assign('shop_id', $staff['shop_id']);
    	}    	

    	// 如果店铺登录
    	if (session('shop')) {
    		$shop = session('shop');
    		$this->assign('shop_id', $shop['id']);
    	}

    	return true;
    }
    protected function checkPower(){
        //全部转成小写
        $module = strtolower($this->request->module());
        $controller = humpToLine($this->request->controller());
        $action = strtolower($this->request->action());//全小写、带下划线
        //var_dump($module);var_dump($controller);var_dump($action);die;
        
        $whiteList = [
            '/index/index/login',
            '/index/index/shop_login',
            '/index/auth/stafflogin',
            '/index/auth/shoplogin',
            '/index/index/shop_select',
            '/index/index/no_power',
            '/index/index/index'
        ];
        $link = "/{$module}/{$controller}/{$action}";
        
        if(in_array($link, $whiteList)){
            return true;
        }

        $powerArr = session('power');
        if(empty($powerArr) || empty($powerArr['actions'])){
            $this->warningNoPower();
        }

        //验证权限
        $powerArr = $powerArr['actions'];
        
        //不验证ajax
        if(!$this->request->isAjax()){
            if(empty($powerArr[$module][$controller][$action])){
                $this->warningNoPower();
            }
        }
        return true;
    }
    protected function warningNoPower(){
        if($this->request->isAjax()){
            echoJson([
                'code'      =>  10001,
                'status'    =>  'ERROR',
                'msg'       =>  '没有权限'
            ]);
            exit;
        }
        header('Location:/index/index/no_power');
        exit;
    }

	public function _initialize(){
		parent::_initialize();

		// 实例助手类
		$this->helper = new Helper($this->request);

		// 创建服务类
        $this->service = new Service();
	}

	/**
	 * [_empty 使用空方法直接寻找视图]
	 * @return [type] [description]
	 */
	public function _empty() {

		// 获取当前url
    	$url = $_SERVER['REQUEST_URI'];

        // 获取当前请求方法名称
        $action = $this->request->action();

		// 如果控制器是orders       	
       	$controller = $this->request->controller();

       	// 模型名称
       	$module = $this->request->module();

        // pe($this->request);

       	/*============= ★Andy自定义新增，如果方法不存在，则判断视图模板是否存在 ==========*/
        // 模板名称
        $tplName = APP_PATH.'/'.$module.'/view/'.humpToLine($controller).'/'.$action.'.html';
        
        if (file_exists($tplName)) {

            // 添加公共头部
            $headerContent = file_get_contents(APP_PATH.'common/view/common/header.html');
            // 添加公共尾部
            $footerContent = file_get_contents(APP_PATH.'common/view/common/footer.html');

            // 视图
            $view = \think\View::instance(Config::get('template'), Config::get('view_replace_str'));
		
			// 输出视图
            echo $headerContent;
            echo $view->fetch($tplName, input());
            echo $footerContent;
            exit;

            // 存在则输出
            // echo $headerContent . file_get_contents($tplName);exit;
        }
        /*============= 自定义新增，如果方法不存在，则判断视图模板是否存在 ==========*/
	   
        // 转发操作
        $forward = new Forward();
        return $forward->go($this->request);
    }

	/**
	 * [getHeader 获取请求头参数]
	 * @param  string $param [单个参数名称: 不传入则返回全部请求头参数]
	 * @return [type]        [description]
	 */
	protected function getHeader($param = ''){
		if (empty($param)) {
			return $this->request->header();
		}

		$header = $this->request->header();

		if (isset($header[$param])) {
			return $header[$param];
		}

		return null;
	}	


    /**
     * [getClientTime 获取客户端时间戳]
     * @return [type] [description]
     */
    protected function getClientTime(){
    	// 目前直接返回系统时间，待后续完善
    	return time();
    }

}