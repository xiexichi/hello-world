<?php
error_reporting(E_ALL^E_NOTICE);
define("INPW","MA");
define("BASEURL","m.25boy.cn");
define("Root",dirname(__FILE__));
define('PROMOTE_HTTP', "http://m.25boy.cn/"); //推广链接默认开头,上线后记得把mm改为m,最后的'/'是必须的
$lifeTime = 86400;//session生存时间为一天，以秒为单位
session_set_cookie_params($lifeTime);
session_start();
header("Content-type: text/html; charset=utf-8");
$model_path = "model";
$view_path = "view";
$base_url = constant('BASEURL');
$uid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : 0;

// waf.php 可以有效防护XSS，sql注射，代码执行，文件包含等多种高危漏洞。
include_once($_SERVER['DOCUMENT_ROOT']."/waf.php");

$Error = new Error();
include_once($_SERVER['DOCUMENT_ROOT']."/error.php");

require_once $_SERVER['DOCUMENT_ROOT'].'/log.php';

//初始化日志
$logHandler= new CLogFileHandler($_SERVER['DOCUMENT_ROOT']."/logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);


$Base = new Base();
$Cache = new Cache($_SERVER['DOCUMENT_ROOT']."/cache/system/");
$CKey = 'setting';
$resultCache = $Cache->get($CKey);
if (is_null($resultCache)){
    //$xml = new xml($_SERVER['DOCUMENT_ROOT']."/config.xml");
    $SITECONFIGER = array(
        "db"=>array(
            "host"=>"7c62BzSK9gXSYPh8rUODezZk27nCUpx3wCjcoKGPjw/Vx1l/+aGrQoY/EjeQcrES98ouEoAV1l6kYPn5mz5nBMqKuN16ylFz2muQ",
            "name"=>"9b365KF7jNZ+RWfkKSbwz6jBCd2z2E6rDy+S1L+QRwvp",
            "user"=>"9b365KF7jNZ+RWfkKSbwz6jBCd2z2E6rDy+S1L+QRwvp",
            "password"=>"9b365KF7jNZ+RWfkKXX2nvjJXork3k/0WniRhLnJVizZVR65cI705AzvMZeb"
        ),
        "dbtest"=>array(
            "host"=>"b41dhBQ4E5g3CYbRa0qnqNjZ/4Mrg9/bR5WS/vumBEojo5s/E7KcZq8RJSKeci2II5eMKn9KCJJtrFMD8xrSS0qDpJeqJPwm3g",
            "name"=>"9b365KF7jNZ+RWfkKSbwz6jBCd2z2E6rDy+S1L+QRwvp",
            "user"=>"9b365KF7jNZ+RWfkKSbwz6jBCd2z2E6rDy+S1L+QRwvp",
            "password"=>"9b365KF7jNZ+RWfkKXX2nvjJXork3k/0WniRhLnJVizZVR65cI705AzvMZeb"
        ),

        /*"dbtest"=>array(
            "host"=>"5c428PQylWDANl84Xwxg/FZQMAYddewNP9P9uBk6nU1Mtg4+APY",
            "name"=>"d8ccwmg/jg9mLCIyat2SDilzMlI3cqUAe8Zz7nArBzQw",
            "user"=>"d355HpaHqvUjbYxVSF2Hr9KBywg2do0vOx0WFpYqtg4j",
            "password"=>"d0aedKjHa4jNY6iM5dQFu13bGS+3UnUXTf9XezyNBwYqDBk"
        ),*/
        "wx"=>array(
            "AppID"=>"c30cc66PNpWTfa7hMChsNqb/ZrP/6xdVE4K4C8jOq7idPo4zixc0Xe4h4FdPBXs",
            "AppSecret"=>"b1c76K/l+ZYDtnIYCD2x77MBvk9A2mNg9TTC7OFYnnJOHptiar+sRpOIZyhRF0yDOioztoaGBH/ejKS8Ag",
        ),
        "wxtest"=>array(
            "AppID"=>"1cc4jBcx4hwZsxyiFHJwZ7qV6e0yqFqNEk26+rKLwqcCO5ZdYMQrlkjBbBZrzYk",
            "AppSecret"=>"680497AD7yj6zbU2wmLiQUa+gz2/BSpGCJEZ/wMUMVuYRSHQsMYLpa9lGAPDe4fe9eB+jcfmxumuwXWhig",
        ),
        // 不允许注册的昵称
        'deny_nickname' => array('二五仔','25仔','管理','二五','管理员','额菲尔','银鳞堂','高桥石尚','二五仔男装','YLT','EFE','admin','25boy','hea','hea75','he75','he75denim','hardlyeversga','hardlyevers','hardlyever'),
        'kuaidi'=>array(
            'kuaidi100_appkey' => 'AHobZJqU4870',
            'ship_company' => array('yto'=>'圆通快递','sf'=>'顺丰快递','zto'=>'中通快递','postb'=>'国内邮政小包','ems'=>'EMS','jdkd'=>'京东快递','yt'=>'圆通快递','zt'=>'中通快递'),
        ),
        'sys'=>array(
            'default_depot_id' => 1,
        ),
    );

    $Cache->set($CKey, $SITECONFIGER);
}else{
    $SITECONFIGER = $resultCache;
}

$DB = new mysql();
$Common = new Common();


// 引入系统配置文件
$sysinfo = array();
$sysinfo_path = $_SERVER['DOCUMENT_ROOT'].'/cache/system/sysinfo.php';
if(is_file($sysinfo_path)){
    include($sysinfo_path); // 嵌入配置文件
}else{
    $string = "<?php\n"; 
    $string .= "/*\n由后台生成的配置文件 admin/sysinfo\n"; 
    $string .= "最后更新时间：".date('Y-m-d H:i:s',time())."\n*/\n\n"; 

    $Row = $DB->Get('sysinfo','`field`,`name`,`wap`');
    $Row = $DB->result;
    while($result = $DB->fetch_assoc($Row)){
        $string .= "// {$result ['name']}\n";
        $string .= "\$sysinfo['".$result['field']."'] = '".$result['wap']."';\n\n";
        $sysinfo[$result['field']] = $result['wap'];
    }
    // 写入文件
    file_put_contents($sysinfo_path, $string);
}

// 赋值配置参数
$page_sed_title = '25BOY原创国潮品牌';
$page_title = isset($sysinfo['title']) ? $sysinfo['title'] : '25BOY原创国潮品牌';
$seo_keyword = isset($sysinfo['keys']) ? $sysinfo['keys'] : '潮牌,新国货,男装,国潮,HEA,狮子头,25boy,二五仔';
$seo_desc = isset($sysinfo['desc']) ? $sysinfo['desc'] : '25boy潮牌新国货，7天无理由退换货。复古,古着,原创,中国风T恤，高桥石尚运动服，潮牌与运动完美结合';
$SITECONFIGER['order']['order_auto_close_time'] = $sysinfo['order_auto_close_time'];
$SITECONFIGER['order']['auto_confirm_order_time'] = $sysinfo['auto_confirm_order_time'];
$SITECONFIGER['order']['exchange_timeout'] = $sysinfo['exchange_timeout'];
$SITECONFIGER['info']['serviceTel'] = $sysinfo['serviceTel'];
$SITECONFIGER['info']['tel'] = $sysinfo['tel'];
$SITECONFIGER['info']['qq'] = $sysinfo['qq'];
$SITECONFIGER['info']['address'] = $sysinfo['address'];
$SITECONFIGER['vote']['spac_time'] = $sysinfo['spac_time'];
define('REG_COUPON', $sysinfo['reg_coupon']);   //注册代金券id

// smarty模板
require_once($_SERVER['DOCUMENT_ROOT']."/class/smarty/Smarty.class.php");
$sm = new Smarty;
$sm->cache_dir="./cache/smarty";
$sm->compile_dir="./cache/view";
$sm->debugging = false;
$sm->caching = true;
$sm->cache_lifetime = 86400;
$sm->unmuteExpectedErrors();
$sm->template_dir="./".$view_path;


// 加载模型
function __autoload($classname){
    $classpath=$_SERVER['DOCUMENT_ROOT'].'/class/'.$classname.'.php';
    if(file_exists($classpath)){
        require_once($classpath);
    }else{
        echo ('错误：类文件 '.$classpath.' 不存在 !');
    }
}


//微信jssdk
function is_weixin(){ 
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
    }   
    return false;
}
$signPackage = array();
if(is_weixin()){
    require_once($_SERVER['DOCUMENT_ROOT']."/class/wxJSSDK.php");
    $wxJS = new wxJSSDK();
    $signPackage = $wxJS->GetSignPackage();
}


/*
* 获取分销商
* 保存seller数组，后面页面用到
*/
$is_seller = 0;
if($uid) {
	$result = $DB->query("SELECT * FROM seller s LEFT JOIN seller_level sl ON s.seller_level_id = sl.id WHERE user_id = $uid");
	$seller = $DB->fetch_array($result);
	if(isset($seller) && !empty($seller)) {
		$is_seller = 1;
	}
}


/* **********************************************************************************
 * 获取导购者
 * **********************************************************************************/
$is_promote = 0;
$promote = array();
if($uid) {
    $result = $DB->query("SELECT * FROM promote WHERE user_id = $uid");
    $promote = $DB->fetch_array($result);
    if(empty($promote)) {
        // 自动成为推广者
        $DB->Add('promote',array(
            'user_id'       => $uid,
            'create_time'   => date("Y-m-d H:i:s")
        ));
    }else{
        $is_promote = 1;
    }
}

// print_r($promote);
//是否开启钱包支付功能，分销与普通会员相同
$account_balance = 'hide';
$bagconfig_path = $_SERVER['DOCUMENT_ROOT'].'/../miao/application/config/bagconfig.php';
if(@is_file($bagconfig_path)){
    @include($bagconfig_path); // 嵌入配置文件
    $bagconfig = $config;
    unset($config);
    if(isset($bagconfig['user_account_balance']) && $bagconfig['user_account_balance']==true){
    	$account_balance = 'show';
    }
}

/* **********************************************************************************
 * 获取导购配置文件
 * **********************************************************************************/
$config_path = $_SERVER['DOCUMENT_ROOT'].'/../miao/application/config/promote_config.php';
if(@is_file($config_path)){
    @include($config_path); // 嵌入配置文件
    $promote_config = $config;
    unset($config);
}


/**
* 设置初始值
* @param  val   [变量]
* @param  mixed [初始值]
* @param  bool  [是否返回]
* @param  bool  [表示零值是否需要初始化]
*/
if ( ! function_exists('set_init'))
{
    function set_init(&$data, $val = '', $ifZeroisEmpty = TRUE)
    {
        if (is_array($val)) {
            $data = empty($data) ? $val : $data;
        }elseif (is_int($val)) {
            if ($ifZeroisEmpty)
                $data = empty($data) ? $val : intval(trim($data));
            else 
                $data = empty($data) && $data != '0' ? $val : intval(trim($data));
        }else {
            if ($ifZeroisEmpty) 
                $data = empty($data) ? $val : trim($data);
            else 
                $data = empty($data) && $data != '0' ? $val : trim($data);
        }
        return $data;
    }
}

?>