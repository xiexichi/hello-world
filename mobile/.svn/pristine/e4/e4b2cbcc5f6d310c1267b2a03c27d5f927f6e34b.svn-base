<?php
/**
 * 确认收货
 */

include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

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

// 登录验证
if(!isset($_SESSION["user_id"]) || !$_SESSION["user_id"]) {
    echo json_encode(array(
        "status"=>"error"
    ));
    exit;
}


// 接收数据
$data = $_POST;

if (!isset($data['order_id']) && empty($data['order_id'])) {
	echoJson(['code' => 1000,'msg' => '缺失参数']);
}

// 查找订单是否存在
$order = $DB->GetRs('o2o_order', '*', "WHERE order_id = ".(int)$data['order_id']." AND user_id = {$_SESSION["user_id"]}");

if (!$order) {
	echoJson(['code' => 1001,'msg' => '订单不存在']);
}


/*------------------ 确认收货 ------------------*/

// 开启事务
$DB->trans_begin();

// 1.修改确认收货时间
$confirm_date = date('Y-m-d H:i:s');
$res = $DB->Set('o2o_order',['confirm_date' => $confirm_date], 'WHERE order_id = '.$order['order_id']);

if (!$res) {
	// 回滚
	$DB->trans_rollback();
	echoJson(['code' => 1002,'msg' => '确认收货失败']);
}

// 2.改变订单状态
$res = $DB->Set('o2o_order_join', ['status' => 3], 'WHERE order_id = '.$order['order_id']);
if (!$res) {
	// 回滚
	$DB->trans_rollback();
	echoJson(['code' => 1003,'msg' => '确认收货失败']);
}

// 提交事务
$DB->trans_commit();
echoJson(['code' => 0,'msg' => '确认收货成功']);