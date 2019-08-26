<?php
require "config.php";

function db_open () {
	global $link;
	global $sql_host,$sql_login,$sql_passe,$sql_dbase;
	$link = mysql_connect($sql_host,$sql_login,$sql_passe)
	        or die ("Can't connect to host!\n");
	mysql_select_db ($sql_dbase)
	        or die ("Can't select database dbase!\n");
}
	
function db_close () {
	global $link;
	mysql_close($link);
}

function mysql_protect($s) {
	return "\"" . mysql_escape_string ($s) . "\"";
}

function db_add_record() {
	$_table_tongji = 'tongji';
	$_table_tongji_pc = 'tongji_pc';
	$_table_tongji_wap = 'tongji_wap';
	$_table_tongji_apps = 'tongji_apps';
	global $_SERVER;

	// 请求页面/受访页面 JS
	if(isset($_GET['u'])){
		$request_url = base64_decode(urldecode($_GET['u']));
		$url_arr = parse_url($request_url);
		@parse_str($url_arr['query'],$url_param);
	}else{
		$request_url = '';
	}

	// 来源页面 JS
	if(isset($_GET['r'])){
		$referer = base64_decode(urldecode($_GET['r']));
	}else{
		$referer = '';
	}
	
	// 请求页面/受访页面 PHP
	/*$request_url	= "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];/

	// 来源页面 PHP
	/*if (isset($_SERVER['HTTP_REFERER']))
		$referer	= $_SERVER['HTTP_REFERER'];
	else
		$referer	= "-";*/

	// 远程主机/IP地址
	$remote_host	= clientIp();

	// 浏览器标识
	if (isset($_SERVER['HTTP_USER_AGENT']))
		$user_agent	= $_SERVER['HTTP_USER_AGENT'];
	else
		$user_agent	= "";

	// 用户标识
	if(isset($_COOKIE['stats_mark'])){
		$stats_mark = $_COOKIE['stats_mark'];
	}else{
		$stats_mark = base64_encode($remote_host);
		setcookie('stats_mark',$stats_mark,time()+3600*24*7,'/');
	}

	// 参数
	$from = isset($url_param['fr'])?trim($url_param['fr']):NULL;
	$channel = isset($url_param['ch'])?trim($url_param['ch']):NULL;
	$user_id = isset($_SESSION['user_id'])?intval($_SESSION['user_id']):0;
	$stats_type = empty($_SESSION['stats_type'])?'wap':$_SESSION['stats_type'];
	$sms_code = empty($_SESSION['stats_sms_code'])?'':$_SESSION['stats_sms_code'];
	$sms_send_date = empty($_SESSION['stats_sms_sendtime'])?'':$_SESSION['stats_sms_sendtime'];

	// 主表数据 tongji
	$tongji = array();
	$tongji['type'] = 'wap'; //$stats_type;
	$tongji['stats_mark'] = $stats_mark;
	$tongji['add_date'] = date("Y-m-d H:i:s",time());
	$keys = $values = array();
	foreach ($tongji as $key => $val) {
		$keys[] = '`'.$key.'`';
		$values[] = mysql_protect($val);
	}
	$query = "INSERT INTO ".$_table_tongji." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
	$result = mysql_query($query);
	$lastid = mysql_insert_id();
	if ($result){
		// 分表数据 sub_data
		$sub_table = $_table_tongji_wap;
		$sub_data = array();
		$sub_data['tj_id'] = $lastid;
		$sub_data['from'] = $from;
		$sub_data['channel'] = $channel;
		$sub_data['user_id'] = $user_id;
		$sub_data['remote_host'] = $remote_host;
		$sub_data['referer'] = $referer;
		$sub_data['stats_mark'] = $stats_mark;
		$sub_data['request'] = $request_url;
		$sub_data['user_agent'] = $user_agent;
		$sub_data['add_date'] = $tongji['add_date'];

		// 来自于短信插入表 tongji_sms
		// if($stats_type == 'sms'){
			// $sub_table = $_table_tongji_sms;
			$sub_data['sms_code'] = $sms_code;
			$sub_data['send_date'] = $sms_send_date;
		// }

		$keys = $values = array();
		foreach ($sub_data as $key => $val) {
			$keys[] = '`'.$key.'`';
			$values[] = mysql_protect($val);
		}
		$query = "INSERT INTO ".$sub_table." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
		$result = mysql_query($query);
		if (!$result)
		        print "query failed : " . mysql_error() . " : $query\n";
	}
}


/**
* 获取客户端IP地址
* */
function clientIp(){
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
	  $onlineip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
	  $onlineip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
	  $onlineip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
	  $onlineip = $_SERVER['REMOTE_ADDR'];
	}
	preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
	$onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
	unset($onlineipmatches);
	return $onlineip;
}

# remove displaying of errors, warning ini_set is disabled on free.fr
error_reporting(0);

db_open();
db_add_record();
db_close();
?>
