<?php
if($_SESSION["user_id"]!=0 && isset($_SESSION["user_id"]) && $_SESSION["user_id"]!=""){
    header("location:/?m=account");exit;
}

$page_title = "帐户绑定";
$page_sed_title = '新浪微博绑定';

if(!isset($_GET['id']) || !$_GET['id']){
    header("location:/");exit;
}
$user_profile = $_GET;

$result = $DB->GetRs("social","*","where is_bind=1 and type='weibo' and social_id='".$user_profile["id"]."'");
if(!empty($result)){
    //echo "1";
    session_start();
    $_SESSION["user_id"] = $result['user_id'];
    $_SESSION["login_time"] = date('Y-m-d H:i:s');

    if(isset($_SESSION["openid"]) && !empty($_SESSION["openid"])){
        $res = $DB->Set('users',array("openid"=>$_SESSION["openid"]),"where user_id='".$result['user_id']."'");
    }
    header('location:'.$user_profile["return_to_url"]);
    exit;
}

// print_r($user_profile);
/*setcookie('profile_image_url',$user_profile['profile_image_url'],time()+3600,'/');
setcookie('avatar_hd',$user_profile['avatar_hd'],time()+3600,'/');
setcookie('social_id',$user_profile['id'],time()+3600,'/');
setcookie('social_name',$user_profile['name'],time()+3600,'/');*/

//获取weibo信息
$_SESSION['profile_image_url'] = $user_profile['profile_image_url'];
$_SESSION['avatar_hd'] = $user_profile['avatar_hd'];
$_SESSION['social_id'] = $user_profile['id'];
$_SESSION['social_name'] = $user_profile['name'];



$self_url = urlencode($user_profile["return_to_url"]);
$sm->assign("self_url",urlencode($self_url),true);
$sm->assign("user_profile", $user_profile, true);