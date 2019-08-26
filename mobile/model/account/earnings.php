<?php
$page_title = "我的二五";
$page_sed_title = '我的收益';
$module = 'promote'; //模块
$submodule = 'promote_earnings';
$GLOBALS['DB'] = $DB;

$Base->check_permission($is_promote);

function getWhenSql($time_field,$when) {
    switch ($when) {
        case 'yesterday':
            $when = " TO_DAYS($time_field) = TO_DAYS(now()) - 1";
            break;
        case 'this_week':
            $when = " YEARWEEK($time_field) = YEARWEEK(NOW())";
            break;
        case 'last_week':
            $when = " YEARWEEK($time_field) = YEARWEEK(NOW()) - 1";
            break;
        case 'this_month':
            $when = " date_format($time_field,'%Y-%m') = date_format(curdate(),'%Y-%m')";
            break;
        case 'last_month':
            $when = " date_format($time_field,'%Y-%m') = date_format(DATE_SUB(curdate(), INTERVAL 1 MONTH),'%Y-%m')";
            break;
        default://today
            $when = " TO_DAYS($time_field) = TO_DAYS(now());";
            break;
    }
    return $when;
}

//取得预估收入
function get_earnings($promote_id,$when) {
    $whenSql = getWhenSql('received_time',$when);
    $sql   = "SELECT sum(earnings) AS total FROM promote_earnings
             WHERE promote_id = $promote_id AND $whenSql";
    $query = $GLOBALS['DB']->query($sql);
    $row   = $GLOBALS['DB']->fetch_array();
    return empty($row['total']) ? '0.00' : $row['total'];
}

//按月取得实际收入
function get_earnings_monthly($promote_id,$year,$month) {
    $month = str_pad($month,2,'0',STR_PAD_LEFT);
    $monthly = "$year-$month";

    $sql = "SELECT sum(earnings) AS total FROM promote_earnings
            WHERE promote_id = $promote_id  AND date_format(received_time,'%Y-%m') = '$monthly'";
    $query = $GLOBALS['DB']->query($sql);
    $row   = $GLOBALS['DB']->fetch_array();
    return empty($row['total']) ? '0.00' : $row['total'];
}

//按月取得付款笔数
function get_paid_order_monthly($promote_id,$year,$month) {
    $month = str_pad($month,2,'0',STR_PAD_LEFT);
    $monthly = "$year-$month";

    $sql = "SELECT count(DISTINCT po.order_id)AS total FROM promote_order po
            LEFT JOIN orders o ON po.order_id = o.order_id
            WHERE o.pay_status = 1 AND o.pay_method <> 2 AND po.promote_id = $promote_id AND date_format(o.pay_date,'%Y-%m') = '$monthly'";
    $query = $GLOBALS['DB']->query($sql);
    $row   = $GLOBALS['DB']->fetch_array();
    return $row['total'];
}

//按月取得充值笔数
function get_recharge_num_monthly($promote_id,$year,$month) {
    $month = str_pad($month,2,'0',STR_PAD_LEFT);
    $monthly = "$year-$month";

    $sql = "SELECT count(DISTINCT pearnings_id) AS total FROM promote_earnings
            WHERE promote_id = $promote_id AND earnings_type = 're_recharge' AND date_format(received_time,'%Y-%m') = '$monthly'";
    $query = $GLOBALS['DB']->query($sql);
    $row   = $GLOBALS['DB']->fetch_array();
    return $row['total'];
}


if(isset($_SESSION["user_id"])&&!empty($_SESSION["user_id"])) {

    $promote_id = $promote['promote_id'];

   // 今日预估收入
    $promote_earnings['today'] = get_earnings($promote_id,'today');
   // 昨日预估收入
    $promote_earnings['yesterday'] = get_earnings($promote_id,'yesterday');
   // 本月预估收入
    $promote_earnings['this_month'] = get_earnings($promote_id,'this_month');
   // 上月月预估收入
    $promote_earnings['last_month'] = get_earnings($promote_id,'last_month');

    //指定年份的收益情况
    $this_year  = date('Y');
    $this_month = date('n');//数字表示的月份，没有前导零
    $this_day   = date('j'); //月份中的第几天，没有前导零
    $last_month = $this_month - 1;
    $promote_monthly = array();

    for ($i=1; $i <=$last_month; $i++) {
        //预估收入
        $promote_monthly[$i]['earnings'] = get_earnings_monthly($promote_id,$this_year,$i);
        //付款笔数
        $promote_monthly[$i]['paid_num'] = get_paid_order_monthly($promote_id,$this_year,$i);
        //充值笔数
        $promote_monthly[$i]['recharge_num'] = get_recharge_num_monthly($promote_id,$this_year,$i);
    }
    //按照并保留键名，逆向排序
    krsort($promote_monthly);

    //提现方式
    $result = $DB->Get('promote_withdrawal','*',"WHERE promote_id = $promote_id ORDER BY create_time DESC");
    $withdrawal['all_withdrawal'] = $all_withdrawal = array();
    while($row = $DB->fetch_array($result)) {
        array_push($withdrawal['all_withdrawal'], $row);
    }

    $this_day = date('j'); //月份中的第几天，没有前导零
    //是否允许提现
    $withdrawal['is_withdrawal'] = 0;
    $withdrawal['withdrawal_notice'] = '不可提现';
    $promote_withdrawal = $promote_config['promote_withdrawal'];

    $all_withdrawal['third'] = $withdrawal['all_withdrawal'];
    $all_withdrawal['bag']   = $promote_withdrawal;

    $thisWithdrawal = '';//取得本次采用的提现方式
    $isAlreadyWithdrawal = 0;//判断本月是否已申请提现

    //先判断可提金额，再判断是否已申请，最后判断提现时间
    if($promote['cash_total'] > 0){
        $row = $DB->GetRs('promote_cash','pwithdrawal_id,apply_time',"WHERE promote_id = $promote_id AND date_format(apply_time,'%Y-%m') =  date_format(CURDATE(),'%Y-%m')");
        if(empty($row)) {
            if(!empty($promote_withdrawal['withdrawal_start']) && !empty($promote_withdrawal['withdrawal_end'])) {
                if($this_day >= (int)$promote_withdrawal['withdrawal_start'] && $this_day <= (int)$promote_withdrawal['withdrawal_end']) {
                    //在规定的时间内，如果本月没申请提现，且余额大于0，可以提现 
                    $withdrawal['is_withdrawal'] = 1;
                }else {
                    $withdrawal['withdrawal_notice'] = "提现时间为每月的{$promote_withdrawal['withdrawal_start']}号到{$promote_withdrawal['withdrawal_end']}号";   
                }
            }
        }else {
            if($row['pwithdrawal_id']) {
                // print_r($row);
                $withdrawal['withdrawal_notice'] = '本月已申请提现';
                //取得本次采用的提现方式
                $thisWithdrawal = $DB->GetRs("promote_withdrawal","*","WHERE pwithdrawal_id = ".$row['pwithdrawal_id']);
                $thisWithdrawal['apply_time'] = $row['apply_time'];
                // print_r($thisWithdrawal);exit();
                $isAlreadyWithdrawal = 1;//判断本月是否已申请提现
            }
        }
    }else {
        $withdrawal['withdrawal_notice'] = '可提余额为零，不可提现';   
    }


}
// print_r($all_withdrawal);exit();
$sm->assign("promote", $promote, true);
$sm->assign("promote_earnings", $promote_earnings, true);
$sm->assign("is_promote", $is_promote, true);
$sm->assign("this_year", $this_year, true);
$sm->assign("this_month", $this_month, true);
$sm->assign("promote_monthly", $promote_monthly, true);
$sm->assign("page_sed_search", $page_sed_search, true);
$sm->assign("withdrawal", $withdrawal, true);
$sm->assign("all_withdrawal", json_encode($all_withdrawal), true);
$sm->assign("this_withdrawal", json_encode($thisWithdrawal), true);
$sm->assign("is_already_withdrawal", $isAlreadyWithdrawal, true);
$sm->assign("module", $module, true);
$sm->assign("submodule", $submodule, true);
