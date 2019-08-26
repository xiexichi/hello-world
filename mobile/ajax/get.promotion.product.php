<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$searchCategory = empty($_GET['category'])?'':intval($_GET['category']);
$searchKeywords = empty($_GET['keywords'])?'':htmlspecialchars($_GET['keywords']);
$pageSize       = intval($_GET["pagesize"]);
$pagecurrent    = intval($_GET["page"]);
$when           = empty($_GET['when'])?'yesterday':htmlspecialchars($_GET['when']);
$offset         = ($pagecurrent - 1) * $pageSize;
$pageTotal      = 0; //总页数
$promote_id     = $is_promote ? $promote['promote_id'] : 0;


/* ******************************************************************************
 *  获取可推广单品
 * ******************************************************************************/
$searchArr = array(
    'searchCategory' => $searchCategory,
    'searchKeywords' => $searchKeywords
);

//可推广项目
$promote_product_list = $Common->get_beyond_product_list($promote_id,$searchArr);


/* ******************************************************************************
 *  获取总页数
 * ******************************************************************************/
$sql = "SELECT count(pi.pitem_id) AS total FROM promote_item pi
        LEFT JOIN products p ON pi.item_id = p.product_id";

if(!empty($searchCategory)) $sql .= " LEFT JOIN product_to_category pc ON pi.item_id = pc.product_id";

$sql .= " WHERE pi.promote_id = {$promote_id} AND type = 0";

if(!empty($searchCategory)) $sql .= "  AND pc.category_id = {$searchCategory}";


if(!empty($searchKeywords)) $sql .= "  AND (p.product_name LIKE '%{$searchKeywords}%' OR p.sku_sn LIKE '%{$searchKeywords}%')";

$result = $DB->query($sql);
$row = $DB->fetch_array($result);
// echo count($row);exit();
$pageTotal = ceil($row['total'] / $pageSize);
// echo $pageTotal;exit();

/* ******************************************************************************
 *  获取数据
 * ******************************************************************************/
$sql = "SELECT *,(SELECT i.url FROM product_img i WHERE pi.item_id = i.product_id LIMIT 1) as url
        FROM promote_item pi
        LEFT JOIN products p ON pi.item_id = p.product_id";

if(!empty($searchCategory)) $sql .= " LEFT JOIN product_to_category pc ON pi.item_id = pc.product_id";

$sql .= " WHERE pi.promote_id = {$promote_id} AND type = 0";

if(!empty($searchCategory)) $sql .= "  AND pc.category_id = {$searchCategory}";

if(!empty($searchKeywords)) $sql .= "  AND (p.product_name LIKE '%{$searchKeywords}%' OR p.sku_sn LIKE '%{$searchKeywords}%')";

$sql .= " ORDER BY pi.pitem_id DESC LIMIT $offset,$pageSize";

$result = $DB->query($sql);
$promote_product = array();
while ($rows = $DB->fetch_array($result)) {
    $arr = array();
    $arr['pitem_id']    = $rows['pitem_id'];
    $arr['url']         = $rows['url'];
    $arr['product_id']  = $rows['item_id'];
    $arr['link']        = empty($rows['link']) ? '' : $rows['link'];
    $arr['product_name']= $rows['product_name'];
    array_push($promote_product, $arr);
}


//得出 点击量，付款笔数，效果预估，预估收入
foreach ($promote_product as $key => $value) {
    //获取链接
    $promote_product[$key]['link'] = $Base->getPromoteLink(PROMOTE_HTTP,$promote_id,0,$value['product_id']);

    //是否有效
    $promote_product[$key]['is_valid'] = empty($promote_product_list[$value['product_id']]) ? 0 : 1;

    //点击数
    $whenSql = $Common->getWhenSql('click_time',$when);
    $sql   = "SELECT count(pitem_id) AS total FROM promote_click WHERE pitem_id = {$value['pitem_id']} AND $whenSql";
    $query = $DB->query($sql);
    $promote_product[$key]['click_num'] = $DB->fetch_array()['total'];

    //付款笔数
    $whenSql = $Common->getWhenSql('o.pay_date',$when);
    $sql   = "SELECT count(DISTINCT po.order_id)AS total FROM promote_order po
              LEFT JOIN orders o ON po.order_id = o.order_id
              WHERE o.pay_status = 1 AND o.pay_method <> 2 AND po.pitem_id = {$value['pitem_id']} AND $whenSql";
    $query = $DB->query($sql);
    $promote_product[$key]['paid_order_num'] = $DB->fetch_array()['total'];

    //效果预估
    $whenSql = $Common->getWhenSql('o.pay_date',$when);
    $sql   = "SELECT sum(po.commission) AS total FROM promote_order po
              LEFT JOIN orders o ON po.order_id = o.order_id
              WHERE o.pay_status = 1 AND o.pay_method <> 2 AND po.pitem_id = {$value['pitem_id']} AND $whenSql";
    $query = $DB->query($sql);
    $row   = $DB->fetch_array();
    $promote_product[$key]['paid_order_total'] = empty($row['total']) ? '0.00' : $row['total'];

    //预估收入
    $whenSql = $Common->getWhenSql('o.pay_date',$when);
    $sql   = "SELECT sum(pe.earnings) AS total FROM promote_earnings pe
            LEFT JOIN orders o ON pe.order_id = o.order_id
            WHERE o.status = 8 AND pe.pitem_id = {$value['pitem_id']} AND $whenSql";
    $query = $DB->query($sql);
    $row   = $DB->fetch_array();
    $promote_product[$key]['received_order_total'] = empty($row['total']) ? '0.00' : $row['total'];
}
// echo count($promote_product);exit();

if($pagecurrent>$pageTotal){
    echo json_encode(array("status"=>"nomore"));

}else{
    echo json_encode(array("status"=>"success","list"=>$promote_product,"listLength"=>count($promote_product),'promote_id'=>$promote_id));
}


?>