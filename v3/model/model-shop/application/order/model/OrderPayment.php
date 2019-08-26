<?php

namespace app\order\model;

class OrderPayment extends Base
{
    protected $name = 'order_payment';

    const BALANCE_PAY = 0; //余额支付
    const ALLIN_PAY = 1; //支付宝支付
    const WX_PAY = 2; //微信支付
    const CARD_PAY = 3; //银行卡支付

    public static $map_pay_type = array(
        self::BALANCE_PAY => array(
            'desc' => '余额支付'
        ),
        self::ALLIN_PAY => array(
            'desc' => '支付宝支付'
        ),
        self::WX_PAY => array(
            'desc' => '微信支付'
        ),
        self::CARD_PAY => array(
            'desc' => '银行卡支付'
        )
    );

    //获取订单信息
    public function createPaymentSn($order_sn){
        return $order_sn.rand(100,999);
    }

    /**
     * 生成支付流水号
     * @param $order_id     订单order_id
     * @param $pay_price    支付金额
     * @return bool
     */
    public function createPaymentOrder($order_id,$price){
        //获取订单信息
        $orderModel = new \app\order\model\Order();
        $orderInfo = $orderModel->where('order_id',$order_id)->find();
        if( empty($orderInfo) ){
            $this->error = '订单不存在';
            return false;
        }
//        //检查商品合计金额
//        $orderGoodsModel = new \app\order\model\OrderGoods();
//        $orderGoodsList = $orderGoodsModel->getItemList($order_id);
//        if( !empty($orderGoodsList) ){
//            $this->error = '订单商品获取失败';
//            return false;
//        }
//        $pay_price = 0;
//        foreach( $orderGoodsList as $goods ){
//            if($pay_price){
//                $pay_price = bcadd($pay_price,$goods['item_price'],2);
//            }
//        }
        if( $price > bcsub($orderInfo['goods_price'],$orderInfo['pay_price'],2) ){
            $this->error = '订单金额异常';
            return false;
        }
        //生成支付单号
        $insertData['order_payment_sn'] = $this->createPaymentSn($orderInfo['order_sn']);
        $insertData['order_id'] = $order_id;
        $insertData['pay_price'] = $price;
        if( !$this->insert($insertData) ){
            $this->error = '网络错误，单号生成失败';
            return false;
        }
        return $insertData['order_payment_sn'];
    }



}
