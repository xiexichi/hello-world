<?php

namespace app\activity\controller;

use think\Exception;

class Gift extends Base
{
    public function indexBefore(){
        $param = $this->validate->index(input());
        if( $param === false ){
            $this->isExit = true;
            $this->code = 040201;
            $this->error = $this->validate->getError();
        }
        $where = [];
        if( isset($param['id']) && !empty($param['id']) ){
            $where['gi.id'] = $param['id'];
        }
        if( isset($param['status']) && $param['status'] != '' ){
            $where['gi.status'] = $param['status'];
        }
        if( isset($param['type']) && empty($param['type']) ){
            $where['gi.type'] = $param['type'];
        }
        $this->model->where($where);
    }

    public function createGift(){
        $code = 040201;
        if( !$param = $this->validate->detailGift(input('post.')) ){
            return errorJson($code,$this->validate->getError());
        }
        $this->model->startTrans();
        try{
            $t = date('Y-m-d H:i:s');
            //这里的检查留给开启时检查了
//            $where = "goods_id = {$param['goods_id']}";
//            $where .= " and ( start_time between '{$param['start_time']}' and '{$param['end_time']}' or end_time between '{$param['start_time']}' and '{$param['end_time']}' ) ";
//            //检查赠品相同时段是否有重复设置
//            if( $this->model->where($where)->find() ){
//                $code = 040210;
//                throw new Exception('赠送商品相同时间段不能设置多个活动');
//            }
            //添加设置
            $tableFields = $this->model->getTableFields();
            $insertData = [];
            foreach( $param as $k => $v ){
                if( in_array($k,$tableFields) ){
                    $insertData[$k] = $v;
                }
            }
            $insertData['create_time'] = $t;
            $insertData['update_time'] = $t;
            if( !$gift_id = $this->model->insertGetId($insertData) ){
                $code = 040211;
                throw new Exception('信息错误，添加失败');
            }
            //检查是否指定商品
            if( $insertData['use_goods_type'] > 0 ){
                $giftGoodsModel = new \app\activity\model\GiftGoods();
                if( !$giftGoodsModel->setGoods($gift_id,$param['goods_list']) ){
                    $code = 040212;
                    throw new Exception('商品信息处理失败');
                }
            }
            //检查是否指定店铺
            if( $insertData['use_shop_type'] > 0 ){
                $giftShopModel = new \app\activity\model\GiftShop();
                if( !$giftShopModel->setShop($gift_id,$param['shop_list']) ){
                    $code = 040213;
                    throw new Exception('商品信息处理失败');
                }
            }
            actionLogs('创建赠品活动 id:'.$gift_id,$this->model);
            $this->model->commit();
        }catch( Exception $e ){
            $this->model->rollback();
            return errorJson($code,$e->getMessage());
        }
        return successJson('success','设置成功');
    }

    public function saveGift(){
        $code = 040201;
        if( !$param = $this->validate->detailGift(input('post.')) ){
            return errorJson($code,$this->validate->getError());
        }
        $t = date('Y-m-d H:i:s');
        $gift_id = $param['gift_id'];
        $this->model->startTrans();
        try{
            //获取优惠券信息
            $where['id'] = $gift_id;
            $gift_info = $this->model->where($where)->find();
            if( empty($gift_info) ){
                $code = 040210;
                throw new Exception('活动信息获取失败');
            }
            $gift_info = $gift_info->toArray();
            $updateData = [];
            foreach( $gift_info as $key => $val ){
                if( isset($param[$key]) && $param[$key] != $val ){
                    $updateData[$key] = $param[$key];
                }
            }
            $updateData['order_group'] = isset($param['order_group']) ? $param['order_group'] : 0;
            $updateData['order_o2o'] = isset($param['order_o2o']) ? $param['order_o2o'] : 0;
            $updateData['order_online'] = isset($param['order_online']) ? $param['order_online'] : 0;
            //这里的检查留给开启时检查了
//            if( isset($updateData['goods_id']) || isset($updateData['start_time']) || isset($updateData['end_time']) ){
//                $check_where = "goods_id = {$param['goods_id']}";
//                $check_where .= " and ( start_time between '{$param['start_time']}' and '{$param['end_time']}' or end_time between '{$param['start_time']}' and '{$param['end_time']}' ) ";
//                //检查赠品相同时段是否有重复设置
//                if( $this->model->where($check_where)->find() ){
//                    $code = 040211;
//                    throw new Exception('赠送商品相同时间段不能设置多个活动');
//                }
//            }
            //更新信息
            $updateData['update_time'] = $t;
            if( !$this->model->where($where)->update($updateData) ){
                $code = 040212;
                throw new Exception('活动信息更新失败');
            }
            //检查是否指定商品
            if( $param['use_goods_type'] > 0 ){
                $goods_list = empty($param['goods_list']) ? [] : $param['goods_list'];
                $giftGoodsModel = new \app\activity\model\GiftGoods();
                if( !$giftGoodsModel->setGoods($gift_id,$goods_list) ){
                    $code = 040213;
                    throw new Exception('商品信息处理失败');
                }
            }
            //检查是否指定店铺
            if( $param['use_shop_type'] > 0 ){
                $shop_list = empty($param['shop_list']) ? [] : $param['shop_list'];
                $giftShopModel = new \app\activity\model\GiftShop();
                if( !$giftShopModel->setShop($gift_id,$shop_list) ){
                    $code = 040214;
                    throw new Exception('商品信息处理失败');
                }
            }
            actionLogs('修改赠品活动 id:'.$gift_id,$this->model);
            $this->model->commit();
        }catch( Exception $e ){
            $this->model->rollback();
            return errorJson($code,$e->getMessage());
        }
        return successJson('success','保存成功');
    }

    //获取可选赠品列表
    public function getGiftList(){
        if( !$param = $this->validate->getGiftList(input('post.')) ){
            return errorJson(000001,$this->validate->getError());
        }
        $cart_id = isset($param['cart_id']) ? $param['cart_id'] : 0;
        $gift_list = $this->model->getGoodsGiftList($param['shop_id'],$param['goods_id'],$cart_id);
        return successJson($gift_list);
    }
}
