<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$promote_id = isset($_POST["promote_id"]) ? (int)$_POST["promote_id"] : 0;
$type = isset($_POST["type"]) ? (int)$_POST["type"] : 0;
$item_id = isset($_POST["item_id"]) ? (int)$_POST["item_id"] : 0;

$link = $Base->get_link($promote_id,$type,$item_id);

$sql = "INSERT INTO promote_item (promote_id,type,item_id,link,create_time) VALUES ({$promote_id},{$type},{$item_id},'{$link}',now())";
$result = $DB->query($sql);

if($DB->insert_id() > 0) {
	echo $link;
}else {
	echo 0;
}
exit;
?>
