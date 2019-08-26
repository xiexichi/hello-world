<?php

function p($data){
    echo '<pre>';
    print_r($data);
}

function pe($data){
    p($data);
    exit;
}


$page_title = "线下订单";
$page_sed_title = '全部';
$page_sed_search = 'search_orders';

$sort = isset($_GET["s"]) ? strval($_GET["s"])  : "";
$keywords = (isset($_GET["k"]) && !empty($_GET["k"])) ? strval($_GET["k"])  : "";
$order=array();

$order_status = array(
    '0'  => '等待付款',
    '1'  => '等待发货',
    '2'  => '待收货',
    '3'  => '交易成功',
    '4'  => '退款中',
    '5'  => '退款完成',
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

// 表前缀
$tablePerfix = 'o2o_';


$deliverys = array();
$result = $DB->Get('delivery','*',"WHERE status = 1 ORDER BY is_default DESC,delivery_id ASC");
while (!!$rows = $DB->fetch_array($result)) {
    $deliverys[$rows['delivery_id']] = $rows;
}


$sm->assign("spanbg", $spanbg, true);
if($_SESSION["user_id"]!=""&&$_SESSION["user_id"]!=0) {

    // 用户id
    $user_id = $_SESSION["user_id"];

    // 自动关闭超时o2o订单
    // $Common->auto_close_notpay_order($_SESSION["user_id"]);
    // // 自动关闭超时未寄回订单
    // $Common->auto_close_exchange_order($_SESSION["user_id"]);

    /*  -1=关闭 
        0=未付款 
        1=待发货 
        2=已发货 
        3=交易完成 
        4=退款中 
        5=退款完成 
        6=换货中  (后台使用)
        7=退货中 （后台使用）
    */

    // $Condition = "where (o.relation_order is null OR o.relation_order='') and o.user_id=" . $_SESSION["user_id"];

    // 初始化查询条件
    $Condition = '';
        
    switch ($sort) {
        case "0": case "nopay":
            $Condition .= " and oj.status=0 ";
            $page_sed_title = '待付款';
            break;
        case "1": case "pack":
            $Condition .= " and oj.status=1 ";
            $page_sed_title = '等待发货';
            break;
        case "2": case "wait":
            $Condition .= " and oj.status=2 ";
            $page_sed_title = '待收货';
            break;
        case "3":
            $Condition .= " and oj.status=3 ";
            $page_sed_title = '交易完成';
            break;
        case "4":
            $Condition .= " and oj.status=4 ";
            $page_sed_title = '退款中';
            break;
        case "5":
            $Condition .= " and oj.status=5 ";
            $page_sed_title = '退款完成';
            break;
        case "-1":
            $Condition .= " and oj.status=-1 ";
            $page_sed_title = '交易关闭';
            break;
        case "all":
            $page_sed_title = '全部';
            break;
    }

    //关键字查询
    if($keywords) {
        // $Condition .= " and (o.order_sn LIKE '%$keywords%'
        //                     OR o.receiver_name LIKE '%$keywords%' 
        //                     OR o.receiver_phone LIKE '%$keywords%'
        //                     OR oj.buyer_note LIKE '%$keywords%'
        //                     OR oi.product_name LIKE '%$keywords%'
        //             )";

        $Condition .= " and (o.order_sn LIKE '%$keywords%'
                            OR o.receiver_name LIKE '%$keywords%' 
                            OR o.receiver_phone LIKE '%$keywords%'
                            OR oj.buyer_note LIKE '%$keywords%'
                    )";
    }


    $current_title = $page_sed_title;
    $page_sed_title .= '订单';

    $Table = $tablePerfix."order o JOIN {$tablePerfix}order_join oj ON o.order_id = oj.order_id JOIN o2o_order_item oi ON o.order_id = oi.order_id";
    $Fileds = "o.*,oj.*";

    $Condition .= "WHERE o.user_id = {$user_id} GROUP BY o.order_id order by o.create_date desc ";

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
            $order_totalnum = 0;
            $TableItems = $tablePerfix."order_item";
            $ConditionItems = " where order_id=" . $result["order_id"];
            $order_items = $DB->GetAll($TableItems, '*', $ConditionItems, 0);

            // 是否能退换
            $result['is_seller'] = false;

            foreach ($order_items as $k => $v) {
                // 查找商品名称
                $product = $DB->GetRs('products','product_name','WHERE product_id = '.$v['product_id']);
                if ($product) {
                    $order_items[$k]['product_name'] = $product['product_name'];
                }

                // 查找商品属性
                $stock = $DB->GetRs('stock','size_prop,color_prop',"WHERE sku_sn = '{$v['sku_sn']}' AND sku_prop = '{$v['sku_prop']}'");

                if ($stock) {
                    $order_items[$k]['size_prop'] = $stock['size_prop'];
                    $order_items[$k]['color_prop'] = $stock['color_prop'];                    
                }

                // 判断是否能退换
                if (!$result['is_seller'] && $v['status'] == 1) {
                    $result['is_seller'] = true;
                }
            }

            // 查询关联的售后订单
            // if($result["status"]==7){
            //     $Table2="orders";
            //     $Fileds2 = "order_id,order_sn,relation_order,status";
            //     $Condition2 = "where relation_order='".$result["order_sn"]."'";
            //     $reOrder = $DB->GetRs($Table2,$Fileds2,$Condition2);
            // }

            // 查询订单历史
            $Table3=$tablePerfix."order_history";
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
                "order_type" => $result["order_type"],
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

    // pe($order);

    // print_r($order);
    $sm->assign("order", $order, true);
    $sm->assign("showPage", $showPage, true);

}


// 判断关键字是否为空
$sort = ($sort === '' || $sort == 'all') ? '1000' : $sort;

// print_r($order);exit();
$sm->assign("order_status", $order_status, true);
$sm->assign("s", $sort, true);
$sm->assign("current_title", $current_title, true);
$sm->assign("page_sed_search", $page_sed_search, true);

