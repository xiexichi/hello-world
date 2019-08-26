<?php
namespace app\share\validate;

class ShareActivity extends Base
{

    public function getIndex($data){
        $rule = [
            'type' => 'integer|in:1,2,3',
            'status' => 'integer|in:1,2,3',
            //'start_time' => 'integer'
        ];
        $message = [
            'type' => '参数错误',
            'status' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }
    
    public function add($data){
    	$rule = [
    		'title'		=>	'require|max:30',
    		'content'	=>	'max:200',
    		'status'	=>	'require|in:1,2,3',//晒图活动状态，1待启动，2启动中，3已关闭（一旦这个表有一个启动，就会将其他启动中的设为关闭）
    		'start_time'=>	'require|date',
    		'end_time'	=>	'require|date',
    		'type'		=>	'require|in:1,2,3',//晒图活动类型，1送券，2送积分，3送礼品
    		'good_id'	=>	'integer|gt:0',
    		'integral'	=>	'integer|gt:0',
    		'coupon_id'	=>	'integer|gt:0'
    	];
    	$message = [
    		'title'		=>	'标题必填且在30字内',
    		'content'	=>	'描述在200字内',
    		'status'	=>	'请选择晒图活动状态',//晒图活动状态，1待启动，2启动中，3已关闭（一旦这个表有一个启动，就会将其他启动中的设为关闭）
    		'start_time'=>	'请选择开始日期',
    		'end_time'	=>	'请选择结束日期',
    		'type'		=>	'请选择晒图活动类型',//晒图活动类型，1送券，2送积分，3送礼品
    		'good_id'	=>	'请选择赠送的商品',
    		'integral'	=>	'请填写赠送的积分',
    		'coupon_id'	=>	'请选择赠送的优惠券'
    	];
    	// 返回验证结果
    	return $this->validate($rule, $data,$message);
    }
    
    public function edit($data){
    	$rule = [
    		'id'		=>	'require|integer|gt:0',
    		'title'		=>	'require|max:30',
    		'content'	=>	'max:200',
    		'status'	=>	'require|in:1,2,3',//晒图活动状态，1待启动，2启动中，3已关闭（一旦这个表有一个启动，就会将其他启动中的设为关闭）
    		'start_time'=>	'require|date',
    		'end_time'	=>	'require|date',
    		'type'		=>	'require|in:1,2,3',//晒图活动类型，1送券，2送积分，3送礼品
    		'good_id'	=>	'integer|gt:0',
    		'integral'	=>	'integer|gt:0',
    		'coupon_id'	=>	'integer|gt:0'
    	];
    	$message = [
    		'id'		=>	'参数错误',
    		'title'		=>	'标题必填且在30字内',
    		'content'	=>	'描述在200字内',
    		'status'	=>	'请选择晒图活动状态',//晒图活动状态，1待启动，2启动中，3已关闭（一旦这个表有一个启动，就会将其他启动中的设为关闭）
    		'start_time'=>	'请选择开始日期',
    		'end_time'	=>	'请选择结束日期',
    		'type'		=>	'请选择晒图活动类型',//晒图活动类型，1送券，2送积分，3送礼品
    		'good_id'	=>	'请选择赠送的商品',
    		'integral'	=>	'请填写赠送的积分',
    		'coupon_id'	=>	'请选择赠送的优惠券'
    	];
    	// 返回验证结果
    	return $this->validate($rule, $data,$message);
    }
    
    public function setStatus($data){
    	$rule = [
    		'id'	=>	'require|integer|gt:0',
    		'status'=>	'require|in:2,3'
    	];
    	$message = [
    		'id'	=>	'参数错误',
    		'status'=>	'参数错误'
    	];
    	return $this->validate($rule, $data, $message);
    }
    
    public function getEditData($data){
    	$rule = ['id'=>'require|integer|gt:0'];
    	$message = ['id'=>'参数错误'];
    	return $this->validate($rule, $data, $message);
    }
}