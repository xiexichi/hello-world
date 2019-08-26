<?php
$page_title = "我的二五";
$page_sed_title = '我的票券';

//$ssql = ' WHERE `user_id` = '.$data['user_id'].' AND `method` IN (2,3,4) ORDER BY `create_date` DESC';
$coupon_list = $prize_list = $coupon_data = $lottery_list = array();
$today = date('Y-m-d',time());

$type = isset($_GET['c']) ? htmlspecialchars($_GET['c']) : 'coupon';

if(isset($_SESSION["user_id"])&&$_SESSION["user_id"]!=0) {

    if($type == 'lottery'){

        // 抽奖码 lottery_code 表
        $end_date = strtotime(date('Y-m-d',strtotime('+3 day')));
        $Table = "lottery_code";
        $Fileds = "*";
        $Condition = "where user_id=".$_SESSION["user_id"]." order by end_date DESC";
        $Row = $DB->Get($Table, $Fileds, $Condition, 0);
        $Row = $DB->result;
        $RowCount = $DB->num_rows($Row);
        if ($RowCount != 0) {
            while ($result = $DB->fetch_assoc($Row)) {
                array_push($lottery_list,array(
                    'title'         => $result['activity'].'抽奖码：'.$result['code'],
                    'result'        => $result['result'],
                    'start_date'    => $result['start_date']?date('Y-m-d',strtotime($result["start_date"])):'有效期',
                    "end_date"      => $result['end_date']?date('Y-m-d',strtotime($result["end_date"])):'',
                    'timeout'       => strtotime($result["end_date"])+86400-time(),
                ));
            }
        }

    }else{

        // 代金券
        $Table = "v_coupon";
        $Fileds = "*";
        $Condition = "where (user_id=".$_SESSION["user_id"].") OR (is_pws=0 AND exp_date>='".$today."') order by exp_date desc";
        $Row = $DB->Get($Table, $Fileds, $Condition, 0);
        $Row = $DB->result;
        $RowCount = $DB->num_rows($Row);
        if ($RowCount != 0) {
            while ($result = $DB->fetch_assoc($Row)) {
                $date_distance = strtotime($result["exp_date"])-strtotime(date('Y-m-d H:i:s'));
                $date_distance = $date_distance/3600/24;
                array_push($coupon_list,array(
                    "title"=>$result['coupon_title'],
                    'start_date' => $result['start_date']?$result['start_date']:'有效期',
                    "exp_date"=>date('Y-m-d',strtotime($result["exp_date"])),
                    "remind"=>$date_distance<=4 ? 1 : 0,
                    "used_date"=>$result["used_date"],
                    "is_pws"=>$result["is_pws"],
                    "coupon_type"=>$result["coupon_type"],
                    "coupon_active"=>isset($result["coupon_active"]) ? $result["coupon_active"] : 0,
                    'timeout'   => strtotime($result["exp_date"])-strtotime($today),
                ));
            }
        }

        // 奖品放送
        $Table = "v_prizes";
        $Fileds = "*";
        $Condition = "where user_id=".$_SESSION["user_id"]." order by end_date desc";
        $Row = $DB->Get($Table, $Fileds, $Condition, 0);
        $Row = $DB->result;
        $RowCount = $DB->num_rows($Row);
        if ($RowCount != 0) {
            while ($result = $DB->fetch_assoc($Row)) {
                array_push($prize_list,array(
                    'title'  => $result['title'],
                    'start_date'    => $result['start_date']?date('Y-m-d',strtotime($result["start_date"])):'有效期',
                    "exp_date"=>date('Y-m-d H:i',strtotime($result["end_date"])),
                    'used_date'     => $result['used_date'],
                    'coupon_active' => isset($result["complete"]) ? $result["complete"] : 0,
                    'price_type'    => $result['price_type'],
                    'item_price'    => $result['item_price'],
                    'quantity'      => $result['quantity'],
                    'is_pws'        => 1,
                    'coupon_type'   => 'GT',
                    'timeout'   => strtotime($result["end_date"])-time(),
                ));
            }
        }

        $coupon_data = array_merge($prize_list,$coupon_list);

        // 过期和已使用的放在最后
        $timeout = array();
        foreach ($coupon_data as $key => $val) {
            if($val['coupon_active']==1 || $val['timeout'] < 0){
                $timeout[] = $val;
                unset($coupon_data[$key]);
            }
        }
        $coupon_data = array_merge($coupon_data,array_reverse($timeout));
        // print_r($coupon_data);

    }

}

// 广告
$sm->assign("ad_coupon", $Common->get_picshow(32,1), true);


$sm->assign("coupon_data", $coupon_data, true);
$sm->assign("lottery_list", $lottery_list, true);
$sm->assign('type', $type, true);