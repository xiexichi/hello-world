<?php

namespace app\activity\model;
use app\activity\model\Coupon;
use app\common\controller\Service;

class CouponUser extends Base
{
    protected $name = 'coupon_user';

    protected $order_coupon_list = [];//这类下单使用的 订单可用coupon列表

    public function indexBefore(){
        $couponModel = new Coupon();
        $this->alias('cu')
            ->field('c.*,cu.id as user_coupon_id,cu.use_status,cu.use_time,cu.coupon_sn,cu.is_invalid as user_invalid,cu.user_id')
            ->join($couponModel->getTable().' c','c.id = cu.coupon_id');
    }

    public function indexAfter(){
        if(!empty($this->data)){
            $server = new Service();
            $user_list = [];
            foreach( $this->data as $key => $coupon  ){
                if( empty($user_list[$coupon['user_id']]) ){
                    //不要问我为什么这么慢 怪他 没了它提速 60%↓
                    $user_list[$coupon['user_id']] = $server->setHost('center_data')->post('user/user/one',['id'=>$coupon['user_id']]);
                }
                $coupon['user_name'] = $user_list[$coupon['user_id']]['user_name'];
                $this->data[$key] = $this->couponStatus($coupon);
            }
        }
    }

    public function getOrderCouponAll(){
        return $this->order_coupon_list;
    }

    public function goodsList(){
        return $this->hasMany('CouponGoods','coupon_id','id');
    }
    public function shopList(){
        return $this->hasMany('CouponShop','coupon_id','id');
    }

    public function couponStatus($couponInfo){
        $couponInfo['coupon_desc'] = '';
        if( $couponInfo['limit_price'] > 0 ){
            $couponInfo['coupon_desc'] .= '满 '.$couponInfo['limit_price'].' 元';
            $couponInfo['coupon_desc'] .= ' 享 '.$couponInfo['discount_value'].Coupon::$map_type[$couponInfo['type']]['do'];
        }else{
            $couponInfo['coupon_desc'] = $couponInfo['discount_value'].' '.Coupon::$map_type[$couponInfo['type']]['unit'].Coupon::$map_type[$couponInfo['type']]['desc'];
        }
        $status = 1;
        if( $couponInfo['use_status'] == 0 ){
            if( $couponInfo['user_invalid'] == 1 || $couponInfo['is_invalid'] == 1 || $couponInfo['is_deleted'] == 1 || $couponInfo['use_status'] == 1 ){
                $status = 0;
            }
            $t = time();
            $start_time = $couponInfo['start_time'] == 0 ? 0 : strtotime($couponInfo['start_time']);
            $end_time = $couponInfo['end_time'] == 0 ? 0 : strtotime($couponInfo['end_time']);
            //时间
            if( $start_time > 0 && $end_time == 0 && $start_time > $t ){
                $status = 0;
            }else if( $start_time == 0 && $end_time > 0 && $end_time < $t ){
                $status = 0;
            }else if( $start_time > 0 && $end_time > 0 && ( $start_time > $t || $end_time < $t ) ){
                $status = 0;
            }
        }else{
            $status = 0;
        }
        $couponInfo['status_desc'] = '可用';
        if( $status == 0 ){
            $couponInfo['status_desc'] = '已失效';
        }
        $couponInfo['is_can'] = $status;
        return $couponInfo;
    }

    /**
     * 获取会员优惠券
     * @param $user_id      用户id
     * @param $shop_id      门店筛选条件
     * @param $goods_arr    商品筛选条件
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserList($user_id,$shop_id=0,$goods_arr=[]){
        if( empty($user_id) ){
            return [];
        }
        $where['cu.user_id'] = ['=',intval($user_id)];
        $couponModel = new Coupon();
        $this->alias('cu')
            ->field('
            c.id,
            c.title,
            c.description,
            c.type,
            c.max_qty,
            c.use_max_qty,
            c.use_shop_type,
            c.use_goods_type,
            c.limit_price,
            c.discount_value,
            c.start_time,
            c.end_time,
            c.status,
            c.is_invalid,
            c.is_deleted,
            cu.id as user_coupon_id,
            cu.use_status,
            cu.coupon_sn,
            cu.is_invalid as user_invalid
            ')
            ->join($couponModel->getTable().' c','c.id = cu.coupon_id');
        //配置关联
        $with = [];
        if( !empty($shop_id) ) {
            $with['shop_list'] = 'shopList';
        }
        if( !empty($goods_arr) ){
            $with['goods_list'] = 'goodsList';
        }
        $coupon_all = $this->with($with)
            ->where($where)
            ->order('c.type','asc')
            ->select();
        $coupon_list = [];
        if( !empty($coupon_all) ){
            foreach( $coupon_all as $coupon ){
                $coupon = $this->couponStatus($coupon);
                //过滤筛选
                if( ( !empty($shop_id) || !empty($goods_arr) )){
                    //店过滤
                    if( $coupon['is_can'] == 1 ){
                        $is_shop_can = true;
                        if( !empty($coupon['shop_list']) && $coupon['use_shop_type'] != Coupon::USE_SHOP_ALL ){
                            $is_shop_can = false;
                            foreach( $coupon['shop_list'] as $shop ){
                                if( $shop['shop_id'] == $shop_id ){
                                    switch( $coupon['use_shop_type'] ){
                                        case Coupon::USE_SHOP_PART ://指定店
                                            $is_shop_can = true;
                                            break;
                                        case Coupon::USE_SHOP_EX ://排除店
                                            $is_shop_can = false;
                                            break;
                                        default :
                                            $is_shop_can = true;
                                            break;
                                    }
                                }
                            }
                        }
                        unset($coupon['shop_list']);
                        //商品过滤
                        $is_goods_can = true;
                        if( !empty($coupon['goods_list']) && $coupon['use_goods_type'] != Coupon::USE_GOODS_ALL ){
                            $is_goods_can = false;
                            foreach( $coupon['goods_list'] as $goods ){
                                if( in_array($goods['goods_id'],$goods_arr) ){
                                    switch( $coupon['use_goods_type'] ){
                                        case Coupon::USE_GOODS_PART ://指定商品
                                            $is_goods_can = true;
                                            break;
                                        case Coupon::USE_GOODS_EX ://排除商品
                                            $is_goods_can = false;
                                            break;
                                        default :
                                            $is_goods_can = true;
                                            break;
                                     }
                                }
                            }
                        }
                        unset($coupon['goods_list']);
                        if( $is_shop_can && $is_goods_can ){
                            $coupon_list[] = $coupon;
                            if( !isset($this->order_coupon_list[$coupon['user_coupon_id']]) ){
                                $this->order_coupon_list[$coupon['user_coupon_id']] = $coupon;
                            }
                        }else{
                            $coupon['is_can'] = 0;
                        }
                    }
                }else{
                    $coupon_list[] = $coupon;
                }
            }
        }else{
            $coupon_list = $coupon_all;
        }
        return $coupon_list;
    }

    /**
     * 匹配购物车用户对应商店可用卷
     * 这里不判断优惠券使用要求 因优先级问题需要在其他营销优惠处理后再判断使用要求
     */
    public function getCartCoupon($user_id,$cart_list){
        if( empty($user_id) || empty($cart_list) ){
            return [];
        }
        foreach( $cart_list as $skey => $shop ){
            $goods_list = [];
            foreach( $shop['goods_list'] as $gkey => $goods ){
                if( !in_array($goods['item_info']['goods_id'],$goods_list)){
                    $goods_list[] = $goods['item_info']['goods_id'];
                }
            }
            $shop['coupon_list'] = $this->getUserList($user_id,$shop['shop_id'],$goods_list);
            $cart_list[$skey] = $shop;
        }
        return $cart_list;
    }

}
