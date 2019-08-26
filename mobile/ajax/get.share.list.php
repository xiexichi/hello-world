<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$tag = isset($_GET['tag'])?trim($_GET['tag']):'';
$sort = isset($_GET['sort'])?trim($_GET['sort']):'';

$pageSize = isset($_GET["pagesize"]) ? intval($_GET["pagesize"]) : 10;
$page =  isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$shareList = array("status"=>"success","list"=>array());

// 排序
if($sort == 'zan'){
    $order_sql = ' ORDER BY share_sort DESC,sort DESC, vote DESC, date_added DESC';
}else{
    $order_sql = ' ORDER BY share_sort DESC,sort DESC, date_added DESC';
}

// 分页
$n = ($page-1) * $pageSize;
$limit_sql =  " LIMIT {$n}, {$pageSize}";

$sql = "SELECT share.*,(SELECT count(*) FROM share_comment where share_comment.share_id = share.share_id) share_comment_count,
        (select count(*) from vote where vote_type='share' and item_id=share_id and vote=1) AS `vote`
        FROM share WHERE status=1 " . $order_sql . $limit_sql;
$query = $DB->query($sql);
while($result = $DB->fetch_assoc($query)){
    $result['photos'] = unserialize($result['photos']);
    $result['img_url'] = $Base->site_img($result['photos'][0]);
    $result['userimg'] = $Base->site_img($result['userimg']);
    $shareList['list'][] = $result;
}



/*$Table = 'v_share';
$Condition = ' WHERE status=1 ';
if(!empty($tag)){
    $Condition .= " AND `content` like '%".$tag."%' ";
}
$Condition .= ' order by share_sort desc,sort desc, date_added desc';
// $Condition .= ' order by sort desc, date_added desc';

$pageSize = intval($_GET["pagesize"]);
$pagecurrent =  intval($_GET["page"]);
$shareList = array("status"=>"success","list"=>array());

$Fileds = "*,(SELECT count(*) FROM share_comment where share_comment.share_id = ".$Table.".share_id) share_comment_count ";

$Row = $DB->getPage($Table,$Fileds,$Condition,$pageSize);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);
$pageAll = $DB->pageAll;
if($RowCount!=0){
    while($result = $DB->fetch_assoc($Row)){
    	$result['photos'] = unserialize($result['photos']);
        $result['img_url'] = $Base->site_img($result['photos'][0]);
        $result['userimg'] = $Base->site_img($result['userimg']);
        $result['desc'] = trim($result['desc']);
        $shareList['list'][] = $result;
    }
}*/

// print_r($shareList);

if($pagecurrent>$pageAll){
    echo json_encode(array("status"=>"nomore"));

}else{
    echo json_encode($shareList);
}


?>