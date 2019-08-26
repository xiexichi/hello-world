<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$searchCategory = empty($_GET['category'])?'':intval($_GET['category']);
$searchKeywords = empty($_GET['keywords'])?'':htmlspecialchars($_GET['keywords']);
$pageSize       = intval($_GET["pagesize"]);
$pagecurrent    = intval($_GET["page"]);
$offset         = ($pagecurrent - 1) * $pageSize;
$pageTotal      = 0; //总页数
$promote_id     = $is_promote ? $promote['promote_id'] : 0;


/* ******************************** 获取可推广商品 ******************************** */
$searchArr = array(
    'searchCategory' => $searchCategory,
    'searchKeywords' => $searchKeywords
);
//推广单品
$promote_product = $Common->get_beyond_product_list($promote_id,$searchArr);
//总条数
$total = count($promote_product);
//总页数
$pageTotal = ceil($total / $pageSize);
//计算月销量，并按月销量从高到低排列
$rows = $new_promote_product = array();
//处理数组，计算月销量
foreach ($promote_product as $key => $value) {
    $monthSale = ceil(abs($value['sale'])*30/$value['datediff']);
    $rows[$key] = $monthSale;
    $promote_product[$key]['monthSale'] = $monthSale;
    // $promote_product[$key]['sale'] = abs($value['sale']);
}
//排序
arsort($rows);
foreach ($rows as $key => $value) {
    $new_promote_product[$key] = $promote_product[$key];
}
// print_r($new_promote_product);exit();
$promote_product = $new_promote_product;

//获得加载数据
//这里array_slice第四个参数如果为true,排序会乱，不知道为什么
$promote_product = array_slice($promote_product, $offset,10);
// print_r($promote_product);

//查找链接
foreach ($promote_product as $key => $value) {
    $promote_product[$key]['link'] = $Base->getPromoteLink(PROMOTE_HTTP,$promote_id,0,$value['product_id']);
}

if($pagecurrent>$pageTotal){
    echo json_encode(array("status"=>"nomore"));

}else{
    echo json_encode(array("status"=>"success","list"=>$promote_product,"listLength"=>count($promote_product),"promote_id"=>$promote_id));
}


?>