<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$Table="v_product_list";
$Fileds = "DISTINCT(product_id),sku_sn,brand_name,product_name,date_added,click,market_price,price,miao_price,total_quantity,sale,hot,url";

$k = isset($_GET["k"]) ? $_GET["k"] : "";
$k = js_unescape($k);

$default = isset($_GET["default"]) ? $_GET["default"] : 0;
$click = isset($_GET["click"]) ? $_GET["click"] : 0;
$sale = isset($_GET["sale"]) ? $_GET["sale"] : 0;
$price = isset($_GET["price"]) ? $_GET["price"] : 0;
$start_price = isset($_GET["start_price"]) ? $_GET["start_price"] : 0;
$end_price = isset($_GET["end_price"]) ? $_GET["end_price"] : 0;
$new = isset($_GET["new"]) ? $_GET["new"] : 0;
$start_price = is_numeric($start_price) ? $start_price : 0;
$end_price = is_numeric($end_price) ? $end_price : 0;

$brand = isset($_GET["brand"]) ? $_GET["brand"] : 0;
$brand_id = isset($_GET["brand_id"]) ? $_GET["brand_id"] : 0;
if(!$brand){
    $brand_id=0;
}

$product_ids = "";
$product_tags = array();
$TableTag="v_tags";
$FiledsTag = "product_id";
$ConditionTag = "where tag_name like '%".$k."%' Order by clicks desc";
$producttags = array();
$RowTag = $DB->Get($TableTag,$FiledsTag,$ConditionTag,0);
$RowTag = $DB->result;
$RowCountTag = $DB->num_rows($RowTag);
if($RowCountTag!=0){
    while($resultTag = $DB->fetch_assoc($RowTag)){
        array_push($product_tags, $resultTag["product_id"]);
    }
}
if(count($product_tags)>0){
    $product_ids = implode(",",$product_tags);
}
//echo $product_ids;


$Condition = "where stock=1";
if($k!=""){
    if($product_ids!=""){
        $Condition .= " and (product_id in (".$product_ids.") or sku_sn like '%".$k."%' or product_name like '%".$k."%')";
    }else{
        $Condition .= " and (sku_sn like '%".$k."%' or product_name like '%".$k."%')";
    }
}
if($brand_id){
    $Condition .= " and brand_id =".(int)$brand_id;
}

if($start_price!=0&&$end_price!=0){
    if($start_price==$end_price){
        $Condition .= " and price=".$start_price;
    }else{
        if($start_price>$end_price){
            $Condition .= " and price<=".$start_price." and price>=".$end_price;
        }else{
            $Condition .= " and price>=".$start_price." and price<=".$end_price;
        }

    }
}
$Condition .= " Order by ";
if($default==1){
    $Condition .= "date_added desc";
}else{
    if($click){
        $Condition .= "click desc,";
    }
    if($sale){
        $Condition .= "sale desc,";
    }
    if($price==1){
        $Condition .= "price desc,";
    }
    if($price==2){
        $Condition .= "price asc,";
    }
    if($new){
        $Condition .= "date_added desc";
    }
    if($click==0&&$sale==0&&$price==0&&$new==0){
        $Condition .= "date_added desc";
    }else{
        $Condition = rtrim($Condition, ",");
    }

}

///echo $Condition;


$pageSize = $_GET["pagesize"];
$pagecurrent =  $_GET["page"];
$productlist = array();

$Row = $DB->getPage($Table,$Fileds,$Condition,$pageSize);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);
$pageAll = $DB->pageAll;

if($RowCount!=0){
    while($result = $DB->fetch_assoc($Row)){
        array_push($productlist, array(
            "product_id"=>$result["product_id"],
            "product_name"=>$result["product_name"],
            "miao_price"=>$result["miao_price"] ? ceil($result["miao_price"]) : "",
            "market_price"=>ceil($result["market_price"]),
            "price"=>ceil($result["price"]),
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
    return $ret;
}

?>