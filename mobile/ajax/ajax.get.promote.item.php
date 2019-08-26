<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$item_id = empty($_POST['item_id'])?'':intval($_POST['item_id']);
if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] != 0) {
    $promote = $DB->GetRs("promote","*","WHERE user_id = ".$_SESSION["user_id"]);
    if(!empty($promote)) {
		$promote_id = $promote['promote_id'];
		$type       = 0;
		$item_id    = $item_id;
		$link = $Base->get_link($promote_id,$type,$item_id);
		$DB->Add('promote_item',array(
			'promote_id' => $promote_id,
			'type' => $type,
			'item_id' => $item_id,
			'link' => $link,
			'create_time' => date('Y-m-d H:i:s'),
		));
		if($DB->insert_id()) {
		    echo json_encode(array('status'=>'success','data'=>$link));
		    exit();
		}
    }
}

echo json_encode(array('status'=>'failed'));

?>