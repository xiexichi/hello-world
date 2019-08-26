<?php
namespace app\power\model;

class PowerModule extends Base
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	protected $table = 'power_module';
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';

	public function delAfter(){
	    //删除完module后就删除controller
	    $modelPowerController = new PowerController();
	    $controllerIds = $modelPowerController->field('id')->where('module_id','in',$this->data['ids'])->select();
	    if(empty($controllerIds)){
	        return true;
	    }
	    $controllerIds = collection($controllerIds)->toArray();
	    $controllerIds = implode(',', array_column($controllerIds, 'id'));
	    
	    $modelPowerController->tempData['delAfter'] = ['controller_ids' => $controllerIds];
	    $modelPowerController->setWhere('id','in',$controllerIds)->del();
	}
	
	public function getEditData($selectedModuleID){
	    $list = $this->field('*,0 AS is_selected')->select();
	    foreach ($list as $k=>$v){
	        if($v['id'] == $selectedModuleID){
	            $list[$k]['is_selected'] = 1;
	            break;
	        }
	    }
	    return $list;
	}

}