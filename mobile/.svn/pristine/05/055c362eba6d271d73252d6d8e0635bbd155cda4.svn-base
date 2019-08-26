<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$Table="products AS p LEFT JOIN brands AS b ON p.brand_id=b.brand_id 
        LEFT JOIN product_to_category AS pc ON p.product_id=pc.product_id";
$Fileds = "DISTINCT(p.product_id),b.brand_id,b.brand_name,p.product_name,p.date_added,p.click,p.market_price,p.price,p.total_quantity,p.sale,p.hot,p.status,(select url from product_img where product_id=p.product_id order by sort asc limit 1) as url";

$k = isset($_GET["k"]) ? addslashes(htmlspecialchars(trim($_GET["k"]))) : "";
$cid = isset($_GET["cid"]) ? $_GET["cid"] : 0;
$default = isset($_GET["default"]) ? $_GET["default"] : 0;
$click = isset($_GET["click"]) ? $_GET["click"] : 0;
$sale = isset($_GET["sale"]) ? $_GET["sale"] : 0;
$price = isset($_GET["price"]) ? $_GET["price"] : 0;
$start_price = isset($_GET["start_price"]) ? $_GET["start_price"] : 0;
$end_price = isset($_GET["end_price"]) ? $_GET["end_price"] : 0;
$new = isset($_GET["new"]) ? $_GET["new"] : 0;
$start_price = is_numeric($start_price) ? $start_price : 0;
$end_price = is_numeric($end_price) ? $end_price : 0;
$user_id = $_SESSION["user_id"] ? intval($_SESSION["user_id"]) : '';

$brand = isset($_GET["brand"]) ? $_GET["brand"] : 0;
$brand_id = isset($_GET["brand_id"]) ? $_GET["brand_id"] : 0;
if(!$brand){
    $brand_id=0;
}

$Condition = "where p.stock=1";

// 商品状态，0=正常，1=仅会员可见，2=仅分销可见
$sql_status = "p.status = 0";
if(!empty($user_id)){
  if(!empty($is_seller)){
    $sql_status = "p.status <> 1";
  }else{
    $sql_status = "p.status <> 2";
  }
}
$Condition .= ' AND '.$sql_status;

// 关键字
if(!empty($k)){
    $Condition .= " and (p.sku_sn like '%".$k."%' or p.product_name like '%".$k."%')";
}

// 当前分类ID集合
$category_ids = $Common->checkCategoryLevle($cid,'category');
if($cid){
    $Condition .= " and pc.category_id IN(".$category_ids.")";
}
if($brand_id){
    $Condition .= " and p.brand_id =".(int)$brand_id;
}

if($start_price!=0&&$end_price!=0){
    if($start_price==$end_price){
        $Condition .= " and p.price=".$start_price;
    }else{
        if($start_price>$end_price){
            $Condition .= " and p.price<=".$start_price." and p.price>=".$end_price;
        }else{
            $Condition .= " and p.price>=".$start_price." and p.price<=".$end_price;
        }

    }
}
$Condition .= " Order by ";
if($default==1){
    $Condition .= "p.date_added desc";
}else{
    if($click){
        $Condition .= "p.click desc,";
    }
    if($sale){
        $Condition .= "p.sale desc,";
    }
    if($price==1){
        $Condition .= "p.price desc,";
    }
    if($price==2){
        $Condition .= "p.price asc,";
    }
    if($new){
        $Condition .= "p.date_added desc";
    }
    if($click==0&&$sale==0&&$price==0&&$new==0){
        $Condition .= "p.date_added desc";
    }else{
        $Condition = rtrim($Condition, ",");
    }
}

// echo $Condition; exit;

// 商品列表分页
$pageSize = $_GET["pagesize"];
$pagecurrent =  $_GET["page"];
$productlist = array();
$Row = $DB->getPage($Table,$Fileds,$Condition,$pageSize);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);
$pageAll = $DB->pageAll;
if($RowCount!=0){
    while($result = $DB->fetch_assoc($Row)){
        $query = $DB->query("SELECT mi.miao_price FROM miao_item mi LEFT JOIN miao m ON m.miao_id = mi.miao_id WHERE mi.product_id = {$result['product_id']} AND (m.start_date < now() AND m.end_date > now() OR m.start_date > now())");
        $miao = $DB->fetch_array($query);
        array_push($productlist, array(
            "product_id"=>$result["product_id"],
            "product_name"=>$result["product_name"],
            "miao_price"=>isset($miao["miao_price"]) ? $miao["miao_price"] : 0,
            "market_price"=>$result["market_price"],
            "price"=>$result["price"],
            "total_quantity"=>$result["total_quantity"],
            "sale"=>$result["sale"],
            "hot"=>$result["hot"],
            "brand_name"=>$result["brand_name"],
            "thumb"=>$result["url"]."!w390"
        ));
    }
}

if($pagecurrent>$pageAll){
    echo json_encode(array());
}else{
    echo json_encode($productlist);
}


function js_unescape($str){
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++)
    {
        if ($str[$i] == '%' && $str[$i+1] == 'u')
        {
            $val = hexdec(substr($str, $i+2, 4));
            if ($val < 0x7f) $ret .= chr($val);
            else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));                        else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
            $i += 5;
        }
        else if ($str[$i] == '%')
        {
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        }
        else $ret .= $str[$i];
    }
}