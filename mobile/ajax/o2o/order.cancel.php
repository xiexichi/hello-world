<?php
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
		exit(json_encode($data));
	}
}

// 订单数据验证
$order_id = isset($_POST["order_id"]) ? (int)$_POST["order_id"] : 0;
$title = isset($_POST["title"]) ? $_POST["title"] : "";
$content = isset($_POST["content"]) ? $_POST["content"] : "";

$content = $title.'<br/>'.$content;

if($order_id==0){
    echo json_encode(array(
        "status"=>"no_order_id"
    ));
}


// $order = $DB->GetRs("o2o_order","user_id,status,order_id","where user_id='".$_SESSION["user_id"]."' and order_id=".(int)$order_id);

// 写原生sql
$sql = "SELECT a.user_id,b.status,a.order_id,b.order_type FROM o2o_order a JOIN o2o_order_join b ON a.order_id = b.order_id WHERE a.order_id = {$order_id} AND a.user_id = {$_SESSION["user_id"]} LIMIT 1";

// 获取订单数据
$order = $DB->fetch_assoc($DB->query($sql));

if(empty($order['user_id'])){
    echoJson(["status"=>"no_order_id"]);
}

if($order['status'] != 0 ){
    echoJson(["status"=>"is_payed"]);
}


// 2. 查找订单项数据
$orderItems = $DB->GetAll('o2o_order_item','*','WHERE order_id = '.$order_id);
if(!$orderItems){
	// 没有订单项数据
    echoJson(["status"=>"no_items"]);
}

// 开启事务
$DB->trans_begin();

// 2.1 更改订单为取消状态
$res = $DB->Set('o2o_order_join', ['status' => -1], 'WHERE order_id = '.$order_id);
if (!$res) {
	// 回滚
	$DB->trans_rollback();
    echoJson(["status"=>"update_status_fail"]);
}

// 2.2修改关闭时间
$closeDate = date('Y-m-d H:i:s');
$res = $DB->Set('o2o_order', ['close_date'=>date('Y-m-d H:i:s')],'WHERE order_id = '.$order_id);
if (!$res) {
	// 回滚
	$DB->trans_rollback();
    echoJson(["status"=>"update_close_date_fail"]);
}

// 2.3 添加订单历史
$history = [
	'order_id' => $order_id,
	'status'   => -1,
	'create_date' => $closeDate,
	'msg' => '用户取消订单（手机版）'
];
$res = $DB->Add('o2o_order_history', $history);
if (!$res) {
	// 回滚
	$DB->trans_rollback();
    echoJson(["status"=>"add_history_fail"]);
}

// 如果代发订单，才返回库存数量
if ($order['order_type'] != 'issuing') {

	// 2.4查找批次库存数据，将订单库存返回到店铺库存
	foreach ($orderItems as $k => $v) {
		$orderItemBatch = $DB->GetAll('o2o_order_item_batch','*','WHERE item_id = '.$v['item_id'].' AND order_id = '.$order_id);

		// 订单库存 -> 店铺库存
		if ($orderItemBatch) {
			foreach ($orderItemBatch as $k1 => $v1) {
				// 查找现有库存
				$purchaseStock = $DB->GetRs('o2o_purchase_stock','*','WHERE purchase_stock_id = '.$v1['purchase_stock_id']);

				if (!$purchaseStock) {
					// 回滚
					$DB->trans_rollback();
					// 没有库存数据错误
				    exit(json_encode(array(
				        "status"=>"no_stock_fail"
				    )));
				}

				$res = $DB->Set('o2o_purchase_stock',['quantity' => (int)$purchaseStock['quantity'] + (int)$v1['batch_num']], 'WHERE purchase_stock_id = '.$v1['purchase_stock_id']);
				if (!$res) {
					// 回滚
					$DB->trans_rollback();
					// 没有返回库存失败
				    exit(json_encode(array(
				        "status"=>"return_stock_fail"
				    )));
				}
			}
		} else {
			// 回滚
			$DB->trans_rollback();
			// 没有订单项数据
		    exit(json_encode(array(
		        "status"=>"no_orderItemBatch"
		    )));
		}
	}

}

// pe('success');

// 提交事务
$DB->trans_commit();
// 返回成功
exit(json_encode(array(
    "status"=>"success"
)));