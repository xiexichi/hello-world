<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
if(!isset($_SESSION["user_id"])||$_SESSION["user_id"]=="") {
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}
$order_id = isset($_POST["order_id"]) ? (int)$_POST["order_id"] : 0;
$title = isset($_POST["title"]) ? $_POST["title"] : "";
$content = isset($_POST["content"]) ? $_POST["content"] : "";

$content = $title.'<br/>'.$content;

if($order_id==0){
    echo json_encode(array(
        "status"=>"no_order_id"
    ));
    exit;
}


$order = $DB->GetRs("orders","user_id,status,order_id,relation_order,business_code,is_issuing,client","where user_id='".$_SESSION["user_id"]."' and order_id=".(int)$order_id);
if(empty($order['user_id'])){
    echo json_encode(array(
        "status"=>"no_order_id"
    ));
    exit;
}

if($order['info']['status'] > 0 ){
    echo json_encode(array(
        "status"=>"is_payed"
    ));
    exit;
}

$rsorder = $DB->GetRs("orders","order_id,order_sn","where order_sn='".$order['relation_order']."'");

if($order['relation_order'] && $order['relation_order']!=''){
    // 删除售后订单，回滚原订单状态
    $res = $DB->Del('orders',"","","order_id=".(int)$order_id);
    $resItem = $DB->Del('order_items',"","","order_id=".(int)$order_id);
    if($res){
        // 回滚原订单状态
        $rsorder = $DB->GetRs("orders","order_id,order_sn","where order_sn='".$order['relation_order']."'");
        if(isset($rsorder['order_id'])){
            // 添加订单历史记录
            $result = $DB->Add("order_history",array(
                "order_id"=>$rsorder['order_id'],
                "condition"=>isset($condition)?$condition:null,
                "content"=>'取消换货',
                "status"=>-2,
                "create_date"=>date('Y-m-d H:i:s')
            ));
            $result = $DB->Set("orders",array(
                "status"=>'2',
                'return_num'=> '0',
                'reout'=>NULL,
            ),"where order_id=".(int)$rsorder['order_id']);
        }
    }
}else{

    $result = $DB->Add("order_history",array(
        "order_id"=>$order_id,
        "condition"=>isset($condition)?$condition:null,
        "content"=>$content,
        "status"=>-1,
        "create_date"=>date('Y-m-d H:i:s')
    ));

    if($result){
        //初始化25库存
        $depot_id = $SITECONFIGER['sys']['default_depot_id'];

        //线下库存变动三大条件：1.business_code非空 2.client='o2o' 3.非二五代发
        if(!empty($order['business_code']) && $order['client'] == 'o2o' && !$order['is_issuing']) {
            $business = $DB->GetRs("business","depot_id","WHERE business_code = '".$order['business_code']."'");
            $depot_id = isset($business['depot_id'])?$business['depot_id']:0;
        }

        $result = $DB->Set("orders",array("status"=>-1),"where order_id=".(int)$order_id);

        $TableItems = "order_items";
        $ConditionItems = " where order_id=".(int)$order_id;
        $RowItems = $DB->Get($TableItems, "product_id,size_prop,color_prop,num,sku_prop", $ConditionItems, 0);
        $RowCountItems = $DB->num_rows($RowItems);
        if ($RowCountItems != 0) {
            while($resultItems = $DB->fetch_assoc($RowItems)){
                //根据商品id求出商品sku
                $pro = $DB->GetRs("products","sku_sn","WHERE product_id = ".(int)$resultItems['product_id']);
                $sku_sn = $pro['sku_sn'];
                $sku_prop = $resultItems['sku_prop'];

                //获取现时商品库存
                $_row = $DB->GetRs("stock", "quantity", "WHERE depot_id = {$depot_id} AND sku_sn = '{$sku_sn}' AND sku_prop = '{$sku_prop}'");
                $before_quantity = isset($_row['quantity']) ? $_row['quantity'] : 0;

                //回滚库存
                $DB->Set(
                    "stock",
                    "quantity=quantity+".(int)$resultItems['num'],
                    "where sku_sn ='".$sku_sn."' AND sku_prop = '".$sku_prop."' AND depot_id = ".$depot_id
                );

                //获取变动后商品库存
                $_row = $DB->GetRs("stock", "quantity", "WHERE depot_id = {$depot_id} AND sku_sn = '{$sku_sn}' AND sku_prop = '{$sku_prop}'");
                $after_quantity = isset($_row['quantity']) ? $_row['quantity'] : 0;

                //线下申请退款时退回线下库存需要记录库存变动
                if(!empty($order['business_code']) && $order['client'] == 'o2o' && !$order['is_issuing']) {
                    $_insert = array(
                        'depot_id'          => $depot_id,
                        'order_id'          => $order_id,
                        'type'              => 'user',
                        'sku_sn'            => $sku_sn,
                        'sku_prop'          => $sku_prop,
                        'change_value'      => $resultItems['num'],
                        'before_quantity'   => $before_quantity,
                        'after_quantity'    => $after_quantity,
                        'note'              => '订单取消，退回库存',
                        'create_time'       => date('Y-m-d H:i:s')
                    );
                    //新增线下库存变动记录
                    $DB->Add('stock_change', $_insert);
                }else {
                    $DB->Set(
                        "products",
                        "sale=sale-".(int)$resultItems['num'].",total_quantity=total_quantity+".(int)$resultItems['num'],
                        "where product_id=".(int)$resultItems['product_id']
                    );
                }

            }
        }
    }

}


echo json_encode(array(
    'order_id'=>isset($rsorder['order_id'])?$rsorder['order_id']:$order_id,
    "status"=>"success",
));