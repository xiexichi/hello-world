<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pramga: no-cache");
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

$link = isset($_GET['link']) ? htmlspecialchars_decode($_GET['link']) : NULL;
$user_id = empty($_SESSION['user_id']) ? 0 : intval($_SESSION['user_id']);
$promote = $DB->GetRs("promote","*","WHERE user_id = ".$user_id);

if(!empty($promote) && !$promote['is_frozen'] && !empty($link)) {
    $promote_id = $promote['promote_id'];
    // $link = $Base->get_link($promote_id,$type,$item_id);

    // 生成内容
    $len = strlen($user_id);
    $rand = substr(str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT), 0, 8-1-$len);
    $text = $len . $user_id . $rand . time();

    // 参数设置
    $tempDir = '../cache/images/'; 
    $errorCorrectionLevel = 'H';    //容错级别 
    $matrixPointSize = 8;   //生成图片大小 
    $QR = $tempDir.'QR_'.$text.'.png';
    $BR = $tempDir.'BR_'.$text.'.png';

    /* 生成二维码图片 */ 
    QRcode::png($link, $QR, $errorCorrectionLevel, $matrixPointSize, 2); 

    echo json_encode(array(
            'ms_code' => 'success',
            'ms_msg' => '',
            'qr' => '<img src="'.$QR.'" class="qrimg">',
            'br' => '<img src="'.$BR.'" class="brimg" width="90%">',
            'code' => $link
    ));
    exit;

}else {
    echo json_encode(array(
            'ms_code' => 'failed',
    )); 
}


// switch ($act) {
//     case 'my':   
//         // 检测会员
//         $user = $DB->GetRs('users','user_id,image_url,flag',"WHERE user_id=".intval($user_id));
//         if(empty($user['flag'])){
//             echo json_encode(array(
//                     'ms_code' => 'noflag',
//                     'ms_msg' => '未认证手机禁止使用！'
//                 ));
//             return false;
//         }
//         if(empty($user['user_id'])){
//             echo json_encode(array(
//                     'ms_code' => 'nologin',
//                     'ms_msg' => '请登录后使用！'
//                 ));
//             return false;
//         }

//         // 生成内容
//         $len = strlen($user_id);
//         $rand = substr(str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT), 0, 8-1-$len);
//         $text = $len . $user_id . $rand . time();
//         // $text = "159207341474096812";

//         // 参数设置
//         $tempDir = '../cache/images/'; 
//         $errorCorrectionLevel = 'H';    //容错级别 
//         $matrixPointSize = 8;   //生成图片大小 
//         $QR = $tempDir.'QR_'.$text.'.png';
//         $BR = $tempDir.'BR_'.$text.'.png';*/

//         /* 生成二维码图片 */ 
//         //QRcode::png($text, $QR, $errorCorrectionLevel, $matrixPointSize, 2); 


//          生成条形码图片  
//         // The arguments are R, G, and B for color.
// /*        $colorFront = new BCGColor(0, 0, 0);
//         $colorBack = new BCGColor(255, 255, 255);

//         // Font for Label
//         $font = new BCGFontFile($_SERVER['DOCUMENT_ROOT'].'/class/brcode/font/Arial.ttf', 16);

//         // Creating the Barcode
//         $code = new BCGcode128();
//         $code->setScale(2);
//         $code->setThickness(30);
//         $code->setForegroundColor($colorFront);
//         $code->setBackgroundColor($colorBack);
//         $code->setFont($font);
//         $code->parse($text);

//         // Saving the Barcode to a File
//         $drawing = new BCGDrawing($BR, $colorBack);
//         $drawing->setBarcode($code);
//         $drawing->draw();
//         $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);*/
// /*
//         echo json_encode(array(
//                 'ms_code' => 'success',
//                 'ms_msg' => '',
//                 'qr' => '<img src="'.$QR.'" class="qrimg">',
//                 'br' => '<img src="'.$BR.'" class="brimg" width="90%">',
//                 'code' => $text
//             ));
//         exit;

//         break;
//     default:
//         # code...
//         break;*/
// // }
