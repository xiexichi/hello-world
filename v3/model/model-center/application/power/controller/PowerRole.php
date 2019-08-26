<?php
namespace app\power\controller;

use app\power\model\PowerRoleGroup;

class PowerRole extends Base
{
    public function getList(){
        $adminIsSuper = input('admin_is_super');
        $adminMerchantID = input('admin_merchant_id');
        $adminRoleID = input('admin_role_id');
        $adminID = input('admin_id');

        //设置能看度
        if(empty($adminIsSuper)){//非超管
            if(!empty($adminMerchantID)){//商户的只能看到自己商户主体的角色
                $this->model->where('type',2)->where('merchant_id',$adminMerchantID);
            }else{//管理员的只能看到自己同等级（role下级）
                $this->model->where('type',1)->where('pid',$adminRoleID);
            }
        }

        return $this->index();
    }

    public function getAddData(){
        //获取自己的角色id，新增/编辑 【角色】时，只能分配自己所拥有的权限
        $adminRoleID = input('admin_role_id');
        $adminIsSuper = input('admin_is_super');

        //权限树
        $roleTree = $this->model->getRoleGroupTreeByRoleID($adminIsSuper,$adminRoleID);
        $adminIsSuper = empty($adminIsSuper) ? 0 : 1;

        return successJson(['trees'=>$roleTree,'adminIsSuper'=>$adminIsSuper]);
    }

    public function addBefore(){
        if (!$checkData = $this->validate->add(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }

        $adminID = input('admin_id');
        $adminIsSuper = input('admin_is_super');
        $adminRoleID = input('admin_role_id');
        $adminRoleType = input('admin_role_type');

        if(empty($adminIsSuper)){//不是超管
            //获取我的账号类型
            $checkData['type'] = $adminRoleType;
        }

        if(!empty($checkData['authids'])){
            $checkData['authids'] = $this->filterTreeSelecterVal($checkData['authids']);

            if(empty($this->checkIsOverGroup($adminIsSuper, $adminRoleID, $checkData['authids']))){
                return errorJson(123, '所设置的权限大于所拥有的权限');
            }
        }

        //超管创建的角色全是一级的//其他角色创建的角色都是自己的下级
        $checkData['pid'] = input('admin_role_id');
        $checkData['merchant_id'] = input('admin_merchant_id');
        $checkData['create_user_id'] = $adminID;
        $this->model->data($checkData);
    }
    /**
     * 过滤前端的选择树的值
     * @param unknown $returnVal
     * @return array
     */
    protected function filterTreeSelecterVal($returnVal){
        $tempList = [];
        foreach ($returnVal as $v){
            if(!empty($v) && !in_array($v, $tempList)){
                array_push($tempList, $v);
            }
        }
        return $tempList;
    }
    /**
     * 检查设置的权限是否大于自己拥有的
     * @param unknown $adminAccountType
     * @param unknown $adminRoleIDgetRoleGroupTreeByRoleIDgetRoleGroupTreeByRoleID
     * @param unknown $selectGroups
     * @return boolean
     */
    protected function checkIsOverGroup($adminIsSuper,$adminRoleID,$selectGroups){
        //所设置的权限不能大于自己的
        if(empty($adminIsSuper)){//不是超级管理员
            $myGroupIDs = (new PowerRoleGroup())->getGroupIDsByRoleID($adminRoleID);
            $myGroupIDs = explode(',', $myGroupIDs);

            //差集
            if(!empty(array_diff($selectGroups, $myGroupIDs))){
                return false;
            }
        }
        return true;
    }

    public function getEditData(){
        if (!$checkData = $this->validate->getEditData(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }

        //获取自己的角色id，新增/编辑 【角色】时，只能分配自己所拥有的权限
        $adminRoleID = input('admin_role_id');
        $adminIsSuper = input('admin_is_super');

        //角色信息
        $roleData = $this->model->where('id',$checkData['id'])->find();
        //权限树
        $roleTree = $this->model->getRoleGroupTreeByRoleID($adminIsSuper,$adminRoleID,$checkData['id']);

        $roleData['roleTree'] = $roleTree;
        $roleData['adminIsSuper'] = empty($adminIsSuper)?0:1;
        return successJson($roleData);

    }

    public function editBefore(){
        if (!$checkData = $this->validate->edit(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }

        $adminID = input('admin_id');
        $adminIsSuper = input('admin_is_super');
        $adminRoleID = input('admin_role_id');

        if(empty($adminIsSuper)){//非超管，无法指定角色类型（后台/商户类型）
            unset($checkData['type']);
        }

        if(!empty($checkData['authids'])){
            $checkData['authids'] = $this->filterTreeSelecterVal($checkData['authids']);

            if(empty($this->checkIsOverGroup($adminIsSuper, $adminRoleID, $checkData['authids']))){
                return errorJson(123, '所设置的权限大于所拥有的权限');
            }
        }

        $this->model->data($checkData);

    }


}
