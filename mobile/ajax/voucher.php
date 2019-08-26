<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/Voucher.php");
$voucherModel = new Voucher();

$m = isset($_REQUEST["m"]) ? trim($_REQUEST["m"]) : '';

$respoonse = [
	'code' => -1,
	'msg' => 'error'
];

// 转到voucher类处理
if( !empty($m) && method_exists($voucherModel, $m) ){
	$respoonse = $voucherModel->{$m}();
}

echo json_encode($respoonse);