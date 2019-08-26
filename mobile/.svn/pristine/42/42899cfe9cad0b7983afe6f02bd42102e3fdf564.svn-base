<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if(empty($_SESSION["user_id"])) {
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}

$array = array(
    'paid'=>'已付款',
    'payment'=>'未付款',
    'alipay'=>'支付宝',
    'bag'=>'钱包',
    'weixin'=>'微信',
    'goods'=>'商品消费',
    'prepaid'=>'充值',
    'refund'=>'退款',
    'ship_fee'=>'售后运费',
    '25boy'=>'25BOY',
);

$pageSize = isset($_GET["pagesize"])?(int)$_GET["pagesize"]:0;
$pagecurrent = isset($_GET["page"])?(int)$_GET["page"]:0;
$c = isset($_GET["c"]) ? htmlspecialchars($_GET["c"]) : 'xf';

$bag_list = array("status"=>"success","list"=>array());
if(!empty($_SESSION["user_id"])){
    $Condition =  "where user_id=" . (int)$_SESSION["user_id"];
    switch($c){
        case 'xf':
            $Condition .= " AND `type`='goods' ";
            break;
        case 'cz':
            $Condition .= " AND (`type`='prepaid' OR `type`='25boy' OR `type`='withdrawal') ";
            break;
        case 'tk':
            $Condition .= " AND (`type`='refund' OR `type`='deduct') ";
            break;
        case 'yf':
            $Condition .= " AND `type`='ship_fee' ";
            break;
        default:
            break;
    }
    $Condition .= " ORDER BY create_date DESC ";

    $Row = $DB->getPage("bag", "`bag_id`,`money`,`method`,`create_date`,`pay_sn`,`pay_status`,`type`,`note`,`plus_price`,`transaction_id`", $Condition, $pageSize);
    $Row = $DB->result;
    $RowCount = $DB->num_rows($Row);
    $pageAll = $DB->pageAll;
    if ($RowCount != 0) {
        while($result = $DB->fetch_assoc($Row)){
            array_push($bag_list["list"], array(
                "bag_id"=>$result["bag_id"],
                "money"=>$result["money"],
                "method"=>$array[$result["method"]],
                "type"=>$array[$result["type"]],
                "typeCode"=>$result["type"],
                "create_date"=>$result["create_date"],
                "pay_sn"=>!empty($result["pay_sn"]) ? $result["pay_sn"] : "",
                "pay_status"=>$array[$result["pay_status"]],
                "transaction_id"=>!empty($result["transaction_id"]) ? $result["transaction_id"] : "",
                "plus_price"=>$result["plus_price"],
                "note"=>$result["note"],
            ));

        }
    }
}
if($pagecurrent>$pageAll){
    echo json_encode(array("status"=>"nomore"));
}else{
    echo json_encode($bag_list);
}