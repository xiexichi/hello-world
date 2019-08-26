<?php
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-type: application/x-javascript;charset=utf-8");
header("Expires: -1"); 

$data = array(
	'status' => 'error',	// ok
	'type' => 'order'		// 类型，order/prepaid
);
$callback = $_GET['callback'];
echo $callback.'('.json_encode($data).')';
exit;
?>