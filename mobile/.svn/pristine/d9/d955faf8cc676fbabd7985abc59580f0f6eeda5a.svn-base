<?php
/*
* 潮人来的，领取50元红包
* 每个用户限领1次，分销不能领取
* 2016-12-23
*/

$do = isset($_POST['do']) ? htmlspecialchars_decode($_POST['do']) : null;
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if(isset($is_seller) && $is_seller==1){
    header('location:/');
    exit;
}

$view = array();

// / 页面广告
$sm->assign("adset", $Common->get_picshow(41), true);
// 商品列表
$sm->assign("prolist", $Common->get_picshow(42), true);


// 页面seo
$page_title = '领取50元红包';
$page_sed_title = '领取红包';
$sm->assign("hide_site_top_banner", true, true);