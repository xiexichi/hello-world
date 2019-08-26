<?php

/**
 * 申请退货
 */

include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/upyun.php");

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


// post数据
$data = $_POST;

/*------------------ 1.验证数据 ----------------------*/
// 验证post数据
if (!isset($data['order_id']) || empty($data['order_id'])) {
	echoJson(['code' => 1000, 'msg' => '缺失参数order_id']);
}

// 订单id
$orderId = $data['order_id'];

if (!isset($data['reason']) || empty($data['reason'])) {
	echoJson(['code' => 1000, 'msg' => '缺失参数reason']);
}

if(!in_array($data['reason'],[1,2])){
	echoJson(['code' => 1000, 'msg' => '参数reason值范围错误']);
}

// 验证退货项数据
if (!isset($data['requantitys']) || empty($data['requantitys'])) {
	echoJson(['code' => 1001, 'msg' => '缺失参数requantitys']);
}

// 讲json数据转换为数组
$requantitys = json_decode($data['requantitys'],TRUE);

if (!is_array($requantitys)) {
	echoJson(['code' => 1002, 'msg' => '参数requantitys数据格式错误']);
}


// 如果是质量问题，请上传图片
if ($data['reason'] == 1) {
	if (!isset($data['note']) || empty($data['note'])) {
		echoJson(['code' => 1002, 'msg' => '缺失参数note']);
	}

	// 处理上传图片
	if (count($_FILES) == 0) {
		echoJson(['code' => 1003, 'msg' => '请上传商品问题图片']);
	}

	// 限制上传图片数量
	if (count($_FILES) >= 3) {
		echoJson(['code' => 1003, 'msg' => '最多上传3张图片']);
	}

	// 验证图片格式
	foreach ($_FILES as $v) {
		// 判断文件是否有错误
		if ($v['error'] != 0) {
			echoJson(['code' => 1013, 'msg' => '上传图片错误']);
		}

		if (!in_array($v['type'],['image/jpeg','image/png','image/gif'])) {
			echoJson(['code' => 1013, 'msg' => '上传图片格式不正确']);
		}

		// 限制图片最大6M
		if ($v['size'] > (1024 * 1024 * 6)) {
			echoJson(['code' => 1013, 'msg' => '上传图片不能大于6M']);
		}
	}

	// 上传图片
	// 1226 图片空间目录
	$upyun = new Upyun();

	// 添加订单历史的图片
	$imgs = [];
	$imgTmps = [];
	foreach ($_FILES as $v) {
		$tree_id = '1226';
		$fileMd5 = md5_file($v['tmp_name']);
		$ext = explode('/',$v['type'])[1];
		// 文件名称
		$filename = '/'.$tree_id.'/'.$fileMd5.'.'. $ext;

		// 上传图片
		$tmp = $upyun->writeFile($filename, file_get_contents($v['tmp_name']), True);

		if (!isset($tmp['x-upyun-height']) || !isset($tmp['x-upyun-width']) || !isset($tmp['x-upyun-file-type'])) {
			echoJson(['code' => 1013, 'msg' => '上传图片失败']);
		}

		// 添加图片到数组中
		$imgs[] = $filename;

		// 图片信息
		$imgInfos[] = [
			'tree_id'  => $tree_id,
			'file_id'  => $fileMd5,
			'filename' => $fileMd5.'.'.$ext,
			'file_type'=> $tmp['x-upyun-file-type'],
			'file_width' => $tmp['x-upyun-width'],
			'file_height' => $tmp['x-upyun-height']
		];
	}

}



/*--------------------- 2.验证可退货数量 ----------------------*/
// 2.2 查找订单项数据

// 查找商品项可退数量
$canRequantity = "(o2o_order_item.quantity - (SELECT IFNULL(SUM(re_num),0) FROM o2o_reorder_item a JOIN o2o_reorder b ON a.reorder_id = b.reorder_id WHERE b.order_id = {$orderId} AND a.item_id = o2o_order_item.item_id AND b.status >= 2 AND b.substatus > 0)) can_requantity";

// 退货项id
$reitemIds = join(',',array_keys($requantitys));

// 重新拼接item_id
$orderItemTable = 'o2o_order_item';
$fields = 'o2o_order_item.*, p.free_return,p.product_name, s.color_prop, s.size_prop,'.$canRequantity;
$leftJoin = 'LEFT JOIN products p ON o2o_order_item.product_id = p.product_id LEFT JOIN stock s ON o2o_order_item.sku_sn = s.sku_sn AND o2o_order_item.sku_prop = s.sku_prop ';
$condition = $leftJoin."WHERE o2o_order_item.order_id = {$orderId} AND o2o_order_item.item_id in ({$reitemIds})";
$orderItems = $DB->GetAll($orderItemTable, $fields, $condition);


$checkIndex = 0;
// 验证可退货数量
foreach ($requantitys as $k => $v) {
	foreach ($orderItems as $k1 => $v1) {
		if ($v1['item_id'] == $k) {
			// 验证可退货数量
			if ($v > $v1['can_requantity']) {
				echoJson(['code' => 1004, 'msg' => $v1['product_name'].$v1['color_prop'].$v1['size_prop'].'可退货数量小于'.$v]);
			}

			// 验证数量+1
			$checkIndex++;
		}
	}
}

// 验证验证数量是否小于退货商品项数量
if ($checkIndex < count($requantitys)) {
	echoJson(['code' => 1005, 'msg' => '退货数量不正常']);
}


/*--------------------- 3.创建退货单 ----------------------*/

// 查找business_id
$condition = 'JOIN business b ON o2o_order.business_code = b.business_code WHERE o2o_order.order_id = '.$orderId;
$order = $DB->GetRs('o2o_order','o2o_order.order_id,b.business_id',$condition);
if (!$order) {
	echoJson(['code' => 1006, 'msg' => '订单不存在']);
}

// 判断是否不允许退货
if ($order['is_returned'] == 0) {
	echoJson(['code' => 1006, 'msg' => '此订单不允许退换']);
}

// 3.1 添加退货单数据
// 退货单数据
$create_date = date('Y-m-d H:i:s');
// 获取5位随机数
$reorder_sn = 'T'.date('YmdHis',strtotime($create_date)).rand(10000,99999);
$reorderData = [
	'reorder_sn' => $reorder_sn,
	'order_id' => $order['order_id'],
	'business_id' => $order['business_id'],
	'status' => 3,
	'substatus' => 1,
	're_type' => 1,
	'create_date' => $create_date,
	'reason' => $data['reason'],
	'client' => 'wap'
];

// 如果是质量问题
if ($data['reason'] == 1) {
	// 添加备注
	$reorderData['re_note'] = $data['note'];
}

// 开启事务
$DB->trans_begin();

$res = $DB->Add('o2o_reorder', $reorderData);
if (!$res) {
	// 回滚
	$DB->trans_rollback();
	echoJson(['code' => 1007, 'msg' => '添加退货单失败']);
}

// 退货单id
$reorderId = $DB->insert_id();

// 3.2 添加退货项数据
// reorder_id item_id re_num
foreach ($requantitys as $k => $v) {
	$reitemData = [
		'reorder_id' => $reorderId,
		'item_id' 	 => $k,
		're_num'     => $v
	];
	$res = $DB->Add('o2o_reorder_item', $reitemData);
	if (!$res) {
		// 回滚
		$DB->trans_rollback();
		echoJson(['code' => 1008, 'msg' => '添加退货单商品项失败']);
	}
}

// 3.3 添加退货单历史
$reorder_hository = [
	'reorder_id' => $reorderId,
	'status' => $reorderData['status'],
	'substatus' => $reorderData['substatus'],
	'msg' => '申请换货单成功',
	'img' => '',
	'create_date' => $create_date
];
// 如果是质量问题
if ($data['reason'] == 1) {
	$prefix = 'http://img.25miao.com';
	foreach ($imgs as $k => $v) {
		$imgs[$k] = $prefix.$v;
	}
	$reorder_hository['img'] = join(',', $imgs);

	// 添加图片到图片空间
	foreach ($imgInfos as $k => $v) {
		// 如果没有图片信息，则添加
		$file = $DB->GetRs('space','*',"WHERE file_id = '{$v['file_id']}'");
		if (!$file) {
			$v['create_date'] = date('Y-m-d H:i:s');
			$v['status'] = 1;
			$DB->Add('space',$v);
		}
	}
}

$res = $DB->Add('o2o_reorder_history', $reorder_hository);
if (!$res) {
	// 回滚
	$DB->trans_rollback();
	echoJson(['code' => 1009, 'msg' => '添加退货单历史失败']);
}

// 提交事务
$DB->trans_commit();

// 返回成功
echoJson(['code' => 0, 'msg' => 'success']);