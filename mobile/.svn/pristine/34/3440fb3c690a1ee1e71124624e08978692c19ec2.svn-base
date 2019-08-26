<?php
/*
* 获取广告列表
* 先取得缓存数据
* 再用 array_slice 数据分页返回数据
*/

include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$posid = isset($_GET['posid'])?intval($_GET['posid']):0;
$page = isset($_GET['page'])?intval($_GET['page']):0;
$pagesize = isset($_GET['pagesize'])?intval($_GET['pagesize']):10;

$datalist = array();
$start = ($page-1)*$pagesize;
if(!empty($posid)){
    $dataAll = $datalist = $Common->get_picshow($posid);
    $datalist = array_slice($datalist,$start,$pagesize);
}

$pageAll = 0;
if(count($dataAll) > 0){
	$pageAll = count($dataAll)/$pagesize;
}

if($page>$pageAll){
    echo json_encode(array("status"=>"nomore"));

}else{
    echo json_encode($datalist);
}