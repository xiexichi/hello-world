<?php

namespace app\activity\controller;

class CouponUser extends Base
{
    public function indexBefore()
    {
        $param = $this->validate->index(input());
        if( $param === false ){
            $this->isExit = true;
            $this->code = 000001;
            $this->error = $this->validate->getError();
        }
        $where = [];
        if(isset($param['coupon_id']) && !empty($param['coupon_id']) ){
            $where['c.id'] = ['=',$param['coupon_id']];
        }
        if(isset($param['coupon_sn']) && !empty($param['coupon_sn']) ){
            $where['cu.coupon_sn'] = ['=',$param['coupon_sn']];
        }
        if(isset($param['user_id']) && !empty($param['user_id']) ){
            $where['cu.user_id'] = ['=',$param['user_id']];
        }
        if(isset($param['use_status']) && $param['use_status'] != '' ){
            $where['cu.use_status'] = ['=',$param['use_status']];
        }
        $this->model->where($where)->order(['cu.create_time'=>'desc','cu.use_time'=>'desc']);
    }

    public function getUserList(){
        if( !$param = $this->validate->getUserList(input()) ){
            return errorJson(000001,$this->validate->getError());
        }
        $coupon_list = $this->model->data($param)->getUserList($param['user_id']);

        if (empty($coupon_list)) {
            return errorJson(000001,'没有可用的优惠券！');
        }

        return $coupon_list;
    }
}
