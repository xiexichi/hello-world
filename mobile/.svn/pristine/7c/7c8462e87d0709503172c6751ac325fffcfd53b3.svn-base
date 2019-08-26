<?php
class Base {
	
	
	public function __construct(){
		
	}

	/*
	 *	通过crypt散列加密密码
	*/
	public function pass_crypt($pass) {
		$salt = '$2a$07$wwbomeyywelcooutocw25onsule$';
		return crypt($pass,$salt);
	}


	/*
	 *	通过password_hash散列加密密码
	 * 	php>5.5.0
	*/
	public function pass_hash($pass) {
		$options  = array(
		     'cost'  => 11 ,
		     'salt'  =>  mcrypt_create_iv (22,MCRYPT_DEV_URANDOM),
		);
		$pass_hash = password_hash($pass,PASSWORD_BCRYPT,$options);
		return $pass_hash;
	}

	/*
	 *	判断密码是否相等
	 *	pass 原生密码
	 *	pass_hash 经过加密后的密码
	*/
	public function pass_verify($pass,$pass_hash) {
		if(password_verify($pass,$hash)) {
			echo '密码正确';
		}else {
			echo "密码错误";
		}
	}

	function ErrorMessage($string){
		echo $string;
	}
	//压缩
	function cp($str){
		return $this->bstr2bin(gzcompress($str));
	}
	//解压缩
	function uncp($str){
		return gzuncompress($this->bin2bstr($str));
	}
	//二转十
	function bin2bstr($input){
		if (!is_string($input)) return null; // Sanity check
		$input = str_split($input, 4);
		$str = '';
		foreach ($input as $v){
			$str .= base_convert($v, 2, 16);
		}
		$str =  pack('H*', $str);
		return $str;
	}
	//十转二
	function bstr2bin($input){
		if (!is_string($input)) return null; // Sanity check
		$value = unpack('H*', $input);
		$value = str_split($value[1], 1);
		$bin = '';
		foreach ($value as $v){
			$b = str_pad(base_convert($v, 16, 2), 4, '0', STR_PAD_LEFT);
			$bin .= $b;
		}
		return $bin;
	}

	//加密
	function encrypt($key, $plain_text) {
		$plain_text = trim($plain_text);
		$iv = substr(md5($key), 0,mcrypt_get_iv_size (MCRYPT_CAST_256,MCRYPT_MODE_CFB));
		$c_t = mcrypt_cfb (MCRYPT_CAST_256, $key, $plain_text, MCRYPT_ENCRYPT, $iv);
		return trim(chop(base64_encode($c_t)));
	}
	//解密
	function decrypt($key, $c_t) {
		$c_t = trim(chop(base64_decode($c_t)));
		$iv = substr(md5($key), 0,mcrypt_get_iv_size (MCRYPT_CAST_256,MCRYPT_MODE_CFB));
		$p_t = mcrypt_cfb (MCRYPT_CAST_256, $key, $c_t, MCRYPT_DECRYPT, $iv);
		return trim(chop($p_t));
	}

	//查找指定值数组,返回数组
	function getArrayValue($array,$key,$id){
		$value=array();
		foreach($array as $val){
			if($val[$id]==$key){
				$value=$val;
			}
		}
		return $value;
	}
	/*时间转换函数*/
	function transTime($ustime){
		$ustime = strtotime($ustime);
		$ytime = date("Y-m-d H:i",$ustime);
		$rtime = date("n月j日 H:i",$ustime);
		$htime = date("H:i",$ustime);
		$time = time() - $ustime;
		$todaytime = strtotime("today");
		$time1 = time() - $todaytime;
		if($time < 60){
			$str = '刚刚';
		}else if($time < 60 * 60){
			$min = floor($time/60);
			$str = $min.'分钟前';
		}else if($time < $time1){
			$str = '今天 '.$htime;
		}else{
			$str = $rtime;
		}
		return $str;
	}

	/*时间转换函数*/
	function FormatTime($ustime,$type = "full",$ai = false){
		$newstrtotime = strtotime($ustime);
		$cur_ytime = date("Y",$newstrtotime);
		$now_ytime = date("Y",time());
		$dis_ytime = $cur_ytime - $now_ytime;
		$ftime = date("Y/m/d H:i:s",$newstrtotime);
		$rtime = date("n月j日 H:i",$newstrtotime);
		$mdtime = date("n月j日",$newstrtotime);
		$hitime = date("H:i",$newstrtotime);
		$ymdtime = date("Y年m月d日",$newstrtotime);
		$ymdtime_nochinese = date("Y-m-d",$newstrtotime);
		$htime = date("H:i",$newstrtotime);
		$hourtime = date("G",$newstrtotime);
		$time = time() - $newstrtotime;

		$month = date("m",$newstrtotime);
		$day = date("d",$newstrtotime);

		//echo $month."+".$day;

		$todaytime = strtotime("today");

		$time1 = time() - $todaytime;

		if($type=="full"){

			$str = $ftime;
		}else if($type=="md"){
			$str = $mdtime;
		}else if($type=="ymd_sign"){
			$str = $ymdtime_nochinese." (".$this->get_zodiac_sign($month, $day).")";
		}else if($type=="ymd"){
			$str = $ymdtime;
		}else if($type=="hi"){
			$str = $hitime;
		}else{
			if($ai == true){
				$zh = "";
				if($time < 60){
					$str = '刚刚';
				}else if($time < 60 * 60){
					$min = floor($time/60);
					$str = $min.'分钟前';
				}else if($time < $time1){
					if($hourtime>=0&&$hourtime<5||$hourtime>=23){
						$zh = "深夜";
					}else if($hourtime>=5&&$hourtime<=6){
						$zh = "清晨";
					}else if($hourtime>6&&$hourtime<11){
						$zh = "上午";
					}else if($hourtime>11&&$hourtime<=13){
						$zh = "中午";
					}else if($hourtime>13&&$hourtime<17){
						$zh = "下午";
					}else if($hourtime>=17&&$hourtime<=19){
						$zh = "傍晚";
					}else if($hourtime>19&&$hourtime<23){
						$zh = "夜晚";
					}
					$str = '今天'.$zh." ".$htime;
				}else{
					if($dis_ytime>0){
						$str = $ftime;
					}else{
						$str = $rtime;
					}
				}
			}else{
				if($dis_ytime>0){
					$str = $ftime;
				}else{
					$str = $rtime;
				}
			}
		}

		
		return $str;
	}

	// 计算剩余时间
	function timeLeft($time1,$time2){
		$second = $time1-$time2;
	    $day = floor($second/(3600*24));
	    $second = $second%(3600*24);	//除去整天之后剩余的时间
	    $hour = floor($second/3600);
	    $second = $second%600;	//除去整小时之后剩余的时间
	    $minute = floor($second/60);
	    $second = $second;	//除去整分钟之后剩余的时间
	    //返回字符串
	    $time = array(
	    	'day'=>$day,
	    	'hour'=>$hour,
	    	'minute'=>$minute,
	    	'second'=>$second,
	    );
	    return $time;
	}

	function get_zodiac_sign($month, $day){
		// 检查参数有效性
		if ($month < 1 || $month > 12 || $day < 1 || $day > 31)
			return (false);
			// 星座名称以及开始日期
		$signs = array(
			array( "20" => "水瓶座"),
			array( "19" => "双鱼座"),
			array( "21" => "白羊座"),
			array( "20" => "金牛座"),
			array( "21" => "双子座"),
			array( "22" => "巨蟹座"),
			array( "23" => "狮子座"),
			array( "23" => "处女座"),
			array( "23" => "天秤座"),
			array( "24" => "天蝎座"),
			array( "22" => "射手座"),
			array( "22" => "摩羯座")
		);
		list($sign_start, $sign_name) = each($signs[(int)$month-1]);
		if ($day < $sign_start)
			list($sign_start, $sign_name) = each($signs[($month -2 < 0) ? $month = 11: $month -= 2]);
		return $sign_name;
	}

	function StringToArray($string="",$reg = ",") {
		$tempstring = $string;
		if($tempstring!=""){
			$tempstring = substr($tempstring, 0, strlen($tempstring)-1);
			return explode($reg, $tempstring);
		}else{
			return array();
		}
	}
	/**    
	* Send http request to back location，需要2次请求    
	*    
	* @param string $alert             the alert message    
	* @param string $url               default is TMConfig::Domain    
	* @return string $content    
	*/     
	function sendAlertBack($alert = "", $url="") {     
		if (!empty($alert)) {     
			$alertstr = "alert('" . $alert . "');";     
		} else {     
			$alertstr = "";     
		}     
		if (empty ($url)) {     
			$gotoStr = "window.history.back();";     
		} else {     
			$gotoStr = "window.location.href='" . $url . "'";     
		}        
		$content = "<script language=javascript>";     
		if (!empty($alertstr)) {     
			$content .= $alertstr;     
		}     
		if($url != "NONE") {     
			$content .= $gotoStr;     
		}     
		$content .= "</script>";     
		return $content;
	}

	/**
	* 字符串截取
	*
	* @param string $str 原始字符串
	* @param int    $len 截取长度（中文/全角符号默认为 2 个单位，英文/数字为 1。
	*                    例如：长度 12 表示 6 个中文或全角字符或 12 个英文或数字）
	* @param bool   $dot 是否加点（若字符串超过 $len 长度，则后面加 "..."）
	* @return string
	*/
	function g_substr($str, $len = 12, $dot = true) {
		$i = 0;
		$l = 0;
		$c = 0;
		$a = array();
		while ($l < $len) {
			$t = substr($str, $i, 1);
			if (ord($t) >= 224) {
				$c = 3;
				$t = substr($str, $i, $c);
				$l += 2;
			} elseif (ord($t) >= 192) {
				$c = 2;
				$t = substr($str, $i, $c);
				$l += 2;
			} else {
				$c = 1;
				$l++;
			}
			// $t = substr($str, $i, $c);
			$i += $c;
			if ($l > $len) break;
			$a[] = $t;
		}
		$re = implode('', $a);
		if (substr($str, $i, 1) !== false) {
			array_pop($a);
			($c == 1) and array_pop($a);
			$re = implode('', $a);
			$dot and $re .= '...';
		}
		return $re;
	}


	// $string： 明文 或 密文  
	// $operation：DECODE表示解密,其它表示加密  
	// $key： 密匙  
	// $expiry：密文有效期  
	function enccode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

		$ckey_length = 4;
		$key = md5($key ? $key : 'letus');  
		                                //abc是key, 自己改改， 不知道这key， 基本逆向解密比较困难
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}


	function js_unescape($str){ 
		$ret = ''; 
		$len = strlen($str); 
		for ($i = 0; $i < $len; $i++){ 
			if ($str[$i] == '%' && $str[$i+1] == 'u'){ 
				$val = hexdec(substr($str, $i+2, 4)); 
				if ($val < 0x7f) $ret .= chr($val); 
				else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f)); 
				else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f)); 
				$i += 5; 
			}else if ($str[$i] == '%'){ 
				$ret .= urldecode(substr($str, $i, 3)); 
				$i += 2; 
			}else $ret .= $str[$i]; 
		} 
		return $ret; 
	}


	// 检查图片url
	function site_img($src){
		if(empty($src)){
			return '/statics/img/user_default_icon.png';
		}

		if (strpos($src, 'http://') === false && strpos($src, 'https://') === false && strpos($src, '//') !== 0) {
    		$src = 'http://www.25boy.cn'.$src;
    	}
    	return $src;
	}

	/**
	 * 获取客户端IP地址
	 * */
	function clientIp(){
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$onlineip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$onlineip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$onlineip = getenv('REMOTE_ADDR');
		} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$onlineip = $_SERVER['REMOTE_ADDR'];
		}
		preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
		$onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
		unset($onlineipmatches);
		return $onlineip;
	}

	/**
	* 得到新订单号
	* @return  string
	*/
	function build_order_no($f=null, $size=6)
	{
		return $f.date('YmdHis').str_pad(mt_rand(1, 99999), $size, '0', STR_PAD_LEFT);
	}

	/**
	* 验证手机号是否正确
	* @param INT $phone
	*/
	function is_phone_number($phone) {
		if (!is_numeric($phone)) {
		    return false;
		}
		// if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){  
		// 11位号码可通过
		if(preg_match("/^\d{11}$/",$phone)){
		    return true;
		}else{  
		    return false;
		}  

		// return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $phone) ? true : false;
	}

	/**
	* 验证E-mail是否正确
	* @param INT $email
	*/
	function is_email($email) {
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) ? FALSE : TRUE;
	}

	/*
	* 返回字符长度
	* 中文2字符
	*/
	function strLenW2($str){
	    return (strlen($str)+mb_strlen($str,'UTF8'))/2;
	 }

	/*
	* UTF-8汉字字母数字下划线正则表达式
	* return @bool
	*/
	function strCheck($str){
		if(!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u",$str)){
	  	// if(preg_match("/[\'.,:;*?~`丶!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$str)){
			return false;
	    }
	    return true;
	}

	//utf-8中文截取，单字节截取模式
    function cn_substr_utf8($str,$length,$append='...',$start=0){
        if(strlen($str)<$start+1){
            return '';
        }
        preg_match_all("/./su",$str,$ar);
        $str2='';
        $tstr='';
        for($i=0;isset($ar[0][$i]);$i++){
            if(strlen($tstr)<$start){
                $tstr.=$ar[0][$i];
            }else{
                if(strlen($str2)<$length + strlen($ar[0][$i])){
                    $str2.=$ar[0][$i];
                }else{
                    break;
                }
            }
        }
        return $str==$str2?$str2:$str2.$append;
    }

    //生成推广链接
	function get_link($promote_id,$type = 2,$item_id = 0) {
	    $main = array('z','o','t','h','f','i','s','v','e','n');
	    $left = $middle = $right = '';
	    foreach (str_split($promote_id) as $v) {
	        $left .= $main[$v];
	    }

	    $middle .= $main[$type];

	    foreach (str_split($item_id) as $v) {
	        $right .= $main[$v];
	    }

	    $delimiter = 'abcdgkmpqruwxy';
	    $d1 = $delimiter[rand(0,13)];
	    $d2 = $delimiter[rand(0,13)];
	    $link = "http://un.25boy.com/{$left}{$d1}{$middle}{$d2}{$right}";
	    return $link;
	}

	//检查模块的权限
    function check_permission($module) {
        if(empty($module)) {
        	echo $this->sendAlertBack('未登录，请登录后操作');
            // header("Location:?m=hd&a=promoteApply");
            exit();
        }
    }


    //码数键值排序
	public function propKsSort($arr = array(),$substr=true) {
        $sort 	 = array(
        				'XS'=>1,'S'=>2,'M'=>3,'L'=>4,'XL'=>5,'XXL'=>6,'3XL'=>7,'4XL'=>8,'5XL'=>9,'6XL'=>10,'7XL'=>11,
		        		"28"=>1,"29"=>2,"30"=>3,"31"=>4,"32"=>5,"33"=>6,"34"=>7,"35"=>8,"36"=>9,"37"=>10,"38"=>11,"39"=>12,"40"=>13,"41"=>14,"42"=>16,"43"=>17,"44"=>17,
		        		"均码"=>1,
        			);
        $sortArr = array();
        foreach ($arr as $key => $value) {
        	$newKey = $key;
        	if($substr == true){
	        	$newKey = substr($key, 1);
        	}
        	if(isset($sort[$newKey])) {
        		$newKey = $sort[$newKey];
        	}
			$sortArr[$newKey] = $value;
        }
        ksort($sortArr);
        return array_values($sortArr);
	}

	/*
	 *  排序
	 *  例子：array('L','M','3XL','XL')
 	 */
	public function sizePorpSort($arr = array()) {
        $sort = array(
        		'XS'=>1,'S'=>2,'M'=>3,'L'=>4,'XL'=>5,'XXL'=>6,'3XL'=>7,'4XL'=>8,'5XL'=>9,'6XL'=>10,'7XL'=>11,
        		"28"=>1,"29"=>2,"30"=>3,"31"=>4,"32"=>5,"33"=>6,"34"=>7,"35"=>8,"36"=>9,"37"=>10,"38"=>11,"39"=>12,"40"=>13,"41"=>14,"42"=>16,"43"=>17,"44"=>17,
        		"均码"=>1,
        		);
        $sortArr = array();
        foreach ($arr as $key => $value) {
        	if(isset($sort[$value])) {
        		$k = $sort[$value];
        	}
			$sortArr[$k] = $value;
        }
        ksort($sortArr);
        return array_values($sortArr);
	}

	//编码
	public function myEncode($data){
		$data = base64_encode($data);
		return preg_replace ('/=/','',$data);
	}   
	 
	 //解码
	public function myDecode($data){
       return intval(base64_decode($data));
	}

	//获得推广完整链接
	public function getPromoteLink($promote_http,$promote_id,$type,$item_id = null) {
      	$pidCode = $this->myEncode($promote_id);
      	switch ($type) {
      		case '0':
				$promote_link = $promote_http."?m=category&a=product&id=$item_id&PI=".$pidCode;
      			break;
      		case '1':
				$promote_link = $promote_http."?m=category&cid=$item_id&PI=".$pidCode;
      			break;
      		case 'redpack':
				$promote_link = $promote_http."?m=hd&a=redpack&PI=".$pidCode;
      			break;
      		default:
				$promote_link = $promote_http."?PI=".$pidCode;
      			break;
      	}
      	return $promote_link;
	}


	public function ucpaas_sms($phone, $param, $templateId){
		//载入ucpass类
        require_once('Ucpaas.php');

        //初始化必填
        $options = array();
        $options['accountsid']='b3c64016b67d021d62078cf7a805c553';
        $options['token']='1c93ba432864332a6d397bd03747c1a5';
        
        //初始化 $options必填
        $ucpass = new Ucpaas($options);
        
        //短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
        $appId = "b2562847c6b5474c9cda85fe70e4f3d8";
        $to = $phone;
        // $templateId = $templateId ? intval($templateId) : 132210;
        $param = is_array($param) ? implode(',', $param) : trim($param);
        $result = $ucpass->templateSMS($appId,$to,$templateId,$param);
        $result = json_decode($result, TRUE);
        if(isset($result['resp']['respCode']) && $result['resp']['respCode'] == "000000"){
            return true;
        }else{
            return false;
        }
	}
}
