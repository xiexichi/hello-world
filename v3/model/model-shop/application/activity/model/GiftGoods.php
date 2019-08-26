<?php

namespace app\activity\model;

class GiftGoods extends Base
{
    protected $name = 'gift_goods';

    public function setGoods($gift_id,$goodsList){
        if( empty($gift_id) ){
            return false;
        }
        //获取原商品列表
        $where['gift_id'] = $gift_id;
        $goods_all = $this->field('gift_id,goods_id')
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
                    'gift_id' => $gift_id,
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
