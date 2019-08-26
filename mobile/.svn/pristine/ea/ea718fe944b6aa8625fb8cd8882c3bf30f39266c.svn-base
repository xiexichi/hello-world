<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$catid = isset($_GET['id'])?(int)$_GET['id']:0;
$tag = isset($_GET['tag'])?trim($_GET['tag']):'';
if(!$catid) {
    echo json_encode(
        array("status"=>"no_catid")
    );
    exit;
}

$catids = $Common->checkCategoryLevle($catid);
$Table = 'article';
$Fileds = '`article_id`,`title`,`keys`,`article_cid`,`desc`,`click`,`img_url`,`date_added`';
$Condition = '';
$Condition = "where article_cid in(".$catids.") ";
if(!empty($tag)){
    $Condition .= " AND (`keys` like '%".$tag."%' OR `title` like '%".$tag."%') ";
}
$Condition .= ' order by sort desc, date_added desc ';


$pageSize = $_GET["pagesize"];
$pagecurrent =  $_GET["page"];
$trendList = array("status"=>"success","list"=>array());

$Row = $DB->getPage($Table,$Fileds,$Condition,$pageSize);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);
$pageAll = $DB->pageAll;
if($RowCount!=0){
    while($result = $DB->fetch_assoc($Row)){
        $result['img_url'] = $Base->site_img($result['img_url']);
        $result['desc'] = trim($result['desc']);
        $trendList['list'][] = $result;
    }
}

// print_r($trendList);

if($pagecurrent>$pageAll){
    echo json_encode(array("status"=>"nomore"));

}else{
    echo json_encode($trendList);
}


?>