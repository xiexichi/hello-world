<?php
namespace app\share\controller;

use app\activity\model\Coupon;
use app\depot\model\Goods;

class ShareActivity extends Base
{
	public function getIndex(){
		$checkData = $this->validate->getIndex(input());
		if (false === $checkData) {
			return errorJson(123, $this->validate->getError());
		}
		
		if(!empty($checkData['type'])){
			$this->model->where('type',$checkData['type']);
		}
		if(!empty($checkData['status'])){
			$this->model->where('status', $checkData['status']);
		}

		$this->model->order('id DESC');
		
		return $this->index();
	}
	public function getindexAfter(){
		foreach ($this->data['items'] as $v){
			$v['type_name']  = $this->model::$mapShareActivityType[$v['type']]['desc'];
			$v['status_name']= $this->model::$mapShareActivityStatus[$v['status']]['desc'];
		}
	}
	
	public function addBefore(){
		if(!$checkData = $this->validate->add(input())){
			return errorJson(123, $this->validate->getError());
		}
		
		//活动类型
		if($checkData['type'] == 1 && empty($checkData['coupon_id'])){
			return errorJson(123, '当前晒图活动类型为赠送优惠券,请选择赠送的优惠券');
		}
		if($checkData['type'] == 2 && empty($checkData['integral'])){
			return errorJson(123, '当前晒图活动类型为赠送积分,请选择赠送的积分');
		}
		if($checkData['type'] == 3 && empty($checkData['good_id'])){
			return errorJson(123, '当前晒图活动类型为赠送商品,请选择赠送的商品');
		}
		//活动时间
		if(strtotime($checkData['start_time']) >= strtotime($checkData['end_time'])){
			return errorJson(123, '开始时间不能大于等于结束时间');
		}
	}
	
	public function editBefore(){
		if(!$checkData = $this->validate->edit(input())){
			return errorJson(123, $this->validate->getError());
		}
		
		//活动类型
		if($checkData['type'] == 1 && empty($checkData['coupon_id'])){
			return errorJson(123, '当前晒图活动类型为赠送优惠券,请选择赠送的优惠券');
		}
		if($checkData['type'] == 2 && empty($checkData['integral'])){
			return errorJson(123, '当前晒图活动类型为赠送积分,请选择赠送的积分');
		}
		if($checkData['type'] == 3 && empty($checkData['good_id'])){
			return errorJson(123, '当前晒图活动类型为赠送商品,请选择赠送的商品');
		}
		//活动时间
		if(strtotime($checkData['start_time']) >= strtotime($checkData['end_time'])){
			return errorJson(123, '开始时间不能大于等于结束时间');
		}
	}
	
	public function setStatus(){
		if(!$checkData = $this->validate->setStatus(input())){
			return errorJson(123, $this->validate->getError());
		}
		$this->model->data($checkData);
		if($this->model->setStatus()){
			return successJson([], '操作成功');
		}
		return errorJson(123, '操作失败');
	}
	
	public function getEditData(){
		if(!$checkData = $this->validate->getEditData(input())){
			return errorJson(123, $this->validate->getError());
		}
		
		//获取晒图活动详情
		$activityData = $this->model->where('id',$checkData['id'])->find();
		if(empty($activityData)){
			return errorJson(123, '找不到当前活动详情');
		}
		
		if($this->model::SHARE_ACTIVITY_TYPE_COUPON == $activityData['type']){//活动类型为【赠送优惠券】
			$activityData['couponInfo'] = (new Coupon())->where('id',$activityData['coupon_id'])->field('id,title')->find();
		}elseif($this->model::SHARE_ACTIVITY_TYPE_GOOD == $activityData['type']){//活动类型为【赠送礼品】
			$tempGoodID = db('share_activity_goods')->where('share_activity_id',$activityData['id'])->value('good_id');
			$activityData['goodInfo'] = [];
			if(!empty($tempGoodID)){
				$activityData['goodInfo'] = (new Goods())->field('id,goods_name')->where('id',$tempGoodID)->find();
			}
		}

		return successJson($activityData);
	}

}
