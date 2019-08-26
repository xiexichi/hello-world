<?php
/**
 * 订单退货
 */

include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if(!isset($_SESSION["user_id"]) || !$_SESSION["user_id"]) {
    echo json_encode(array(
        "status"=>"error"
    ));
    exit;
}

/**
 * [o2oShowError 输出json数据]
 * @param  [type] $data [输出数据]
 * @return [type]       [description]
 */
if ( ! function_exists('o2oShowError')){
	function o2oShowError($data){
		// 直接输出js提示
		exit('
		<script>	
			confirm("'.$data['msg'].'");
			window.location.href = "/?m=account&a=o2o_order_detail&order_id='.$_GET['order_id'].'";
		</script>
		');


		// 输出json对象数据
		// header('Content-type:application/json; charset=utf-8');
		// exit(json_encode($data,256));
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

/*------------- 1.参数验证 --------------*/
// 
if (!isset($_GET['order_id']) && empty($_GET['order_id'])) {
	o2oShowError(['code'=>1000,'msg'=>'缺失参数order_id']);
}

/*------------- 2.查找订单项数据 --------------*/

// 订单id
$orderId = (int)$_GET['order_id'];
// 2.1 查找订单是否存在
$orderTable = 'o2o_order';
$fields = 'o2o_order.*, oj.status';
$condition = "JOIN o2o_order_join oj ON o2o_order.order_id = oj.order_id WHERE o2o_order.order_id = {$orderId} AND o2o_order.user_id = {$_SESSION['user_id']}";
$order = $DB->GetRs($orderTable, $fields, $condition);
if (!$order) {
	o2oShowError(['code'=>1002,'msg'=>'订单不存在']);
}

// 判断订单状态
if ( !in_array($order['status'],[2,3]) ) {
	o2oShowError(['code'=>1003,'msg'=>'当前订单状态不允许申请退货']);
}


// 2.2 查找订单项数据

// 查找商品项可退数量
$canRequantity = "(o2o_order_item.quantity - (SELECT IFNULL(SUM(re_num),0) FROM o2o_reorder_item a JOIN o2o_reorder b ON a.reorder_id = b.reorder_id WHERE b.order_id = {$orderId} AND a.item_id = o2o_order_item.item_id AND b.status >= 2 AND b.substatus > 0)) can_requantity";

// 重新拼接item_id
$orderItemTable = 'o2o_order_item';
$fields = 'o2o_order_item.*, p.free_return,p.product_name, s.color_prop, s.size_prop,'.$canRequantity;
$leftJoin = 'LEFT JOIN products p ON o2o_order_item.product_id = p.product_id LEFT JOIN stock s ON o2o_order_item.sku_sn = s.sku_sn AND o2o_order_item.sku_prop = s.sku_prop ';
$condition = $leftJoin."WHERE o2o_order_item.order_id = {$orderId} AND o2o_order_item.status = 1 GROUP BY o2o_order_item.item_id";
$orderItems = $DB->GetAll($orderItemTable, $fields, $condition);

if (!$orderItems) {
	o2oShowError(['code'=>1004,'msg'=>'当前订单没有已发货商品']);
}
// pe($orderItems);

// 分配显示数据
$sm->assign("order_items", $orderItems, true);

// 隐藏底部导航栏
$site_nav_display = 'hide';