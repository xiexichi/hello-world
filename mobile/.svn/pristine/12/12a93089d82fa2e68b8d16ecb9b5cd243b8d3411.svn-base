<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$withdrawal_type    = empty($_GET['withdrawal_type'])?'alipay':htmlspecialchars($_GET['withdrawal_type']);
$withdrawal_account = htmlspecialchars($_GET['withdrawal_account']);
$withdrawal_name    = htmlspecialchars($_GET['withdrawal_name']);
$promote_id     = $promote['promote_id'];
$user_id        = intval($_SESSION['user_id']);

//添加提现方式
$sql = "INSERT INTO promote_withdrawal (promote_id,withdrawal_type,withdrawal_account,withdrawal_name,create_time) 
        VALUES ({$promote_id},'{$withdrawal_type}','{$withdrawal_account}','{$withdrawal_name}',now())";

$result = $DB->query($sql);
$pwithdrawal_id = $DB->insert_id();

if($pwithdrawal_id) {
    echo json_encode(array('status'=>'success','msg'=>'添加成功！'));
}else {
    echo json_encode(array('status'=>'failed','msg'=>'添加失败！'));
}


?>