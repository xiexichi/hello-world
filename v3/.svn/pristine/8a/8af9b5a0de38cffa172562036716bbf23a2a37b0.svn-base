<?php
namespace app\power\controller;

class PowerRole extends Base
{  
    public function getAllList(){
        $merchantShopID = input('shop_id');
        $this->model->where('shop_id', $merchantShopID);
        return $this->all();
    }
    
    public function getList(){
        $merchantShopID = input('shop_id');
        
        $this->model->where('shop_id', $merchantShopID);
        
        return $this->index();
    }
    
    public function getAddData(){
        $merchantShopID = input('shop_id');
        //权限树//所有
        $roleTree = $this->model->getRoleGroupTree();
        return successJson(['trees'=>$roleTree]);
    }
    public function addBefore(){
        if (!$checkData = $this->validate->add(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }
        
        $merchantStaffID = input('ctrl_staff_id');
        $merchantAdminID = input('ctrl_admin_id');
        $merchantAccountType = input('account_type');
        $merchantShopID = input('shop_id');
        $merchantRoleID = input('role_id');
        
        if(!empty($checkData['authids'])){
            $checkData['authids'] = $this->filterTreeSelecterVal($checkData['authids']);
        }
        
        $checkData['shop_id'] = $merchantShopID;
        $checkData['create_user_type'] = $merchantAccountType;
        $checkData['create_user_id'] = $merchantAdminID ?: $merchantStaffID;
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
    public function getEditData(){
        if (!$checkData = $this->validate->getEditData(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }
        
        //角色信息
        $roleData = $this->model->where('id',$checkData['id'])->find();
        //权限树
        $roleData['trees'] = $this->model->getRoleGroupTree($checkData['id']);
        return successJson($roleData);
    }
    
    public function editBefore(){
        if (!$checkData = $this->validate->edit(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }
        
        $merchantStaffID = input('ctrl_staff_id');
        $merchantAdminID = input('ctrl_admin_id');
        $merchantAccountType = input('account_type');
        $merchantShopID = input('shop_id');
        $merchantRoleID = input('role_id');
        
        //不是该shop_id的当然无法修改
        //角色信息
        $roleShopID = $this->model->where('id',$checkData['id'])->value('shop_id');
        if($roleShopID != $merchantShopID){
            $this->setExitErrorInfo('无法修改非本店的角色', 10001);
            return false;
        }
        
        if(!empty($checkData['authids'])){
            $checkData['authids'] = $this->filterTreeSelecterVal($checkData['authids']);
        }
        
        $this->model->data($checkData);
    }
    

}