<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

$returnjson = array(
    "status"=>"success",
    "bycolor"=>array(),
    "bysize"=>array()
);

if($id==0){
    echo json_encode($returnjson);
    exit;
}

//查询产品是否下架
$Table="products";
$Fileds = "*";
$Condition = "where product_id=".$id;

$row = $DB->GetRs($Table,$Fileds,$Condition);
if(empty($row)){
    $returnjson["status"] = "noproduct";
    echo json_encode($returnjson);
    exit;
}else{


    if($row["stock"]==1&&$row["total_quantity"]>0){
        $undercarriage = false;
    }else{
        $undercarriage = true;

    }

}

if($undercarriage){
    $returnjson["status"] = "undercarriage";
    echo json_encode($returnjson);
    exit;
}

///////////////////////////////
$bycolor = array();

$Table="prop";
$Fileds = "distinct color_prop,color_photo";
$Condition = "where quantity > 0 AND product_id=".$id;
$Row = $DB->Get($Table,$Fileds,$Condition,0);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);
if($RowCount!=0){
    while($result = $DB->fetch_assoc($Row)) {
        $temp_size = array();
        $totalquantity = 0;
        $TableFind="prop";
        $FiledsFind = "size_prop,quantity,presale";
        $ConditionFind = "where quantity > 0 AND product_id=".$id. " AND color_prop='".$result['color_prop']."'";
        $RowFind = $DB->Get($TableFind,$FiledsFind,$ConditionFind,0);
        $RowFind = $DB->result;
        $RowCountFind = $DB->num_rows($RowFind);
        if($RowCountFind!=0){
            while($resultFind = $DB->fetch_assoc($RowFind)) {
                $temp_size[$resultFind['size_prop']] = array(
                    "sizename"=>$resultFind['size_prop'],
                    "quantity"=>$resultFind['quantity'],
                    "presale"=>isset($resultFind['presale'])?(int)$resultFind['presale']:0,
                );
                $totalquantity = $totalquantity+$resultFind['quantity'];
            }
        }
        array_push($bycolor,array(
            "colorname"=>$result['color_prop'],
            "colorimg"=>$result['color_photo'],
            "quantity"=>$totalquantity,
            "size"=>$temp_size
        ));
    }
    $returnjson["bycolor"]=$bycolor;
}else{
    $returnjson["status"] = "quantity";
    echo json_encode($returnjson);
    exit;
}

///////////////////////////////
$bysize = array();

$Table="prop";
$Fileds = "distinct size_prop,presale";
$Condition = "where quantity > 0 AND product_id=".$id;
$Row = $DB->Get($Table,$Fileds,$Condition,0);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);
if($RowCount!=0){
    while($result = $DB->fetch_assoc($Row)) {
        $temp_size = array();
        $totalquantity = 0;
        $TableFind="prop";
        $FiledsFind = "color_prop,quantity";
        $ConditionFind = "where quantity > 0 AND product_id=".$id. " AND size_prop='".$result['size_prop']."'";
        $RowFind = $DB->Get($TableFind,$FiledsFind,$ConditionFind,0);
        $RowFind = $DB->result;
        $RowCountFind = $DB->num_rows($RowFind);
        if($RowCountFind!=0){
            while($resultFind = $DB->fetch_assoc($RowFind)) {
                $totalquantity = $totalquantity+$resultFind['quantity'];
            }
        }
        $bysize[$result['size_prop']] = array(
            "sizename"=>$result['size_prop'],
            "quantity"=>$totalquantity,
            "presale"=>isset($result['presale'])?(int)$result['presale']:0,
        );
    }
    $returnjson["bysize"]=$bysize;

}else{
    $returnjson["status"] = "quantity";
    echo json_encode($returnjson);
    exit;
}

// print_r($returnjson);
echo json_encode($returnjson);
exit;