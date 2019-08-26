<?php

namespace app\activity\controller;

use app\common\controller\Service;
use think\Exception;

class Coupon extends Base
{
    public function indexBefore(){
        $where = [];
        $param = $this->validate->index(input());
        if( $param === false ){
            $this->isExit = true;
            $this->code = 050101;
            $this->error = $this->validate->getError();
        }
        if( isset($param['use_goods_type']) && $param['use_goods_type'] != '' ){
            $where['c.use_goods_type'] = $param['use_goods_type'];
        }
        if( isset($param['status']) &&  $param['status'] != '' ){
            $where['c.status'] = $param['status'];
        }
        if( isset($param['type']) && $param['type'] != '' ){
            $where['c.type'] = $param['type'];
        }
        $couponUserModel = new \app\activity\model\CouponUser();
        $this->model->alias('c')
            ->field([
                'c.*',
                "(select count(cu.id) from {$couponUserModel->getTable()} cu where cu.coupon_id = c.id )as sand_sum"
            ])
            ->where($where)
            ->order(['c.status'=>'desc','c.create_time'=>'desc']);
    }

    public function createCoupon(){
        $code = 040101;
        if( !$param = $this->validate->detailCoupon(input('post.')) ){
            return errorJson($code,$this->validate->getError());
        }
        //创建优惠券
        $t = date('Y-m-d H:i:s');
        $this->model->startTrans();
        try{
            $tableFields = $this->model->getTableFields();
            //创建券主信息
            $insertData = [];
            foreach( $param as $k => $v ){
                if( in_array($k,$tableFields) ){
                    $insertData[$k] = $v;
                }
            }
            $insertData['create_time'] = $t;
            $insertData['update_time'] = $insertData['create_time'];
            if( !$coupon_id = $this->model->insertGetId($insertData) ){
                $code = 040110;
                throw new Exception('优惠券创建失败');
            }
            //检查是否指定商品
            if( $insertData['use_goods_type'] > 0 ){
                $couponGoodsModel = new \app\activity\model\CouponGoods();
                if( !$couponGoodsModel->setGoods($coupon_id,$param['goods_list']) ){
                    $code = 040111;
                    throw new Exception('商品信息处理失败');
                }
            }
            //检查是否指定店铺
            if( $insertData['use_shop_type'] > 0 ){
                $couponShopModel = new \app\activity\model\CouponShop();
                if( !$couponShopModel->setShop($coupon_id,$param['shop_list']) ){
                    $code = 040111;
                    throw new Exception('商品信息处理失败');
                }
            }
            actionLogs('创建优惠券id:'.$coupon_id,$this->model);
            $this->model->commit();
        }catch( Exception $e ){
            $this->model->rollback();
            return errorJson($code,$e->getMessage());
        }
        return successJson('success','创建成功');
    }

    public function saveCoupon(){
        $code = 040101;
        if( !$param = $this->validate->detailCoupon(input('post.')) ){
            return errorJson($code,$this->validate->getError());
        }
        $t = date('Y-m-d H:i:s');
        $ids = $param['coupon_id'];
        $this->model->startTrans();
        try{
            //获取优惠券信息
            $where['id'] = $ids;
            $couponInfo = $this->model->where($where)->find();
            if( empty($couponInfo) ){
                $code = 040110;
                throw new Exception('优惠券信息获取失败');
            }
            $couponInfo = $couponInfo->toArray();
            $updateData = [];
            foreach( $couponInfo as $key => $val ){
                if( isset($param[$key]) && $param[$key] != $val ){
                    $updateData[$key] = $param[$key];
                }
            }
            $updateData['update_time'] = $t;
            //更新信息
            if( !$this->model->where($where)->update($updateData) ){
                $code = 040111;
                throw new Exception('优惠券信息更新失败');
            }
            //检查是否指定商品
            if( $param['use_goods_type'] > 0 ){
                $goods_list = empty($param['goods_list']) ? [] : $param['goods_list'];
                $couponGoodsModel = new \app\activity\model\CouponGoods();
                if( !$couponGoodsModel->setGoods($ids,$goods_list) ){
                    $code = 040112;
                    throw new Exception('商品信息处理失败');
                }
            }
            //检查是否指定店铺
            if( $param['use_shop_type'] > 0 ){
                $shop_list = empty($param['shop_list']) ? [] : $param['shop_list'];
                $couponShopModel = new \app\activity\model\CouponShop();
                if( !$couponShopModel->setShop($ids,$shop_list) ){
                    $code = 040111;
                    throw new Exception('商品信息处理失败');
                }
            }
            actionLogs('修改优惠券信息 券id:'.$ids,$this->model);
            $this->model->commit();
        }catch( Exception $e ){
            $this->model->rollback();
            return errorJson($code,$e->getMessage());
        }
        return successJson('success','保存成功');
    }

    //领取优惠券
    function getCoupon(){
        if( !$param = $this->validate->getCoupon(input('post.')) ){
            return errorJson(000001,$this->validate->getError());
        }
        //检查用户信息
        $server = new Service();
        $user_info = $server->setHost('center_data')->post('user/user/one',['id'=>$param['user_id']]);
        if( empty($user_info) ){
            return errorJson(000001,'用户信息获取失败');
        }
        if( !$this->model->sand($user_info['id'],$param['coupon_id']) ){
            return errorJson(000010,$this->model->getError());
        }
        return successJson('SUCCESS','领取成功');
    }

}
