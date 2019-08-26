<?php
$page_title = "订单中心";
$page_sed_title = '售后';

$sort = isset($_GET["s"]) ? strval($_GET["s"])  : "";
$sort = strval($sort);
$order=array();

$order_status = array(
    '0'  => '等待付款',
    '1'  => '等待发货',
    '2'  => '已经发货',
    '3'  => '退款审核中',
    '4'  => '已经退款',
    '5'  => '退货审核中',
    '6'  => '已经退货',
    '7'  => '换货审核中',
    '8'  => '交易完成',
    '-1' => '交易关闭',
);
$spanbg = array(
    '0' => 'importbg',
    '1' => 'normalbg',
    '2' => 'normalbg',
    '3' => 'normalbg',
    '4' => 'disbg',
    '5' => 'normalbg',
    '6' => 'disbg',
    '7' => 'normalbg',
    '8' => 'disbg',
    '-1' => 'disbg',
);

/******************************************************************
    配送方式
*******************************************************************/

$deliverys = array();
$result = $DB->Get('delivery','*',"WHERE status = 1 ORDER BY is_default DESC,delivery_id ASC");
while (!!$rows = $DB->fetch_array($result)) {
    $deliverys[$rows['delivery_id']] = $rows;
}

$sm->assign("spanbg", $spanbg, true);
if($_SESSION["user_id"]!=""&&$_SESSION["user_id"]!=0) {
    $Condition = "where relation_order<>'' and user_id=" . $_SESSION["user_id"];
    switch ($sort) {
        case "0":
            $Condition .= " and status=0 ";
            $page_sed_title = '待付运费';
            break;
        case "1":
            $Condition .= " and status=1 ";
            $page_sed_title = '待发货';
            break;
        case "2":
            $Condition .= " and status=2 ";
            $page_sed_title = '待收货';
            break;
        case "8":
            $Condition .= " and status=8 ";
            $page_sed_title = '交易完成';
            break;
        case "all":
            $page_sed_title = '售后';
            break;
    }
    $current_title = $page_sed_title;
    $page_sed_title .= '订单';

    $Table = "orders";
    $Fileds = "*";
    $Condition .= " order by order_date desc";
    $pageSize = 10;
    $pagecurrent =  (int)$_GET["page"];
    $Row = $DB->getPage($Table, $Fileds, $Condition, $pageSize);
    $Row = $DB->result;
    $RowCount = $DB->num_rows($Row);
    $pageAll = $DB->pageAll;
    $showPage = $DB->showPageforFront(array('prev','next','info'));
    if ($RowCount > 0 && $pageAll>=$pagecurrent) {
        while ($result = $DB->fetch_assoc($Row)) {

            $order_items = array();
            $order_totalnum = 0;
            $TableItems = "v_order_items";
            $ConditionItems = " where order_id=" . $result["order_id"];
            $RowItems = $DB->Get($TableItems, $Fileds, $ConditionItems, 0);
            $RowCountItems = $DB->num_rows($RowItems);
            if ($RowCountItems != 0) {
                while ($resultItems = $DB->fetch_assoc($RowItems)) {
                    array_push($order_items, array(
                        "order_id" => $resultItems["order_id"],
                        "product_id" => $resultItems["product_id"],
                        "size_prop" => $resultItems["size_prop"],
                        "color_prop" => $resultItems["color_prop"],
                        "price" => $resultItems["price"],
                        "num" => $resultItems["num"],
                        "amount" => $resultItems["amount"],
                        "product_name" => $resultItems["product_name"],
                        "color_photo" => $resultItems["color_photo"] . "!w200"
                    ));
                    $order_totalnum = $order_totalnum + $resultItems["num"];
                }
            }

            // 查询关联的原订单
            if($result["relation_order"]){
                $Table2="orders";
                $Fileds2 = "order_id,order_sn,status,return_num,reout";
                $Condition2 = "where order_sn='".$result["relation_order"]."'";
                $orgOrder = $DB->GetRs($Table2,$Fileds2,$Condition2);
            }

            array_push($order, array(
                "order_id" => $result["order_id"],
                "order_sn" => $result["order_sn"],
                "strat_date" => $result["order_date"],
                "order_date" => $Base->FormatTime($result["order_date"], "", true),
                "status" => $result["status"],
                "status_name" => $order_status[$result["status"]],
                "pay_total" => $result["pay_total"],
                "pay_method" => $result["pay_method"],
                "receiver_name" => $result["receiver_name"],
                "receiver_phone" => $result["receiver_phone"],
                "location" => $result["location"],
                "exp_date" => $result["exp_date"],
                "ship_price" => $result["ship_price"],
                "order_items" => $order_items,
                "order_totalnum" => $order_totalnum,
                "order_time" => strtotime($result['exp_date']) - time(),
                "return_num" => $result["return_num"],
                "reout" => $result["reout"],
                'relation_order' => $result['relation_order'],
                'orgOrder' => $orgOrder,
                 "delivery"=>empty($deliverys[$result['delivery_id']]) ? null : $deliverys[$result['delivery_id']],
            ));
        }
    }

    $sm->assign("order", $order, true);
    $sm->assign("showPage", $showPage, true);

}
$sm->assign("current_title", $current_title, true);

