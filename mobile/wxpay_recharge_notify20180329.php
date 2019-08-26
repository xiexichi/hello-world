<?php
/**
 * 通用通知接口demo
 * ====================================================
 * 支付完成后，微信会把相关支付和用户信息发送到商户设定的通知URL，
 * 商户接收回调信息后，根据需要设定相应的处理流程。
 *
 * 这里举例使用log文件形式记录回调信息。
 */
require_once "config.php";
include_once("log_pay.php");
include_once("pay/wx/WxPayPubHelper.php");

include_once($_SERVER['DOCUMENT_ROOT']."/class/Balance.php");
$balanceclass = new Balance();

// $DB = new mysql();

//使用通用通知接口
$notify = new Notify_pub();

//存储微信的回调
// $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
// if(empty($xml)) return false;
// $notify->saveData($xml);


// 测试数据
$notify->data = unserialize('a:17:{s:5:"appid";s:18:"wx03cbfff87f584209";s:6:"attach";s:4:"1587";s:9:"bank_type";s:3:"CFT";s:8:"cash_fee";s:3:"7200";s:8:"fee_type";s:3:"CNY";s:12:"is_subscribe";s:1:"Y";s:6:"mch_id";s:10:"1219578801";s:9:"nonce_str";s:32:"g47smntoh2auyg68k2gcuj63spy9zxy7";s:6:"openid";s:28:"oXNwpuLQZmwmjh7bjWBekV1SCHyw";s:12:"out_trade_no";s:24:"121957880120160531115821";s:11:"result_code";s:7:"SUCCESS";s:11:"return_code";s:7:"SUCCESS";s:4:"sign";s:32:"A12A6542E5BBB448BE0A33B3388AF9E0";s:8:"time_end";s:14:"20160531115831";s:9:"total_fee";s:5:"75000";s:10:"trade_type";s:5:"JSAPI";s:14:"transaction_id";s:28:"4007492001201605316550868128";}');
$notify->data["attach"] = $_GET['bid'];  //bag_id
$notify->data['total_fee'] = $_GET['mo']*100;  //money
$notify->setReturnParameter("return_code","SUCCESS");//设置返回码


//验证签名，并回应微信。
//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
//尽可能提高通知的成功率，但微信不保证通知最终能成功。
if($notify->checkSign() == FALSE || !$test){
    $notify->setReturnParameter("return_code","FAIL");//返回状态码
    $notify->setReturnParameter("return_msg","签名失败");//返回信息
}else{
    $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
}

$returnXml = $notify->returnXml();
echo $returnXml;


//==商户根据实际情况设置相应的处理流程，此处仅作举例=======

//以log文件形式记录回调信息
$log_ = new Log_();
$log_name="logs/".date('Y-m-d')."-recharge-notify.log";//log文件路径
$log_->log_result($log_name,"【接收到的notify通知】\n".$xml."\n");

/*测试 1*/
if($notify->checkSign() == TRUE || !$test)
{
    $total_fee = (float)($notify->data['total_fee']/100);
    $transaction_id = $notify->data['transaction_id'];
    $last_id = $notify->data["attach"];
    $last_id = (int)$last_id;
    // echo $last_id;exit();
    if(!empty($last_id)){

        if ($notify->data["return_code"] == "FAIL") {
            $log_->log_result($log_name,"【通信出错】\n".$xml."\n");
        }elseif($notify->data["result_code"] == "FAIL"){
            //此处应该更新一下订单状态，商户自行增删操作
            $log_->log_result($log_name,"【业务出错】\n".$xml."\n");

        }else{
            //此处应该更新一下订单状态，商户自行增删操作
            $bag = $DB->GetRs("bag", "*", "WHERE bag_id=".$last_id);

            /*
            * 判断支付金额与记录金额是否相符
            * 2017-02-07
            */
            if($total_fee != $bag['money']){
                // 1. 金额不符增加一条新流水
                $newBag['pay_status'] = $bag['pay_status'];
                $newBag['type']       = $bag['type'];
                $newBag['pay_sn']     = $Base->build_order_no('B');
                $newBag['user_id']    = $bag['user_id'];
                $newBag['money']      = $total_fee;
                $newBag['method']     = $bag['method'];
                $newBag['balance']    = $bag['balance'];
                $newBag['plus_price']  = 0;
                $newBag['create_date'] = date('Y-m-d H:i:s',time());
                $newBag['business_code'] = $bag['business_code'];
                $newBag['business_qrcode'] = $bag['business_qrcode'];
                $newBag['business_call'] = $bag['business_call'];
                $newBag['client'] = $bag['client'];
                $newBag['note']       = $bag['note'];
                $DB->Add('bag',$newBag);
                $last_id = $DB->insert_id();

                // 2.金额不符清空之前的流水备注
                $DB->Set("bag","`note`=''", "WHERE bag_id=".$bag['bag_id']);

                // 3. 重写$bag数组
                $newBag['bag_id'] = $last_id;
                $bag = $newBag;
            }


            // 逻辑处理
            if(!empty($bag) && $bag['pay_status']=='payment' && $total_fee==$bag['money']){
                // 查询用户余额
                $user = $DB->GetRs("v_users", "user_id,bag_total,level,nickname,seller_id", "WHERE user_id=".(int)$bag['user_id']);
                $now_time = strtotime(date('Y-m-d'));
                
                /*
                * 充值赠送金额
                */
                if(!empty($bag['note']) && strpos($bag['note'],'合并付款') !== FALSE){
                    $plus_price = 0;  // 20171230 合并付款没有赠送金额
                }else{
                    //获取充值优惠
                    $plus_price = $Common->getRechargePlus($bag['user_id'],$total_fee,$bag['business_code']);
                }

                // 增加钱包流水备注
                $bagSet = array();
                if($plus_price > 0){
                    $bagSet['note'] = ($bag['note']?$bag['note'].'-':'')."充 {$total_fee} 送 {$plus_price}";
                    $bagSet['plus_price'] = $plus_price;
                }

                $bag_total_price = $total_fee+$plus_price;
                $total_money = (float)$user['bag_total']+(float)$bag_total_price;
                // $return = $DB->Set("users","bag_total=".$total_money,"where user_id=".(int)$bag['user_id']);

                $return  = true;

                if ($return) {
                    // 更新流水
                    $bagSet['method'] = 'weixin';
                    $bagSet['pay_status'] = 'paid';
                    $bagSet['transaction_id'] = $transaction_id;
                    $bagSet['balance'] = $total_money;
                    $bagSet['money'] = $total_fee;
                    $bagSet['pay_date'] = date("Y-m-d H:i:s");
                    $DB->Set("bag", $bagSet, "WHERE bag_id=".$last_id);
                    $result = $DB->affected_rows();
                    if($result){
                        // 添加期末余额记录
                        $balanceclass->save_user_bag(array_merge($bag,$bagSet));
                    }

                    /* *********************************************************************
                     * 充值返(有且只有普通会员充值时，推广者才享受返佣收益，合并支付也有佣金)
                     * *********************************************************************/
                    // if(empty($bag['note']) || strpos($bag['note'],'合并付款') === FALSE){
                        if(empty($user['seller_id'])) {
                            $out_trade_no = $bag['pay_sn'];
                            $promote_earnings = $DB->GetRs('promote_earnings','pearnings_id',"WHERE pay_sn = '{$out_trade_no}'");
                            if(empty($promote_earnings)) {
                                $promote_order = $DB->GetRs('promote_order','*',"WHERE pay_sn = '{$out_trade_no}'");   
                                if(!empty($promote_order)) {
                                    $promote_id = $promote_order['promote_id'];
                                    $promote = $DB->GetRs('promote','*',"WHERE promote_id = ".$promote_id);   
                                    if(!empty($promote) && !$promote['is_frozen']) {
                                       //首次充返
                                       $promote_first = $Common->get_beyond_first($promote_id,5);
                                       //后续充返
                                       $beyond_recharge = $Common->get_beyond_recharge($promote_id); 
                                       //判断是否首次充值
                                       $row = $DB->GetRs("bag","*","WHERE user_id = ".$user['user_id']." AND pay_status = 'paid' AND type = 'prepaid' AND method <> '25boy' ORDER BY pay_date");
                                       if(isset($row['pay_sn']) && $row['pay_sn'] == $out_trade_no && !empty($promote_first)) {
                                           //首次充值
                                           $commission_rate = $promote_first['commission_rate'];
                                           $commission      = round($total_fee * $commission_rate / 100,2);
                                           $pplan_id        = $promote_first['pplan_id'];

                                           //返佣下单记录
                                           $DB->Add('promote_earnings',array(
                                                   'earnings_type'  => 're_recharge',
                                                   'pplan_id'       => $pplan_id,
                                                   'pay_sn'         => $out_trade_no,
                                                   'promote_id'     => $promote_id,
                                                   're_price'       => $total_fee,
                                                   'commission_rate'=> $commission_rate,
                                                   'earnings'       => $commission,
                                                   'received_time'  => date("Y-m-d H:i:s"),
                                           ));

                                            $DB->query("UPDATE promote SET earnings_total = earnings_total + $commission WHERE promote_id = $promote_id");
                                       }else {
                                           if(!empty($beyond_recharge)) {
                                               //后续充返
                                               $commission_rate = $beyond_recharge['commission_rate'];
                                               $commission      = round($total_fee * $commission_rate / 100,2);
                                               $pplan_id        = $beyond_recharge['pplan_id'];

                                               //返佣下单记录
                                               $DB->Add('promote_earnings',array(
                                                       'earnings_type'  => 're_recharge',
                                                       'pplan_id'       => $pplan_id,
                                                       'pay_sn'         => $out_trade_no,
                                                       'promote_id'     => $promote_id,
                                                       're_price'       => $total_fee,
                                                       'commission_rate'=> $commission_rate,
                                                       'earnings'       => $commission,
                                                       'received_time'  => date("Y-m-d H:i:s"),
                                               ));
                                            $DB->query("UPDATE promote SET earnings_total = earnings_total + $commission WHERE promote_id = $promote_id");
                                           }

                                       }
                                    }
    
                                }
                            }

                        }
                    // }

                    /*
                    * 充值完成跳转到这里处理合并付款
                    * 合并付款-R20160525162542096379
                    * 匹配 note 字段是否包含 '合并付款'，以 - 分割得到订单号
                    */
                    if(!empty($bag['note']) && strpos($bag['note'],'合并付款') !== FALSE){
                        $ordarr = explode('-', $bag['note']);
                        $order = $DB->GetRs('orders','*',"WHERE order_sn='".$ordarr[1]."'");
                        $pay_total = isset($order['pay_total'])?(float)$order['pay_total']:0;
                        $pay_status = isset($order['pay_status'])?$order['pay_status']:0;
                        $order_id = isset($order['order_id'])?$order['order_id']:0;
                        $order_sn = isset($order['order_sn'])?$order['order_sn']:NULL;

                        //余额足已支付
                        if($pay_status==0 && $total_money >= $pay_total){
                            $new_balance = $total_money - $pay_total;
                            //更新余额
                            $DB->Set("users","bag_total=".(float)$new_balance,"WHERE user_id=".(int)$bag['user_id']);
                            //更新订单历史
                            $DB->Add("order_history",array(
                                "order_id"=>$order_id,
                                "content"=>"钱包支付成功（手机）",
                                "status"=>1,
                                "create_date"=>date('Y-m-d H:i:s')
                            ));
                            //更新订单状态
                            $DB->Set("orders","status=1,pay_status=1,pay_date=NOW(),pay_method=2","WHERE order_id=".$order_id);
                            //备注
                            $note = "手机支付";
                            $business_code = '';
                            $business_qrcode = '';
                            //线下
                            if(!empty($order['business_code'])) {
                                $business_code = $order['business_code'];
                                $business = $DB->GetRs("business","business_name","WHERE business_code = '{$business_code}'");
                                $note = $business['business_name']."用户消费(手机支付)";
                            }
                            // 扫码消费
                            if(!empty($order['business_qrcode'])) $business_qrcode = $order['business_qrcode'];
                                
                            //更新钱包付款状态
                            $bag = array(
                                "pay_status"=>'paid',
                                "pay_sn"=>$order_sn,
                                "method"=>'bag',
                                "user_id"=>$bag['user_id'],
                                "create_date"=>date('Y-m-d H:i:s'),
                                "money"=>-$pay_total,
                                "type" => 'goods',
                                "note"=>$note,
                                "transaction_id"=>$bag['pay_sn'],
                                "balance"=>$new_balance,
                                "pay_date"=>date('Y-m-d H:i:s'),
                                "business_code"=>$business_code,
                                "business_qrcode"=>$business_qrcode
                            );
                            $DB->Add("bag", $bag);
                            $bag_id = $DB->insert_id();
                            if($bag_id)
                            {
                                // 添加期末余额记录
                                $bag['bag_id'] = $bag_id;
                                $balanceclass->save_user_bag($bag);

                                //更新用户消费总额
                                $DB->Set("users","consume_total = consume_total + ".$pay_total,"where user_id=".(int)$bag['user_id']);

                                $log_->log_result($log_name,"【订单合并付款成功】".$order_sn."\n");

                                // 添加 调用gyerp类，添加订单
                                include_once($_SERVER['DOCUMENT_ROOT']."/class/grerp.php");
                                $gyerp = new gyerp();
                                $erp = $gyerp->getOrderForErp($order_id);
                                $gyerp->add_order($erp);
                            }
                        }
                    }
                }
            }

            $log_->log_result($log_name,"【支付成功】".$bag['pay_sn']."\n");
        }
    }

    //商户自行增加处理流程,
    //例如：更新订单状态
    //例如：数据库操作
    //例如：推送支付完成信息
}

?>