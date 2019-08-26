<?php
/**
 * 保存收货地址
 */

include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if(!isset($_SESSION["user_id"]) || !$_SESSION["user_id"]) {
    echo json_encode(array(
        "status"=>"error"
    ));
    exit;
}

/**
 * [echoJson 输出json数据]
 * @param  [type] $data [输出数据]
 * @return [type]       [description]
 */
if ( ! function_exists('echoJson')){
	function echoJson($data){
		// 输出json对象数据
		header('Content-type:application/json; charset=utf-8');
		exit(json_encode($data,256));
	}
}

// 打印函数
if ( ! function_exists('p')){
	function p($data){
	    echo '<pre>';
	    print_r($data);
	}
}

if ( ! function_exists('pe')){
	function pe($data){
	    p($data);
	    exit;
	}
}

// 接收post数据
$data = $_POST;

// 检测订单id
if (!isset($data['order_sn']) || empty($data['order_sn'])) {
	echoJson(["status"=>"no_order_sn"]);
}

// 检测收货地址信息
if (!isset($data['location']) || !is_array($data['location'])) {
	echoJson(["status"=>"location_error"]);
}

// 验证参数是否齐全
$locationInfo = ['state_name','city_name','district_name','address','receiver_name','receiver_phone'];
$diffs = array_diff($locationInfo, array_keys($data['location']));
if ($diffs) {
	echoJson(["status"=>"no_".array_values($diffs)[0]]);
}

// 验证收货信息
foreach ($data['location'] as $k => $v) {
	if (empty($v)) {
		echoJson(["status"=>"no_".$k]);
	}
}

// 验证是否手机
if(!preg_match("/^1[34578]{1}\d{9}$/",$data['location']['receiver_phone'])){  
    echoJson(["status"=>"no_mobile"]); 
}


// 1.查找订单信息
$orderTable = 'o2o_order';
$fields = '*';
$where = "WHERE order_sn = '{$data['order_sn']}' AND user_id = {$_SESSION["user_id"]}";
$order = $DB->GetRs($orderTable, $fields, $where);

// 订单不存在
if (!$order) {
	echoJson(["status"=>"no_order"]);
}

// 2.修改location -> json数据保存
$orderJoinTable = 'o2o_order_join';
$fields = '*';
$where = "WHERE order_id = {$order['order_id']}";
$orderJoin = $DB->GetRs($orderJoinTable, $fields, $where);


// 开启事务
$DB->trans_begin();

// 4.保存收货人与联系电话
$orderData = [
	'receiver_name' => $data['location']['receiver_name'],
	'receiver_phone' => $data['location']['receiver_phone']
];
$res = $DB->Set($orderTable, $orderData, 'WHERE order_id = '.$order['order_id']);
if (!$res) {
	// 回滚
	$DB->trans_rollback();
	echoJson(["status"=>"save_error"]);
}

// 3.保存location信息
$location = [
	'state' => $data['location']['state_name'],
	'city' => $data['location']['city_name'],
	'district' => $data['location']['district_name'],
	'address' => $data['location']['address'],
	'receiver_name' => $data['location']['receiver_name'],
	'receiver_phone' => $data['location']['receiver_phone']
];

// 替换详细地址中的省市区，避免地址信息重复
$address = $data['location']['address'];
$address = str_replace($location['state'],'',$address);
$address = str_replace($location['city'],'',$address);
$address = str_replace($location['district'],'',$address);

$location['address'] = $address;

// 转义
$orderJoinData['location'] = addslashes(json_encode($location));
$res = $DB->Set($orderJoinTable, $orderJoinData, 'WHERE order_id = '.$order['order_id']);
if (!$res) {
	// 回滚
	$DB->trans_rollback();
	echoJson(["status"=>"save_error"]);
}

// 提交事务
$DB->trans_commit();

// 保存成功
echoJson(["status"=>"success"]);