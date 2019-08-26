<?php

namespace app\merchant\controller;


class Merchant extends Base
{
    /**
     * 获取商户主体，如果当前登录的人属于某商户，则只返回的列表中只有该商户
     * @return \think\response\Json
     */
    public function getMerchantList(){
        $adminMerchantID = input('admin_merchant_id');
        dump($this->model->getMerchantListByMerchantID($adminMerchantID));
        return successJson($this->model->getMerchantListByMerchantID($adminMerchantID));

    }
}
