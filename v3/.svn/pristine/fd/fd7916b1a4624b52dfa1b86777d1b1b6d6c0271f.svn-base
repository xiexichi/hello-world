<?php
namespace app\user\controller;

use app\user\model\User;

class UserLevel extends Base
{
    public function addBefore(){
        if (!$checkData = $this->validate->add(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }
        if(!empty($checkData['auto_update'])){//开启了自动升级的话，升级的界限必须大于0
            if(empty($checkData['level_limit'])){
                $this->setExitErrorInfo('设置了自动升级就必须填升级的界限', 10001);
                return false;
            }
        }
    }
    public function editBefore(){
        if (!$checkData = $this->validate->edit(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }
        if(!empty($checkData['auto_update'])){//开启了自动升级的话，升级的界限必须大于0
            if(empty($checkData['level_limit'])){
                $this->setExitErrorInfo('设置了自动升级就必须填升级的界限', 10001);
                return false;
            }
        }
    }
    public function deleteBefore(){
        if (!$checkData = $this->validate->delete(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }
        //如果有会员是该会员等级的，就无法删除
        if(!empty((new User())->isLevelHasUser($checkData['ids']))){
            $this->setExitErrorInfo('当前会员等级存在会员，无法删除', 10001);
            return false;
        }
        $this->model->where('id', $checkData['ids']);
    }
    

        
    
}
