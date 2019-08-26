<?php
namespace app\power\model;

class PowerController extends Base
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	protected $table = 'power_controller';
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';

	public function delAfter(){
	    $controllerIds = '';
	    if(!empty($this->tempData['delAfter'])){//上级删除时调用的
	        $controllerIds = $this->tempData['delAfter']['controller_ids'];
	    }elseif(!empty($this->data['ids'])){//前端删除时的
	        $controllerIds = $this->data['ids'];
	    }
	    
	    if(empty($controllerIds)){
	        return true;
	    }
	    
	    //删除方法
	    $modelPowerAction = new PowerAction();
	    $actionIds = $modelPowerAction->field('id')->where('controller_id','in',$controllerIds)->select();
	    if(!empty($actionIds)){
	        $actionIds = collect+ion($actionIds)->toArray();
	        $actionIds = implode(',', array_column($actionIds, 'id'));
	        $modelPowerAction->tempData['delAfter']['action_ids'] = $actionIds;
	        $modelPowerAction->setWhere('id','in',$actionIds)->del();
	    }
	    
	    //删除方法组
	    $modelPowerGroup = new PowerGroup();
	    $groupIds = $modelPowerGroup->field('id')->where('controller_id','in',$controllerIds)->select();
	    if(!empty($groupIds)){
	        $groupIds = collection($groupIds)->toArray();
	        $groupIds = implode(',', array_column($groupIds, 'id'));
	        $modelPowerGroup->tempData['delAfter']['group_ids'] = $groupIds;
	        $modelPowerGroup->setWhere('id','in',$groupIds)->del();
	    }

	}
	
	public function getEditData($selectedControllerID){
	    $list = $this->field('*,0 AS is_selected')->select();
	    foreach ($list as $k=>$v){
	        if($v['id'] == $selectedControllerID){
	            $list[$k]['is_selected'] = 1;
	            break;
	        }
	    }
	    return $list;
	}
}