<?php
$page_title = "我的二五";
$page_sed_title = '账户概览';

$user = array(
    "nickname"=>"",
    "icon"=>"",
    "bag"=>0,
    "integral"=>0,
    "address"=>"",
    "order"=>array(
        "total"=>0,
        "re_total"=>0,
        "nopay"=>0,
        "wait"=>0,
        "comment"=>0,
        "return"=>0
    ),
    "coupon"=>0,
    "favorite"=>0,
    "history"=>0
);
//图标
$v_img = '';

if($_SESSION["user_id"]!=""&&$_SESSION["user_id"]!=0){

    $Table = "browse_history";
    $Fileds = "*";
    $Condition = "where user_id=" . $_SESSION["user_id"];
    $Row = $DB->Get($Table, $Fileds, $Condition, 0);
    $RowCount = $DB->num_rows($Row);
    $user["history"]=$RowCount;


    $Table = "favorites";
    $Fileds = "*";
    $Condition = "where user_id=" . $_SESSION["user_id"];
    $Row = $DB->Get($Table, $Fileds, $Condition, 0);
    $RowCount = $DB->num_rows($Row);
    $user["favorite"]=$RowCount;


    //判断可用的代金券数量(未激活，未过使用期)
    $Table = "v_coupon";
    $Fileds = "coupon_id";
    $Condition = "where (is_pws=1 AND coupon_active = 0 AND user_id=".$_SESSION["user_id"]." AND exp_date > NOW()) or (is_pws=0 AND exp_date > NOW()) ";
    $Row = $DB->Get($Table, $Fileds, $Condition, 0);
    // $used = 0;
    // while ($row = $DB->fetch_array($Row)) {
    //     $where = "WHERE coupon_id = {$row['coupon_id']} AND exp_date > NOW()";
    //     $result = $DB->GetRs('coupon', 'coupon_id', $where, 0);
    //     if($result) $used++;
    // }
    // $user["coupon"]=$used;
    $user["coupon"] = $DB->num_rows($Row);
    // 抽奖码加入我的卡券数量
    $Table = "lottery_code";
    $Fileds = "code_id";
    $Condition = "where result = 0 AND user_id=".$_SESSION["user_id"]." AND end_date > NOW() ";
    $Row = $DB->Get($Table, $Fileds, $Condition, 0);
    $user["coupon"] += $DB->num_rows($Row);


    $Table="v_users";
    $Fileds = "*";
    $Condition = "where user_id=".$_SESSION["user_id"];
    $row = $DB->GetRs($Table,$Fileds,$Condition);
    if(!empty($row)){
        if($row["address_id"]!=0){
            $rowsub = $DB->GetRs("area","area_name","where area_id=".(int)$row["state"]);
            $state_name = empty($rowsub) ? "" : $rowsub["area_name"];

            $rowsub = $DB->GetRs("area","area_name","where area_id=".(int)$row["district"]);
            $district_name = empty($rowsub) ? "" : $rowsub["area_name"];

            $rowsub = $DB->GetRs("area","area_name","where area_id=".(int)$row["city"]);
            $city_name = empty($rowsub) ? "" : $rowsub["area_name"];
            $user["address"]     = $state_name." ".$city_name." ".$district_name." ".$row["address"];
        }


        $user["nickname"]    = $row["nickname"];
        $user["icon"]        = $row["image_url"]==""?"/statics/img/user_default_icon.png":$row["image_url"];
        $user["bag"]         = $row["bag_total"];
        $user["integral"]    = $row["integral_total"];

    }

    $relation_order_count = 0;
    $Table = "orders";
    $Fileds = "*";
    $Condition = "where user_id=" . $_SESSION["user_id"];
    $Row = $DB->Get($Table, $Fileds, $Condition, 0);
    $RowCount = $DB->num_rows($Row);
    if ($RowCount != 0) {

        $nopay = $wait = $comment = $return = 0;
        while($result = $DB->fetch_assoc($Row)){
            if($result["status"]==0){
                $nopay ++;
            }
            if($result["status"]==2){
                $wait ++;
            }
            if($result["return"]==3||$result["return"]==5||$result["return"]==7){
                $return ++;
            }
            if($result["relation_order"]!=""){
                $relation_order_count ++;
            }

        }
        $user["order"]["re_total"]=$relation_order_count;
        $user["order"]["total"]=$RowCount-$relation_order_count;
        $user["order"]["nopay"]=$nopay;
        $user["order"]["wait"]=$wait;
        $user["order"]["return"]=$return;

    }

    /**************************************************
        图标入口
    ***************************************************/

    //vip会员
    if(!$is_seller && $row['level'] > 0) {
        //vip
        $vip = $DB->GetRs("level",'*',"where id=" . $row['level']);
        $v = substr($vip['level'],-1);
        $img = '';
        switch ($v) {
            case '1':
                $img = '&#xe651;';
                break;
            case '2':
                $img = '&#xe652;';
                break;
            case '3':
                $img = '&#xe653;';
                break;
            default:
                $img = '&#xe654;';
                break;          
        }   
        $v_img = $img;   
    }

    //分销
    if($is_seller) {
        $v_img = '&#xe655;';
    }

}
$sm->assign("user", $user, true);
$sm->assign("is_seller", $is_seller, true);
$sm->assign("is_promote", $is_promote, true);
$sm->assign("promote", $promote, true);
$sm->assign("v_img", $v_img, true);

// 显示主导航
$site_nav_display = 'show';