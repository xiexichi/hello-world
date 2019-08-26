<?php

namespace app\order\model;

class OrderDiscount extends Base
{
    protected $name = 'order_discount';
    protected $pk = 'id';

    protected $discount_content = array();//下单所有已使用的优惠类型

    public function discountLog($order_id,$orderInfo,$discountContent){
        if( empty($order_id) ){
            $this->error = '缺少订单';
            return false;
        }
        if( empty($discountContent) ){
            return true;
        }
        $discount_list = [];//优惠列表
        foreach( $discountContent as $key => $cont ){
            if( empty($cont) ){
                continue;
            }
            switch($key){
                case 'point' ://积分
                    $discount_list[] = [
                        'order_id' => $order_id,
                        'discount_type' => 0,
                        'discount_title' => '积分抵扣',
                        'discount_content' => '使用比例: '.$cont.' : 1',
                        'discount_price' => $orderInfo['point_price']
                    ];
                    break;
                case 'coupon' ://优惠券 未有数据结构
                    $content = '使用优惠券：';
                    $updateWhere = '';
                    if( $orderInfo['coupon_price'] > 0 && empty($cont[$orderInfo['shop_id']]) ){
                        $this->error = '优惠券使用信息缺失';
                        return false;
                    }
                    foreach( $cont[$orderInfo['shop_id']] as $coupon ){
                        $updateWhere .= $coupon['user_coupon_id'].',';
                        $content .= $coupon['coupon_sn'].',';
                    }
                    $couponUserModel = new \app\activity\model\CouponUser();
                    $where['id'] = ['in',trim($updateWhere,',')];
                    $updateData['use_status'] = 1;
                    $updateData['use_time'] = $orderInfo['create_time'];
                    if( !$couponUserModel->where($where)->update($updateData) ){
                        $this->error = '优惠券使用失败';
                        return false;
                    }
                    $discount_list[] = [
                        'order_id' => $order_id,
                        'discount_type' => 0,
                        'discount_title' => '优惠券',
                        'discount_content' => trim($content,','),
                        'discount_price' => $orderInfo['coupon_price']
                    ];
                    break;
                case 'activity' ://营销活动优惠 未有现成功能
                    foreach( $cont[$orderInfo['shop_id']] as $k => $v ){
                        $discount_list[] = [
                            'order_id' => $order_id,
                            'discount_type' => 0,
                            'discount_title' => '营销活动',
                            'discount_content' => $v,
                            'discount_price' => $orderInfo['discount_price']
                        ];
                    }
                    break;
            }
        }
        if( !$this->insertAll($discount_list) ){
            $this->error = '信息记录失败';
            return false;
        }
        $this->discount_content = [];//重置使用列表
        return true;
    }
}
