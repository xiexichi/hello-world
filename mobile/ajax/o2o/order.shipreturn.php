<?php
/**
 * 寄回商品快递信息
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


// 接收提交参数
$data = $_POST;

/*------------------- 1.验证数据 -------------------*/
$chcekParams = ['order_id','com','nu'];

if($defects = array_values(array_diff($chcekParams, array_keys($data)))){
	echoJson(['code' => 1000, 'msg' => '缺失参数'.$defects[0]]);
} 


// 1.查找退换单是否存在
$reorder = $DB->GetRs('o2o_reorder','*','WHERE reorder_id = '.(int)$data['order_id']);

if(!$reorder){
	echoJson(['code' => 1001, 'msg' => '退换单不存在']);
}

// 判断退换单状态
if ( !in_array($reorder['status'], [2,3]) || $reorder['substatus'] != 2 ) {
	echoJson(['code' => 1002, 'msg' => '退换单不允许填写寄回信息']);
}

/*------------------- 2.处理数据 -------------------*/

// 开启事务
$DB->trans_begin();

// 2.1 修改退换单 substatus=3
$res = $DB->Set('o2o_reorder',['substatus' => 3],'WHERE reorder_id = '.$reorder['reorder_id']);
if (!$res) {
	// 回滚
	$DB->trans_rollback();
	echoJson(['code' => 1003, 'msg' => '寄回信息失败']);
}

// 2.2 退换单订单历史
$create_date = date('Y-m-d H:i:s');
$msg = addslashes("退换商品已寄出，{$data['com']}:{$data['nu']} （手机版）");
$reorderHistory = [
	'reorder_id' => $reorder['reorder_id'],
	'status' => $reorder['status'],
	'substatus' => 3,
	'msg' => '',
	'create_date' => $create_date
];

$res = $DB->Add('o2o_reorder_history', $reorderHistory);
if (!$res) {
	// 回滚
	$DB->trans_rollback();
	echoJson(['code' => 1004, 'msg' => '寄回信息失败']);
}

// 提交事务
$DB->trans_commit();
echoJson(['code' => 0, 'msg' => '填写寄回信息成功']);