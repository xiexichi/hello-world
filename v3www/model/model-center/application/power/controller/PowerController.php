<?php
namespace app\power\controller;


class PowerController extends Base
{
    public function allBefore(){
        if (!$checkData = $this->validate->all(input())) {
            $this->setExitErrorInfo($this->validate->getError(), 10001);
            return false;
        }
        
        switch ($checkData['type']){
            case 'son_of_module':
                $this->model->where('module_id','=',$checkData['module_id'])->order('sort','desc');
                break;
            default:
                break;
        }
        
        
        
    }

}