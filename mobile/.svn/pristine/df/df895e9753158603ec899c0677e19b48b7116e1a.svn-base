<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pramga: no-cache");
// 允许跨域ajax域名
header('Access-Control-Allow-Origin:http://www.25boy.cn');
/*
* 生成二唯码
* phpqrcode
*/
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
include_once($_SERVER['DOCUMENT_ROOT'].'/class/qrcode/qrlib.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/class/brcode/BCGFontFile.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/class/brcode/BCGColor.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/class/brcode/BCGDrawing.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/class/brcode/BCGcode128.barcode.php');

$act = isset($_GET['act']) ? htmlspecialchars_decode($_GET['act']) : NULL;
$user_id = empty($_SESSION['user_id']) ? 0 : intval($_SESSION['user_id']);

switch ($act) {

    /*
    * 我的二唯码
    * o2o付款与充值码
    */
    case 'my':   
        // 检测会员
        $user = $DB->GetRs('users','user_id,image_url,flag',"WHERE user_id=".intval($user_id));
        if(empty($user['flag'])){
            echo json_encode(array(
                    'ms_code' => 'noflag',
                    'ms_msg' => '未认证手机禁止使用！'
                ));
            return false;
        }
        if(empty($user['user_id'])){
            echo json_encode(array(
                    'ms_code' => 'nologin',
                    'ms_msg' => '请登录后使用！'
                ));
            return false;
        }

        // 生成内容
        $len = strlen($user_id);
        $rand = substr(str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT), 0, 8-1-$len);
        $text = $len . $user_id . $rand . time();
        // $text = "159207341474096812";

        // 参数设置
        $tempDir = '../cache/images/'; 
        $errorCorrectionLevel = 'H';    //容错级别 
        $matrixPointSize = 8;   //生成图片大小 
        $QR = $tempDir.'QR_'.$text.'.png';
        $BR = $tempDir.'BR_'.$text.'.png';

        /* 生成二维码图片 */ 
        QRcode::png($text, $QR, $errorCorrectionLevel, $matrixPointSize, 2); 


        /* 生成条形码图片 */ 
        // The arguments are R, G, and B for color.
        $colorFront = new BCGColor(0, 0, 0);
        $colorBack = new BCGColor(255, 255, 255);

        // Font for Label
        $font = new BCGFontFile($_SERVER['DOCUMENT_ROOT'].'/class/brcode/font/Arial.ttf', 16);

        // Creating the Barcode
        $code = new BCGcode128();
        $code->setScale(2);
        $code->setThickness(30);
        $code->setForegroundColor($colorFront);
        $code->setBackgroundColor($colorBack);
        $code->setFont($font);
        $code->parse($text);

        // Saving the Barcode to a File
        $drawing = new BCGDrawing($BR, $colorBack);
        $drawing->setBarcode($code);
        $drawing->draw();
        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

        echo json_encode(array(
                'ms_code' => 'success',
                'ms_msg' => '',
                'qr' => '<img src="'.$QR.'" class="qrimg">',
                'br' => '<img src="'.$BR.'" class="brimg" width="90%">',
                'code' => $text
            ));
        exit;

        break;


    /*
    * 生成来源url
    * 根据id查询qrcode表
    */
    case 'code':
        // 接收参数
        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
        // 查询记录
        $row = $DB->GetRs('qrcode','url',"WHERE id='{$id}'");
        if(empty($row['url'])) return false;

        // 生成内容
        $text = $row['url'];

        // 参数设置
        $tempDir = '../cache/images/'; 
        $errorCorrectionLevel = 'H';    //容错级别 
        $matrixPointSize = 25;   //生成图片大小 
        $imgname = 'QR_'.md5($text).'.png';
        $QR = $tempDir.$imgname;

        /* 生成二维码图片 */ 
        QRcode::png($text, $QR, $errorCorrectionLevel, $matrixPointSize, 2); 

        $data = array(
                'ms_code' => 'success',
                'ms_msg' => '',
                'src' => '/cache/images/'.$imgname,
                'code' => $text
            );

        if(empty($_GET['callback'])){
            echo json_encode($data);
        }else{
            $callback = isset($_GET['callback']) ? $_GET['callback'] : 'callback';
            echo $callback.'('.json_encode($data).')';
        }
        exit;
        break;

    // 我的专用推广链接
    case 'byPromoteUrl':

        // 检查登录
        if(empty($user_id)){
            echo json_encode(array(
                    'code' => -1,
                    'msg' => '请登录后获取您的专属返佣链接。'
                ));
            return false;
        }

        $url = isset($_GET['url']) ? htmlspecialchars_decode($_GET['url']) : '';
        $type = isset($_GET['type']) ? htmlspecialchars_decode($_GET['type']) : '';
        // $scene = isset($_GET['scene']) ? htmlspecialchars_decode($_GET['scene']) : '';
        $promote_id = isset($promote['promote_id']) ? intval($promote['promote_id']) : 0;
        if(empty($promote_id)){
            // 加入推广者表
            $DB->Add('promote',array(
                'user_id'       => $user_id,
                'create_time'   => date("Y-m-d H:i:s"),
            ));
            $promote_id = $DB->insert_id();
        }

        if(stripos($url, 'pid=') === false){
            if($type == 'weapp_temp'){
                $url = $url . '-pid_'.$promote_id;
            }else{
                if(stripos($url, '?') === false){
                    $url = $url . '?pid='.$promote_id;
                }else{
                    $url = $url . '&pid='.$promote_id;
                }
            }
        }

        $api_url = "http://api.25boy.cn/?c=Qrcode&a=weapp&type={$type}&path=".urlencode($url);

        // 小程序二唯码
        $curl = curl_init($api_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT,60);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        // 执行命令
        $output = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //关闭URL请求
        curl_close($curl);

        echo $output;
        exit;
        break;
}
