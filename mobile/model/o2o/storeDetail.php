<?php
$code = isset($_GET['code']) ? trim($_GET['code']) : '';

// 查询商户
$query = $DB->query("SELECT * FROM business WHERE business_code='{$code}'");
$business = $DB->fetch_array($query);
unset($business['password']);
unset($business['recharge_pass']);
if(empty($business)){
    header("Location: /?m=o2o&a=store");
}

// print_r($business);
$page_title = $business['business_name'];
$sm->assign("business", $business, true);