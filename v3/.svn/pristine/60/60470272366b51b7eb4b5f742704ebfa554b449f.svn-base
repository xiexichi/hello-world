<?php
namespace app\share\model;

use app\activity\model\Coupon;

class ShareActivity extends Base{
	protected $autoWriteTimestamp = 'datetime';
	protected $table = 'share_activity';
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';

	// 晒图活动类型，1送券，2送积分，3送礼品
	const SHARE_ACTIVITY_TYPE_COUPON	= 1;
	const SHARE_ACTIVITY_TYPE_INTEGRAL	= 2;
	const SHARE_ACTIVITY_TYPE_GOOD		= 3;
	public static $mapShareActivityType = [
		self::SHARE_ACTIVITY_TYPE_COUPON 	=> ['desc' => '送券'],
		self::SHARE_ACTIVITY_TYPE_INTEGRAL 	=> ['desc' => '送积分'],
		self::SHARE_ACTIVITY_TYPE_GOOD 		=> ['desc' => '送礼品']
	];
	
	//晒图活动状态，1待启动，2启动中，3已关闭（一旦这个表有一个启动，就会将其他启动中的设为关闭）
	const SHARE_ACTIVITY_STATUS_WAIT	= 1;
	const SHARE_ACTIVITY_STATUS_START	= 2;
	const SHARE_ACTIVITY_STATUS_CLOSE	= 3;
	public static $mapShareActivityStatus = [
		self::SHARE_ACTIVITY_STATUS_WAIT	=>	['desc'=>'待启动'],
		self::SHARE_ACTIVITY_STATUS_START	=>	['desc'=>'启动中'],
		self::SHARE_ACTIVITY_STATUS_CLOSE	=>	['desc'=>'已关闭']
	];
	
	public function addAfter(){
		// 晒图活动状态，1待启动，2启动中，3已关闭（一旦这个表有一个启动，就会将其他启动中的设为关闭）
		if($this->data['status'] == 2){
			$this->closeOtherStartingActivity($this->data['id']);
		}
		
		if(self::SHARE_ACTIVITY_TYPE_GOOD == $this->data['type']){
			//追加到晒图活动商品表
			$modelShareActivityGoods = new ShareActivityGoods();
			$modelShareActivityGoods->data(['good_id'=>$this->data['good_id'],'share_activity_id'=>$this->data['id']]);
			if(empty($modelShareActivityGoods->add())){
				$this->isExit = true;
				$this->code = 100000;
				$this->error = '新增商品表失败';
				return false;
			}
		}
		return true;
	}
	
	public function editAfter(){
		// 晒图活动状态，1待启动，2启动中，3已关闭（一旦这个表有一个启动，就会将其他启动中的设为关闭）
		if($this->data['status'] == 2){
			$this->closeOtherStartingActivity($this->data['id']);
		}
		
		//更新到晒图活动商品表
		$modelShareActivityGoods = new ShareActivityGoods();
		$modelShareActivityGoods->where('share_activity_id',$this->data['id'])->delete();
		if(self::SHARE_ACTIVITY_TYPE_GOOD == $this->data['type']){
			$modelShareActivityGoods->data(['good_id'=>$this->data['good_id'],'share_activity_id'=>$this->data['id']]);
			if(empty($modelShareActivityGoods->add())){
				return $this->setErrorAndCodeExit('新增商品表失败', 123);
			}
		}
		return true;
	}
	
	
	
	/**
	 * 将非本id且状态为启动中的晒图活动都设置为关闭状态
	 * @param int $id
	 * @return boolean
	 */
	public function closeOtherStartingActivity(int $id){
		$this->where('id', '<>', $id)->where('status', 2)->setField('status', 3);
		return true;
	}
	
	/**
	 * 设置某活动的状态
	 * @param $this->data['id']
	 * @param $this->data['status']
	 * @return bool
	 */
	public function setStatus():bool{
		if($this->data['status'] == 2){
			$this->closeOtherStartingActivity($this->data['id']);
		}
		if(empty($this->where('id',$this->data['id'])->setField('status',$this->data['status']))){
			return false;
		}
		return true;
	}
	
	/**
	 * 执行活动
	 */
	public function execActivity($userID){
		$activityInfo = $this->getStartingActivity();
		
		//没有启动中且仍在活动期内的晒图活动，就启动系统默认的送积分数
		if(empty($activityInfo)){
			
		}else{//存在启动中且仍在活动期内的晒图活动
			switch ($activityInfo['type']){
				case self::SHARE_ACTIVITY_TYPE_COUPON:
					if (empty($activityInfo['coupon_id'])){
						return $this->setErrorAndCodeExit('找不到要赠送的优惠券id', 123);
					}
					$modelCoupon = new Coupon();
					if(empty($modelCoupon->sand($userID, $activityInfo['id']))){
						return $this->setErrorAndCodeExit($modelCoupon->getError(),$modelCoupon->getCode());
					}
					break;
				case self::SHARE_ACTIVITY_TYPE_INTEGRAL:
					
					
					
					
					
					
					
					
					break;
				case self::SHARE_ACTIVITY_TYPE_GOOD:
					
					
					
					
					
					
					
					break;
				default:
					return $this->setErrorAndCodeExit('晒图活动类型异常', 123);
					break;
			}
			return true;
		}
	}
	
	/**
	 * 找出当前启动中且仍在活动期内的晒图活动
	 * @return unknown
	 */
	public function getStartingActivity(){
		$nowTime = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
		$activityInfo = $this->where('status',2)
							->where('start_time','>=',$nowTime)
							->where('end_time','<=',$nowTime)
							->find();
		return $activityInfo;
	}
	
	
	
}
