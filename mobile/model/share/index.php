<?php
$page_title = "25BOY晒图有礼 - 送EVISU牛仔裤";
$page_sed_title = '达人晒图';
$seo_desc = '来25BOY，看看潮人是怎样穿衣打扮的吧！';

$sort = isset($_GET['sort'])?trim(htmlentities($_GET['sort'])):'time';

// 排序
if($sort == 'zan'){
	$filterName = '按票数';
}else{
	$filterName = '按时间';
}

// 微信分享
$wxconfigarray = array(
	'title' => '#晒图有礼# 送EVISU牛仔裤！',  // #25BOY晒图# 看看潮人如何穿着吧！
	'link' => "http://m.25boy.cn/?m=share",
	'imgUrl' => 'http://img.25miao.com/114/1501397328.jpg',
	// 'desc' => '拿20元代金券',
);

if(isset($promote['promote_id'])){
    $PID = $Base->myEncode($promote['promote_id']);
    $wxconfigarray['link'] .= "&PI={$PID}";
}

$sm->assign("addend_top_banner", $Common->get_picshow(39,1), true);
$sm->assign("hide_site_top_banner", true, true);

$sm->assign("goback", '/', true);
$sm->assign("tag", $tag, true);
$sm->assign("shareList", $shareList, true);
$sm->assign("sort", $sort, true);
$sm->assign("filterName", $filterName, true);
