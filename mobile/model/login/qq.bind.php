<?php

if($_SESSION["user_id"]!=0 && isset($_SESSION["user_id"]) && $_SESSION["user_id"]!=""){
    header("location:/?m=account");exit;
}

$page_title = "帐户绑定";
$page_sed_title = 'QQ帐户绑定';

if(empty($_SESSION['social_id'])){
	header("location:/");exit;
}
$user_profile = array(
    'profile_image_url'=>$_SESSION['profile_image_url'],
    'avatar_hd'=>$_SESSION['avatar_hd'],
    'social_id'=>$_SESSION['social_id'],
    'social_name'=>$_SESSION['social_name'] 
);

// print_r($user_profile);

$self_url = urlencode($_GET["uri"]);
$sm->assign("self_url",urlencode($self_url),true);
$sm->assign("user_profile", $user_profile, true);

