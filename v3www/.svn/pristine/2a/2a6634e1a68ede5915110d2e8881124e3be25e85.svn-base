<?php

namespace app\activity\model;

class CouponGoods extends Base
{
    protected $name = 'coupon_goods';

    public function setGoods($coupon_id,$goodsList){
        if( empty($coupon_id) ){
            return false;
        }
        //获取原商品列表
        $where['coupon_id'] = $coupon_id;
        $goods_all = $this->field('coupon_id,goods_id')
            ->where($where)
            ->select();
        //取消绑定
        if( !empty($goods_all) ){
            if( !$this->where($where)->delete() ){
                return false;
            }
        }
        //绑定
        if( !empty($goodsList) ){
            $insertData = [];
            foreach( $goodsList as $key => $val ){
                $insertData[] = [
                    'coupon_id' => $coupon_id,
                    'goods_id' => $val
                ];
            }
            if( !$this->insertAll($insertData) ){
                return false;
            }
        }
        return true;
    }
}
