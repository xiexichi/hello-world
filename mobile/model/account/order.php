<?php
$page_title = "订单中心";
$page_sed_title = '全部';
$page_sed_search = 'search_orders';

$sort = isset($_GET["s"]) ? strval($_GET["s"])  : "";
$keywords = (isset($_GET["k"]) && !empty($_GET["k"])) ? strval($_GET["k"])  : "";
$order=array();

$order_status = array(
    '0'  => '等待付款',
    '1'  => '等待发货',
    '2'  => '已经发货',
    '3'  => '退款审核中',
    '4'  => '已经退款',
    '5'  => '退货审核中',
    '6'  => '已经退货',
    '7'  => '换货中',
    '8'  => '交易成功',
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

    // 自动关闭超时订单
    $Common->auto_close_notpay_order($_SESSION["user_id"]);
    // 自动关闭超时未寄回订单
    $Common->auto_close_exchange_order($_SESSION["user_id"]);

    $Condition = "where (o.relation_order is null OR o.relation_order='') and o.user_id=" . $_SESSION["user_id"];
    switch ($sort) {
        case "0": case "nopay":
            $Condition .= " and o.status=0 ";
            $page_sed_title = '待付款';
            break;
        case "1": case "pack":
            $Condition .= " and o.status=1 ";
            $page_sed_title = '等待发货';
            break;
        case "2": case "wait":
            $Condition .= " and o.status=2 ";
            $page_sed_title = '待收货';
            break;
        case "3":
            $Condition .= " and (o.status=3 or o.status=4) ";
            $page_sed_title = '退款';
            break;
        case "5":
            $Condition .= " and o.status=5 ";
            $page_sed_title = '退货';
            break;
        case "7":
            $Condition .= " and o.status=7 ";
            $page_sed_title = '换货';
            break;
        case "8":
            $Condition .= " and o.status=8 ";
            $page_sed_title = '交易成功';
            break;
        case "-1":
            $Condition .= " and o.status=-1 ";
            $page_sed_title = '交易关闭';
            break;
        case "return":
            $Condition .= " and (o.status=5 or o.status=7) ";
            $page_sed_title = '退换货';
            break;
        case "all":
            $page_sed_title = '全部';
            break;
    }

    //关键字查询
    if($keywords) {
        $Condition .= " and (o.order_sn LIKE '%$keywords%'
                            OR o.receiver_name LIKE '%$keywords%' 
                            OR o.receiver_phone LIKE '%$keywords%'
                            OR o.remark LIKE '%$keywords%'
                            OR oi.product_name LIKE '%$keywords%'
                    )";
    }


    $current_title = $page_sed_title;
    $page_sed_title .= '订单';

    $Table = "orders o LEFT JOIN order_items oi ON o.order_id = oi.order_id";
    $Fileds = "*";
    $Condition .= " GROUP BY o.order_id order by o.order_date desc ";

    $pageSize = 10;
    $pagecurrent =  (int)$_GET["page"];

    $Row = $DB->getPage($Table, $Fileds, $Condition, $pageSize);
    $Row = $DB->result;
    $RowCount = $DB->num_rows($Row);
    $pageAll = $DB->pageAll;
    $showPage = $DB->showPageforFront(array('prev','next','info'));
    if ($RowCount > 0 && $pageAll>=$pagecurrent) {
        while ($result = $DB->fetch_assoc($Row)) 
        {
            if(!$result["order_id"]) continue;
            $order_items = array();
            $order_totalnum = 0;
            $TableItems = "v_order_items";
            $ConditionItems = " where order_id=" . $result["order_id"];
            $RowItems = $DB->Get($TableItems, $Fileds, $ConditionItems, 0);
            $RowCountItems = $DB->num_rows($RowItems);
            if ($RowCountItems != 0) {
                while ($resultItems = $DB->fetch_assoc($RowItems)) {
                    $presale_date = '';
                    if($resultItems['presale']==1){
                        $presale_date = strtotime($resultItems['presale_date'])-time()>0?'&nbsp;<font color="red">[预售,'.date('Y-m-d',strtotime($resultItems['presale_date'])).'发货]</font>':'';
                    }
                    array_push($order_items, array(
                        "items_id" => $resultItems["items_id"],
                        "order_id" => $resultItems["order_id"],
                        "product_id" => $resultItems["product_id"],
                        "size_prop" => $resultItems["size_prop"],
                        "color_prop" => $resultItems["color_prop"],
                        "price" => $resultItems["price"],
                        "num" => $resultItems["num"],
                        "amount" => $resultItems["amount"],
                        "product_name" => $resultItems["product_name"],
                        "color_photo" => $resultItems["color_photo"] . "!w200",
                        "presale" => $resultItems["presale"],
                        "presale_date" => $presale_date,
                    ));
                    $order_totalnum = $order_totalnum + $resultItems["num"];
                }
            }

            // 查询关联的售后订单
            if($result["status"]==7){
                $Table2="orders";
                $Fileds2 = "order_id,order_sn,relation_order,status";
                $Condition2 = "where relation_order='".$result["order_sn"]."'";
                $reOrder = $DB->GetRs($Table2,$Fileds2,$Condition2);
            }

            // 查询订单历史
            $Table3="order_history";
            $Fileds3 = "*";
            $Condition3 = "where order_id='".$result["order_id"]."' order by create_date DESC";
            $history_result = $DB->Get($Table3,$Fileds3,$Condition3);
            $history = array();
            while ($rs = $DB->fetch_assoc($history_result)) {
                $history[] = $rs;
            }
            array_push($order, array(
                "order_id" => $result["order_id"],
                "order_sn" => $result["order_sn"],
                "strat_date" => $result["order_date"],
                "order_date" => $Base->FormatTime($result["order_date"], "", true),
                "status" => $result["status"],
                "status_name" => $order_status[$result["status"]],
                "order_total" => $result["order_total"],
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
                "history" => $history,
                "saOrder" => isset($reOrder)?$reOrder:'',
                "is_seller"=>$result['is_seller'],
                "flag_color"=>$result['flag_color'],
                "remark"=>$result['remark'],
                "delivery"=>empty($deliverys[$result['delivery_id']]) ? null : $deliverys[$result['delivery_id']],
            ));
        }

    }
    // print_r($order);
    $sm->assign("order", $order, true);
    $sm->assign("showPage", $showPage, true);

}
    // print_r($order);exit();
$sm->assign("current_title", $current_title, true);
$sm->assign("page_sed_search", $page_sed_search, true);

