<?php

namespace app\order\model;

use app\common\controller\Service;

class OrderReturn extends Base
{
    protected $name = 'order_return_info';

    const RETURN_TYPE_CHANGE = 1;
    const RETURN_TYPE_REFUND = 2;

    public static $map_return_type = array(
        self::RETURN_TYPE_CHANGE => array(
            'desc' => '换货',
            'code' => 'H',
            'status_start' => 15,
            'status_end' => 0
        ),
        self::RETURN_TYPE_REFUND => array(
            'desc' => '退货',
            'code' => 'T',
            'status_start' => 15,
            'status_end' => 35
        )
    );

    //退换状态
    public function statusDesc($returnOrderInfo){
        $desc = '正常';
        switch( $returnOrderInfo['status'] ){
            case 1 :
                $desc = '已完成';
                break;
            case 2 :
                $desc = '已取消';
                break;
            default :
                switch( $returnOrderInfo['verify'] ){
                    case 0 :
                        $desc = '待店铺审核';
                        break;
                    case 2 :
                        $desc = '审核不通过';
                        break;
                    default :
                        switch( $returnOrderInfo['goods_back_status'] ){
                            case 0 :
                                $desc = '待用户寄回';
                                break;
                            case 1 :
                                $desc = '待确认收件';
                                break;
                            default :
                                switch( $returnOrderInfo['return_type']){
                                    case self::RETURN_TYPE_CHANGE ://换货
                                        $desc = '换货中';
                                        if( $returnOrderInfo['return_type'] == 3 ){
                                            $desc = '换货单发出';
                                        }
                                        break;
                                    case self::RETURN_TYPE_REFUND ://退款
                                        $desc = '待退款审核';
                                        if( $returnOrderInfo['return_pay_status'] == 1 ){
                                            $desc = '审核通过';
                                        }else if( $returnOrderInfo['return_pay_status'] == 2 ){
                                            $desc = '审核不通过';
                                        }
                                        break;
                                }
                                break;
                        }
                        break;
                }
        }
        return $desc;
    }

    public function orderGoods(){
        return $this->hasOne('OrderGoods','id','order_goods_id');
    }

    public function returnLog(){
        return $this->hasMany('OrderReturnLog','order_return_id','id')->order('create_time','desc');
    }

    public function returnReason(){
        return $this->hasOne('OrderReturnReason','id','return_reason_id');
    }

    public function oneBefore(){
        $this->with('orderGoods')->with('returnLog')->with('returnReason');
    }

    public function oneAfter(){
        if( !empty($this->data) ){
            $data = [];
            $server = new Service();
            //获取会员信息
            $data['user_info'] = $server->setHost('center_data')->post('user/user/one',['id'=>$this->data['user_id']]);
            $data['goods_info'] = $this->data['order_goods'];
            unset($this->data['order_goods']);
            $data['return_reason'] = $this->data['return_reason'];
            unset($this->data['return_reason']);
            $data['return_log'] = $this->data['return_log'];
            unset($this->data['return_log']);
            $this->data['return_status_desc'] = $this->statusDesc($this->data);
            $this->data['return_images'] = json_decode($this->data['return_images'],true);
            $data['return_info'] = $this->data;
            $this->data = $data;
        }
    }

    public function indexAfter(){
        if( !empty($this->data) ){
            foreach( $this->data as $key => $order ){
                $order['return_type_desc'] = self::$map_return_type[$order['return_type']]['desc'];
                $order['return_status_desc'] = $this->statusDesc($order);
                $this->data[$key] = $order;
            }
        }
    }

    //获取商品 已申请/申请中 退换的数量
    public function getGoodsRefundNum($order_goods_id){
        $num = $this->where("order_goods_id = {$order_goods_id} and status = 0")
            ->value('sum(return_num)');
        if( is_null($num) ){
            $num = 0;
        }
        return $num;
    }
}
