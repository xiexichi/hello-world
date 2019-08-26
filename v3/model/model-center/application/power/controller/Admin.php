<?php
namespace app\power\controller;

use app\power\model\PowerRole;


use app\merchant\model\Merchant;
use app\merchant\model\Shop;
use app\merchant\model\AdminShop;
use app\power\model\Admin as adminModel;

class Admin extends Base
{
    public function getAddData(){
        //获取自己的角色id，新增/编辑 【角色】时，只能分配自己所拥有的角色下级
        $adminRoleID = input('admin_role_id');
        $adminIsSuper = input('admin_is_super');
        $adminRoleType = input('admin_role_type');
        $adminMerchantID = input('admin_merchant_id');

        //我的下级角色列表
        $roleList = [];
        $modelPowerRole = new PowerRole();
        if(empty($adminIsSuper)){//不是超管
            if($adminRoleType == 1){//后台管理员
                $roleList = $modelPowerRole->getAdminNextRoles($adminRoleID);
            }else{//商户
                $roleList = $modelPowerRole->getMerchantNextRoles($adminMerchantID);
            }
        }else{//超管获取所有一级角色
            $roleList = $modelPowerRole->getAllFirstRoles();
        }

        return successJson(['roleList'=>$roleList]);
    }
    public function addBefore(){
        if (!$checkData = $this->validate->add(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }

        $adminID = input('admin_id');
        $adminRoleID = input('admin_role_id');
        $adminIsSuper = input('admin_is_super');

        if(empty($adminIsSuper)){//非超管

        }else{

        }

        //所要设置的角色的信息
        $roleInfo = (new PowerRole())->where('id',$checkData['role_id'])->find();
        if(empty($roleInfo)){
            $this->setExitErrorInfo('找不到当前要设置的角色', 10001);
            return false;
        }
        if($roleInfo['type'] == 2){
            if(empty($checkData['merchant_id'])){
                $this->setExitErrorInfo('请选择当前商户所属的商户主体', 10001);
                return false;
            }
        }else{
            $checkData['merchant_id'] = 0;
        }
        $checkData['create_user_id'] = $adminID;

         if(!empty($checkData['shop_ids'])){//如果有绑定就顺便看看绑定的店铺是不是都属于该商户主体
             if(!(new Shop())->isShopsAllInMerchant($checkData['shop_ids'], $checkData['merchant_id'])){
                 $this->setExitErrorInfo('请正确选择商户下的店铺', 10001);
                 return false;
             }
         }


//
//        $pwdData = $this->model::makePassword($checkData['password']); //初始的
        $pwdData = adminModel::makePassword($checkData['password']);
        $checkData['password'] = $pwdData['pwd'];
        $checkData['salt']  =  $pwdData['salt'];
        $this->model->data($checkData);
    }

    public function getAdminList(){
        $checkData = $this->validate->getAdminList(input());
        if(false === $checkData){
            return errorJson(123, $this->validate->getError());
        }
        $adminIsSuper = input('admin_is_super');
        $adminMerchantID = input('admin_merchant_id');
        $adminRoleID = input('admin_role_id');
        $adminID = input('admin_id');
        $this->model->alias('a')
                    ->join('merchant m','a.merchant_id = m.id', 'LEFT')
                    ->field('a.id,a.realname,a.code,a.status,a.last_ip,a.last_time,m.name AS merchant_name')
                    ->where('a.is_super','<>',1)
                    ->where('a.id','<>',$adminID);//列表不能看到自己，因为不能自己编辑自己
        if(!empty($checkData['realname'])){
            $this->model->where('a.realname','LIKE',"%{$checkData['realname']}%");
        }
        if(!empty($checkData['status'])){
            $this->model->where('a.status',$checkData['status']);
        }

        if(empty($adminIsSuper)){//非超管
            //设置能看度
            if(!empty($adminMerchantID)){//商户的只能看到自己商户主体的
                $this->model->where('a.merchant_id',$adminMerchantID);
            }else{//管理员的只能看到自己同等级（role下级）
                $roles = (new PowerRole())->getAdminNextRoles($adminRoleID);
                if(empty($roles)){
                    return successJson([]);
                }
                $roles = setTreeList($roles);
                $roleIDs = implode(',', array_column($roles, 'id'));
                $this->model->join('power_role_admin pra','a.id=pra.admin_id','INNER')
                            ->where('pra.role_id','IN',$roleIDs);
            }
        }

        return $this->index();
    }

    public function getAdminEdit(){
        $adminRoleID = input('admin_role_id');
        $adminRoleType = input('admin_role_type');
        $adminMerchantID = input('admin_merchant_id');
        $adminID = input('admin_id');
        $adminIsSuper = input('admin_is_super');

        $checkData = $this->validate->getAdminEdit(input());
        if(false === $checkData){
            return errorJson(123, $this->validate->getError());
        }
        //获取某管理员详情
        $adminDetail = $this->model->getAdminDetail($checkData['id']);
        if(empty($adminDetail)){
            return errorJson(123, '没有数据');
        }
        unset($adminDetail['password']);

        //不允许编辑自己
        if($adminDetail['id'] == $adminID){
            return errorJson(123, '无法编辑自己');
        }

        $modelPowerRole = new PowerRole();
        //获取某管理员的角色信息
        $roleInfo = $modelPowerRole->getRoleInfoByAdminID($checkData['id']);
        //获取角色组
        $roleList = [];

        if(empty($adminIsSuper)){//非超管
            if($adminRoleType == 1){//后台管理员
                $roleList = $modelPowerRole->getAdminNextRoles($adminRoleID);
            }elseif ($adminRoleType == 2){//商户
                $roleList = $modelPowerRole->getMerchantNextRoles($adminMerchantID);
            }
        }else{//超管。获取两种角色
            $roleList = $modelPowerRole->getAllFirstRoles();
        }

        $roleList = collection($roleList)->toArray();
        foreach ($roleList as &$v){
            $v['is_selected'] = 0;
            if($v['id'] == $roleInfo['id']){
                $v['is_selected'] = 1;
            }
        }
        unset($v);

        //商户列表如果该管理员是【商户持有者】或者【商户经理】,就要获取商户列表
        $merchantList = [];
        if($roleInfo['type'] == 2){
            $merchantList = (new Merchant())->getMerchantListByMerchantID($adminMerchantID);
        }

        //如果该管理员是【商户经理】，就顺便获取所管核的店铺列表
        $shopList = [];
        if(!empty($roleInfo['merchant_id'])){//证明是商户的下级人员
            $shopList = (new Shop())->field('id,name,0 AS is_selected')
                                    ->where('merchant_id',$roleInfo['merchant_id'])
                                    ->select();
            $adminShopIDs = (new AdminShop())->where('admin_id',$adminDetail['id'])
                                    ->column('shop_id');

            if(!empty($adminShopIDs)){
                foreach ($shopList as $k=>$v){
                    if(in_array($v['id'], $adminShopIDs)){
                        $shopList[$k]['is_selected'] = 1;
                    }
                }
            }
        }

        return successJson([
            'adminData'     =>  $adminDetail,
            'roleList'      =>  $roleList,
            'merchantList'  =>  $merchantList,
            'shopList'      =>  $shopList,
            'roleInfo'      =>  $roleInfo
        ]);
    }

    public function editBefore(){
        if (!$checkData = $this->validate->edit(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }

        $adminID = input('admin_id');
        $adminRoleID = input('admin_role_id');
        $adminIsSuper = input('admin_is_super');

        if($checkData['id'] == $adminID){
            $this->setExitErrorInfo('无法编辑自己', 10001);
            return false;
        }

        if(empty($adminIsSuper)){//非超管

        }else{

        }

        //所要设置的角色的信息
        $roleInfo = (new PowerRole())->where('id',$checkData['role_id'])->find();
        if(empty($roleInfo)){
            $this->setExitErrorInfo('找不到当前要设置的角色', 10001);
            return false;
        }
        $this->model->tempData['roleInfo'] = $roleInfo;

        if($roleInfo['type'] == 2){
            if(empty($checkData['merchant_id'])){
                $this->setExitErrorInfo('请选择当前商户所属的商户主体', 10001);
                return false;
            }
        }else{
            $checkData['merchant_id'] = 0;
        }
//
//         if(!empty($checkData['shop_ids'])){//如果有绑定就顺便看看绑定的店铺是不是都属于该商户主体
//             if(!(new Shop())->isShopsAllInMerchant($checkData['shop_ids'], $checkData['merchant_id'])){
//                 $this->setExitErrorInfo('请正确选择商户下的店铺', 10001);
//                 return false;
//             }
//         }

        if(!empty($checkData['password'])){
//            $pwdData = $this->model::makePassword($checkData['password']); //初始的,就是想调用跟自己同名的model,写成这种乱七八糟的
            $pwdData = adminModel::makePassword($checkData['password']); //我自己改的
            $checkData['password'] = $pwdData['pwd'];
            $checkData['salt']  =  $pwdData['salt'];
        }else{
            unset($checkData['password']);
        }

        $this->model->data($checkData);
    }

    public function login(){
        if (!$checkData = $this->validate->login(input())) {
            return errorJson(10001, $this->validate->getError());
        }

        if (!$adminData = $this->model->data($checkData)->login()) {
            return errorJson(10001, $this->model->getError());
        }

        $adminData['role'] = $this->model->getAdminRole($adminData['id']);
        $adminData['power'] = $this->model->getAdminPower($adminData['role']['id']);

        return successJson($adminData);
    }

    public function shopLogin(){
        if (!$checkData = $this->validate->login(input())) {
            return errorJson(10001, $this->validate->getError());
        }

        if (!$adminData = $this->model->data($checkData)->login()) {
            return errorJson(10001, $this->model->getError());
        }

        $adminRole = $this->model->getAdminRole($adminData['id']);
        if(empty($adminRole)){
            return errorJson(10001, '暂无权限');
        }

        if($adminRole['type'] != 2){
            return errorJson(10001, '非商户账号');
        }

        if(empty($adminData['merchant_id'])){
            return errorJson(10001, '尚未指定商户主体');
        }

        //获取店铺列表，前端用来选择要进入的店铺
        $shops = [];
        if($adminRole['pid'] == 0){//一级账号，即商户大佬
            $adminData['level'] = 1;
            $shops = (new Shop())->getShopsByMerchantID($adminData['merchant_id']);
        }else{//商户大佬下级的账号(区域经理之类)
            $adminData['level'] = 2;
            $shops = (new Shop())->getShopsByAdminID($adminData['id']);
        }

        $adminData['shops'] = $shops;
        //$adminData['power'] = $this->model->getShopPower($adminData['role']['id']);
        $adminData['power'] = ['menu'=>[],'actions'=>[]];//全权

        return successJson($adminData);
    }












































}
