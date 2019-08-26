<?php

/*
 * 手机验证类的控制页面
*/

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/ajax/PhoneCode.class.php');

$pcode = new PhoneCode($DB,$Base);

//安全检测
$action = (isset($_GET['a']) && !empty($_GET['a'])) ? $_GET['a'] : '';

try {
	if(empty($action) || !method_exists($pcode, $action)) {
		throw new Exception("非法操作!", -1);
	}
}catch(Exception $e) {
	echo $e->getCode();
	exit();
}

//执行操作
$pcode->$action();

