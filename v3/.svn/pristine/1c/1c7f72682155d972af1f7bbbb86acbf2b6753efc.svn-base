<?php
namespace app\user\validate;

class UserThird extends Base{
	public function add($data){
		$rule = [
			'user_id' => 'require|integer|gt:0',
			'third_type' => 'require|in:weixin,weapp,alipay,qq,sina',
			'openid' => 'require',
			'unionid' => 'require',
			'third_nickname' => 'require',
			'is_follow' => 'in:0,1'
		];
		
		$message = [
			'user_id' => '用户id错误',
			'third_type' => '第三方类型错误',
			'openid' => 'openid错误',
			'unionid' => 'unionid错误',
			'third_nickname' => '第三方昵称错误',
			'is_follow' => '是否关注错误'
		];
		// 返回验证结果
		return $this->validate($rule, $data, $message);
	}

}
