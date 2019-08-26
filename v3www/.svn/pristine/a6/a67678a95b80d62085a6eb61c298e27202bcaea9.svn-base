<?php

namespace app\activity\model;

use app\common\controller\Service;
use think\Exception;

class Coupon extends Base
{
    protected $name = 'coupon';

    const TYPE_MONEY = 0;//抵扣券 代金券
    const TYPE_PERCEN = 1;//折扣券

    public static $map_type = array(
        self::TYPE_MONEY => array(
            'do' => '元抵扣',
            'desc' => '代金券',
            'code' => 'D',
            'unit' => '元'
        ),
        self::TYPE_PERCEN => array(
            'do' => '折',
            'desc' => '折扣券',
            'code' => 'Z',
            'unit' => '折'
        )
    );

    const USE_SHOP_ALL = 0;
    const USE_SHOP_PART = 1;
    const USE_SHOP_EX = 2;

    const USE_GOODS_ALL = 0;
    const USE_GOODS_PART = 1;
    const USE_GOODS_EX = 2;

    public function indexAfter(){
        if( !empty($this->data) ){
            foreach( $this->data as $k => $v ){
                $v['type_desc'] = self::$map_type[$v['type']]['desc'];
                $v['goods_area'] = $v['use_goods_type'] > 0 ? '部分商品' : '所有商品';
                $v['shop_area'] = $v['use_shop_type'] > 0 ? '部分店' : '所有店';
                $v['status_desc'] = $v['status'] ? '发放中' : '不可发放';
                if( $v['is_invalid'] == 1 ){
                    $v['status_desc'] = '已失效';
                }
                if( $v['is_deleted'] == 1 ){
                    $v['status_desc'] = '已删除';
                }
                $this->data[$k] = $v;
            }
        }
    }

    public function editBefore(){
        if (isset($this->data['qty'])) {
            $coupon_info = $this->where('id', $this->data['id'])->find();
            $coupon_info = $coupon_info->toArray();
            if( empty($coupon_info) ){
                $this->isExit = true;
                $this->code = 040110;
                $this->error = '优惠券信息获取失败';
                return false;
            }
            if( $this->data['qty'] != $coupon_info['qty'] ){
                //记录日志
                $qtyLogModel = new \app\activity\model\CouponQtyLog();
                if( !$qtyLogModel->markLog($this->data['qty'],$coupon_info) ){
                    $this->isExit = true;
                    $this->code = 040111;
                    $this->error = '操作记录失败';
                    return false;
                }
            }
        }
        $this->data['update_time'] = date('Y-m-d H:i:s');
    }

    public function shopList(){
        return $this->hasMany('CouponShop','coupon_id','id');
    }

    public function goodsList(){
        $goodsModel = new \app\goods\model\Goods();
        $categoryModel = new \app\goods\model\Category();
        $goodsBrandsModel = new \app\goods\model\GoodsBrands();
        $where['g.is_deleted'] = ['=',0];
        return $this->hasMany('CouponGoods','coupon_id','id')
            ->alias('cg')
            ->field('cg.coupon_id,g.*,c.cate_name,b.brand_name,img.image')
            ->join($goodsModel->getTable().' g','g.id = cg.goods_id')
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
        $this->data['get_start_time'] = $this->data['get_start_time'] == 0 ? '' : $this->data['get_start_time'];
        $this->data['get_end_time'] = $this->data['get_end_time'] == 0 ? '' : $this->data['get_end_time'];
        $this->data['start_time'] = $this->data['start_time'] == 0 ? '' : $this->data['start_time'];
        $this->data['end_time'] = $this->data['end_time'] == 0 ? '' : $this->data['end_time'];
        $this->data['shop_list'] = $shop_list;
    }

    public function sand($user_id,$coupon_id,$num=1){
        if( empty($coupon_id) || empty($user_id) || empty($num) ){
            return false;
        }
        $t = date('Y-m-d H:i:s');
        $where = "id = {$coupon_id} AND (";
        $where .= " ( get_start_time <= '{$t}' AND get_end_time = 0 ) OR ";
        $where .= " ( get_start_time = 0 AND get_end_time >= '{$t}' ) OR ";
        $where .= " ( get_start_time <> 0 AND get_end_time <> 0 AND get_start_time <= '{$t}' AND get_end_time >= '{$t}' ) ";
        $where .= ")";
        $coupon_info = $this->where($where)->find();
        if( empty($coupon_info) || $coupon_info['status'] == 0 ){
            $this->error = '优惠券不可发放';
            return false;
        }
        if( $coupon_info['is_invalid'] || $coupon_info['is_deleted'] ){
            $this->error = '优惠券已失效不能发放';
            return false;
        }
        if( $coupon_info['qty'] == 0 || $coupon_info['qty'] < $num ){
            $this->error = '已领光';
            return false;
        }
        //限制用户领取数
        $couponUserModel = new \app\activity\model\CouponUser();
        if( $coupon_info['max_qty'] != 0){
            $count = $couponUserModel->where(['user_id'=>$user_id,'coupon_id'=>$coupon_id])->count('id');
            if( $count >= $coupon_info['max_qty']){
                $this->error = '此券最多领取'.$coupon_info['max_qty'].'张';
                return false;
            }
        }
        $rand = md5($coupon_id.time() . $user_id);
        //领取更新数据
        $this->startTrans();
        try{
            $insertData = [];
            $insertData['user_id'] = $user_id;
            $insertData['coupon_id'] = $coupon_id;
            $insertData['coupon_sn'] = self::$map_type[$coupon_info['type']]['code'].strtoupper(substr($rand,rand(0,mb_strlen($rand)-8),8));
            $insertData['create_time'] = date('Y-m-d H:i:s');
            if($num == 1){
                if( !$couponUserModel->insert($insertData) ){
                    throw new Exception('网络错误，领取失败');
                }
            }else if($num > 1){
                $data = [];
                for( $x = 0; $x < $num; $x++ ){
                    $data[] = $insertData;
                }
                if( !$couponUserModel->insertAll($insertData) ){
                    throw new Exception('网络错误，领取失败');
                }
            }
            //更新优惠券可发数量 (不更新优惠券时间)
            $update['qty'] = bcsub($coupon_info['qty'],$num,0);
            if( !$this->where($where)->update($update) ){
                throw new Exception('优惠券信息更新错误');
            }
            $this->commit();
        }catch( Exception $e ){
            $this->rollback();
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

    //检查是否可用
    public function checkCouponCanUse($coupon_id,$shop_id,$goods_id){
        if( empty($coupon_id) || empty($shop_id) || empty($goods_id) ){
            return false;
        }
        $t = date('Y-m-d H:i:s');
        $where = "id = {$coupon_id} AND (";
        $where .= " ( start_time <= '{$t}' AND end_time = 0 ) OR ";
        $where .= " ( start_time = 0 AND end_time >= '{$t}' ) OR ";
        $where .= " ( start_time <> 0 AND end_time <> 0 AND start_time <= '{$t}' AND end_time >= '{$t}' ) ";
        $where .= ")";
        $coupon_info = $this->where($where)->find();
        if( empty($coupon_info) ){
            $this->error = '优惠券不可使用';
            return false;
        }
        //检查店
        $where = [];
        $couponShopModel = new \app\activity\model\CouponShop();
        $where['coupon_id'] = $coupon_id;
        $where['shop_id'] = $shop_id;
        $checkInfo = $couponShopModel->where($where)->find();
        switch( $coupon_id['use_shop_type'] ){
            case self::USE_SHOP_PART :
                if( empty($checkInfo) ){
                    $this->error = '店铺不在优惠券使用范围';
                    return false;
                }
                break;
            case self::USE_SHOP_EX :
                if( !empty($checkInfo) ){
                    $this->error = '店铺不在优惠券使用范围';
                    return false;
                }
                break;
        }
        $where = [];
        $couponGoodsModel = new \app\activity\model\CouponGoods();
        $where['coupon_id'] = $coupon_id;
        $where['goods_id'] = $goods_id;
        $checkInfo = $couponGoodsModel->where($where)->find();
        switch( $coupon_id['use_goods_type'] ){
            case self::USE_GOODS_PART :
                if( empty($checkInfo) ){
                    $this->error = '商品不在优惠券使用范围';
                    return false;
                }
                break;
            case self::USE_GOODS_EX :
                if( !empty($checkInfo) ){
                    $this->error = '商品不在优惠券使用范围';
                    return false;
                }
                break;
        }
        if( $coupon_id['use_goods_type'] != self::USE_GOODS_ALL ){
            $where['goods_id'] = $goods_id;
            if( !$checkInfo = $couponGoodsModel->where($where)->find() ){
                $this->error = '商品不在优惠券使用范围';
                return false;
            }
        }
        return true;
    }
}
