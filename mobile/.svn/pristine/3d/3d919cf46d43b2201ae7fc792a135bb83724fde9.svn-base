<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

// 打印函数
function p($data){
    echo '<pre>';
    print_r($data);
}

function pe($data){
    p($data);
    exit;
}

/**
 * [echoJson 输出json数据]
 * @param  [type] $data [输出数据]
 * @return [type]       [description]
 */
if ( ! function_exists('echoJson')){
    function echoJson($data){
        exit(json_encode($data));
    }
}

// 加载o2o配置文件
$o2oConfig = require($_SERVER['DOCUMENT_ROOT'].'/o2o_config.php');
$bagTable = $o2oConfig['bag_table'];

$order_id = isset($_POST["order_id"]) ? (int)$_POST["order_id"] : 0;

if(!isset($_SESSION["user_id"])||empty($_SESSION["user_id"])) {
    echoJson(array("status"=>"no_login"));
}
$order = array();

$returnjson = array(
    "status"=>"error",
);

if($order_id==0){
    echo json_encode($returnjson);
    exit;
}

// 写原生sql
$sql = "SELECT a.user_id,b.status,a.order_id,a.order_sn,b.order_type,b.pay_total FROM o2o_order a JOIN o2o_order_join b ON a.order_id = b.order_id WHERE a.order_id = {$order_id} AND a.user_id = {$_SESSION["user_id"]} LIMIT 1";
// 获取订单数据
$result = $DB->fetch_assoc($DB->query($sql));


if(!empty($result)) {
    $prices = (float)$result["pay_total"];
    $order = $result;
}else{
    echo json_encode($returnjson);
    exit;
}
if(isset($result['status']) && $result['status']!=0){
    echoJson(array("status"=>"is_payed"));
}




if($prices>=0){
    $result = $DB->GetRs("users","*","where user_id=".$_SESSION["user_id"]);

    if(!empty($result)) {

        $bag_total = (float)$result["bag_total"];

        if($bag_total>=$prices){

            // 开启事务
            $DB->trans_begin();

            $new_balance = $bag_total - $prices;
            $result = $DB->Set("users",array("bag_total"=>$new_balance),"where user_id=".$_SESSION["user_id"]);

            if($result){
                // 支付订单成功
                $res = update_order($prices,$order_id,$DB,$order,$bagTable,$new_balance);

                if ($res) {

                    // 提交事务
                    $DB->trans_commit();
                    // 输出结果
                    echoJson(array("status"=>"success",'order_sn'=>$order["order_sn"],'order_id'=>$order["order_id"]));
                }
                
            }

            // 回滚
            $DB->trans_rollback();
            // 输出结果
            echoJson(array("status"=>"error",'order_sn'=>$order["order_sn"],'order_id'=>$order["order_id"]));

        } else {
            
            $returnjson['msg'] = '钱包余额小于支付金额';
        }

    }else{

        echoJson(array(
            "status"=>"no_balance",
            "balance"=>$bag_total,
            "price"=>$prices,
            "order_sn"=>$order["order_sn"],
            "order_id"=>$order["order_id"]
        ));
    }

}


/**
 * [update_order 更新订单]
 * @param  [type]  $prices    [description]
 * @param  [type]  $order_id  [description]
 * @param  [type]  $DB        [description]
 * @param  [type]  $order     [description]
 * @param  [type]  $bagTable  [description]
 * @param  integer $bag_total [description]
 * @return [type]             [description]
 */
function update_order($prices,$order_id,$DB,$order,$bagTable,$bag_total=0){

    //添加历史
    $res = $DB->Add("order_history",array(
        "order_id"=>$order_id,
        "content"=>"余额支付成功（手机）",
        "status"=>1,
        "create_date"=>date('Y-m-d H:i:s')
    ));

    if (!$res) {
        return FALSE;
    }

    //更新订单状态
    $res = $DB->Set("o2o_order_join","status=1,pay_status=1,pay_method='bag'","WHERE order_id=".$order_id);
    if (!$res) {
        return FALSE;
    }

    // 修改订单支付时间
    $res = $DB->Set('o2o_order','pay_date=NOW()',"WHERE order_id=".$order_id);
    if (!$res) {
        return FALSE;
    }

    //更新钱包付款状态
    $bag_type = (substr($order["order_sn"],0,1)=='S'?'ship_fee':'goods');
    //备注
    $note = "手机支付";
    $business_code = '';
    //线下
    if(!empty($order['business_code'])) {
        $business_code = $order['business_code'];
        $business = $DB->GetRs("business","business_name","WHERE business_code = '{$business_code}'");
        $note = $business['business_name']."用户消费(手机支付)";
    }

    // 添加支付流水
    $res = $DB->Add($bagTable,array(
        "pay_status"=>'paid',
        "pay_sn"=>$order["order_sn"],
        "method"=>'bag',
        "user_id"=>$_SESSION["user_id"],
        "create_date"=>date('Y-m-d H:i:s'),
        "money"=>-$prices,
        "type" => $bag_type,
        "note"=>$note,
        "balance"=>$bag_total,
        "pay_date"=>date('Y-m-d H:i:s'),
        "business_code"=>$business_code
    ));

    if (!$res) {
        return FALSE;
    }

    //更新用户消费总额
    $res = $DB->Set("users","consume_total = consume_total + ".$prices,"where user_id=".$_SESSION["user_id"]);

    if (!$res) {
        return FALSE;
    }

    //如果不是线下订单，则同步ERP
    if(empty($order['business_code'])) {
        // 添加 调用gyerp类，添加订单
        include_once($_SERVER['DOCUMENT_ROOT']."/class/grerp.php");
        $gyerp = new gyerp();
        $erp = $gyerp->getO2oOrderForErp($order_id);
        // 如果有数据则同步到erp
        if ($erp) {
            if ($gyerp->add_o2o_order($erp)) {
                // 修改订单同步状态
                $res = $DB->Set('o2o_order_join','is_sync=1',"WHERE order_id=".$order_id);
            }
        }
    }

    return TRUE;
}


echo json_encode($returnjson);
exit;