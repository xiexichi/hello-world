<?php

namespace app\activity\model;

class GiftShop extends Base
{
    protected $name = 'gift_shop';

    public function setShop($gift_id,$shopList){
        if( empty($gift_id) ){
            return false;
        }
        //获取原商品列表
        $where['gift_id'] = $gift_id;
        $shop_all = $this->field('gift_id,shop_id')
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
                    'gift_id' => $gift_id,
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
