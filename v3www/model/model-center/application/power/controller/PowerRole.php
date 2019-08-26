<?php
namespace app\power\controller;

use app\power\model\PowerRoleGroup;

class PowerRole extends Base
{  
    public function getAddData(){
        //获取自己的角色id，新增/编辑 【角色】时，只能分配自己所拥有的权限
        $adminRoleID = input('admin_role_id');
        $adminAccountType = input('admin_account_type');

        //权限树
        $roleTree = $this->model->getRoleGroupTreeByRoleID($adminAccountType,$adminRoleID);
        $rolesList = $this->model->myNextRoles($adminAccountType,$adminRoleID);

        return successJson(['trees'=>$roleTree,'roles'=>$rolesList]);
    }
    
    public function addBefore(){
        if (!$checkData = $this->validate->add(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }
        $adminRoleID = input('admin_role_id');
        $adminID = input('admin_id');
        $adminAccountType = input('admin_account_type');
        
        //只保存group_id？不保存moudle和controller
        if(!empty($checkData['authids'])){
            $checkData['authids'] = $this->filterTreeSelecterVal($checkData['authids']);
            
            if(empty($this->checkIsOverGroup($adminAccountType, $adminRoleID, $checkData['authids']))){
                return errorJson(123, '所设置的权限大于所拥有的权限');
            }
        }

        $checkData['create_user_type'] = $adminAccountType;
        $checkData['create_user_id'] = $adminID;
        $this->model->data($checkData);
    }
    
    public function editBefore(){
        if (!$checkData = $this->validate->edit(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }
        
        $this->addBefore();
        
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
     * @param unknown $adminRoleID
     * @param unknown $selectGroups
     * @return boolean
     */
    protected function checkIsOverGroup($adminAccountType,$adminRoleID,$selectGroups){
        //所设置的权限不能大于自己的
        if($adminAccountType != 0){//不是超级管理员
            $myGroupIDs = (new PowerRoleGroup())->getGroupIDsByRoleID($adminRoleID);
            $myGroupIDs = explode(',', $myGroupIDs);
            
            //差集
            if(!empty(array_diff($selectGroups, $myGroupIDs))){
                return false;
            }
        }
        return true;
    }
    
    public function getMyNextRoles(){
        $adminRoleID = input('admin_role_id');
        $adminAccountType = input('admin_account_type');
        
        $roles = $this->model->myNextRoles($adminAccountType, $adminRoleID);
        return successJson($roles);
    }
    
    public function getMySetRoles(){
        $adminAccountType = input('admin_account_type');
        $adminID = input('admin_id');
        
        $roles = $this->model->mySetRoles($adminAccountType, $adminID);
        return successJson($roles);
    }
    
    public function getEditData(){
        if (!$checkData = $this->validate->getEditData(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }
        
        //获取自己的角色id，新增/编辑 【角色】时，只能分配自己所拥有的权限
        $adminRoleID = input('admin_role_id');
        $adminAccountType = input('admin_account_type');
        
        //角色信息
        $roleData = $this->model->where('id',$checkData['id'])->find();
        //我的下级角色列表
        $roleList= $this->model->myNextRoles($adminAccountType,$adminRoleID);

        foreach ($roleList as $k=>$v){
            if($v['id'] == $roleData['id']){
                $roleList[$k]['is_selected'] = 1;
            }else{
                $roleList[$k]['is_selected'] = 0;
            }
        }
        $roleData['roleList'] = $roleList;
       
        $roleTree = $this->model->getRoleGroupTreeByRoleID($adminAccountType,$adminRoleID,$checkData['id']);
        $roleData['roleTree'] = $roleTree;
       
        return successJson($roleData);
    }
}