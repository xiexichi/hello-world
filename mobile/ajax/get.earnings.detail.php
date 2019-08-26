<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$pageSize       = intval($_GET["pagesize"]);
$pagecurrent    = intval($_GET["page"]);
$offset         = ($pagecurrent - 1) * $pageSize;
$pageTotal      = 0; //总页数
$promote_id     = $is_promote ? $promote['promote_id'] : 0;



/* ******************************************************************************
 *  获取总页数
 * ******************************************************************************/

$sql = "SELECT count(pearnings_id) AS total FROM promote_earnings pe WHERE pe.promote_id = $promote_id";
$result = $DB->query($sql);
$row = $DB->fetch_array($result);
$pageTotal = ceil($row['total'] / $pageSize);
// echo $pageTotal;exit();

/* ******************************************************************************
 *  获取数据
 * ******************************************************************************/
$sql = "SELECT pe.earnings_type,pe.re_price,pe.commission_rate,pe.earnings,pe.is_get,pe.re_price,pe.product_num,pe.received_time,p.product_name,b.method FROM promote_earnings pe
    LEFT JOIN products p  ON pe.product_id = p.product_id
    LEFT JOIN bag b ON pe.bag_id = b.bag_id
    WHERE pe.promote_id = $promote_id
    ORDER BY received_time DESC";

$sql .= " LIMIT $offset,$pageSize";

$result = $DB->query($sql);
$earnings_detail = array();
while ($row = $DB->fetch_array($result)) {
    array_push($earnings_detail, $row);
}

if($pagecurrent>$pageTotal){
    echo json_encode(array("status"=>"nomore"));

}else{
    echo json_encode(array("status"=>"success","list"=>$earnings_detail,"listLength"=>count($earnings_detail),'promote_id'=>$promote_id));
}


?>