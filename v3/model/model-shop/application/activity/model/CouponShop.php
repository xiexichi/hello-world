<?php

namespace app\activity\model;

class CouponShop extends Base
{
    protected $name = 'coupon_shop';

    public function setShop($coupon_id,$shopList){
        if( empty($coupon_id) ){
            return false;
        }
        //获取原商品列表
        $where['coupon_id'] = $coupon_id;
        $shop_all = $this->field('coupon_id,shop_id')
            ->where($where)
            ->select();
        //取消绑定
        if( !empty($shop_all) ){
            if( !$this->where($where)->delete() ){
                return false;
            }
        }
        //绑定
        if( !empty($shopList) ){
            $insertData = [];
            foreach( $shopList as $key => $val ){
                $insertData[] = [
                    'coupon_id' => $coupon_id,
                    'shop_id' => $val
                ];
            }
            if( !$this->insertAll($insertData) ){
                return false;
            }
        }
        return true;
    }
}
