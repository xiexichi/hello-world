<?php
namespace app\order\controller;

use app\common\controller\Service;
use think\Exception;

class Order extends Base{

    public function indexBefore()
    {
        $param = $this->validate->index(input());
        if( $param === false ){
            $this->isExit = true;
            $this->code = 020101;
            $this->error = $this->validate->getError();
        }
        $where = [];
        //审核状态筛选
        if( isset($param['verify']) && $param['verify'] != '' ){
            $where['verify'] = ['=',$param['verify']];
        }
        //支付状态
        if( isset($param['pay_status']) && $param['pay_status'] != '' ){
            $where['pay_status'] = ['=',$param['pay_status']];
        }
        //商家id
        if( isset($param['shop_id']) && !empty($param['shop_id']) ){
            $where['shop_id'] = ['=',$param['shop_id']];
        }
        //订单号
        if( isset($param['order_sn']) && !empty($param['order_sn']) ){
            $where['order_sn'] = ['=',$param['order_sn']];
        }
        //订单类型
        if( isset($param['order_type']) ) {
            if (!empty($this->model::$map_order_type[$param['order_type']])) {
                $where['order_type'] = ['=', $param['order_type']];
            }
        }
        //下单渠道
        if( isset($param['channel_id']) ) {
            if (!empty($this->model::$map_channel[$param['channel_id']])) {
                $where['add_channel'] = ['=', $param['channel_id']];
            }
        }
        //时间筛选
        if( !empty($param['start_time']) && empty($param['end_time']) ) {
            $where['create_time'] = ['>=', $param['start_time']];
        }else if( empty($param['start_time']) && !empty($param['end_time']) ){
            $where['create_time'] = ['<=', $param['end_time']];
        }else if( !empty($param['start_time']) && !empty($param['end_time']) ){
            $where['create_time'] = ['between', [$param['start_time'],$param['end_time']]];
        }
        //会员id
        if( isset($param['user_id']) && !empty($param['user_id']) ){
            //待付款 //待发货 //待自提 // 待收货 //待评价
            if( isset($param['getType']) && !empty($param['getType']) ){
                $where = [];
                switch($param['getType']){
                    case 'paying' ://待支付
                        $where['pay_status'] = ['=',0];
                        break;
                    case 'ship' ://待发货console.log(res);
                        $where['pay_status'] = ['=',1];
                        $where['order_type'] = ['<>',1];
                        $where['verify'] = ['=',1];
                        break;
                    case 'self_lifting' ://待自提
                        $where['pay_status'] = ['=',1];
                        $where['order_type'] = ['=',1];
                        $where['verify'] = ['=',1];
                        break;
                    case 'storage' ://待收货
                        $where['pay_status'] = ['=',1];
                        $where['order_type'] = ['<>',1];
                        $where['verify'] = ['=',1];
                        break;
                    case 'eval' ://待评价
                        $where['finished_status'] = ['=',1];
                        $where['pay_status'] = ['=',1];
                        $where['verify'] = ['=',1];
                        $where['evaluation_status'] = ['=',1];
                        break;
                }
            }
            $where['user_id'] = ['=',$param['user_id']];
        }
        $this->model->where($where)->order(['create_time'=>'desc','pay_status'=>'asc']);
    }

    //获取订单类型
    public function getOrderType(){
        return successJson($this->model::$map_order_type,'success');
    }

    //获取订单渠道类型
    public function getOrderChannel(){
        return successJson($this->model::$map_channel,'success');
    }

    //获取订单信息
    public function getOrderInfo(){
        $code = 020101;
        dump(input('post'));die();
        if( !$param = $this->validate->getOrderInfo(input('post.')) ){
            return errorJson($code, $this->validate->getError());
        }
        $json = [];
        try{
            $order_id = $param['order_id'];
            $order_info = $this->model->where('order_id',$order_id)->find();
            if( empty($order_info) ){
                $code = 020110;
                throw new Exception('订单信息获取失败');
            }
            $order_info['orderStatusDesc'] = $this->model->orderStatusDesc($order_info);
            $order_info['orderTypeDesc'] = $this->model::$map_order_type[$order_info['order_type']]['desc'];
            $json['order_info'] = $order_info;
            //获取下单用户信息
            $server = new Service();
            $user_info = $server->setHost('center_data')->post('user/user/one',['id'=>$order_info['user_id']]);
            if( empty($user_info) ){
                $code = 020111;
                throw new Exception('用户信息获取失败');
            }
            $json['user_info'] = $user_info;
            //获取店铺信息
            $shop_info = $server->setHost('center_data')->post('merchant/shop/one',['id'=>$order_info['shop_id']]);
            if( empty($shop_info) ){
                $code = 020112;
                throw new Exception('店铺信息获取失败');
            }
            $json['shop_info'] = $shop_info;
            //获取收货地址信息
            $orderConsigneeModel = new \app\order\model\OrderConsignee();
            $consignee = $orderConsigneeModel->getOrderConsignee($order_info['order_id']);
            if( empty($consignee) ){
                $consignee = [];
            }
            $json['consignee'] = $consignee;
            //获取订单商品
            $orderGoodsModel = new \app\order\model\OrderGoods();
            $goods_list = $orderGoodsModel->getItemList($order_info['order_id']);
            //获取订单发货包裹
            $orderDeliveryPackageModel = new \app\order\model\OrderDeliveryPackage();
            $delivery_package = $orderDeliveryPackageModel->getOrderPackage($order_info['order_id']);
            $goods_package = [];
            if( !empty($delivery_package) ){
                foreach($delivery_package as $dkey => $package){
                    foreach( $package['package_goods'] as $goods ){
                        $goods_package[$goods['order_goods_id']] = $dkey+1;
                    }
                    $package['package_goods'] = [];
                    $delivery_package[$dkey] = $package;
                }
            }
            foreach( $goods_list as $gkey => $goods ){
                $goods['packageNum'] = 0;
                if( !empty($goods_package[$goods['id']]) ){
                    $goods['packageNum'] = $goods_package[$goods['id']];
                }
                $goods_list[$gkey] = $goods;
            }
            $json['goods_list'] = $goods_list;
            $json['order_package'] = $delivery_package;
            //获取日志记录表
            $orderLogModel = new \app\order\model\OrderLog();
            $log = $orderLogModel->where('order_id',$order_info['order_id'])
                ->order('create_time','desc')
                ->select();
            if( empty($log) ){
                $log = [];
            }
            $json['log'] = $log;
            $orderDiscountModel = new \app\order\model\OrderDiscount();
            $discount_list = $orderDiscountModel->where('order_id',$order_id)->select();
            $json['discount_list'] = $discount_list;
        }catch( Exception $e ){
            return errorJson($code, $e->getMessage());
        }
        return successJson($json);
    }

    /**
     * 获取下单商品列表
     */
    public function getBuyGoodsList(){
        if( !$param = $this->validate->getTotal(input('post.')) ) {
            return errorJson(0000201, $this->validate->getError());
        }
        $user_id = $param['user_id'];
        $address_id = empty($param['address_id']) ? 0 : $param['address_id'];//收货地址
        $shop_delivery = empty($param['shop_delivery']) ? [] : $param['shop_delivery'];//物流类型
        $shop_id = empty($param['shop_id']) ? 0 : $param['shop_id'];
        $channel = empty($param['channel']) ? $this->model::CHANNEL_PC : $param['channel'];
        $point = empty($param['point']) ? 0 : $param['point'];
        $coupon_list = empty($param['coupon_list']) ? [] : $param['coupon_list'];
        $disData = [];
        $disData['channel'] = $channel;
        $disData['shop_delivery'] = $shop_delivery;
        $disData['point'] = bcadd($point,0,2);
        $disData['coupon'] = $coupon_list;
        if( !$this->model->setInfoContent($disData) ){
            return errorJson(000010, $this->model->getError());
        }
        $shop_list = $this->model->getBuyGoodsList($user_id,$shop_id);
        if( $shop_list === false ){
            return errorJson(000010, $this->model->getError());
        }
        $server = new Service();
        //检查用户信息
        $user_info = $server->setHost('center_data')->post('user/user/one',['id'=>$user_id]);
        if( !empty($user_id) ) {
            //获取会员收货地址信息
            $address_info = $server->setHost('center_data')->post('user/address/one', ['id' => $address_id, 'user_id' => $user_info['id']]);
        }else{
            return errorJson(000011,'用户信息错误');
        }
        $shop_list = $this->model->setShopShip($address_info,$shop_list);
        return successJson($shop_list);
    }

    //购物车已选商品合计信息
    public function getTotal(){
        if( !$param = $this->validate->getTotal(input('post.')) ) {
            return errorJson(0000201, $this->validate->getError());
        }
        $user_id = $param['user_id'];
        $channel = empty($param['channel']) ? $this->model::CHANNEL_PC : $param['channel'];
        $shop_id = empty($param['shop_id']) ? 0 : $param['shop_id'];
        //获取统计信息
        $address_id = empty($param['address_id']) ? 0 : $param['address_id'];//收货地址
        $shop_delivery = empty($param['shop_delivery']) ? [] : $param['shop_delivery'];//物流类型
        $point = empty($param['point']) ? 100 : $param['point'];
        $coupon_list = empty($param['coupon_list']) ? [] : $param['coupon_list'];
        $disData = [];
        $disData['channel'] = $channel;
        $disData['shop_delivery'] = $shop_delivery;
        $disData['point'] = bcadd($point,0,2);
        $disData['coupon'] = $coupon_list;
        if( !$this->model->setInfoContent($disData) ){
            return errorJson(000010, $this->model->getError());
        }
        //检查用户信息
        $server = new Service();
        $user_info = $server->setHost('center_data')->post('user/user/one',['id'=>$user_id]);
        if( empty($user_info) ){
            return errorJson(000010, '会员信息获取失败');
        }
        $shop_list = $this->model->getBuyGoodsList($user_id,$shop_id);
        $order_total = $this->model->getTotal($user_id,$shop_list,$address_id);
        if( $order_total === false ){
            return errorJson(000010, $this->model->getError());
        }
        return successJson($order_total);
    }

    /**
     * 下单生成订单
     */
    public function createOrder(){
        if( !$param = $this->validate->createOrder(input('post.')) ) {
            return errorJson(0000201, $this->validate->getError());
        }
        //信息准备and处理
        $user_id = $param['user_id'];
        $address_id = $param['address_id'];
        $shop_delivery = empty($param['shop_delivery']) ? [] : $param['shop_delivery'];
        $shop_id = $param['shop_id'];
        //设置自提店铺用的参数
        $self_take_shop = empty($param['self_take_shop']) ? [] : $param['self_take_shop'];

        $channel = empty($param['channel']) ? $this->model::CHANNEL_PC : $param['channel'];
        $remark = empty($param['remark']) ? '' : $param['remark'];
        $coupon_list = empty($param['coupon_list']) ? [] : $param['coupon_list'];
        $point = empty($param['point']) ? 0 : $param['point'];
        $disData = [];
        $disData['channel'] = $channel;
        $disData['shop_delivery'] = $shop_delivery;
        $disData['point'] = bcadd($point,0,2);
        $disData['coupon'] = $coupon_list;
        $code = 000010;
        if( !$this->model->setInfoContent($disData) ){
            return errorJson($code, $this->model->getError());
        }
        //检查用户信息
        $server = new Service();
        $user_info = $server->setHost('center_data')->post('user/user/one',['id'=>$user_id]);
        if( empty($user_info) ){
            return errorJson($code, '用户信息获取失败');
        }
        //检查收货地址信息
        $address_info = $server->setHost('center_data')->post('user/address/one', ['id' => $address_id, 'user_id' => $user_id]);
        if( empty($address_info) ){
            return errorJson($code, '收货地址信息获取失败');
        }
        //检查店铺参数
        if( !empty($shop_id) ){
            //获取店铺信息
            $shop_info = $server->setHost('center_data')->post('merchant/shop/one',['id'=>$shop_id]);
            if( empty($shop_info) ){
                return errorJson($code, '店铺信息获取失败');
            }
        }
        //获取预购商品列表
        $shop_list = $this->model->getBuyGoodsList($user_id,$shop_id);
        if( empty($shop_list) ){
            return errorJson($code, $this->model->getError());
        }
        //获取订单合计信息 （会有用的 先注释了）
//        $order_total = $this->model->getTotal($user_id,$shop_list,$address_id);
//        $shop_list = $this->model->getShopList();
        $shop_list = $this->model->setShopShip($address_info,$shop_list);
        $this->model->startTrans();
        $order_list = [];
        try{
            $masterModel = new \app\order\model\OrderMaster();
            $orderGoodsModel = new \app\order\model\OrderGoods();
            $orderConsigneeModel = new \app\order\model\OrderConsignee();
            $orderLogModel = new \app\order\model\OrderLog();
            $shopDepotModel = new \app\depot\model\ShopDepot();
            $shopDepotChangeModel = new \app\depot\model\ShopDepotChange();
            $orderDiscountModel = new \app\order\model\OrderDiscount();
            //创建主订单
            $t = date(' Y-m-d H:i:s');
            $masterData = [];
            $masterData['order_master_sn'] = $this->model->createSn($channel);
            $masterData['create_time'] = $t;
            $master_id = $masterModel->insertGetId($masterData);
            if( empty($master_id) ){
                $code = 000011;
                throw new Exception('订单创建失败');
            }
            //创建子订单
            foreach( $shop_list as $shop ){
                if( $shop['ship_status'] == 0 ){
                    throw new Exception($shop['shop_name'].'店铺所选物流方式不支持收货地址配送');
                }
                //初始化 默认线上发货单
                $order_type = $this->model::ORDER_TYPE_ONLINE;
                //根据用户自提要求 和 店铺属性判断订单发货类型
                if( ( $shop['shop_sale_type_id'] != $this->model::SHOP_TYPE_ONLINE && in_array($shop['shop_id'],$self_take_shop) ) || $shop['shop_sale_type_id'] == $this->model::SHOP_TYPE_OFFLINE ){
                    $order_type = $this->model::ORDER_TYPE_O2O;
                }
                $orderInfoData = [];
                $orderInfoData['order_master_id'] = $master_id;
                $orderInfoData['order_sn'] = $this->model->createSn($channel);
                $orderInfoData['user_id'] = $user_id;
                $orderInfoData['goods_price'] = $shop['goods_price'];
                $orderInfoData['coupon_price'] = $shop['coupon_price'];
                $orderInfoData['point_price'] = $shop['point_price'];
                $orderInfoData['discount_price'] = $shop['discount_price'];
                $orderInfoData['ship_price'] = $shop['ship_price'];
                $orderInfoData['order_price'] = bcadd($shop['ship_price'],
                    bcsub(
                        $shop['goods_price'],
                        bcadd(
                            $shop['coupon_price'],
                            bcadd(
                                $shop['point_price'],
                                $shop['discount_price'],
                                2
                            ),
                            2
                        ),
                        2
                    ),
                    2
                );
                $orderInfoData['remark'] = $remark;
                $orderInfoData['shop_id'] = $shop['shop_id'];
                $orderInfoData['order_type'] = $order_type;
                $orderInfoData['delivery_id'] = $shop['delivery_id'];
                $orderInfoData['add_channel'] = $channel;
                $orderInfoData['create_time'] = $t;
                $orderInfoData['update_time'] = $orderInfoData['create_time'];
                $order_info_id = $this->model->insertGetId($orderInfoData);
                if( empty($order_info_id) ){
                    $code = 000012;
                    throw new Exception('店铺订单创建失败');
                }
                //创建优惠记录 //优惠券使用也在这里处理
                if( !$orderDiscountModel->discountLog($order_info_id,$orderInfoData,$this->model->getOrderDiscountContent()) ){
                    $code = 000012;
                    throw new Exception($orderDiscountModel->getError());
                }
                $order_list[] = $order_info_id;
                $insertGoodsData = [];
                foreach( $shop['goods_list'] as $goods ){
                    //商品库存操作
                    $erp_info = explode(',',$goods['item_info']['erp_code']);
                    if( !$shopDepotModel->SaleInventoryOperation($orderInfoData['order_sn'],$shopDepotChangeModel::TYPE_SALES_SELL ,$shop['shop_id'] , $erp_info[0],$erp_info[1],$goods['num']) ){
                        $code = 000013;
                        throw new Exception($shopDepotModel->getError());
                    }
                    //配置添加参数
                    $goodsData = [];
                    $goodsData['order_id'] = $order_info_id;
                    $goodsData['goods_id'] = $goods['item_info']['goods_id'];
                    $goodsData['goods_item_id'] = $goods['item_info']['id'];
                    $goodsData['item_images'] = $goods['item_info']['item_img'];
                    $goodsData['erp_code'] = $goods['item_info']['erp_code'];
                    $goodsData['num'] = $goods['num'];
                    $goodsData['item_price'] = $goods['item_info']['item_price'];
                    $goodsData['coupon_price'] = $goods['coupon_price'];
                    $goodsData['point_price'] = $goods['point_price'];
                    $goodsData['discount_price'] = $goods['discount_price'];
                    $goodsData['gift_id'] = $goods['gift_id'];
                    $goodsData['create_time'] = $t;
                    $goodsData['update_time'] = $goodsData['create_time'];
                    $insertGoodsData[] = $goodsData;
                }
                //创建商品单
                if( !empty($insertGoodsData) && !$orderGoodsModel->insertAll($insertGoodsData) ){
                    $code = 000013;
                    throw new Exception('商品单创建失败');
                }
                //配置收货地址
                $consigneeData = [];
                $consigneeData['order_id'] = $order_info_id;
                $consigneeData['consignee_name'] = $address_info['name'];
                $consigneeData['mobile'] = $address_info['phone'];
                $consigneeData['province_id'] = $address_info['prov_id'];
                $consigneeData['city_id'] = $address_info['city_id'];
                $consigneeData['area_id'] = $address_info['area_id'];
                $consigneeData['address'] = $address_info['address'];
                $consigneeData['create_time'] = $t;
                $consigneeData['update_time'] = $consigneeData['create_time'];
                if( !$orderConsigneeModel->insert($consigneeData) ){
                    $code = 000014;
                    throw new Exception('商品单创建失败');
                }
                //订单日志记录
                if( !$orderLogModel->markLog($user_id,$order_info_id,'用户下单') ){
                    $code = 000015;
                    throw new Exception('下单记录失败');
                }
                //检查订单是否需要支付 不需要则自动完成支付流程
                if( !$this->model->checkCanNotPayOrder($order_info_id) ){
                    $code = 000016;
                    throw new Exception('订单支付状态更新失败');
                }
            }
            //清空购物车已下单商品
            $cartModel = new \app\cart\model\Cart();
            if( !$cartModel->cartOrderClear($user_id) ){
                $code = 000017;
                throw new Exception('购物车处理失败');
            }
            $this->model->commit();
        }catch( Exception $e ){
            $this->model->rollback();
            return errorJson($code, $e->getMessage());
        }
        return successJson($order_list,'下单成功');
    }

    /**
     * 更换订单商品
     */
    public function replaceOrderGoods(){
        $code = 020101;
        if( !$param = $this->validate->replaceOrderGoods(input('post.')) ) {
            return errorJson($code, $this->validate->getError());
        }
        $order_goods_id = $param['order_goods_id'];
        $item_id = $param['item_id'];
        $num = $param['num'];
        $this->model->startTrans();
        try{
            //获取订单信息
            $orderGoodsModel = new \app\order\model\OrderGoods();
            $where['id'] = $order_goods_id;
            $orderGoodsInfo = $orderGoodsModel->where($where)->find();
            $orderInfo = $this->model->field('order_sn,shop_id')->where('order_id',$orderGoodsInfo['order_id'])->find();
            $order_sn = $orderInfo['order_sn'];
            $shop_id = $orderInfo['shop_id'];
            if( empty($orderGoodsInfo) || empty($order_sn) ){
                $code = 020110;
                throw new Exception('商品订单信息获取失败');
            }
            $newOrderGoodsData = $orderGoodsInfo = $orderGoodsInfo->toArray();
            unset($newOrderGoodsData['id']);
            if( $orderGoodsInfo['ship_status'] == 1 ){
                $code = 020111;
                throw new Exception('已发货商品不能更换');
            }
            if( $orderGoodsInfo['is_gift'] > 1 ){
                $code = 020112;
                throw new Exception('赠品不能更换');
            }
            //获取替换的商品信息
            $goodsItemModel = new \app\goods\model\GoodsItem();
            $item_info = $goodsItemModel->getItemInfo($item_id);
            if( empty($item_info) ){
                $code = 020113;
                throw new Exception('商品订单信息获取失败');
            }
            //库存返还
            $erp_info = explode(',',$orderGoodsInfo['erp_code']);
            $shopDepotModel = new \app\depot\model\ShopDepot();
            $shopDepotChangeModel = new \app\depot\model\ShopDepotChange();
            if( !$shopDepotModel->SaleInventoryOperation($order_sn,$shopDepotChangeModel::TYPE_SALES_RETURN ,$shop_id , $erp_info[0],$erp_info[1],$num) ){
                $code = 020112;
                throw new Exception($shopDepotModel->getError());
            }
            if( $num > $orderGoodsInfo['num'] ){
                $code = 020113;
                throw new Exception('超出最大可替换数');
            }else if( $num == $orderGoodsInfo['num'] ){//数量满额
                //作废原订单商品
                $update['status'] = 40;
            }else if( $num < $orderGoodsInfo['num'] ){//部分数量
                //更新商品单数
                $update['num'] = bcsub($orderGoodsInfo['num'],$num,0);
                //活动优惠金额不转移
                $newOrderGoodsData['coupon_price'] = 0;
                $newOrderGoodsData['point_price'] = 0;
                $newOrderGoodsData['discount_price'] = 0;
            }
            if( !$orderGoodsModel->where($where)->update($update) ){
                $code = 020114;
                throw new Exception('商品信息更新失败');
            }
            //添加新的订单商品
            $newOrderGoodsData['goods_id'] = $item_info['goods_id'];
            $newOrderGoodsData['goods_item_id'] = $item_id;
            $newOrderGoodsData['item_images'] = $item_info['item_img'];
            $newOrderGoodsData['erp_code'] = $item_info['erp_code'];
            $newOrderGoodsData['num'] = $num;
//            $newOrderGoodsData['item_price'] = $item_info['item_price'];//不允许更改价格
            $newOrderGoodsData['ship_status'] = 0;
            $newOrderGoodsData['status'] = 0;
            $newOrderGoodsData['create_time'] = date('Y-m-d H:i:s');
            $newOrderGoodsData['update_time'] = $newOrderGoodsData['create_time'];
            //出库
            $erp_info = explode(',',$newOrderGoodsData['erp_code']);
            if( !$shopDepotModel->SaleInventoryOperation($order_sn,$shopDepotChangeModel::TYPE_SALES_SELL ,$shop_id , $erp_info[0],$erp_info[1],$num) ){
                $code = 020115;
                throw new Exception($shopDepotModel->getError());
            }
            //插入商品单
            if( !$orderGoodsModel->insert($newOrderGoodsData) ){
                $code = 020116;
                throw new Exception('商品信息替换更新失败');
            }
            //若需要更新订单信息 在这里处理 start

            //若需要更新订单信息 在这里处理 end
            //订单日志记录
            $admin_id = 1;
            $orderLogModel = new \app\order\model\OrderLog();
            if( !$orderLogModel->markLog($admin_id,$orderGoodsInfo['order_id'],'商品'.$orderGoodsInfo['erp_code'].'替换'.$num.'件'.$newOrderGoodsData['erp_code'],$orderLogModel::OPERATOR_TYPE_ADMIN) ){
                $code = 020117;
                throw new Exception('订单记录失败');
            }
            actionLogs('商品'.$orderGoodsInfo['erp_code'].'替换'.$num.'件'.$newOrderGoodsData['erp_code'],$this->model);
            $this->model->commit();
        }catch( Exception $e ){
            $this->model->rollback();
            return errorJson($code,$e->getMessage());
        }
        return successJson('success','替换成功');
    }

    //订单支付状态更改
    public function setOrderPay(){
        if( !$param = $this->validate->setOrderPay(input('post.')) ) {
            return errorJson(000001, $this->validate->getError());
        }
        $this->model->startTrans();
        try{
            $t = date('Y-m-d H:i:s');
            $where['order_id'] = $param['order_id'];
            $updateData = [];
            $updateData['pay_status'] = in_array($param['pay_status'],[0,1]) ? $param['pay_status'] : 0;
            $updateData['pay_time'] = $t;
            $updateData['update_time'] = $updateData['pay_time'];
            if( !$this->model->where($where)->update($updateData) ){
                throw new Exception('网络错误，设置失败');
            }
            //$user_id 管理员id
            $admin_id = 1;
            //订单日志记录
            $orderLogModel = new \app\order\model\OrderLog();
            if( !$orderLogModel->markLog($admin_id,$param['order_id'],'设置订单为'.($updateData['pay_status']==1 ? '已支付' : '未支付'),$orderLogModel::OPERATOR_TYPE_ADMIN) ){
                throw new Exception('订单记录失败');
            }
            actionLogs('设置订单'.$param['order_id'].'为'.($updateData['pay_status']==1 ? '已支付' : '未支付'),$this->model);
            $this->model->commit();
        }catch( Exception $e ){
            $this->model->rollback();
            return errorJson(020110, $e->getMessage());
        }
        return successJson('success','设置成功');
    }

    //订单审核
    public function orderVerify(){
        if( !$param = $this->validate->orderVerify(input('post.')) ) {
            return errorJson(000001, $this->validate->getError());
        }
        $remark = isset($param['remark']) ? '复审操作备注：'.$param['remark'] : '';
        $this->model->startTrans();
        try{
            $where['order_id'] = $param['order_id'];
            $orderInfo = $this->model->where($where)->find();
            //获取原订单信息
            $t = date('Y-m-d H:i:s');
            $updateData = [];
            $updateData['verify'] = in_array($param['verify'],[1,2]) ? $param['verify'] : 0;
            //检查是否重复操作 且必须有备注内容
            if( $orderInfo['verify'] != 0 && $param['remark'] == '' ){
                throw new Exception('反审操作必须有备注内容');
            }
            $updateData['verify_time'] = $t;
            $updateData['update_time'] = $updateData['verify_time'];
            if( !$this->model->where($where)->update($updateData) ){
                throw new Exception('网络错误，审核失败');
            }
            //$user_id 管理员id
            $user_id = 1;
            //订单日志记录
            $orderLogModel = new \app\order\model\OrderLog();
            if( !$orderLogModel->markLog($user_id,$param['order_id'],'订单审核：'.($updateData['verify']==1 ? '通过' : '未通过').'。'.$remark,$orderLogModel::OPERATOR_TYPE_ADMIN) ){
                throw new Exception('订单记录失败');
            }
            actionLogs('审核订单'.$param['order_id'].'为'.($updateData['verify']==1 ? '通过' : '未通过').'。'.$remark,$this->model);
            $this->model->commit();
        }catch( Exception $e ){
            $this->model->rollback();
            return errorJson(020110, $e->getMessage());
        }
        return successJson('success','审核成功');
    }

    //会员订单确认收货（设为已完成）
    public function userOrderFinished(){
        if( !$param = $this->validate->userOrderFinished(input('post.')) ) {
            return errorJson(000001, $this->validate->getError());
        }
        $this->model->startTrans();
        try{
            //获取订单信息
            $where['order_id'] = $param['order_id'];
            $where['user_id'] = $param['user_id'];
            if( !$this->model->where($where)->find() ){
                throw new Exception('订单信息获取失败');
            }
            $t = date('Y-m-d H:i:s');
            $updateData = [];
            $updateData['finished_status'] = 1;
            $updateData['finished_time'] = $t;
            $updateData['update_time'] = $updateData['finished_time'];
            if( !$this->model->where($where)->update($updateData) ){
                throw new Exception('网络错误，操作失败');
            }
            // 管理员id
            $user_id = 1;
            //订单日志记录
            $orderLogModel = new \app\order\model\OrderLog();
            if( !$orderLogModel->markLog($user_id,$param['order_id'],'用户确认收货',$orderLogModel::OPERATOR_TYPE_USER) ){
                throw new Exception('订单记录失败');
            }
            actionLogs('设置订单：'.$param['order_id'].'已收货',$this->model);
            $this->model->commit();
        }catch( Exception $e ){
            $this->model->rollback();
            return errorJson(020110, $e->getMessage());
        }
        return successJson('success','操作成功');
    }

    //设置订单已完成
    public function setOrderFinished(){
        if( !$param = $this->validate->setOrderFinished(input('post.')) ) {
            return errorJson(000001, $this->validate->getError());
        }
        $this->model->startTrans();
        try{
            $t = date('Y-m-d H:i:s');
            $where['order_id'] = $param['order_id'];
            $updateData = [];
            $updateData['finished_status'] = 1;
            $updateData['finished_time'] = $t;
            $updateData['update_time'] = $updateData['finished_time'];
            if( !$this->model->where($where)->update($updateData) ){
                throw new Exception('网络错误，操作失败');
            }
            // 管理员id
            $admin_id = 1;
            //订单日志记录
            $orderLogModel = new \app\order\model\OrderLog();
            if( !$orderLogModel->markLog($admin_id,$param['order_id'],'设置订单为已收货',$orderLogModel::OPERATOR_TYPE_ADMIN) ){
                throw new Exception('订单记录失败');
            }
            actionLogs('设置订单：'.$param['order_id'].'已收货',$this->model);
            $this->model->commit();
        }catch( Exception $e ){
            $this->model->rollback();
            return errorJson(020110, $e->getMessage());
        }
        return successJson('success','设置成功');
    }

    //管理员取消订单
    public function adminCancelOrder(){
        if( !$param = $this->validate->adminCancelOrder(input('post.')) ) {
            return errorJson(020101, $this->validate->getError());
        }
        $admin_id = 1;
        $order_id = $param['order_id'];
        $where['order_id'] = $order_id;
        //获取订单信息
        $orderInfo = $this->model->where($where)->find();
        if( empty($orderInfo) ){
            return errorJson(020110,'订单信息获取失败');
        }
        if( !$this->model->cancelOrder($orderInfo,$admin_id,true) ){
            return errorJson(020111, $this->model->getError());
        }
        //记录日志
        return successJson('success','取消成功');
    }

    //用户取消订单
    public function userCancelOrder(){
        if( !$param = $this->validate->userCancelOrder(input('post.')) ) {
            return errorJson(000001, $this->validate->getError());
        }
        $user_id = $param['user_id'];
        $order_id = $param['order_id'];
        $where['order_id'] = $order_id;
        $where['user_id'] = $user_id;
        //获取订单信息
        $orderInfo = $this->model->where($where)->find();
        if( empty($orderInfo) ){
            return errorJson(000010,'订单信息获取失败');
        }
        if( $orderInfo['verify'] == 1 ){
            return errorJson(000011,'已审核订单不能取消');
        }
        if( !$this->model->cancelOrder($orderInfo,$user_id) ){
            return errorJson(000012, $this->model->getError());
        }
        return successJson('success','取消成功');
    }

    public function createPaymentOrder(){
        if( !$param = $this->validate->createPaymentOrder(input('post.')) ) {
            return errorJson(000001, $this->validate->getError());
        }
        $paymentModel = new \app\order\model\OrderPayment();
        if( !$payOrderSn = $paymentModel->createPaymentOrder($param['order_id'],$param['price']) ){
            return errorJson(000010, $paymentModel->getError());
        }
        return successJson($payOrderSn,'success');
    }

}
?>
