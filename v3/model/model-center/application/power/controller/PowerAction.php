<?php
namespace app\power\controller;

class PowerAction extends Base
{
    public function allBefore(){
        if (!$checkData = $this->validate->all(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }
        
        switch ($checkData['type']){
            case 'son_of_controller':
                $this->model->where('controller_id','=',$checkData['controller_id']);
                break;
            default:
                break;
        }

    }
    
    public function addAll(){
        if (!$checkData = $this->validate->addAll(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }
        $nameArr = explode(',', trim($checkData['name'], ','));
        $data = [];
        foreach ($nameArr as $v){
            $checkData['name'] = $v;
            $data[] = $checkData;
        }
        $this->model->data($data);
        if(!$this->model->addAll()){
            return errorJson(123, $this->model->getError());
        }
        return successJson([],'添加成功');  
    }
    
    public function setDefault(){
        if (!$checkData = $this->validate->setDefault(input())) {
            return errorJson(10001, $this->validate->getError());
        }
        //全部设为非默认
        $this->model->setUpdateCondition(['controller_id'=>$checkData['controller_id']])->data('is_default',2)->edit();
        //设置默认方法
        $this->model->setUpdateCondition(['id'=>$checkData['action_id']])->data('is_default',1)->edit();
        return successJson([],'修改成功');
    }
}