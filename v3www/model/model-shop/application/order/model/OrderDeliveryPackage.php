<?php

namespace app\order\model;

class OrderDeliveryPackage extends Base
{
    protected $name = 'order_delivery_package';

    public function packageGoods(){
        return $this->hasMany('OrderPackageGoods','order_delivery_package_id','id');
    }

    public function getOrderPackage($order_id){
        if( empty($order_id) ){
            return [];
        }
        $expressModel = new \app\system\model\Express();
        $list = $this->with('packageGoods')
            ->field('odp.id,e.name,odp.express_sn,odp.remark,odp.create_time')
            ->alias('odp')
            ->join($expressModel->getTable().' e','e.id = odp.express_id')
            ->where('odp.order_id',$order_id)->select();
        return $list;
    }

}
