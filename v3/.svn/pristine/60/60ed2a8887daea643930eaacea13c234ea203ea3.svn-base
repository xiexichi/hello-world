<?php

namespace app\cart\model;
use app\order\model\Order;
use \app\common\controller\Service;

class Cart extends Base
{

    /**
     * 购物车初始化
     * 清除所有必须立即购买的活动商品
     * @param $user_id      用户id
     */
    public function cartReset($user_id){
        $where['user_id'] = $user_id;
        $where['is_group'] = 1;
        $where = "user_id = {$user_id} and ";
        $where .= " ( is_group = {$user_id}";
        //这个是赠品 注释 这相当于不允许进入购物车 只能在下单页操作
        //默认最好是这个操作，因为一个商品会同时符合多个赠送活动而只能送一个赠品
        //除非 业务是 送合活动的所有赠品均自动送出 这个就绝壁的注释这吧
//        $where .= " or gift_id > 0";
        $where .= " )";
        if( !$this->where($where)->delete() ){
            return false;
        }
        return true;
    }

    /**
     * 下单成功清空购物车已选商品
     * @param $user_id      用户id
     */
    public function cartOrderClear($user_id){
        $where['user_id'] = $user_id;
        $where['is_selected'] = 1;
        if( !$this->where($where)->delete() ){
            return false;
        }
        return true;
    }

    public function GiftList(){
        return $this->hasMany('Cart','gift_parent_id','id')->where('gift_parent_id > 0 && gift_id > 0');
    }

    public function getCartList($user_id,$shop_id=0,$channel=Order::CHANNEL_PC,$is_selected=false){
        //check channel 预留用于活动使用
        if( in_array($channel,Order::$map_channel) ){
//            return [];
        }
        $where['user_id'] = $user_id;
        $where['gift_parent_id'] = 0;
        $where['gift_id'] = 0;
        if($shop_id){
            $where['shop_id'] = $shop_id;
        }
        if($is_selected){
            $where['is_selected'] = 1;
        }
        $list = $this->with('GiftList')->where($where)->select();
        if( !empty($list) ){
            $server = new Service();
            $goodsItemModel = new \app\goods\model\GoodsItem();
            $giftModel = new \app\activity\model\Gift();
            $shop = [];
            foreach( $list as $key => $val ){
                $itemInfo = $goodsItemModel->getItemInfo($val['goods_item_id']);
                if( empty($shop[$val['shop_id']]) ){
                    //获取店铺信息
                    $shop_info = $server->setHost('center_data')->post('merchant/shop/one',['id'=>$val['shop_id']]);
                    $shop[$val['shop_id']] = [
                        'shop_id' => $val['shop_id'],
                        'shop_name' => $shop_info['name'],
                        'shop_status' => $shop_info['status'],
                        'shop_sale_type_id' => $shop_info['shop_sale_type_id'],
                        'status_desc' => $shop_info['status'] ? '' : '店铺已关闭',
                        'coupon_price' => 0.00,
                        'point_price' => 0.00,
                        'discount_price' => 0.00,
                        'ship_status' => 1,
                        'ship_price' => 0.00,
                        'goods_price' => 0.00,
                        'goods_list' => []
                    ];
                }
                $status = 1;
                $status_desc = '';
                if( $itemInfo['sales_status'] == 0 || $itemInfo['goods_verify'] != 1 || $itemInfo['goods_sales'] == 0 ){
                    $status = 0;
                    $status_desc = '商品已下架';
                }
                if( $itemInfo['is_invalid'] == 1 || $itemInfo['is_deleted'] == 1 || $itemInfo['goods_deleted'] == 1 ){
                    $status = 0;
                    $status_desc = '商品已失效';
                }
                //检查是否有赠品活动
                $has_gift = 0;
                if( $val['gift_id'] == 0 && $giftModel->getGoodsGiftList($val['shop_id'],$val['goods_id'],$val['id']) ){
                    $has_gift = 1;
                }
                //这里处理主动营销活动部分
                // 1-4 不能重复 5 折上折
                // 1.秒杀活动
                // 2.套餐活动
                // 3.拼团活动
                // 4.满减活动
                // 5.会员等级折扣

                //商品小计部分 直接配置活动折后价
                $sub_price = (($val['item_price'] < $itemInfo['item_price']) ? bcsub($itemInfo['item_price'],$val['item_price'],2) : 0);
                $sum_price = bcmul($val['num'],$itemInfo['item_price'],2);
                $shop[$val['shop_id']]['goods_list'][] = [
                    'cart_id' => $val['id'],
                    'item_info' => $itemInfo,
                    'is_selected' => $val['is_selected'],
                    'item_status' => $status,
                    'gift_id' => 0,
                    'has_gift' => $has_gift,
                    'item_status_desc' => $status_desc,
                    'activity_content' => '',
                    'num' => $val['num'],
                    'coupon_price' => 0.00,
                    'point_price' => 0.00,
                    'discount_price' => 0.00,
                    'sub_price' => $sub_price,
                    'sum_price' => $sum_price
                ];
                //这里是赠品处理
                if( !empty($val['gift_list']) ){
                    foreach( $val['gift_list'] as $gift ){
                        $itemInfo = $goodsItemModel->getItemInfo($gift['goods_item_id']);
                        $status = 1;
                        $status_desc = '';
                        if( $itemInfo['sales_status'] == 0 || $itemInfo['goods_verify'] != 1 || $itemInfo['goods_sales'] == 0 ){
                            $status = 0;
                            $status_desc = '商品已下架';
                        }
                        if( $itemInfo['is_invalid'] == 1 || $itemInfo['is_deleted'] == 1 || $itemInfo['goods_deleted'] == 1 ){
                            $status = 0;
                            $status_desc = '商品已失效';
                        }
                        if( $this->checkCartGift($gift['goods_id'],$gift['gift_parent_id'],$gift['gift_id']) === false ){
                            //既然已经不符合规则了 它已经没有存在购物车的必要了
                            //上吧比卡丘 删了它
                            if( !$this->where('id',$gift['id'])->delete() ){
                                $this->error = '购物车信息更新错误';
                                return false;
                            }
                        }else{
                            $itemInfo['item_price'] = 0;
                            $shop[$val['shop_id']]['goods_list'][] = [
                                'cart_id' => $gift['id'],
                                'item_info' => $itemInfo,
                                'is_selected' => $val['is_selected'],//这里的选中由父级购物商品决定
                                'item_status' => $status,
                                'has_gift' => 0,
                                'gift_id' => $gift['gift_id'],
                                'item_status_desc' => $status_desc,
                                'activity_content' => "符合{$gift['gift_id']}号赠品活动",
                                'num' => $gift['num'],
                                'coupon_price' => 0.00,
                                'point_price' => 0.00,
                                'discount_price' => 0.00,
                                'sub_price' => 0.00,
                                'sum_price' => 0.00
                            ];
                        }
                    }
                }
                $shop[$val['shop_id']]['goods_price'] = bcadd($shop[$val['shop_id']]['goods_price'],$sum_price,2);
            }
        }
        if( !empty($shop) ){
            $list = [];
            foreach( $shop as $v ){
                $list[] = $v;
            }
        }
        return $list;
    }

    /**
     * 过滤异常购物车商品
     * @param array $cart_list      购物车列表
     * @return array
     */
    public function cartValid($cart_list=array()){
        if(empty($cart_list)){
            return [];
        }
        $list = [];
        foreach( $cart_list as $key => $shop ){
            //不要问我为什么不用 unset 我是不会告诉你的！！！！
            if( $shop['shop_status'] == 1 ){
                $goods_list = [];
                foreach( $shop['goods_list'] as $gkey => $goods ) {
                    if ($goods['item_status'] == 1) {
                        $goods_list[] = $goods;
                    }
                }
                if( !empty($goods_list) ){
                    $shop['goods_list'] = $goods_list;
                    $list[] = $shop;
                }
            }
        }
        return $list;
    }

    //添加购物车
    public function addCart($user_id,$item_id,$num,$shop_id,$channel=Order::CHANNEL_PC,$is_selected=false,$giftData=[]){
//check channel 预留用于活动使用
        if( in_array($channel,Order::$map_channel) ){
//            return [];
        }
        if( empty($user_id) || empty($item_id) || empty($shop_id) ){
            $this->code = 000010;
            $this->error = '信息错误';
            return false;
        }
        //检查用户是否存在
        $server = new Service();
        //检查用户信息
        $user_info = $server->setHost('center_data')->post('user/user/one',['id'=>$user_id]);
        if( empty($user_info) ){
            $this->code = 000011;
            $this->error = '用户不存在';
            return false;
        }
        //获取商品信息
        $itemModel = new \app\goods\model\GoodsItem();
        $itemInfo = $itemModel->getItemInfo($item_id);
        if( empty($itemInfo) ){
            $this->code = 000012;
            $this->error = '商品已下架';
            return false;
        }
        if( $itemInfo['sales_status'] == 0 || $itemInfo['is_invalid'] == 1 || $itemInfo['is_deleted'] == 1 || $itemInfo['goods_verify'] != 1 || $itemInfo['goods_sales'] == 0 || $itemInfo['goods_deleted'] == 1 ){
            $this->code = 000013;
            $this->error = '商品已下架';
            return false;
        }
        //检查购物车
        $where['user_id'] = $user_id;
        $where['goods_item_id'] = $item_id;
        $where['shop_id'] = $shop_id;
        if( !empty($giftData) ){
            if( $this->checkCartGift($itemInfo['goods_id'],$giftData['gift_parent_id'],$giftData['gift_id']) === false ){
                return false;
            }
            $where['gift_parent_id'] = $giftData['gift_parent_id'];
            $where['gift_id'] = $giftData['gift_id'];
        }
        $cartItemInfo = $this->where($where)->find();
        if( !empty($cartItemInfo) && !$is_selected ){
            $num = bcadd($num,$cartItemInfo['num'],0);
        }
        //检查商品sku库存
        $stockModel = new \app\depot\model\Stock();
        $erp_info = explode(',',$itemInfo['erp_code']);
        if( !$stockModel->checkStockOver($erp_info[0],$erp_info[1],$num,$shop_id) ){
            $this->code = 000014;
            $this->error = '库存不足';
            return false;
        }
        if( !empty($cartItemInfo) ){
            if( !$this->where('id',$cartItemInfo['id'])->update(['num'=>$num]) ){
                $this->code = 000015;
                $this->error = '网络错误';
                return false;
            }
        }else{
            $insertData = $where;
            $insertData['goods_id'] = $itemInfo['goods_id'];
            $insertData['item_price'] = $itemInfo['item_price'];
            $insertData['num'] = $num;
            $insertData['create_time'] = date('Y-m-d H:i:s');
            $insertData['update_time'] = $insertData['create_time'];
            if($is_selected){
                $insertData['is_selected'] = 1;
            }
            if( !$this->insert($insertData) ){
                $this->code = 000015;
                $this->error = '网络错误';
                return false;
            }
        }
        return true;
    }

    public function checkCartGift($goods_id,$gift_parent_id,$gift_id){
        //检查是否活动的商品
        $itemModel = new \app\goods\model\GoodsItem();
        $giftModel = new \app\activity\model\Gift();
        $t = date('Y-m-d H:i:s');
        $gift_where = "id = {$gift_id} and status = 1 and '{$t}' between start_time and end_time";
        $gift_activity_info = $giftModel->where($gift_where)->find();
        if( empty($gift_activity_info) ){
            $this->code = 000014;
            $this->error = '赠品活动场次不存在';
            return false;
        }
        if( $gift_activity_info['goods_id'] != $goods_id ){
            $this->code = 000014;
            $this->error = '赠品信息不符合活动场次';
            return false;
        }
        //检查父级信息是否匹配活动
        //获取父级的关联购物车信息
        if( $gift_activity_info['use_goods_type'] > 0 ){
            $parent_info = $this->field('id,goods_id,goods_item_id,num,shop_id')
                ->where('id',$gift_parent_id)
                ->find();
            if( empty($parent_info) ){
                $this->code = 000014;
                $this->error = '购物车信息获取失败';
                return false;
            }
            $parent_info['item_info'] = $itemModel->getItemInfo($parent_info['goods_item_id']);
            $giftGoods = new \app\activity\model\GiftGoods();
            $checkGoods = $giftGoods->where(['goods_id'=>$parent_info['goods_id'],'gift_id'=>$gift_id])->find();
            if( $gift_activity_info['use_goods_type'] == 1 && empty($checkGoods) ){//指定
                $this->code = 000014;
                $this->error = '下单商品不符合赠送规则';
                return false;
            }else if( $gift_activity_info['use_goods_type'] == 2 && !empty($checkGoods) ){//排除
                $this->code = 000015;
                $this->error = '下单商品不符合赠送规则';
                return false;
            }
        }
        return true;
    }

    /**
     * 更新购物车
     * @param $user_id      用户id
     * @param $item_id      商品sku id
     * @param $num          商品数量 0 为删除已加入购物车的商品
     * @param $shop_id      店铺id
     * @param int $channel  加入渠道
     */
    public function updateCart($cart_id,$item_id,$num,$shop_id,$channel=Order::CHANNEL_PC,$is_selected=false){
        //check channel 预留用于活动使用
        if( in_array($channel,Order::$map_channel) ){
//            return [];
        }
        //获取商品信息
        $itemModel = new \app\goods\model\GoodsItem();
        $itemInfo = $itemModel->getItemInfo($item_id);
        if( empty($itemInfo) ){
            $this->code = 000012;
            $this->error = '商品已下架';
            return false;
        }
        if( $itemInfo['sales_status'] == 0 || $itemInfo['is_invalid'] == 1 || $itemInfo['is_deleted'] == 1 ){
            $this->code = 000013;
            $this->error = '商品已下架';
            return false;
        }
        //检查商品sku库存
        $stockModel = new \app\depot\model\Stock();
        $erp_info = explode(',',$itemInfo['erp_code']);
        if( !$stockModel->checkStockOver($erp_info[0],$erp_info[1],$num,$shop_id) ){
            $this->code = 000014;
            $this->error = '库存不足';
            return false;
        }
        //更新数量
        $update['num'] = $num;
        $update['update_time'] = date('Y-m-d H:i:s');
        if($is_selected){
            $update['is_selected'] = 1;
        }
        $where['id'] = $cart_id;
        if( !$this->where($where)->update($update) ){
            $this->code = 000015;
            $this->error = '网络错误,操作失败';
            return false;
        }
        return true;
    }

}
