<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
if(!isset($_SESSION["user_id"])||(int)$_SESSION["user_id"]==0) {
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}

$order_id = isset($_POST["order_id"]) ? (int)$_POST["order_id"] : 0;
$Condition = "where `order_id`=".(int)$order_id." AND user_id=".(int)$_SESSION["user_id"];
$order = $DB->GetRs("orders", "`order_id`,`pay_total`, `discount`, `ship_price`, `order_sn`,`relation_order`", $Condition);
if($order_id==0 || !isset($order['order_id'])){
    echo json_encode(array(
        "status"=>"no_order_id"
    ));
    exit;
}

$data['status'] = 8;
$data['content'] = '用户确定交易完毕';
$data['order_id'] = $order_id;
$data['create_date'] = date('Y-m-d H:i:s');

$DB->Add("order_history",$data);
$DB->Set("orders",array("status"=>$data['status']),"where order_id=".$order_id);


/* *********************************************************************
 * 收益返佣
 * *********************************************************************/

//如果是SA订单,取出原订单
$result = $DB->query("select o1.order_id as order_id1,o2.order_id as order_id2 from orders o1 left JOIN orders o2 ON o1.relation_order = o2.order_sn WHERE o1.order_id = $order_id");
$_row =  $DB->fetch_array($result);
//原订单id
$_order_id = empty($_row['order_id2']) ? $order_id : $_row['order_id2'];

//先手判断，该订单是否已结算
$row = $DB->GetRs("promote_earnings","pearnings_id","WHERE order_id = $_order_id");
if(empty($row)) {
  //取出下单信息,订单必须满足是第三方付了款，记录收益情况
  $sql = "SELECT * FROM promote_order po JOIN orders o ON po.order_id = o.order_id
          WHERE po.order_id = $_order_id AND o.pay_status = 1 AND o.pay_method <> 2 AND o.pay_method <> 0";
  $result = $DB->query($sql);
  $promote_order = array();
  while($row = $DB->fetch_array($result)) {
      array_push($promote_order, $row);
  }
  if(!empty($promote_order)) {
    $user = $DB->GetRs("users","user_id,pid","where user_id = ".(int)$_SESSION["user_id"]);
    $promote_id = $user['pid']?$user['pid']:0;
    $promote = $DB->GetRs("promote","*","where promote_id = ".$promote_id);

    //判断推广者,如果为空或者被冻结，都没有返佣
    if(!empty($promote) && !$promote['is_frozen']) {
        //获取首次购物
        $promote_first = $Common->get_beyond_first($promote_id,4);
        //后续购物返佣商品列表
        $promote_product_list = $Common->get_beyond_product_list($promote_id);
        //判断是否是首次购物
        $row = $DB->GetRs("orders","order_id","where pay_status = 1 AND user_id = ".$user['user_id']." ORDER BY pay_date");
        if(isset($row['order_id']) && $row['order_id'] == $_order_id && !empty($promote_first)) {
            //首次购物，收货返佣
            foreach ($promote_order as $key => $value) {
                $commission_rate = $promote_first['commission_rate'];
                $commission      = round($value['re_price'] * $value['product_num'] * $commission_rate / 100,2);
                $pplan_id        = $promote_first['pplan_id'];
                $DB->Add('promote_earnings',array(
                    "pplan_id"        => $pplan_id,
                    "pitem_id"        => $value['pitem_id'],
                    "order_id"        => $value['order_id'],
                    "product_id"      => $value['product_id'],
                    "product_num"     => $value['product_num'],
                    "re_price"        => $value['re_price'],
                    "promote_id"      => $value['promote_id'],
                    "commission_rate" => $commission_rate,
                    "earnings"        => $commission,
                    "received_time"   => date("Y-m-d H:i:s")
                ));
                if($DB->affected_rows()){
                    $DB->query("UPDATE promote SET earnings_total = earnings_total + {$commission} WHERE promote_id = {$value['promote_id']}");
                }
            }

        }else {
            //后续购物，收货返佣
            foreach ($promote_order as $key => $value) {
                //判断是否存在可推广商品(后续购物)
                $product_id = $value['product_id'];
                if(!empty($promote_product_list[$product_id])) {
                    $promote_product = $promote_product_list[$product_id];

                    $commission_rate = $promote_product['commission_rate'];
                    $commission      = round($value['re_price'] * $value['product_num'] * $commission_rate / 100,2);
                    $pplan_id        = $promote_product['pplan_id'];
                    $DB->Add('promote_earnings',array(
                        "pplan_id"        => $pplan_id,
                        "pitem_id"        => $value['pitem_id'],
                        "order_id"        => $value['order_id'],
                        "product_id"      => $value['product_id'],
                        "product_num"     => $value['product_num'],
                        "re_price"        => $value['re_price'],
                        "promote_id"      => $value['promote_id'],
                        "commission_rate" => $commission_rate,
                        "earnings"        => $commission,
                        "received_time"   => date("Y-m-d H:i:s")
                    ));
                    if($DB->affected_rows()){
                        $DB->query("UPDATE promote SET earnings_total = earnings_total + {$commission} WHERE promote_id = {$value['promote_id']}");
                    }
                }
            }
        }
    }
   
  }

}



if(!empty($order['relation_order']) && $order['relation_order']!=null){
    $Condition = "where `order_sn`='".$order['relation_order']."' AND user_id=".(int)$_SESSION["user_id"];
    $order = $DB->GetRs("orders", "order_id,user_id ,pay_total", $Condition);
    if(isset($order['order_id'])){
        $DB->Set("orders","status=8","where order_id=".(int)$order['order_id']);
        $DB->Add("order_history",array(
            'order_id'=>$order['order_id'],
            'content'=>'确认售后，订单结束',
            'status'=>(int)$data['status'],
            'create_date'=>date('Y-m-d H:i:s',time()),
        ));
    }
}



//更新会员积分
$DB->Add("integral",array(
    "context"=>"消费记录",
    "integral_value"=>ceil($order['pay_total']),
    "user_id"=>$_SESSION["user_id"],
    "create_date"=>date('Y-m-d H:i:s')
));
$DB->Set("users","integral_total=integral_total+".ceil($order['pay_total']),"where user_id=".$_SESSION["user_id"]);


echo json_encode(array(
    "status"=>"success"
));