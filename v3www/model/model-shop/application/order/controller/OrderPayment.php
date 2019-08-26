<?php
namespace app\order\controller;

use think\Exception;

class OrderPayment extends Base
{

    //订单支付回调
    public function pay_callback($trabe_no,$total_fee,$pay_type,$transaction_id){
        if( empty($trabe_no) || empty($total_fee) || empty($transaction_id) ) {
            return false;
        }
        try{
            if( empty($pay_type) ){
                throw new Exception('不支持的支付类型');
            }
            //检验部分

            //获取原支付单号信息
            $payment_info = $this->model->where('order_payment_sn',$trabe_no)->find();
            if( $payment_info != $total_fee ){
                throw new Exception('支付金额异常，请联系客服处理');
            }
            //状态更新
        }catch( Exception $e ){
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

}
