<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$pwithdrawal_id = empty($_GET['pwithdrawal_id'])?0:intval($_GET['pwithdrawal_id']);
$password       = empty($_GET['password'])?'':htmlspecialchars($_GET['password']);
$promote_id     = empty($promote['promote_id']) ? 0 : $promote['promote_id'];
$user_id        = intval($_SESSION['user_id']);

//验证提现方式
if($pwithdrawal_id < 0) {
    echo json_encode(array('status'=>'failed','msg'=>'没有选择提现方式'));
    exit();
}

//验证密码
$sql = "SELECT * FROM users WHERE user_id = $user_id";
$return = $DB->query($sql);
$user = $DB->fetch_array();

if($user['password'] != md5($password) && $user['password'] != $Base->pass_crypt($password)) {
	echo json_encode(array('status'=>'wrong_pass','msg'=>'密码错误'));
    exit();
}

//判断可提余额是否为零
if($promote['cash_total'] == 0) {
	echo json_encode(array('status'=>'reload','msg'=>'可提余额为零，提现失败！'));
    exit();
}

//判断是否已申请提现
$sql = "SELECT * FROM promote_cash WHERE promote_id = $promote_id AND date_format(apply_time,'%Y-%m') =  date_format(CURDATE(),'%Y-%m') LIMIT 1";
$return = $DB->query($sql);
$row = $DB->fetch_array();

if(!empty($row)) {
	echo json_encode(array('status'=>'reload','msg'=>'当月已申请提现，操作失败！'));
    exit();
}


//添加提现申请
$pay_sn = $Base->build_order_no('B');
$sql = "INSERT INTO promote_cash (promote_id,cash,pwithdrawal_id,pay_sn,apply_time) 
		VALUES ({$promote_id},{$promote['cash_total']},{$pwithdrawal_id},'{$pay_sn}',now())";
$return = $DB->query($sql);
$pcash_id = $DB->insert_id();

if($pcash_id) {
   switch ($pwithdrawal_id) {
   		case '0':
			$promote_withdrawal = $promote_config['promote_withdrawal'];
            $money = $promote['cash_total'];
            $plus = 0;
            //先计算优惠
            if($promote_withdrawal['withdrawal_bag'] && $promote_withdrawal['withdrawal_full']>0 && $promote_withdrawal['withdrawal_plus']>0) {
                $plus = intval($promote['cash_total'] / $promote_withdrawal['withdrawal_full']) * $promote_withdrawal['withdrawal_plus'];
                $money += $plus;
            }

	        $sql = "UPDATE users SET bag_total = bag_total + {$money} WHERE user_id = {$user_id}";
	        $return = $DB->query($sql);
            //入账
            if($DB->affected_rows()) {
                //到账设置
		        $sql = "UPDATE promote_cash SET is_reach = 1,reach_time =  now() WHERE pcash_id = $pcash_id LIMIT 1";
		       	$return = $DB->query($sql);

                //清空可提余额
		        $sql = "UPDATE promote SET cash_total = 0 WHERE promote_id = $promote_id LIMIT 1";
		        $return = $DB->query($sql);

		        //流水
                $DB->Add("bag",array(
                    "pay_status"=> 'paid',
                    "pay_sn"	=> $pay_sn,
                    "method"	=> 'bag',
                    "user_id"	=> $user_id,
                    "create_date"=> date('Y-m-d H:i:s'),
                    "money"		=> $promote['cash_total'],
                    "type" 		=> 'withdrawal',
                    "note"		=> ($plus > 0) ? '二五提现充值，充'.$promote['cash_total'].'送'.$plus : '二五提现充值',
                    "pay_date"	=> date('Y-m-d H:i:s'),
                    "balance"	=> $user['bag_total'] + $money,
                    "plus_price"=> $plus,
                ));
				echo json_encode(array('status'=>'success','msg'=>'提现到钱包余额成功，即时到账'));
				exit();
            }else {
            	$sql = "DELETE FROM promote_cash WHERE pcash_id = $pcash_id";
            	$DB->query();
				echo json_encode(array('status'=>'reload','msg'=>'发生错误，提现失败！'));
			    exit();
            }
   			break;
        default: //提现到第三方
            /*20180223注释 文杰，第三方提现，确认到账时才增加流水
            //取出提现方式
            $sql = "SELECT * FROM promote_withdrawal WHERE pwithdrawal_id = $pwithdrawal_id LIMIT 1";
        	$result = $DB->query($sql);
        	$row = $DB->fetch_array($result);

        	switch ($row['withdrawal_type']) {
        		case 'alipay':
        			$to = '支付宝';
        			break;
        		case 'weixin':
        			$to = '微信';
        			break;
        		default:
                    $to = $row['withdrawal_type'];
        			break;
        	}

		    //流水
            $DB->Add("bag",array(
                "pay_status"=> 'payment',
                "pay_sn"	=> $pay_sn,
                "method"	=> empty($row['withdrawal_type']) ? 'alipay' : $row['withdrawal_type'],
                "user_id"	=> $user_id,
                "create_date"=> date('Y-m-d H:i:s'),
                "money"		=> $promote['cash_total'],
                "type" 		=> 'withdrawal',
                "note"		=> '二五提现到'.$to,
                "pay_date"	=> date('Y-m-d H:i:s'),
                "balance"	=> $user['bag_total'],
                "plus_price"=> 0,
            ));*/
			echo json_encode(array('status'=>'success','msg'=>'提现申请成功，20号后统一划帐。'));
            break;
   }

}else {
	echo json_encode(array('status'=>'failed','msg'=>'提现失败，请稍后重试！'));
}

