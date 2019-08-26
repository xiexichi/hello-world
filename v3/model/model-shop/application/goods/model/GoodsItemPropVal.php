<?php

namespace app\goods\model;

class GoodsItemPropVal extends Base
{
    protected $name = 'goods_item_prop_val';

    const PK = 'id';
    const GOODS = 'goods';//主商品id
    const SKU = 'sku';//sku
    public static $map_show_type = array();

    public function getGoodsProp($ids){
        $goodsItemModel = new \app\goods\model\GoodsItem();
        $goodsPropModel = new \app\goods\model\GoodsProp();
        $goodsPropValModel = new \app\goods\model\GoodsPropVal();
        $where = [];
        $where['ipv.goods_id'] = $ids;
        $where['i.is_invalid'] = 0;
        $propList = $this->field('pv.goods_prop_id,p.prop_name')
            ->alias('ipv')
            ->join($goodsPropValModel->getTable().' pv','pv.id = ipv.goods_prop_val_id')
            ->join($goodsItemModel->getTable().' i','i.id = ipv.goods_item_id')
            ->join($goodsPropModel->getTable().' p','p.id = pv.goods_prop_id')
            ->where($where)
            ->order('p.sort','desc')
            ->group('pv.goods_prop_id')
            ->select();
        $list = [];
        if( !empty($propList) ){
            foreach( $propList as $prop ){
                $list[$prop['goods_prop_id']]= $prop['prop_name'];
            }
        }
        return $list;
    }

    public function getGoodsPropVal($ids,$type=self::GOODS){
        $where = [];
        switch($type){
            case self::GOODS :
                $where['ipv.goods_id'] = $ids;
                break;
            case self::SKU :
                $where['ipv.goods_item_id'] = $ids;
                break;
            default :
                $where['ipv.id'] = $ids;
                break;
        }
        $where['i.is_invalid'] = 0;
        $goodsItemModel = new \app\goods\model\GoodsItem();
        $goodsPropModel = new \app\goods\model\GoodsProp();
        $goodsPropValModel = new \app\goods\model\GoodsPropVal();
        $propList = $this->field('pv.id,pv.pv_name,pv.goods_prop_id')
            ->alias('ipv')
            ->join($goodsPropValModel->getTable().' pv','pv.id = ipv.goods_prop_val_id')
            ->join($goodsItemModel->getTable().' i','i.id = ipv.goods_item_id')
            ->join($goodsPropModel->getTable().' p','p.id = pv.goods_prop_id')
            ->where($where)
            ->order('p.sort','desc')
            ->group('pv.id')
            ->select();
        $list = [];
        if( !empty($propList) ){
            foreach( $propList as $prop ){
                if( $type == self::GOODS ){
                    $list[$prop['goods_prop_id']][$prop['id']] = $prop['pv_name'];
                }else{
                    $list[$prop['goods_prop_id']] = $prop['id'];
                }
            }
        }
        return $list;
    }

}
