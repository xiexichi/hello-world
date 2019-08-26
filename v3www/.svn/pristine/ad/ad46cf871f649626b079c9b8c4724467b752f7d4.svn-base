<?php
/**
 * 生成二唯码
 */
namespace app\picshow\model;

use think\Db;
use app\common\model\CommonModel;
use app\apps\model\AppThird;
use app\common\library\Wechat;

class Qrcode extends CommonModel
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';
	// 错误信息
	public $error; 
	// 图片保存路径
	private $tempPath = TEMP_PATH . 'qrcode/';

	// 一对一关联应用表
	public function appThird()
  {
		return $this->hasOne('app\apps\model\AppThird', 'id', 'third_app_id');
  }


  /**
   * 保存数据
   */
  public function setData($data)
  {
  	$this->data = $data;
  }

  /**
   * 生成二唯码
   * @param $appType [string] 应用类型：小程序/微信
   * @param $codeType [string] 二唯码类型：临时码/永久码
   */
  public function createQrcode()
  {
  	// 根据third_app_id找出二唯码类型
  	if(empty($this->data['third_app_id'])) {
  		$appType = '';
  	}else{
  		$app = AppThird::get($this->data['third_app_id'])->getData();
  		$appType = isset($app['type']) ? $app['type'] : '';
  	}

  	// 取出模型数据
  	switch ($appType) {
  		case 'weapp':
  			// 小程序
	  		return $this->weappCode($app);
  			break;
  		
  		default:
  			// 普通二唯码
  			return $this->phpQrcode();
  			break;
  	}
  }

  /**
   * 生成小程序码
   * https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/qr-code.html
   * 接口A: 适用于需要的码数量较少的业务场景
   * 接口B：适用于需要的码数量极多，或仅临时使用的业务场景
   * 接口C: 获取小程序二维码，适用于需要的码数量较少的业务场景
   */
  public function weappCode($app = NULL)
  {
  	if(empty($this->data['third_app_id']) || empty($this->data['url'])){
  		$this->error = 'third_app_id和url参数不能为空';
  		return false;
  	}
  	if(empty($this->data['code_type'])){
  		$this->error = '二唯码类型必须选择';
  		return false;
  	}

  	// 整理数据
    $fileName = md5($this->data['url']);
    $url = $this->data['url'];
    $width = !empty($this->data['width']) ? $this->data['width'] : 600;
    $optional = ['width' => $width, 'auto_color' => true];

    // 实例化微信类
		$wechat = new Wechat($app);
		switch ($this->data['code_type']) {
			case 'A':
				$response = $wechat->init->app_code->get($url, $optional);
				break;

			case 'B':
				// 拆分url
				$urls = parse_url($url);
				if(empty($urls['query'])) {
					$this->error = '临时码需要传入参数';
					return false;
				}
				if(mb_strlen($urls['query']) >= 32) {
					$this->error = '临时码参数长度不能超出32位';
					return false;
				}
				$optional['path'] = $urls['path'];
				$response = $wechat->init->app_code->getUnlimit($urls['query'], $optional);
				break;

			case 'C':
				$response = $wechat->init->app_code->getQrCode($url, $width);
				break;

			default:
				# code...
				break;
		}

		// 保存小程序码到文件
		if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
			$response->save(TEMP_PATH . 'qrcode', $fileName .'.jpg');
			return $this->base64EncodeImage($this->tempPath . $fileName .'.jpg');
		}
  }


  /**
   * 生成普通二唯码
   * @param logo 二唯码中心图片，绝对路径
   * @param size 图片大小，默认20
   * @param level 容错率，默认 [L,M,Q,H]
   */
  public function phpQrcode()
  {
  	if(empty($this->data['url'])) {
    	$this->error = '二唯码内容不能为空';
    	return false;
    }
    // 整理数据
    $content = $this->data['url'];
    $fileName = md5($this->data['url']);
    $logo = !empty($this->data['logo']) ? ROOT_PATH.getSystemSet('QRCODE_CENTER_LOGO') : '';
    $size = !empty($this->data['size']) ? $this->data['size'] : 20;
    $level = !empty($this->data['level']) ? $this->data['level'] : 'H';

    // 创建目录
    if (!is_dir($this->tempPath)) {
        mkdir($this->tempPath, 0777, true);
    }
    $originalUrl = $this->tempPath . $fileName.'.png';

    // phpqrcode类
    Vendor('phpqrcode.phpqrcode');
    $object = new \QRcode();
    $errorCorrectionLevel = $level;		//容错级别
    $matrixPointSize = $size;					//生成图片大小（这个值可以通过参数传进来判断）
    $object->png($content, $originalUrl, $errorCorrectionLevel, $matrixPointSize, 1);
    
    //判断是否生成带logo的二维码
    if(file_exists($logo))
    {
      $QR = imagecreatefromstring(file_get_contents($originalUrl));        //目标图象连接资源。
      $logo = imagecreatefromstring(file_get_contents($logo));    //源图象连接资源。
      
      $QR_width = imagesx($QR);            //二维码图片宽度
      $QR_height = imagesy($QR);            //二维码图片高度
      $logo_width = imagesx($logo);        //logo图片宽度
      $logo_height = imagesy($logo);        //logo图片高度
      $logo_qr_width = $QR_width / 4;       //组合之后logo的宽度(占二维码的1/5)
      $scale = $logo_width/$logo_qr_width;       //logo的宽度缩放比(本身宽度/组合后的宽度)
      $logo_qr_height = $logo_height/$scale;  //组合之后logo的高度
      $from_width = ($QR_width - $logo_qr_width) / 2;   //组合之后logo左上角所在坐标点
      
      //重新组合图片并调整大小
      imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);
      
      //输出图片
      imagepng($QR, $originalUrl);
      imagedestroy($QR);
      imagedestroy($logo);
    }

    return $this->base64EncodeImage($originalUrl);
  }


  /**
   * 图片转换为 base64格式编码
   * 考虑api多端兼容，转base64输出
   */
	public function base64EncodeImage($image_file)
	{
		$base64_image = '';
		$image_info = getimagesize($image_file);
		$image_data = fread(fopen($image_file, 'r'), filesize($image_file));
		$base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
		return $base64_image;
	}

}