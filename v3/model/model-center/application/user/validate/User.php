<?php
namespace app\user\validate;

class User extends Base{
	public function getIndex($data){
		$rule = [
			'user_id' => 'integer',
			'user_name' => 'chsAlphaNum|notOnlyInt|min:2|max:12',
			'phone' => 'mustPhone'
		];
		
		$message = [
			'user_id' => '用户id为正整数',
			'user_name' => '用户名为2~12个字符且不能为纯数字和特殊符号',
			'phone' => '手机号错误'
		];
		// 返回验证结果
		return $this->validate($rule, $data, $message);
	}

	public function getEditData($data){
		$rule = [
			'id' => 'require|integer|gt:0'
		];
		$message = [
			'id' => 'id错误'
		];
		return $this->validate($rule, $data, $message);
	}

	public function register($data){
		$rule = [
			'phone' => 'mustPhone',
			'email' => 'email',
			'user_name' => 'chsAlphaNum|notOnlyInt|min:2|max:12',
			'pass' => 'require|min:6|max:20',
			'pcode' => 'max:4',
			'shop_id' => 'integer',
			'regist_from' => 'require|in:admin,mobile'
		];
		
		$message = [
			'phone' => '请填写正确手机号',
			'email' => '请填写正确邮箱号',
			'user_name.require' => '请填写用户名',
			'user_name' => '用户名最少2个字符,最多12个字符,支持中文、字母、数字，不可以纯数字和任何符号',
			'pass.require' => '请填写密码',
			'pass' => '密码长度为6~20个',
			'pcode' => '请填写正确的验证码',
			'shop_id' => '参数错误',
			'regist_from' => '参数错误'
		];
		// 返回验证结果
		return $this->validate($rule, $data, $message);
	}

	public function editUser($data){
		$rule = [
			'id' => 'require|integer|gt:0',
			'phone' => 'mustPhone',
			'email' => 'email',
			'user_name' => 'chsAlphaNum|notOnlyInt|min:2|max:12',
			'real_name' => 'chsAlpha|min:2|max:20',
			'pass' => 'min:6|max:20',
			'status' => 'in:0,1',
			'gender' => 'in:1,2',
			'birthday' => 'date',
			'remark' => 'max:50',
			'is_rename' => 'in:0,1',
			'country_id' => 'integer',
			'province_id' => 'integer',
			'city_id' => 'integer',
			'region_id' => 'integer'
		];
		
		$message = [
			'id' => '参数错误',
			'phone' => '手机号错误',
			'email' => '邮箱错误',
			'user_name' => '用户名最少2个字符,最多12个字符,不可以纯数字和任何符号',
			'real_name' => '真实姓名为2~20个字符,且为中文或英文',
			'pass' => '密码为6-20位',
			'status' => '状态错误',
			'gender' => '性别错误',
			'birthday' => '生日日期错误',
			'remark' => '备注不能多于50个字符',
			'is_rename' => '参数错误is_rename',
			'country_id' => '国家错误',
			'province_id' => '省错误',
			'city_id' => '市错误',
			'region_id' => '区错误'
		];
		// 返回验证结果
		return $this->validate($rule, $data, $message);
	}

	public function resetPwd($data){
  		$rule = ['user_id' => 'require|integer|gt:0'];
		$message = ['user_id' => '参数错误'];
		
		// 返回验证结果
		return $this->validate($rule, $data, $message);
	}

	public function loginBySimple($data){
		$rule = [
			'account' => 'require',//可能是手机号/邮箱/昵称
			'password' => 'require'
		];
		
		$message = [
			'account' => '请填写账号',
			'password' => '请填写密码'
		];
		// 返回验证结果
		return $this->validate($rule, $data, $message);
	}
	public function loginBySms($data){
		$rule = [
			'account'	=> 'require|mustPhone',
			'pcode'		=> 'require'
		];
		
		$message = [
			'account' => '请正确填写手机号',
			'pcode' => '请填写手机验证码'
		];
		// 返回验证结果
		return $this->validate($rule, $data, $message);
	}
}
