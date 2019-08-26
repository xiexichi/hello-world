<?php
/**
 * 获取关注公众号用户的信息
 * 		现在需要获取unionid
 */

//开启错误调试
error_reporting(E_ALL);
ini_set('display_errors', true);

// function dump($data){
//     echo '<pre>';
//     var_dump($data);
// }

// $arr = array(
//     'left' => '100px',
//     'top'  => '100px'
// );


//         $access_token_file = './temp/access_token.json';//json文件路径

//         //获取文件内容并转换为数组
//         $access_token_data = json_decode(file_get_contents($access_token_file),true);

// dump((time()+60000));
//         //获取access_token
//         if($access_token_data['expiration_time'] > (time()+60000)){
//             //获取缓存文件中的access_token
//             $access_token = $access_token_data['access_token'];
//         }

// echo 023;
// exit;
/*写入文件测试*/



/**
 * [get_unionid 获取微信unionid]
 * @param  [string] $openid [用户的openid]
 * @return [string]         [用户的unionid]
 */
function get_unionid($openid){
    $appid = 'wx03cbfff87f584209';
    $secret = '5f9503f96db96dd28e14cd93bfa95b43';

    //$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
    //$access_token = json_decode(file_get_contents($url),true)['access_token'];
    // $access_token = json_decode(file_get_contents($url),true);

    //直接使用25boy
    $access_token = "E9XPGQfUqH2FxM3mnEXW2k92TPQaLpX2OYpFV5VOWrj5POJUp3GTr9zzsgNNKb-NNmJ26jCZMu_tsWxPGcZgzp_o9jWA5Fyl7N4bWUcOlrp3Mxw-7XGtitpY9FTlV33aROLjCEAURO";


    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN";
    $result = json_decode(file_get_contents($url),true);
    
    // echo file_get_contents($url);
    // print_r($result);

    // return $result;
    return $result['unionid'];
}


//$openid = "oXNwpuJ0vvNeQIjDvD8l8TqfXKA8";//测试openid

$openid = "oXNwpuKA8zeR0iqxDtdufB0D2eYU";//有25boy的微信平台获取的openid

$openid = "oXNwpuJ0vvNeQIjDvD8l8TqfXKA8";//我的测试openid
$unionid = get_unionid($openid);

print_r($unionid);

//获取的unionid
//oRazyt_my6qrs-t54_JkxZz2acHg
//oRazytw-pDFT0IQBMnuHDcg3lriw
