<?php
header('Content-Type:text/html; charset=utf-8');
require_once $_SERVER['DOCUMENT_ROOT']."/pay/wx/WxPay.Api.php";
/**
 * 
 * JSAPI支付实现类
 * 该类实现了从微信公众平台获取code、通过code获取openid和access_token、
 * 生成jsapi支付js接口所需的参数、生成获取共享收货地址所需的参数
 * 
 * 该类是微信支付提供的样例程序，商户可根据自己的需求修改，或者使用lib中的api自行开发
 * 
 * @author widy
 *
 */
class JsApiPay
{
	/**
	 * 
	 * 网页授权接口微信服务器返回的数据，返回样例如下
	 * {
	 *  "access_token":"ACCESS_TOKEN",
	 *  "expires_in":7200,
	 *  "refresh_token":"REFRESH_TOKEN",
	 *  "openid":"OPENID",
	 *  "scope":"SCOPE",
	 *  "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
	 * }
	 * 其中access_token可用于获取共享收货地址
	 * openid是微信支付jsapi支付接口必须的参数
	 * @var array
	 */
	public $data = null;
	
	/**
	 * 
	 * 通过跳转获取用户的openid，跳转流程如下：
	 * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
	 * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
	 * 
	 * @return 用户的openid
	 */
	public function GetOpenid($url,$field='openid')
	{
		//通过code获得openid
		if (!isset($_GET['code'])){
			//触发微信返回code码
			$baseUrl = urlencode($url);
			$url = $this->__CreateOauthUrlForCode($baseUrl);
			Header("Location: $url");
			exit();
		} else {
			//获取code码，以获取openid
		    $code = $_GET['code'];
			$data = $this->getOpenidFromMp($code);
			
			/*将获取用户的unionid存入session中*/
			$_SESSION['unionid'] = $this->getUnionid($data['openid']);
			//file_get_contents("http://climbweb.duapp.com/keepOpenid.php?openid=".$_SESSION['unionid']);


			if(!empty($field)){
				return isset($data[$field])?$data[$field]:'';
			}else{
				return $data;
			}
		}
	}

	/**
	 * [getUnionid 获取微信的unionid]
	 * @param  [string] [openid] [微信用户关注公众平台后的openid]
	 * @return [string] [unionid]
	 */
	public function getUnionid($openid){

		// file_put_contents('./temp/openid.json', $openid);

		// $access_token_file = './temp/access_token.json';//json文件路径

		// //获取文件内容并转换为数组
		// $access_token_data = json_decode(file_get_contents($access_token_file),true);

		// //获取access_token
		// if($access_token_data['expiration_time'] > (time()+60)){
		// 	//获取缓存文件中的access_token
		// 	$access_token = $access_token_data['access_token'];
		// }else{
		// 	//获取网络的access_token
		//     $appid = 'wx03cbfff87f584209';
  //   		$secret = '5f9503f96db96dd28e14cd93bfa95b43';
		//     /**
		//      * 注意获取unionid只能要使用appid和secret获取的access_token
		//      */
		//     $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
	 //    	$access_token = json_decode(file_get_contents($url),true)['access_token'];
		    
		//     $data = array(//json数据
		//     	'access_token' => $access_token,
		//     	'expiration_time' => (time() + 7200)
		//     );
		//     //写入缓存文件
	 //    	file_put_contents($access_token_file, json_encode($data));			
		// }

	 //    //获取unionid的API地址
	 //    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN";
	 //    $result = json_decode(file_get_contents($url),true);

	    //输出unionid
	    // file_put_contents('./temp/unionid.json', file_get_contents($url));

	    // return $result['unionid'];//返回unionid


	    $appid = 'wx03cbfff87f584209';
	    $secret = '5f9503f96db96dd28e14cd93bfa95b43';

	    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
	    $access_token = json_decode(file_get_contents($url),true)['access_token'];
	    // $access_token = json_decode(file_get_contents($url),true);

	    file_put_contents('./temp/test_access.json', json_encode($access_token));

	    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN";
	    $result = json_decode(file_get_contents($url),true);

	    file_put_contents('./temp/unionid.json', json_encode($result));

	    return $result['unionid'];
	}


	// 获取用户基础信息
	public function GetUserInfo($url)
	{
		
		//通过code获得openid
		if (!isset($_GET['code'])){
			//触发微信返回code码
			$baseUrl = urlencode($url);
			$url = $this->__CreateOauthUrlForCode($baseUrl);
			Header("Location: $url");
			exit();
		}
		
		$urlObj["access_token"] = $this->GetAccessToken();	//获取access_token
		$urlObj["openid"] = $_SESSION['openid'];
		$urlObj["lang"] = "zh_CN";
		$bizString = $this->ToUrlParams($urlObj);

		$uri = "https://api.weixin.qq.com/cgi-bin/user/info?".$bizString;

		//调用 curl
		$data = $this->GetOpenidFromMp('',$uri);
		return $data;
	}

	//获取access_token
	private function GetAccessToken(){
		$access_token_file = './temp/access_token.json';//json文件路径
		//获取文件内容并转换为数组
		$access_token_data = json_decode(file_get_contents($access_token_file),true);
		//获取access_token
		if($access_token_data['expiration_time'] > (time()+60)){
			//获取缓存文件中的access_token
			$access_token = $access_token_data['access_token'];

		}else{
			//获取access_token
			$urlObj["grant_type"] = "client_credential";
			$urlObj["appid"] = WxPayConfig::APPID;
			$urlObj["secret"] = WxPayConfig::APPSECRET;
			$bizString = $this->ToUrlParams($urlObj);		
			$uri = "https://api.weixin.qq.com/cgi-bin/token?".$bizString;
			$data = $this->GetOpenidFromMp('',$uri);

			// return isset($data['access_token'])?$data['access_token']:'';//原有代码
			
			$access_token = isset($data['access_token'])?$data['access_token']:'';//新增代码
			$data = array(//json数据
		    	'access_token' => $access_token,
		    	'expiration_time' => (time() + 7200)
		    );
		    //写入缓存文件
	    	file_put_contents($access_token_file, json_encode($data));	
	    	
		}

		return $access_token;//返回access_token
	}
	
	/**
	 * 
	 * 获取jsapi支付的参数
	 * @param array $UnifiedOrderResult 统一支付接口返回的数据
	 * @throws WxPayException
	 * 
	 * @return json数据，可直接填入js函数作为参数
	 */
	public function GetJsApiParameters($UnifiedOrderResult)
	{
		if(!array_key_exists("appid", $UnifiedOrderResult)
		|| !array_key_exists("prepay_id", $UnifiedOrderResult)
		|| $UnifiedOrderResult['prepay_id'] == "")
		{
			echo "<script language=\"javascript\">location.href = 'javascript:history.back()'</script>";
			//throw new WxPayException("参数错误");
		}
		$jsapi = new WxPayJsApiPay();
		$jsapi->SetAppid($UnifiedOrderResult["appid"]);
		$timeStamp = time();
		$jsapi->SetTimeStamp("$timeStamp");
		$jsapi->SetNonceStr(WxPayApi::getNonceStr());
		$jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
		$jsapi->SetSignType("MD5");
		$jsapi->SetPaySign($jsapi->MakeSign());
		$parameters = json_encode($jsapi->GetValues());
		return $parameters;
	}
	
	/**
	 * 
	 * 通过code从工作平台获取openid机器access_token
	 * @param string $code 微信跳转回来带上的code
	 * 
	 * @return openid
	 */
	public function GetOpenidFromMp($code,$url=null)
	{
		if(!$url){
			$url = $this->__CreateOauthUrlForOpenid($code);
		}

		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if(WxPayConfig::CURL_PROXY_HOST != "0.0.0.0"
			&& WxPayConfig::CURL_PROXY_PORT != 0){
			curl_setopt($ch,CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
		}
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		//print_r($res);
		$data = json_decode($res,true);
		$this->data = $data;
		return $data;
	}
	
	/**
	 * 
	 * 拼接签名字符串
	 * @param array $urlObj
	 * 
	 * @return 返回已经拼接好的字符串
	 */
	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			if($k != "sign"){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 
	 * 获取地址js参数
	 * 
	 * @return 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
	 */
	public function GetEditAddressParameters()
	{	
		$getData = $this->data;
		$data = array();
		$data["appid"] = WxPayConfig::APPID;
		$data["url"] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$time = time();
		$data["timestamp"] = "$time";
		$data["noncestr"] = "1234568";
		$data["accesstoken"] = $getData["access_token"];
		ksort($data);
		$params = $this->ToUrlParams($data);
		$addrSign = sha1($params);
		
		$afterData = array(
			"addrSign" => $addrSign,
			"signType" => "sha1",
			"scope" => "jsapi_address",
			"appId" => WxPayConfig::APPID,
			"timeStamp" => $data["timestamp"],
			"nonceStr" => $data["noncestr"]
		);
		$parameters = json_encode($afterData);
		return $parameters;
	}
	
	/**
	 * 
	 * 构造获取code的url连接
	 * @param string $redirectUrl 微信服务器回跳的url，需要url编码
	 * 
	 * @return 返回构造好的url
	 */
	private function __CreateOauthUrlForCode($redirectUrl)
	{
		$urlObj["appid"] = WxPayConfig::APPID;
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_base";
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = $this->ToUrlParams($urlObj);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}
	
	/**
	 * 
	 * 构造获取open和access_toke的url地址
	 * @param string $code，微信跳转带回的code
	 * 
	 * @return 请求的url
	 */
	private function __CreateOauthUrlForOpenid($code)
	{
		$urlObj["appid"] = WxPayConfig::APPID;
		$urlObj["secret"] = WxPayConfig::APPSECRET;
		$urlObj["code"] = $code;
		$urlObj["grant_type"] = "authorization_code";
		$bizString = $this->ToUrlParams($urlObj);
		return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
	}
}