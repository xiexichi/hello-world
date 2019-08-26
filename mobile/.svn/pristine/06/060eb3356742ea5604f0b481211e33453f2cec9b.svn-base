<?php
/**
 * Date: 2016-01-22
*/
$article_id = isset($_GET['id'])?(int)$_GET['id']:14;
$info = array();

$CKey = 'about_'.$article_id;
$resultCache = $Cache -> get($CKey);
if (is_null($resultCache)){
	$object = $DB->GetRs("article","*","where article_id = ".$article_id);
	if(!isset($object['article_id']) || !$object['article_id']){
		$errorArray = array(
		    "title"=>'访问错误',
		    "description"=>'页面不存在，或已经删除',
		);
		show404($errorArray);
		exit;
	}
    $Cache->set($CKey, $object);
}else{
    $object = $resultCache;
}


$page_title = $object['title']." - 关于我们";
$page_sed_title = $object['title'];
$seo_keyword = $object['keys'].','.$seo_keyword;
$seo_desc = $object['desc']?$object['desc']:$seo_desc;

// print_r($object);
$object['tags'] = explode(',', $object['keys']);
$object['time'] = date('Y-m-d H:i',strtotime($object['date_added']));
$object['img_url'] = $Base->site_img($object['img_url']);
$sm->assign("object", $object, true);

// 显示主导航
$site_nav_display = 'show';