<?php
$article_id = isset($_GET['id'])?(int)$_GET['id']:0;
$object = array();

$CKey = 'trends_'.$article_id;
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

$page_title = $object['title']." - 最新潮流资讯";
$page_sed_title = '潮流资讯';
$seo_keyword = $object['keys'].','.$seo_keyword;
$seo_desc = $object['desc']?$object['desc']:$seo_desc;


// print_r($object);
$object['tags'] = explode(',', $object['keys']);
$object['time'] = date('Y-m-d H:i',strtotime($object['date_added']));
$object['img_url'] = $Base->site_img($object['img_url']);
$sm->assign("object", $object, true);



// 相关文章
$catid = $object['article_cid'];
$catids = $Common->checkCategoryLevle($catid);
$related = array();
$Row = $DB->Get("article","`article_id`,`title`,`keys`,`article_cid`,`desc`,`click`,`img_url`,`date_added`","where article_id<>".$object['article_id']." AND article_cid in(".$catids.") order by sort desc, date_added desc",6);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);
if($RowCount!=0){
    while($result = $DB->fetch_assoc($Row)){
    	$result['img_url'] = $Base->site_img($result['img_url']);
        $related[] = $result;
    }
}
$sm->assign("related", $related, true);


// 阅读 +1
$DB->Set('article', "click=click+1", "where article_id=" . $article_id );
