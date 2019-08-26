<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$total_fee = isset($_GET['money'])?(float)$_GET['money']:0;
$plus_price = 0;
$now_time = strtotime(date('Y-m-d'));

if(!empty($_SESSION["user_id"])){
    $user_id = $_SESSION["user_id"];
    //判断类型，如果是线下扫码，返回商户代码；否则为空。
    $business_code = $Common->getBusinessCodeFrom();
    //求取充值优惠
    $plus_price = $Common->getRechargePlus($user_id,$total_fee,$business_code);

  	// 首次充值返现  2018-12-23 文杰
		// 不可与充值活动共用
    if($plus_price == 0){
    	$firstRecharge = $Common->firstRecharge($user_id, $total_fee);
    	if($firstRecharge['code'] === 0 && isset($firstRecharge['plus_price'])){
    		$plus_price = $firstRecharge['plus_price'];
    	}
    }
}

echo json_encode(array(
	'p'=>$plus_price,
));
