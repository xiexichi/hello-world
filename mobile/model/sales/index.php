<?php
/**
 * Date: 2016-03-10
 * 如果没有id，则取最新的活动页面
*/
$sales_id = isset($_GET['id'])?intval($_GET['id']):0;
$Condition = ' ORDER BY sort DESC,sales_id DESC';
if($sales_id>0){
	$Condition = "where sales_id=".$sales_id.$Condition;
}

$CKey = 'sales_'.$sales_id;
$resultCache = $Cache -> get($CKey);
if (is_null($resultCache)){
	$object = $DB->GetRs("sales","*",$Condition);
	if(!isset($object['sales_id']) || !$object['sales_id']){
		$Error->show('页面不存在，或已经删除','访问错误');
		exit;
	}
    $Cache->set($CKey, $object);
}else{
    $object = $resultCache;
}

// 页面seo
$page_title = $object['title'];
$page_sed_title = $object['title'];
$object['keys'] && $seo_keyword=$object['keys'];
$object['desc'] && $seo_desc=$object['desc'];

// 微信分享
$wxconfigarray = array(
    'title' => $object['title'],
    'link' => 'http://m.25boy.cn/?m=sales&id='.$object['sales_id'],
    'imgUrl' => empty($object['img_url'])?'':$Base->site_img($object['img_url']),
    'desc' => empty($object['desc'])?'25BOY国潮男装':$object['desc'],
);
$sm->assign("object", $object, true);

// 显示主导航
$site_nav_display = 'show';
// 隐藏头banner
$sm->assign("hide_site_top_banner", true, true);

// 阅读 +1
$DB->Set('sales', "click=click+1", "where sales_id=" . $object['sales_id'] );
