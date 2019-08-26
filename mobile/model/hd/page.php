<?php
/*
* 活动二级页 + 商品列表
* 2016-12-02
*/
$id = isset($_GET['id']) ? intval($_GET['id']) : '';
if(empty($id)){
	header("Location: /");
	exit;
}

// 查询广告位
$pos = $DB->GetRs('position',"pos_id,posname", "where pos_id='{$id}'");
if(empty($pos)){
	header("Location: /");
	exit;
}

// 广告位下子类目
$rs = $DB->Get('position',"pos_id,posname", "where parent={$id} order by sort asc limit 2");
$subPos = array();
while($row = $DB->fetch_assoc($rs)) {
    $subPos[] = $row;
}

$sm->assign("adset", $Common->get_picshow($subPos[0]['pos_id']), true);	// 页面广告
$prolist = $Common->get_picshow($subPos[1]['pos_id']);					// 商品列表

$sm->assign("prolist", $prolist, true);
$sm->assign("hide_site_top_banner", true, true);
$page_title = $pos['posname'];
$page_sed_title = $pos['posname'];