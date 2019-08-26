<?php
$page_title = "我的二五";
$page_sed_title = '发起付款';

$store_name = isset($_COOKIE['store_name']) ? htmlspecialchars($_COOKIE['store_name']) : NULL;

if($_SESSION["user_id"] != "" && $_SESSION["user_id"]!=0){

	
}


// 模板赋值
$sm->assign("store_name", $store_name, true);
