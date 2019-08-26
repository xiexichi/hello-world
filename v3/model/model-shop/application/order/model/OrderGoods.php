<?php

namespace app\order\model;

class OrderGoods extends Base
{
    protected $name = 'order_goods_info';

    public function statusDesc($orderGoodsInfo){
        $desc = '';
        switch( $orderGoodsInfo['status'] ){
            case '15' :
                $desc = '退换货中';
                break;
            case '30' :
                $desc = '已退款';
                break;
            case '35' :
                $desc = '退款中';
                break;
            case '40' :
                $desc = '已作废';
                break;
            default :
                $desc = '正常';
                if( $orderGoodsInfo['ship_status'] == 1 ){
                    $desc = '已发货';
                }
                break;
        }
        return $desc;
    }

    public function getItemList($order_id,$limit='',$item_id=0){
        $goodsItemModel = new \app\goods\model\GoodsItem();
        $where['order_id'] = $order_id;
        if( $item_id != 0 ){
            $where['goods_item_id'] = $item_id;
        }
        $item_list = $this->where($where)->limit($limit)->select();
        if(!empty($item_list)){
            foreach( $item_list as $key => $goods ){
                $goods['item_info'] = $goodsItemModel->getItemInfo($goods['goods_item_id']);
                $goods['goods_status_desc'] = $this->statusDesc($goods);
                $item_list[$key] = $goods;
            }
        }
        return $item_list;
    }

}
