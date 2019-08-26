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
    protected $allowIndex = false;
    protected $allowOne = false;
    // 是否允许删除数据（默认禁止，如某些控制器需要操作，则设置此属性为true）
    protected $allowAll = false;
    protected $allowDel = false;
    protected $allowAdd = false;
    protected $allowEdit = false;


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
        // 生成图片空间token
        createJWTtoken();
        
        $this->request = is_null($request) ? Request::instance() : $request;

        // 设置固定参数
        $this->setFixedParams();

    	// 调用父类构造方法
        parent::__construct();
        $this->checkLogin();
    }
    
    protected function checkLogin(){
    	if(!session('user') || !session('user.id')){
    		$data = [
    			'code' => 10000,
    			'status' => 'ERROR',
    			'msg' => '请先登录'
    		];
    		echoJson($data);
    		exit;
    	}
    	return true;
    }
    /**
     * [setFixedParams 设置固定参数]
     */
    protected function setFixedParams(){
    }


	public function _initialize(){
		parent::_initialize();

		// 实例助手类
		$this->helper = new Helper($this->request);

        // 创建服务类
        $this->service = new Service();
	   
        // 执行初始化方法
        $this->init();
    }

    // 初始化方法
    protected function init(){}

	/**
	 * [_empty 使用空方法直接寻找视图]
	 * @return [type] [description]
	 */
	public function _empty() {

		
        
  
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