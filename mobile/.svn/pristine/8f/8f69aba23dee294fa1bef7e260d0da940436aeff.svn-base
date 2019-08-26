<?php
/*
* 充值二级页面
* 分销商直接跳转到充值页面
*/
if(isset($is_seller) && $is_seller==1){
    header('location:/?m=account&a=balance');
    exit;
}

$view = array();

// / 页面广告
$sm->assign("adset", $Common->get_picshow(20), true);

// 商品列表
$sm->assign("prolist", $Common->get_picshow_set(19,array(20)), true);

// 页面seo
$page_title = '充值返现 低至6折';
$page_sed_title = '充值返现';
$object['keys'] && $seo_keyword=$object['keys'];
$object['desc'] && $seo_desc=$object['desc'];