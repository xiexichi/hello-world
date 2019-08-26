<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
if(!isset($_SESSION["user_id"])||(int)$_SESSION["user_id"]==0) {
    echo json_encode(array(
        "ms_code"=>0,
        "ms_msg"=>"登录超时，请登录后操作。",
    ));
    exit;
}

$order_id = isset($_POST["order_id"]) ? (int)$_POST["order_id"] : 0;
$ship_com = isset($_POST["com"]) ? trim($_POST["com"]) : 0;
$ship_nu = isset($_POST["nu"]) ? trim($_POST["nu"]) : 0;

if(empty($order_id) || empty($ship_com) || empty($ship_nu)){
    echo json_encode(array(
        "ms_code"=>-1,
        "ms_msg"=>"提交的信息不正确，请检查后提交。",
    ));
    exit;
}

$Condition = "where (`status`=7 OR `status`=5) AND `order_id`=".(int)$order_id." AND user_id=".(int)$_SESSION["user_id"];
$order = $DB->GetRs("orders", "`order_id`,`order_sn`,`status`,`reout`,`user_id`,`return_num`", $Condition);
if(empty($order['order_id']) || $order['reout']=='expback'){
    echo json_encode(array(
        "ms_code"=>0,
        "ms_msg"=>"找不到订单，或者此订单不在状态。",
    ));
    exit;
}

$return['ms_code'] = 0;
$return['ms_msg']  = '操作失败，请重试。'; 

if(in_array($order['status'], array('5','7'))){
    //将物流信息写入订单历史
    $comp = $ship_com;
    $msg = $ship_com.':'.$ship_nu;
    $status = $order['status'];
    $res = $Common->send_back($order['order_id'],$msg,$status);
    if ($res) {
        $return['ms_code'] = 1;
        $return['ms_msg']  = '操作成功，我们收到寄回商品后第一时间处理。'; 
    }
}


/*
* 更新erp退货
* 2015-09-21
* 修改订单流和后换货不需要向erp生成退换货单，因为直接生成了新订单
* 只退货则向erp生成退货单

* 20161031修改，不需要自动添加退货单，手动在erp创建
*/
/*if($order['status']=="5" && $order['return_num']=="4")
{
    $user = $DB->GetRs("users", "`user_id`,`nickname`", "where user_id=".$_SESSION["user_id"]);

    $Table = 'v_order_items';
    $Condition = "where `order_id`=".$order['order_id'];
    $Row = $DB->Get($Table, "*", $Condition);
    $Row = $DB->result;
    $RowCount = $DB->num_rows($Row);
    $goods = array();
    if($RowCount != 0){
        while($result = $DB->fetch_assoc($Row)){
            $goods[] = $result;
        }
    }
    $itemsns='';
    $prices='';
    $skusns='';
    $nums='';
    foreach ($goods as $k => $v) {
        $itemsns .= $goods[$k]['sku_sn'];
        $prices .= $goods[$k]['price'];
        $skusns .= $goods[$k]['sku_prop'];
        $nums .= $goods[$k]['num'];
        if (array_key_exists($k+1,$goods)) {
            $itemsns.=',';
            $prices.=',';
            $skusns.=',';
            $nums.=',';
            continue;
        }else{
            break;
        }
    }

    $erp['mail'] = $user['nickname'];   //String  是   813406574@qq.com        会员代码
    $erp['outer_tid'] = $order['order_sn'];  //订单来源单号   String  是   2012082800345       订单来源单号
    $erp['itemsns'] = $itemsns;
    $erp['skusns'] = $skusns;
    $erp['prices'] = $prices;
    $erp['nums'] = $nums;
    $erp['outer_refundid'] = 'T'.substr($order['order_sn'],-6,6);   // 网站退货记录ID
    $erp['logistics_type'] = $ship_com;    //String      ems     物流公司代码
    $erp['logistics_fee'] = 0; // String      0   0   物流费用
    $erp['invoice_no'] = $ship_nu;  //String      testwldh00001       退回物流单号
    $erp['trade_memo'] = '退货单';


    // 添加 调用gyerp类，添加退货单
    include_once($_SERVER['DOCUMENT_ROOT']."/class/grerp.php");
    $gyerp = new gyerp();
    $gyerp->add_trade($erp);
}*/

echo json_encode($return);
exit;