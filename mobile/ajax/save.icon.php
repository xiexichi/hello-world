<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/upyun.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/image.php");

if(!isset($_SESSION["user_id"])) {
    echo json_encode(array(
        "status"=>"no_login"
    ));
    exit;
}

$img = isset($_POST['img'])? $_POST['img'] : '';
// 获取图片
list($type, $data) = explode(',', $img);
// 判断类型
if(strstr($type,'image/jpeg')!==''){
    $ext = '.jpg';
}elseif(strstr($type,'image/gif')!==''){
    $ext = '.gif';
}elseif(strstr($type,'image/png')!==''){
    $ext = '.png';
}else{
    $return_json = array(
        "status"=>"error_format"
    );
    echo json_encode($return_json);
    exit;
}
// 生成的文件名
$avatar = time().$ext;

$upyun = new Upyun();
$tmp = $upyun->writeFile('/avatar/'.$avatar, base64_decode($data), True);
$org_image_url = 'http://img.25miao.com/avatar/'.$avatar;


$image = new image();
$img_info = $image->getImageInfo($org_image_url);
$new_avatar = $image->thumb($org_image_url,$_SERVER['DOCUMENT_ROOT']."/tmp/".$avatar,$img_info,360,360,1,1);

$tmp_file = str_replace('http://img.25miao.com/avatar/','',$avatar);
$upyun->deleteFile($tmp_file);


$fh = fopen($new_avatar, 'r');
$tmp = $upyun->writeFile('/avatar/'.$avatar, $fh, True);
fclose($fh);
unlink($new_avatar);
if ($tmp)
{
    $return_json = array(
        "status"=>"success",
        "img_url"=>'http://img.25miao.com/avatar/'.$avatar."!w200"
    );

    $DB->Set("users",array("image_url"=>'http://img.25miao.com/avatar/'.$avatar),"where user_id=".$_SESSION["user_id"]);
    echo json_encode($return_json);

} else {
    $return_json = array(
        "status"=>"error_upload"
    );
    echo json_encode($return_json);
}

?>