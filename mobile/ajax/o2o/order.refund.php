<?php
/**
 * 订单退款
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

// 1.查找订单信息
$orderTable = 'o2o_order';
$fields = '*';
$where = "WHERE order_id = '{$data['order_id']}' AND user_id = {$_SESSION["user_id"]}";
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

// 1.判断订单状态是否
if ($orderJoin['status'] != 1) {
	echoJson(['code' => 1000, 'msg' => '申请失败,订单状态不是待发货']);
}

/*-------------------申请成功，更新订单信息------------------*/
// 3.通过验证，可申请
$DB->trans_begin();


// 2.查看ERP中是否已审核，如已审核则不能申请（待支付同步到ERP中才开启）
// ----------------生成退款单--------------------------------------------------------------
// 添加 调用gyerp类，生成退款单
include_once($_SERVER['DOCUMENT_ROOT']."/class/grerp.php");
$gyerp = new gyerp();
$tradeState = $gyerp->get_order_status($order['order_sn']);
if($tradeState['shenhe']==1){
	// 回滚
	$DB->trans_rollback();
    echoJson(['code' => 1000, 'msg' => '订单已审核，不能申请退款']);
}
// erp退款单
$erp['outer_tid'] = $order['order_sn']; //平台单号
$erp['outer_refundid'] = 'T'.substr($order['order_sn'],-6,6);   //子订单号
$erp['refund_state'] = 1;                   // 0、取消退款 1、标识退款
$erpResult = $gyerp->trade_refund_update($erp);
if(!isset($erpResult['success']) || $erpResult['success'] != 1){
	// 回滚
	$DB->trans_rollback();
	echoJson(['code' => 1004, 'msg' => '申请失败，请稍后重试']);
}
// ----------------生成退款单--------------------------------------------------------------


// 修改订单表
$orderData = [
	'refund_date' => date('Y-m-d H:i:s')
];
$res = $DB->Set($orderTable,$orderData,'WHERE order_id = '.$order['order_id']);
if (!$res) {
	// 回滚
	$DB->trans_rollback();
	echoJson(['code' => 1001, 'msg' => '申请失败']);
}

// 修改订单join表
$orderJoinData = [
	'status' => 4,
	'is_refund' => 1
];
$res = $DB->Set($orderJoinTable,$orderJoinData,'WHERE order_id = '.$order['order_id']);
if (!$res) {
	// 回滚
	$DB->trans_rollback();
	echoJson(['code' => 1002, 'msg' => '申请失败']);
}


// 添加订单历史
$history = [
	'order_id' => $order['order_id'],
	'status'   => $orderJoinData['status'],
	'create_date' => $orderData['refund_date'],
	'msg' => '用户申请退款（手机版）'
];
$res = $DB->Add('o2o_order_history', $history);
if (!$res) {
	// 回滚
	$DB->trans_rollback();
    echoJson(['code' => 1003, 'msg' => '申请失败']);
}

// 提交事务
$DB->trans_commit();

// 返回结果
echoJson(['code' => 0, 'msg' => 'success']);