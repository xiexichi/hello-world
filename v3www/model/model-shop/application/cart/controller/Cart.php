<?php

namespace app\cart\controller;
use \app\common\controller\Service;

class Cart extends Base
{
    //获取购物车列表
    public function getCartList(){
        if( !$cartParam = $this->validate->getCartList(input('post.')) ) {
            return errorJson(000001, $this->validate->getError());
        }
        $user_id = $cartParam['user_id'];
        $channel = empty($cartParam['channel_id']) ? (new \app\order\model\Order())::CHANNEL_PC : $cartParam['channel_id'];
        $shop_id = empty($cartParam['shop_id']) ? 0 : $cartParam['shop_id'];
        $this->model->cartReset($user_id);
        $list = $this->model->getCartList($user_id,$shop_id,$channel);
        if( $list === false ){
            return errorJson(000010, $this->model->getError());
        }
        return successJson($list);
    }

    //加入购物车
    public function addCart(){
        if( !$param = $this->validate->addCart(input('post.')) ) {
            return errorJson(000001, $this->validate->getError());
        }
        $user_id = $param['user_id'];
        $item_id = $param['item_id'];
        $shop_id = $param['shop_id'];
        $num = $param['num'];
        $channel = empty($param['channel']) ? (new \app\order\model\Order())::CHANNEL_PC : $param['channel'];
        $buy_now = empty($param['buy_now']) ? false : true;//是否理解购买参数
        if( !$this->model->addCart($user_id,$item_id,$num,$shop_id,$channel,$buy_now) ){
            return errorJson($this->model->getCode(),$this->model->getError());
        }
        return successJson('success','添加成功');
    }

    public function addGift(){
        if( !$param = $this->validate->addGift(input('post.')) ) {
            return errorJson(000001, $this->validate->getError());
        }
        $user_id = $param['user_id'];
        $item_id = $param['item_id'];
        $shop_id = $param['shop_id'];
        $num = $param['num'];
        $gift_parent_id = $param['gift_parent_id'];
        $gift_id = $param['gift_id'];
        $giftData = [
            'gift_parent_id' => $gift_parent_id,
            'gift_id' => $gift_id
        ];
        $channel = empty($param['channel']) ? (new \app\order\model\Order())::CHANNEL_PC : $param['channel'];
        if( !$this->model->addCart($user_id,$item_id,$num,$shop_id,$channel,true,$giftData) ){
            return errorJson($this->model->getCode(),$this->model->getError());
        }
        return successJson('success','添加成功');
    }

    //购物车操作
    public function updateCart(){
        if( !$param = $this->validate->addCart(input('post.')) ) {
            return errorJson(000001, $this->validate->getError());
        }
        $user_id = $param['user_id'];
        $item_id = $param['item_id'];
        $num = $param['num'];
        $shop_id = $param['shop_id'];
        $channel = empty($param['channel']) ? (new \app\order\model\Order())::CHANNEL_PC : $param['channel'];
        //检查购物车
        $where['user_id'] = $user_id;
        $where['goods_item_id'] = $item_id;
        $where['shop_id'] = $shop_id;
        $cartItemInfo = $this->model->where($where)->find();
        if( !empty($cartItemInfo) && $num == 0 ){
            if( $this->model->where('id',$cartItemInfo['id'])->delete() ){
                return successJson('success','删除成功');
            }else{
                return errorJson(000014,'网络错误');
            }
        }
        if( $this->model->updateCart($cartItemInfo['id'],$item_id,$num,$shop_id,$channel) ){
            return successJson('success','更新成功');
        }else{
            return errorJson($this->model->getCode(),$this->model->getError());
        }
    }

    //选中商品
    public function selectGoods(){
        if( !$param = $this->validate->selectGoods(input('post.')) ) {
            return errorJson(000001, $this->validate->getError());
        }
        $update['is_selected'] = $param['selected'] ? 1 : 0;
        if( !empty($param['user_id']) && !empty($param['shop_id']) ){
            $where['user_id'] = $param['user_id'];
            $where['shop_id'] = $param['shop_id'];
            $where['gift_parent_id'] = 0;
            $where['gift_id'] = 0;
        }else if( empty($param['id']) && !empty($param['user_id']) ){
            $where['user_id'] = $param['user_id'];
            $where['gift_parent_id'] = 0;
            $where['gift_id'] = 0;
        }else{//赠品选中请用这个
            $where['id'] = $param['id'];
        }
        $update['update_time'] = date('Y-m-d H:i:s');
        if( !$this->model->where($where)->update($update) ){
            return errorJson(000010, '网络错误');
        }
        return successJson('success','操作成功');
    }

}
