<?php
$page_title = "最新潮流资讯";
$page_sed_title = '潮流资讯';

$catid = 6;
$tag = isset($_GET['tag'])?trim($_GET['tag']):'';

$catids = $Common->checkCategoryLevle($catid);
$Condition = "where article_cid in(".$catids.")";
$Table = 'article';
if(!empty($tag)){
	$Condition .= " AND (`keys` like '%".$tag."%' OR `title` like '%".$tag."%') ";
}

$trendList = array();
$Row = $DB->Get("article","`article_id`,`title`,`keys`,`article_cid`,`desc`,`click`,`img_url`,`date_added`", $Condition ." order by sort desc, date_added desc",5);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);
if($RowCount!=0){
    while($result = $DB->fetch_assoc($Row)){
    	$result['img_url'] = $Base->site_img($result['img_url']);
        $trendList[] = $result;
    }
}

$sm->assign("tag", $tag, true);
$sm->assign("catid", $catid, true);
$sm->assign("trendList", $trendList, true);