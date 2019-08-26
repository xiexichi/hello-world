<?php
/**
 * 新版流水
 */
class Flow{

	// 主流水号
	public $flow_sn = '';

	// 钱包余额
	public $bag_total = '';

	// 收款账户类型标识
	public $code = '';

	// 数据库标识
	protected $db;

	// 新版商户ID，默认为1
	protected $merchant_id = 0;

	public function __construct($params = [])
	{
		// 连接数据库
    	global $DB;
		$this->db = $DB;

		// 获取默认商户
		// $this->merchant_id = $this->getDefaultMerchantId();
	}

    /**
     * 新增流水
     */
    public function addFlow($data = [])
    {
    	// 合并支付处理：根据旧版支付模式来处理新版
    	// 旧版是先充后付，新版也是先充后付
    	// 旧版是直接两条消费，新版也是直接两条消费

	    // 流水类型
	    // 1: 充值赠送
	    // 2: 仅充值
	    // 3: 仅赠送
	    // 4: 消费
	    // 5: 退款
	    // 6: 扣款

	    $business_id = '';
	    // 获取默认的商户ID，这里使用25商户
	    $merchant_id = $this->merchant_id;
	    // 要操作的金额
	    $flowMoney = ($data['flow_type_id'] == 5) ? -$this->getMoney($data['money']) : $this->getMoney($data['money']);
	    // 当前时间 
	    $time = $this->gettime();
	    // 支付方式
	    $pay_method_id = $this->getPayMethodId($data['user_id'], $data['method']);

	    // 查找商户id
	    if( ! empty($data['business_code'])) {
	    	$business = $this->getBusinessByCode($data['business_code']);
		    if ( ! empty($business)) {
    		    $business_id = $business['business_id'];

    		    // 如果支付方式为25boy,不需要更新商户ID
    		    // 支付方式为非25boy，更新商户ID
    		    if ( ! empty($business['merchant_id']) && $pay_method_id != 8)
	    		    $merchant_id = $business['merchant_id'];
		    }
	    }

	    // 获取收款账户
	    $code = $data['code'];
    	if ( ! empty($data['client'])) {
    		switch ($data['client']) {
    			case 'weapp':case 'alipay':case 'weixin':
    				$code = $data['client'];
    				break;
    			case 'app':
    				$code = 'app_wx';
    				break;
    		}
    	}
    	$this->code = $code;
    	// 手机网页版使用的公众号/微信h5支付，数据库没有相应的收款信息，所以这里肯定是空收款id
	    $receipt_id = $this->getReceiptId($merchant_id, $data['flow_type_id'], $pay_method_id, $this->code);

		// 流水号，唯一
		$this->flow_sn = $this->createSn();

	    // 主流水
	    $_flow = array(
	        'merchant_id' => $merchant_id,
	        'receipt_id' => $receipt_id,
	        'user_id' => $data['user_id'],
	         // 4=消费 
	        'flow_type_id' => $data['flow_type_id'],
	        // 业务类型 shop/inn/prepaid
	        'profession' => $data['profession'],
	        'pay_status' => 1,
	        // 1=钱包
	        'pay_method_id' => $pay_method_id,
	        'money' => $flowMoney,
	        'flow_sn' => $this->flow_sn,
	        'third_sn' => $data['third_sn'],
	        'desc' => !empty($data['note'])?$data['note']:'',
	        'create_time' => $time,
	        'pay_time' => $time
	    );
	    // print_r($flow_id);exit();
	    $flow_id = $this->insert('flow', $_flow);
	    if ( ! $flow_id) return false;

	    // 根据流水类型处理相应的业务流水
	    // prepaid flow_x
	    $_professionFlow = [
	        'flow_id' => $flow_id,
	        'order_id' => !empty($data['order_id'])?$data['order_id']:'',
	        'business_id' => $business_id,
	    ];

	    // 充值类型
	    if (in_array($data['flow_type_id'], [1, 2, 3])) {
	    	// 更新新版充值表数据
	    	// 更新充值流水
	    	$prepaid = $this->db->GetRs('prepaid' , '*', "WHERE pay_sn = '{$data['pay_sn']}'");
	    	if ( ! empty($prepaid) && empty($prepaid['pay_status'])) {
		    	$_prepaid = [
		    		'third_sn' => $data['third_sn'],
		    		'pay_status' => 1,
		    		'pay_time' => $time,
		    	];
		    	$r = $this->db->Set('prepaid', $_prepaid, array('prepaid_id' => $prepaid['prepaid_id']));
		    	if( ! $r) return false;
	    	}

	    	$_professionFlow['prepaid_id'] = !empty($prepaid['prepaid_id'])?$prepaid['prepaid_id']:'';
	    	unset($_professionFlow['order_id']);
	    }
	    $r = $this->insert($data['flow_profession'], $_professionFlow);
	    if ( ! $r) return false;

	    // 处理钱包流水
	    // 流水类型为：充值(1,2,3)需要处理flow_wallet
	    // 流水类型为：消费或者退款或者扣款，方式为钱包
	    if (in_array($data['flow_type_id'], [1, 2, 3, 6]) || $pay_method_id == 1) {
	    	// 取最后一条user_bag数据
		    $userBag = $this->db->GetRs('user_bag' , '*', "WHERE user_id = '{$data['user_id']}' ORDER BY id DESC");
		    // print_r($userBag);exit();
	        $_wallet = [
	            'flow_id' => $flow_id,
	            // 期末基本余额
	            'base_over' => $this->getMoney($userBag['base_over']),
	            // 期末赠送余额
	            'plus_over' => $this->getMoney($userBag['plus_over']),
	            // 发票金额 = 订单金额 * 基本帐户比率
	            'invoice_price' => $this->getMoney(abs($flowMoney) * $userBag['ratio']),
	            'plus_price' => $this->getMoney($data['plus_price']),
	            'balance' => $this->getMoney($this->bag_total),
	        ];

	        switch ($data['flow_type_id']) {
	        	// 充值赠送或者不赠送，发票金额都是充值金额
	        	case 1:case 2:
	        		$_wallet['invoice_price'] = abs($flowMoney);
	        		break;
	        	// 仅赠送，发票金额为0
	        	case 3:
	        		$_wallet['invoice_price'] = 0;
	        		break;
	        	// 扣款，发票金额为扣款中的基本帐户金额
	        	case 6:
	        		// 这里不用，后台使用的
	        		$_wallet['invoice_price'] = 0;	
	        		break;
	        	default:
	        		break;
	        }
	        $r = $this->insert('flow_wallet', $_wallet);
	        if ( ! $r) return false;
	    }

	    return true;
    }

    /**
     * 新增充值记录
     */
    public function addPrepaid($data = [])
    {
    	$business_code = !empty($data['business_code']) ? $data['business_code'] : '';
    	// 支付方式
    	$pay_method_id = $this->getPayMethodId($data['user_id'], $data['method']);
    	// 商户信息
    	$business_id = $this->getBusinessByCode($business_code, 'business_id');

    	$_prepaid = [
    	    'user_id' => $data['user_id'],
    	    'business_id' => $business_id,
    	    // 2为微信支付
    	    'pay_method_id' => $pay_method_id,
    	    'pay_sn' => $data['pay_sn'],
    	    'money' => $this->setInit($data['money'], 0.0),
    	    'pay_status' => 0,
    	    'third_sn' => '',
    	    'create_time' => $this->gettime(),
    	    'pay_time' => '',
    	];
    	return $this->insert('prepaid', $_prepaid);
    }

    /**
     * 新增数据
     */
    protected function insert($table, $data = [])
    {
    	$this->db->Add($table, $data);
    	return $this->db->insert_id();
    }

    /**
     * 金额统一处理
     */
    protected function getMoney($money)
    {
    	return bcadd(abs($money), 0, 6);
    }

    /**
     * 获取支付方式对应的标识
     */
    public function getPayMethodId($user_id, $method)
    {
    	// 获取用户余额
    	$user = $this->db->GetRs('users', '*', "WHERE user_id = $user_id");
    	// 是否为会员
    	$isUser = ! empty($user['level']) && $user['level'] == 12 ? 0 : 1;
    	// 钱包余额
    	$this->bag_total = ! empty($user['bag_total']) ? $user['bag_total'] : 0;

    	// 支付方式
    	$pay_method_id = '';
    	switch ($method) {
    	    case 'bag'   : $pay_method_id = 1; break;
    	    case 'cash'  : $pay_method_id = 5; break;
    	    case 'card'  : $pay_method_id = 4; break;
    	    // 微信支付=2 微信转账=6
    	    case 'weixin': $pay_method_id = $isUser ? 2 : 6; break;
    	    // 支付宝支付=3 支付宝转账=7
    	    case 'alipay': $pay_method_id = $isUser ? 3 : 7; break;
    	    case '25boy' : $pay_method_id = 8; break;
    	}

    	return $pay_method_id;
    }

    /**
     * 获取收款账户
     * @param $merchant_id 商户id
     * @param $flow_type_id 流水类型
     * @param $pay_method_id 支付方式
     * @param $code 收款账号类型代码
     */
    protected function getReceiptId($merchant_id, $flow_type_id, $pay_method_id, $code = '')
    {
	    switch($flow_type_id) {
	    	// 充值或者消费需要获取receipt_id
	    	case 1:case 2:case 4:
			    // 消费需要code
		    	if ( ! empty($merchant_id) && ! empty($code) && $pay_method_id != 1) {
		        	$result = $this->db->query("
		        		SELECT * FROM merchant_receipt mr 
		        		JOIN merchant_receipt_type mrt ON mr.receipt_type_id = mrt.receipt_type_id
		        		WHERE mr.merchant_id = '$merchant_id' AND mrt.code = '{$code}' LIMIT 1
		        	");
		        	$merchantReceipt = $this->db->fetch_array($result);
		    	}
	    		break;
	    	// 退款直接获取订单流水的receipt_id
	    	case 5:
	    		break;
	    	default:
	    		// 其他没有receipt_id
	    		break;
	    }

	    return !empty($merchantReceipt['receipt_id']) ? $merchantReceipt['receipt_id'] : '';
    }

    /**
     * 根据商户代码获取商户ID
     */
    protected function getBusinessByCode($business_code = '', $field  = '')
    {
    	if (empty($business_code)) return '';

	    $business = $this->db->GetRs('business', '*', "WHERE business_code = '{$business_code}'");    

	    if (empty($field))
	    	return $business;
	    else
	    	return !empty($business[$field]) ? $business[$field] : '';
    }

	/**
	 * 生成一个20位的流水交易号
	 * @param  交易号长度
	 */
    protected function createSn()
    {
    	return date('YmdHis', time()).mt_rand(100000, 999999);
    }

	/**
	 * 获取时间
	 */
    protected function gettime()
    {
    	return date('Y-m-d H:i:s', time());
    }


    /**
    * 设置初始值
    * @param  val   [变量]
    * @param  mixed [初始值]
    * @param  bool  [是否返回]
    * @param  bool  [表示零值是否需要初始化]
    */
    protected function setInit(&$data, $val = '', $ifZeroisEmpty = TRUE)
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

    /**
     * 获取默认商户，有仅只能有一个默认商户，只要供线上使用，测试时为0
     */
    protected function getDefaultMerchantId()
    {
    	$merchant_id = $this->merchant_id;

    	$merchant = $this->db->GetRs('merchant', '*', "WHERE is_default = 1");

	    if ( ! empty($merchant['merchant_id']))
	    	$merchant_id = $merchant['merchant_id'];

	    return $merchant_id;
    }

	/**
	 * 	获取服务商下子商户的收款账户信息
	 */
	public function setMerchantReceiptConfig($data = [], $code = 'weapp')
	{
		// 初始化服务商配置信息为空
		$_REQUEST['mch'] = '';

		// xml表示微信回调进行配置 
		if ( ! empty($data['xml'])) {
			// 将微信回调xml转为数组
			$wxNotifyArr = $this->xmlToArray($data['xml']);
			// 判断是否是服务商模式
			if ( ! empty($wxNotifyArr['sub_mch_id'])) {
				// 服务商appid
				$appid = !empty($wxNotifyArr['appid']) ? $wxNotifyArr['appid'] : '';
				// 服务商商户号
				$mch_id = !empty($wxNotifyArr['mch_id']) ? $wxNotifyArr['mch_id'] : '';
				// 子商户商户号
				$sub_mch_id = !empty($wxNotifyArr['sub_mch_id']) ? $wxNotifyArr['sub_mch_id'] : '';

				if ( ! empty($appid) &&  ! empty($mch_id)) {
					$sql = "SELECT * FROM merchant_receipt mr 
							JOIN merchant_receipt_type mrt ON mr.receipt_type_id = mrt.receipt_type_id
							WHERE mr.appid = '{$appid}' AND mr.mch_id = '{$mch_id}' AND mr.sub_mch_id = '{$sub_mch_id}'
							AND mrt.code = '{$code}' LIMIT 1";
					// pe($sql);
					$merchantReceipt = $this->db->find($sql);
					// pe($merchantReceipt);
				}
			}
		}elseif ( ! empty($data['business_code'])) {
			// 微信支付时进行配置

			$sql = "SELECT * FROM business WHERE business_code = '{$data['business_code']}' LIMIT 1";
			$business = $this->db->find($sql);
			// pe($business);
			// 表示要进行收款账户设置 
			if ( ! empty($business['merchant_id'])) {
				$merchant_id = $business['merchant_id'];
				// pe($merchant_id);
				$sql = "SELECT * FROM merchant_receipt mr 
						JOIN merchant_receipt_type mrt ON mr.receipt_type_id = mrt.receipt_type_id
						WHERE mr.merchant_id = '{$merchant_id}'	AND mrt.code = '{$code}' LIMIT 1";
				$merchantReceipt = $this->db->find($sql);
				// pe($merchantReceipt);
			}
		}elseif ( ! empty($data['online'])) {
			// 线上专用

			// 首先获取默认商户
			$merchant = $this->db->GetRs('merchant', '*', "WHERE is_default = 1");  
			// print_r($merchant);exit();

			if ( ! empty($merchant['merchant_id'])) {
				$merchant_id = $merchant['merchant_id'];
				// pe($merchant_id);
				$sql = "SELECT * FROM merchant_receipt mr 
						JOIN merchant_receipt_type mrt ON mr.receipt_type_id = mrt.receipt_type_id
						WHERE mr.merchant_id = '{$merchant_id}'	AND mrt.code = '{$code}' LIMIT 1";
						// print_r($sql);exit();
				$result = $this->db->query($sql);
				$merchantReceipt = $this->db->fetch_array($result);
				// print_r($merchantReceipt);exit();
			}
		}

		// 设置微信收款配置信息
		if ( ! empty($merchantReceipt)) {
			$_REQUEST['mch'] = [
				'appid' => $merchantReceipt['appid'],
				'secret' => $merchantReceipt['appsecret'],
				'mch_id' => $merchantReceipt['mch_id'],
				'key'   => $merchantReceipt['key'],
				'trade_type'   => 'JSAPI',
				'sign_type'   => 'MD5',
			];
			// print_r($_REQUEST);exit();
			return $merchantReceipt;
		}

		return false;
	}


	/**
	 * 	作用：将xml转为array
	 */
	public function xmlToArray($xml)
	{		
        //将XML转为array      
        try {
	        $array_data = @json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
			return $array_data;
         } catch (Exception $e) {
			return null;          	
         }  
	}
}