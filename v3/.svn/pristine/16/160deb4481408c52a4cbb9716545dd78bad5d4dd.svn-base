<?php
namespace app\user\controller;

class UserIntegral extends Base
{
	public function getList(){
	    $checkData = $this->validate->getList(input());
	    if (false === $checkData) {
	        return errorJson(123, $this->validate->getError());
	    }
	    
	    if(!empty($checkData['user_id'])){
	        $this->model->where('ui.user_id',$checkData['user_id']);
	    }
	    if(!empty($checkData['user_name'])){
	        $this->model->where('u.user_name','LIKE',"%{$checkData['user_name']}%");
	    }
	    if(!empty($checkData['integral_type_id'])){
	        $this->model->where('ui.integral_type_id',$checkData['integral_type_id']);
	    }
	    
	    $this->model->alias('ui')
	         ->field('ui.*,u.user_name,u.avatar,uit.name AS integral_type_name')
	         ->join((new \app\user\model\User())->getTable().' u','ui.user_id = u.id','LEFT')
	         ->join((new \app\user\model\UserIntegralType())->getTable().' uit','ui.integral_type_id = uit.id', 'LEFT');
	    
	    return $this->index();
	}
}