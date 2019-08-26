<?php

/*
 * 	session 标识码
 *	1  	网站注册页面
 *	2	网站登录页面
 *	3	个人设置页面
 *	31	qq注册页面

 *	33	微博注册页面

*/



//手机验证类

class PhoneCode {
	private $db;
	private $base;

	public function __construct($db,$base) {
		$this->db = $db;
		$this->base = $base;
	}

	/*
	 *	个人页面申请验证码
	 *	3 为 setting页面标识码
	*/
	public function getCode_Setting() {
		//这里要判断是否允许申请验证码,还没写
	    //申请时间
	    $apply_time = isset($_SESSION['apply_time_3']) ? $_SESSION['apply_time_3'] : 0;
	    //过期时间
	    $expire_time = 60;
	    //此刻的时间
	    $now_time = time();

	    //判断重新申请验证码的时限
	    if(empty($apply_time) || $now_time - $apply_time > $expire_time) {

			$phone_code = $this->apply($_POST['phone']);
			if($phone_code > 0) {
				//短信发送成功
				//设置申请手机验证码的时间
				$_SESSION['apply_time_3'] = time();
				$_SESSION['phone_code_3'] = $phone_code;	//手机验证码
				$_SESSION['phone_3'] = $_POST['phone'];	//手机验证码
				echo '1';
			}else {
				echo "-1";
			}

	    }else {
	    	//在规定时间内不允许申请验证码,返回错误码
	    	echo '-42';
	    }


	}


	/*
	 *	注册页面申请验证码
	 *	1 为 网站注册
	*/
	public function get_reg() {
		$this->_get_reg(1);

	}

	/*
	 *	登录页面申请验证码
	 *	2 为 setting页面标识码
	*/
	public function get_login() {
		$this->_get_login(2);
	}


	/*
	 *	第三方：qq注册页面申请验证码
	 *	31 为 qq注册页面标识码
	*/
	public function get_qq_reg() {
		$this->_get_reg(31);

	}

	/*
	 *	第三方：qq登录页面申请验证码
	*/
	public function get_qq_login() {
		//$this->_get_login(32);
	}

	/*
	 *	第三方：weibo注册页面申请验证码
	 *	33 为 weibo注册页面标识码
	*/
	public function get_weibo_reg() {
		$this->_get_reg(33);

	}

	/*
	 *	第三方：weixin注册页面申请验证码
	 *	33 为 weixin注册页面标识码
	*/
	public function get_weixin_reg() {
		$this->_get_reg(35);

	}


	/*
	 *	验证个人页面的验证码
	*/
	public function verifyCode_Settint() {
		$phone_code = isset($_POST["phone_code"]) ? trim($_POST["phone_code"]) : "";
		//服务器判断数据是否为空
		if(empty($phone_code)){echo 'empty';exit();}

		//短信登录，判断验证码是否正确
		$now_time = time();
		$expire_time = 10 * 60;

		//检查验证码
		if($phone_code!=$_SESSION['phone_code_3'] || ($now_time - $_SESSION['apply_time_3'] > $expire_time)) {
		    echo 'wrong_code';
			exit();
		}

		//最后要验证是否更改了手机号码
		if($_POST['phone']!=$_SESSION['phone_3']) {
		    echo 'wrong_phone';
			exit();
		}

		//取出用户资料
		$Table="users";
		$Condition = "where user_id=".$_SESSION["user_id"];
		$set = array(
			'phone' => $_SESSION['phone_3'],
			'flag' => 1
		);
		$result = $this->db->Set($Table,$set,"where user_id=".$_SESSION["user_id"]);
		echo 'success';
	}

	public function verifyCode_Reg() {

	}

	public function verifyCode_Login() {

	}



	/*
	*	申请验证码
	*	ssid 为 session 标识码
	*/
	private function _get_reg($ssid) {
		$phone = isset($_POST["phone"]) ? trim($_POST["phone"]) : "";
		$session_apply_time = 'apply_time_'.$ssid;
		$session_phone_code = 'phone_code_'.$ssid;
		$session_phone = 'phone_'.$ssid;

		//手机格式
		if(!$this->base->is_phone_number($phone)){
		    echo "is_phone";
		    exit;
		}

		//是否占用
		$Table="users";
		$Fileds = "user_id";
		$row = $this->db->GetRs($Table,$Fileds,"where phone='".$phone."'");
		if(!empty($row)) {
		    echo "has_phone";
		    exit();
		}

		//申请手机验证码

	    //申请时间
	    $apply_time = isset($_SESSION[$session_apply_time]) ? $_SESSION[$session_apply_time] : 0;
	    //过期时间
	    $expire_time = 60;
	    //此刻的时间
	    $now_time = time();

	    if(empty($apply_time) || $now_time - $apply_time > $expire_time) {

			$phone_code = $this->apply($phone);
			if($phone_code > 0) {
				//短信发送成功
				//设置session
				$_SESSION[$session_apply_time] = time();
				$_SESSION[$session_phone_code] = $phone_code;	//手机验证码
				$_SESSION[$session_phone] = $phone;	//手机验证码
				echo '1';
			}else {
				echo "-1";
			}

	    }else {
	    	//在规定时间内不允许申请验证码,返回错误码
	    	echo '-42';
	    }

	}


	/*
	*	申请验证码
	*	ssid 为 session 标识码
	*/
	private function _get_login($ssid) {
		$phone = isset($_POST["phone"]) ? trim($_POST["phone"]) : "";
		$session_apply_time = 'apply_time_'.$ssid;
		$session_phone_code = 'phone_code_'.$ssid;
		$session_phone = 'phone_'.$ssid;

		//手机格式
		if(!$this->base->is_phone_number($phone)){
		    echo "is_phone";
		    exit;
		}

		//判断账号是否存在
		$Table="users";
		$Fileds = "user_id";
	    $Condition = "where phone='".$phone."'";
		$row = $this->db->GetRs($Table,$Fileds,$Condition);
		if(empty($row)){
		    echo "nouser";
		    exit;
		}

	    //申请时间
	    $apply_time = isset($_SESSION[$session_apply_time]) ? $_SESSION[$session_apply_time] : 0;
	    //过期时间
	    $expire_time = 60;
	    //此刻的时间
	    $now_time = time();

	    if(empty($apply_time) || $now_time - $apply_time > $expire_time) {

			$phone_code = $this->apply($phone);
			if($phone_code > 0) {
				//短信发送成功
				//设置session
				$_SESSION[$session_apply_time] = time();
				$_SESSION[$session_phone_code] = $phone_code;	//手机验证码
				$_SESSION[$session_phone] = $phone;	//手机验证码
				echo '1';
			}else {
				echo "-1";
			}

	    }else {
	    	//在规定时间内不允许申请验证码,返回错误码
	    	echo '-42';
	    }




	}


	/*
 	 *	申请验证码
 	 * 	短信发送成功,返回手机验证码,发送失败返回错误码
	*/
	private function apply($phone){
		global $Base;

		//随机手机验证码
		$phone_code = mt_rand(100000,999999);

		$param = array($phone_code, 10);
		$res = $Base->ucpaas_sms($phone, $param, 132175);
		if($res){
			return $phone_code;
		}else{
			return false;
		}

	}




}
