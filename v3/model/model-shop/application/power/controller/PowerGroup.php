<?php
namespace app\power\controller;

use app\power\model\PowerModule;
use app\power\model\PowerController;
use app\power\model\PowerAction;

class PowerGroup extends Base
{
    public function getGroupTree(){
        return successJson($this->model->getGroupTree());
    }

    public function getEditData(){
        if (!$checkData = $this->validate->getEditData(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }
        //组信息
        $groupData = $this->model->where('id',$checkData['id'])->find();
        
        //顺便获取module列表并设置选中状态
        $groupData['moduleList'] = (new PowerModule())->getEditData($groupData['module_id']);
        //顺便获取controller列表并设置选中状态
        $groupData['controllerList'] = (new PowerController())->getEditData($groupData['controller_id']);
        //顺便获取action列表并设置选中状态
        $groupData['actionList'] = (new PowerAction())->getEditData($groupData['controller_id'],$groupData['id']);

        return successJson($groupData);
    }
    
}