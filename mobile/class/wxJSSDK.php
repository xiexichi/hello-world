<?php
class wxJSSDK {
  private $appId;
  private $appSecret;
  private $codekey;
  private $at;

  public function __construct() {
    global $SITECONFIGER; 
    $this->codekey = "KO";
    $this->appId = @Base::enccode($SITECONFIGER["wx"]["AppID"], 'DECODE', $this->codekey, 0);
    $this->appSecret = @Base::enccode($SITECONFIGER["wx"]["AppSecret"], 'DECODE', $this->codekey, 0);
  }

  public function getSignPackage() {

    $jsapiTicket = $this->getJsApiTicket();
    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string,
      "access_token"=>$this->access_token
    );
    return $signPackage; 

  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
    $access_token_file = $_SERVER['DOCUMENT_ROOT'].'/temp/jsapi_ticket.json';//json文件路径
    if(!file_exists($access_token_file)) {
      $ft=fopen($access_token_file,"w");
      fwrite($ft,"");
      fclose($ft);

    }
    $data = json_decode(file_get_contents($access_token_file));
    $data = json_decode(json_encode($data),TRUE);


    if ($data["expire_time"] < time() || $data["expire_time"]=="") {

      $accessToken = $this->getAccessToken();

      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$accessToken;
      $res = $this->httpGet($url);
      $res = json_decode($res);
      $res = json_decode(json_encode($res),TRUE);


      $ticket = $res["ticket"];
      if ($ticket) {
        $data["expire_time"] = time() + 7000;
        $data["jsapi_ticket"] = $ticket;
        $fp = fopen($access_token_file, "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $ticket = $data["jsapi_ticket"];
    }

    return $ticket;
  }

  private function getAccessToken() {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $access_token_file = $_SERVER['DOCUMENT_ROOT'].'/temp/access_token_2.json';//json文件路径
    if(!file_exists($access_token_file)) {
      $ft=fopen($access_token_file,"w");
      fwrite($ft,"");
      fclose($ft);

    }
    $data = json_decode(file_get_contents($access_token_file));
    $data = json_decode(json_encode($data),TRUE);


    if ($data["expire_time"] < time() || $data["expire_time"]=="") {
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appId."&secret=".$this->appSecret;
      $res = $this->httpGet($url);
      $res = json_decode($res);
      $res = json_decode(json_encode($res),TRUE);

      $access_token = $res["access_token"];
      if ($access_token) {
        $data["expire_time"] = time() + 7000;
        $data["access_token"] = $access_token;
        $fp = fopen($access_token_file, "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $access_token = $data["access_token"];
    }
    $at = $access_token;
    return $access_token;
  }

  private function httpGet($url) {

    
    $curl = curl_init();
    curl_setopt($curl,CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($curl);

    curl_close($curl);
    //print_r($res);
    return $res;
  }
}