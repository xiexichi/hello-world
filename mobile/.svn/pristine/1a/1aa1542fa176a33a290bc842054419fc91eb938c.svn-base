<?php
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-type: application/x-javascript;charset=utf-8");
header("Expires: -1"); 

include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$data = array(
	'status' => 'error',	// ok
	'type' => 'order'		// 类型，order/prepaid
);

/*
 *  查询是否下单或者是否充值
 */

$code = isset($_GET['code']) ? htmlspecialchars_decode($_GET['code']) : NULL;

//1.先查询消费 
$order = $DB->GetRs('orders','pay_total,order_id,business_call,user_id,pay_method',"WHERE business_call='$code'");

if(!empty($order)) {
	$user  = $DB->GetRs('users','bag_total,user_id',"WHERE user_id=".$order['user_id']);
	$data = array(
		'status' 	=> 'ok',	
		'type' 		=> 'order',
		'pay_total' => $order['pay_total'],
		'user_id'  	=> $order['user_id'],
		'order_id'	=> $order['order_id'],
		'pay_method'=> $order['pay_method'],
		'bag_total'	=> $user['bag_total']
	);
}else {
	//2.如果没有消费，再查询充值
	$recharge = $DB->GetRs('bag','money,create_date,balance,plus_price,user_id',"WHERE business_call='$code'");
	if(!empty($recharge)) {
		$data = array(
			'status' 	=> 'ok',	
			'type' 		=> 'prepaid',
			'money' 	=> $recharge['money'],
			'plus_price'=> $recharge['plus_price'],
			'balance'	=> $recharge['balance'],
			'user_id'	=> $recharge['user_id'],
			'create_date'=> $recharge['create_date']
		);		
	}

}

$callback = $_GET['callback'];
echo $callback.'('.json_encode($data).')';
exit;
?>