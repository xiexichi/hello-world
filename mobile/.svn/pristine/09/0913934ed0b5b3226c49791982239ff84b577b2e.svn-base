<?php
class Curl
{
	private $baseUrl = 'http://api.25boy.cn/';

	public function __construct(){
		
	}

	private function getUrl($url, $params)
	{
		$arr = explode('/', $url);
		$query = http_build_query($params);
		return $this->baseUrl . "index.php?c={$arr[0]}&a={$arr['1']}" . ($query ? '&'.$query : '');
	}

	/**
	 * 自定义curl发送get数据方法
	 * @param string 上传的地址
	 * @return [type] [description]
	 */
	public function get($url, $params = [])
	{
		$apiurl = $this->getUrl($url, $params);

    //开启curl
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $apiurl);
    //捕获内容，但不输出
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    //绕过权限验证
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    //设置get请求
		// curl_setopt($ch, CURLOPT_GET, 1);
    //发送get请求
    $output = curl_exec($ch);

    if($output === false){
    		$error = curl_error($ch);
    }
    
		//关闭curl
    curl_close($ch);

    return $output ? json_decode($output, true) : $error;
	}

}
