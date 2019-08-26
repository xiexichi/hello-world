<?php
/**
 * app登录接口
 */
//开启错误调试
error_reporting(E_ALL);
ini_set('display_errors', true);

require "../class/PdoModel.class.php";

$db = new PdoModel();//实例数据库对象


/**
 * [echoAPIJsonData 以json格式输出API数据]
 * @param  [string/array] $data [输出的数据]
 * @param  string $type [输出的方式]
 */
function echoAPIJsonData($data,$type = "echo"){
	if($type = "echo"){
		// echo json_encode($data);
		echo '<pre>';
		print_r($data);
	}else{
		return json_encode($data);
	}
	exit;
}

function p($data){
	echo "<pre>";
	print_r($data);
}

// print_r($db);
// exit;

//返回数据数组
$result = array();

//说明：提交的openId就是socail表中的socail_id

//第三方登录的唯一凭证
$type = isset($_POST['type']) ? htmlspecialchars($_POST['type']) : false;
$social_id = isset($_POST['social_id']) ? htmlspecialchars($_POST['social_id']) : false;

//验证参数缺失
if(!$type || empty($type)){
	$result['code'] = -5;
	$result['msg']  = "error: 缺失参数type";
	echoAPIJsonData($result);
}

if(!$social_id || empty($social_id)){
	$result['code'] = -5;
	$result['msg']  = "error: 缺失参数social_id";
	echoAPIJsonData($result);
}

//判断是否微信登录
if($type == 'weixin'){
	//查找用户表
	$sql = "select * from users where unionid = '{$social_id}' limit 1";
}else{
	//根据socail_id查找用户
	$sql = "select b.* from social a join users b on a.user_id = b.user_id where a.social_id = '{$social_id}' limit 1";
}

//用户信息
$userInfo = $db->find($sql);

if(!$userInfo){
	$result['code'] = -2;
	$result['msg']  = "error: 用户不存在或未绑定第三方登录";
	echoAPIJsonData($result);
	exit;
}

//用户id
$user_id = $userInfo['user_id'];
// p($_SESSION);exit;


//更改登录时间
$login_date = date("Y-m-d H:i:s");
$sql = "update users set login_date = '{$login_date}' where user_id = '{$userInfo['user_id']}' limit 1";
$db->db_exec($sql);


//登录成功，查找用户相关的信息
//关注
$sql = "select count(*) favorites from favorites where user_id = {$userInfo['user_id']}";
$userInfo['favorites'] = $db->find($sql)['favorites'];

//浏览历史
$sql = "select count(*) browse_history from browse_history where user_id = {$userInfo['user_id']}";
$userInfo['browse_history'] = $db->find($sql)['browse_history'];

//获取订单总数
$sql = "select count(*) count from orders where user_id = {$user_id} limit 1";
$userInfo['order_total'] = $db->find($sql)['count'];


session_start();//开启session
session_regenerate_id();//更改session_id

// p(session_id());exit;

//登录成功
$result['code'] = 0;
$result['msg']  = "success";
$result['rs']   = array(
	'sessionId'=> session_id(),
	'user_id'   => $userInfo['user_id'],
	'nickname'  => $userInfo['nickname'],
	'realname'  => $userInfo['realname'],
	'email'     => $userInfo['email'],
	'address_id'=> $userInfo['address_id'],
	'image_url' => $userInfo['image_url'],
	'favorites' => $userInfo['favorites'],
	'browse_history'=> $userInfo['browse_history'],
	'order_total'=> $userInfo['order_total']
);



//记录用户信息到session（m.25boy的记录信息）
$_SESSION["user_id"] = $userInfo["user_id"];
$_SESSION["nickname"] = $userInfo["nickname"];


//返回给app的数据
echo json_encode($result);