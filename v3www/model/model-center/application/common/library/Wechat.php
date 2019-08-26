<?php
/**
 * 25boy v3 统一微信调用类
 * 2019-01-24 文杰
 * 基于EasyWeChat
 * 配置信息参考 https://www.easywechat.com/docs/master/official-account/configuration
 */
namespace app\common\library;
use app\apps\model\AppThird;
use EasyWeChat\Factory;

class Wechat
{
	// 微信实例
	public $init;
	// 配置
	private $config;

	/**
	 * 构造方法
	 * @param third_app_id [ID/array]，可以是ID或者配置数据
	 */
	public function __construct($third_app_id)
	{
		if(is_array($third_app_id)){
			$config = $third_app_id;
		}else{
			$config = AppThird::get($third_app_id);
		}
		if(!empty($config['type'])) {
			$this->type = $config['type'];
			$this->initApp($config);
		}
	}

	/**
	 * 自动 实例化
	 */
	private function initApp($data)
	{
		$appInitName = '_' . $this->type . 'Init';
		// 实例化
		if(objHasMethod($this, $appInitName)) {
			$this->config = [
		    'app_id' => $data['appid'],
		    'secret' => $data['secret'],
		    // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
		    'response_type' => 'array',
			];
		}
		$this->$appInitName();
	}

	/**
	 * 实例化小程序
	 */
	private function _weappInit()
	{
		$this->init = Factory::miniProgram($this->config);
	}

	/**
	 * 实例化公众号
	 */
	private function _weixinInit()
	{
		$this->init = Factory::officialAccount($this->config);
	}
}