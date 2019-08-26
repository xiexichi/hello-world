<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$GLOBALS['DB'] = $DB;
$year          = empty($_GET['year'])?date('Y'):intval($_GET['year']);
$promote_id    = $is_promote ? $promote['promote_id'] : 0;

//按月取得预估收入
function get_earnings_monthly($promote_id,$year,$month) {
    $month = str_pad($month,2,'0',STR_PAD_LEFT);
    $monthly = "$year-$month";

    $sql = "SELECT sum(pe.earnings) AS total FROM promote_earnings pe
            WHERE pe.promote_id = $promote_id  AND date_format(pe.received_time,'%Y-%m') = '$monthly'";
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
            WHERE o.pay_status = 1  AND o.pay_method <> 2 AND po.promote_id = $promote_id AND date_format(o.pay_date,'%Y-%m') = '$monthly'";
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
    // echo $sql;exit();
    $query = $GLOBALS['DB']->query($sql);
    $row   = $GLOBALS['DB']->fetch_array();
    return $row['total'];
}


$end_month = $year == date('Y') ? (date('n')-1) : 12;
for ($i=1; $i <=$end_month;$i++) { 
    //预估收入
    $promote_earnings_monthly[$i] = get_earnings_monthly($promote_id,$year,$i);
    //付款笔数
    $promote_paid_order_monthly[$i] = get_paid_order_monthly($promote_id,$year,$i);
    //充值笔数
    $promote_recharge_num_monthly[$i] = get_recharge_num_monthly($promote_id,$year,$i);
  }   

$earnings_info = array(
    'promote_earnings_monthly'    => $promote_earnings_monthly,
    'promote_paid_order_monthly'  => $promote_paid_order_monthly,
    'promote_recharge_num_monthly'=> $promote_recharge_num_monthly,
);
echo json_encode(array('info'=>$earnings_info,'month'=>$end_month));
?>