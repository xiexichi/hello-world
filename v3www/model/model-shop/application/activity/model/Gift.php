<?php

namespace app\activity\model;

use app\common\controller\Service;

class Gift extends Base
{
    protected $name = 'gift';

    const TYPE_MONEY = 1;//满额送
    const TYPE_NUM = 2;//满件送

    public static $map_type = array(
        self::TYPE_MONEY => array(
            'desc' => '满额'
        ),
        self::TYPE_NUM => array(
            'desc' => '满件'
        )
    );

    public function indexBefore(){
        $goodsModel = new \app\goods\model\Goods();
        $this->field('gi.*,g.erp_code')
            ->alias('gi')
            ->join($goodsModel->getTable().' g','g.id = gi.goods_id')
            ->order(['gi.status'=>'desc','gi.end_time'=>'desc','gi.start_time'=>'asc']);
    }

    public function indexAfter(){
        if( !empty($this->data) ){
            foreach( $this->data as $k => $v ){
                $v['type_desc'] = self::$map_type[$v['type']]['desc'];
                $v['status_desc'] = $v['status'] ? '启动中' : '未启动';
                $this->data[$k] = $v;
            }
        }
    }

    public function editBefore(){
        if( isset($this->data['status']) && $this->data['status'] == 1 ){
            $gift_info = $this->where('id',$this->data['id'])->find();
            $check_where = "goods_id = {$gift_info['goods_id']} and status = 1 and id <> {$this->data['id']}";
            $check_where .= " and ( start_time between '{$gift_info['start_time']}' and '{$gift_info['end_time']}' or end_time between '{$gift_info['start_time']}' and '{$gift_info['end_time']}' )";
            //检查赠品相同时段是否有重复设置
            if( $this->where($check_where)->find() ){
                $this->isExit = true;
                $this->code = 040210;
                $this->error = '赠送商品相同时间段不能开启多个活动';
            }
        }
        $this->data['update_time'] = date('Y-m-d H:i:s');
    }

    public function shopList(){
        return $this->hasMany('GiftShop','gift_id','id');
    }

    public function goodsList(){
        $goodsModel = new \app\goods\model\Goods();
        $categoryModel = new \app\goods\model\Category();
        $goodsBrandsModel = new \app\goods\model\GoodsBrands();
        $where['g.is_deleted'] = ['=',0];
        return $this->hasMany('GiftGoods','gift_id','id')
            ->alias('gg')
            ->field('gg.gift_id,g.*,c.cate_name,b.brand_name,img.image')
            ->join($goodsModel->getTable().' g','g.id = gg.goods_id')
            ->join($categoryModel->getTable().' c','c.id = g.cate_id')
            ->join($goodsBrandsModel->getTable().' b','b.id = g.brand_id','LEFT')
            ->join('goods_images img','img.goods_id = g.id')
            ->where($where)
            ->order(['g.sort'=>'desc','g.create_time'=>'desc']);
    }

    public function oneBefore(){
        $couponUserModel = new \app\activity\model\CouponUser();
        $subQuery = $couponUserModel->field('count(id)')->where('coupon_id',$this->data['id'])->buildSql();
        $this->with(['goodsList','shopList'])->field(['*',$subQuery.' as sum']);
    }

    public function oneAfter(){
        $this->data = $this->data->toArray();
        if( !empty($this->data['goods_list']) ){
            $goodsModel = new \app\goods\model\Goods();
            foreach( $this->data['goods_list'] as $k => $goods ){
                $goods['status_desc'] = $goodsModel->goodsStatusDesc($goods);
                $this->data['goods_list'][$k] = $goods;
            }
        }
        $shop_arr = [];
        $shop_list = $this->data['shop_list'];
        if( !empty($this->data['shop_list']) ){
            foreach( $this->data['shop_list'] as $shop ){
                $shop_arr[] = $shop['shop_id'];
            }
            if( $this->data['use_shop_type'] > 0 ){
                $server = new Service();
                $param['where']['id'] = ['in',implode(',',$shop_arr)];
                $shop_list = $server->setHost('center_data')->post('merchant/shop/all',$param);
            }
        }else{
            $shop_list = $shop_arr;
        }
        $this->data['start_time'] = $this->data['start_time'] == 0 ? '' : $this->data['start_time'];
        $this->data['end_time'] = $this->data['end_time'] == 0 ? '' : $this->data['end_time'];
        $this->data['shop_list'] = $shop_list;
    }

    public function GoodsInfo(){
        return $this->hasOne('app\goods\model\Goods','id','goods_id');
    }

    public function GoodsAll(){
        return $this->hasMany('GiftGoods','gift_id','id');
    }

    public function ShopAll(){
        return $this->hasMany('GiftShop','gift_id','id');
    }

    /**
     * 获取下单商品的赠品可选列表
     * @param int   $shop_id          店铺id
     * @param int   $goods_id     商品id
     * @param int   $cart_id      购物车id 若非0 店铺id会被替换成购物车记录的shop_id
     * @param bool  $isCheck      用于是否检查购物车商品是否有赠品
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsGiftList($shop_id,$goods_id=0,$cart_id=0){
        if( empty($shop_id) ){
            return [];
        }
        //这里获取获取时间段内所有活动
        $t = date('Y-m-d H:i:s');
        $where = " status = 1 and '{$t}' between start_time and end_time";
        $gift_list = $this->with(['GoodsInfo','GoodsAll','ShopAll'])->where($where)
            ->select();
        if( empty($gift_list) ){
            return [];
        }
        //获取店铺信息 和购物车信息
        $cart_info = [];
        if( $cart_id > 0 ){
            $cartModel = new \app\cart\model\Cart();
            $cart_info = $cartModel->where('id',$cart_id)->find();
            if( empty($cart_info) ){
                return [];
            }
            $shop_id = $cart_info['shop_id'];
        }
        $orderModel = new \app\order\model\Order();
        $server = new Service();
        $shop_info = $server->setHost('center_data')->post('merchant/shop/one',['id'=>$shop_id]);
        //若是购物车获取 判断商品下单类型
        $list = [];
        foreach( $gift_list as $key => $val ){
            $goods_all = [];
            if( !empty($val['goods_all']) ){
                foreach( $val['goods_all'] as $goods ){
                    $goods_all[] = $goods['goods_id'];
                }
            }
            $shop_all = [];
            if( !empty($val['shop_all']) ){
                foreach( $val['shop_all'] as $shop ){
                    $shop_all[] = $shop['shop_id'];
                }
            }
            $check = true;
            if( $val['use_goods_type'] > 0 ){
                if( in_array($goods_id,$goods_all) ){
                    switch($val['use_goods_type'] ){
                        case 1 :
                            $check  = true;
                            break;
                        case 2 :
                            $check  = false;
                            break;
                    }
                }else{
                    $check  = false;
                }
            }
            if( $val['use_shop_type'] > 0 ){
                if( in_array($shop_id,$shop_all) ){
                    switch($val['use_shop_type'] ){
                        case 1 :
                            $check  = true;
                            break;
                        case 2 :
                            $check  = false;
                            break;
                    }
                }else{
                    $check  = false;
                }
            }
            //判断订单应用范围
            //拼团
            if( $val['order_group'] == 0 && !empty($cart_info) && $cart_info['is_group'] == 1 ){
                $check = false;
            }
            //门店单
            if( $val['order_o2o'] == 0 && $shop_info['shop_sale_type_id'] == $orderModel::SHOP_TYPE_OFFLINE ){
                $check = false;
            }
            //普通单
            if( $val['order_online'] == 0 && !empty($cart_info) && $cart_info['is_group'] == 0 && $shop_info['shop_sale_type_id'] == $orderModel::SHOP_TYPE_ONLINE ){
                $check = false;
            }
            //条件判断
            if( !empty($cart_info) ){
                switch($val['type']){//暂时就在只有这两种
                    case 1 ://满额
                         if( bcmul($cart_info['item_price'],$cart_info['num'],2) < $val['condition'] ){
                             $check = false;
                         }
                        break;
                    case 2 ://满件
                        if( $cart_info['num'] < $val['condition'] ){
                            $check = false;
                        }
                        break;
                }
            }
            if($check){
                unset($val['goods_all']);
                unset($val['shop_all']);
                $list[] = $val;
            }
        }
        return $list;
    }

}
