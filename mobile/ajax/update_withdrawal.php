<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$withdrawal_type    = empty($_GET['withdrawal_type'])?'alipay':htmlspecialchars($_GET['withdrawal_type']);
$withdrawal_account = htmlspecialchars($_GET['withdrawal_account']);
$withdrawal_name    = htmlspecialchars($_GET['withdrawal_name']);
$pwithdrawal_id		= intval($_GET['pwithdrawal_id']);
$promote_id     	= $promote['promote_id'];
$user_id        	= intval($_SESSION['user_id']);

//添加提现方式
$sql = "UPDATE promote_withdrawal SET withdrawal_type = '{$withdrawal_type}',withdrawal_account = '{$withdrawal_account}',withdrawal_name = '{$withdrawal_name}' WHERE pwithdrawal_id = $pwithdrawal_id";

$result = $DB->query($sql);

if($DB->affected_rows()) {
    echo json_encode(array('status'=>'success','msg'=>'修改成功！'));
}else {
    echo json_encode(array('status'=>'failed','msg'=>'没有数据被修改！'));
}


?>