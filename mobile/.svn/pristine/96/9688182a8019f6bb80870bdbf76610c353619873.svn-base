<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if(!isset($_SESSION["user_id"]) || $_SESSION["user_id"]=="") {
    echo 'illegal';
    exit;
}

$order_id = $_POST['order_id'];
$flag_color = $_POST['flag_color'];
$remark = $_POST['remark'];

$result = $DB->query("UPDATE orders SET flag_color = '$flag_color',remark = '$remark' WHERE order_id = $order_id");
$row = $DB->affected_rows();
echo $row ? 1 : 0;
