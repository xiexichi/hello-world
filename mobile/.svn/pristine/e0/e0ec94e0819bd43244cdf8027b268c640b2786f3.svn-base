<?php
$gourl = isset($_GET['gourl'])?base64_decode($_GET['gourl']):'/?m=account';

if($_SESSION["user_id"]!=0&&isset($_SESSION["user_id"])&&$_SESSION["user_id"]!=""){
    header("location:".$gourl);
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT']."/pay/wx/WxPay.Api.php";
require_once $_SERVER['DOCUMENT_ROOT']."/pay/wx/WxPay.JsApiPay.php";
$return_url = "https://".$base_url."/index.php?".$_SERVER['QUERY_STRING'];
$tools = new JsApiPay();
$data = $tools->GetUserInfo($return_url);

if(empty($data['openid'])){
	$data['openid'] = $_SESSION["openid"];
}
if(!isset($data['openid']) || !$data['openid']){
    header("location:/");exit;
}

// 未登录，通过unionid/openid自动登录
$Table="users";
$Fileds = "user_id,nickname,openid,unionid";
if(empty($_SESSION["unionid"])){
    $Condition = "where openid='".$data["openid"]."'";
}else{
    $Condition = "where unionid='".$_SESSION["unionid"]."'";
}
$row = $DB->GetRs($Table,$Fileds,$Condition);
if(!empty($row['user_id'])){
    $_SESSION["user_id"]=$row["user_id"];
    $_SESSION["nickname"]=$row["nickname"];
    echo '<script>alert("微信自动登录成功");window.location.href="'.$gourl.'";</script>';
    // header("location:".$gourl);
    exit;
}

// 未关注用户
/*if(isset($data['subscribe']) && $data['subscribe']==0){
	$data['nickname'] = '未关注用户';
	$data['headimgurl'] = '/statics/img/user_default_icon.png';
}*/

$_SESSION['wx_nickname'] = !empty($data['nickname'])?$data['nickname']:'';

$page_title = "绑定到二五账号";
$page_sed_title = '微信账户绑定';

$user_profile = array(
    'openid' => $data['openid'],
    'nickname' => $data['nickname']?$data['nickname']:'',
    'img_url' => $data['headimgurl']?$data['headimgurl']:'http://www.25boy.cn/images/avatar.jpg',
    'subscribe' => $data['subscribe']
);
/*setcookie('profile_image_url',$data['img_url'],time()+3600,'/');
setcookie('avatar_hd',$data['img_url'],time()+3600,'/');
setcookie('social_id',$data['openid'],time()+3600,'/');
setcookie('social_name',$data['nickname'],time()+3600,'/');*/

//获取weixin信息
$_SESSION['profile_image_url'] = $data['img_url'];
$_SESSION['avatar_hd'] = $data['img_url'];
$_SESSION['social_id'] = $data['openid'];
$_SESSION['social_name'] = $data['nickname'];

$sm->assign("user_profile", $user_profile, true);
$sm->assign("gourl", $gourl, true);