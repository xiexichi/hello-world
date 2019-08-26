<?php
$page_title = "我的二五";
$page_sed_title = '账户充值';


if($_SESSION["user_id"] != "" && $_SESSION["user_id"]!=0){

	// 充值广告,is_seller读取index.php页面参数
	$pos_id = empty($is_seller) ? 18 : 17;
	$sm->assign("balance_ads", $Common->get_picshow($pos_id,1), true);

    //获得商户代码(扫码而来)
    $business_code = $Common->getBusinessCodeFrom();
    //获取充值优惠组
    $recharge = $Common->getRecharge($_SESSION["user_id"],$business_code);

    // 首次充值返现  2018-12-23 文杰
    // 不可与充值活动共用
    if(empty($recharge)){
        $firstRecharge = $Common->firstRecharge($_SESSION["user_id"]);
        $sm->assign("firstRecharge", $firstRecharge, true);
    }
    
	$salt = $_SESSION['salt'] = mt_rand();
	$sm->assign("salt", $salt, true);
	$sm->assign("recharge", $recharge, true);
}
