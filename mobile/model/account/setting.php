<?php
$page_title = "我的二五";
$page_sed_title = '个人账户设置';
$v_img = '';//图标
if($_SESSION["user_id"]!=""){

    $user = array(
        "email"=>"",
        "nickname"=>"",
        "icon"=>"",
        "bag"=>0,
        "integral"=>0,
        "address"=>"",
        "coupon"=>0,
        "favorite"=>0,
        "sex"=>"未知",
        "history"=>0,
        "phone"=>''
    );


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

        $user["email"]       = $row["email"];
        $user["flag"]       =  $row["flag"];
        $user["realname"]    = $row["realname"]=="" ? "未填写" : $row["realname"];
        $user["nickname"]    = trim($row["nickname"]);
        $user["icon"]        = $row["image_url"]==""?"/statics/img/user_default_icon.png":$row["image_url"];
        $user["bag"]         = $row["bag_total"];
        $user["integral"]    = $row["integral_total"];
        $user["sex"]         = $row["gender"]==''?'未知':$row["gender"];
        $user["sex"]         = $user["sex"]!="未知" ? $user["sex"]!="male"?"女士":"先生" : "未知";
        $user["birthday"]    = $row["birthday"]=='' ? '未填写' : $Base->FormatTime($row["birthday"],"ymd_sign",false);
        $user["phone"]       = $row["phone"];
    
   
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

}


$sm->assign("user", $user, true);
$sm->assign("goback", '/?m=account', true);
$sm->assign("v_img", $v_img, true);