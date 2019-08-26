<?php
/**
* 新版云ec-erp接口
* time: 2017-04-25
* author: wenjie
* help: http://gop.guanyierp.com/interface/gy.erp.introd.htm
*
* @param appKey:   197316
* @param secret:   78face58245a42a88f2149336e87417d
* @param sessionKey:  69439a2999ca41e093a0c2634e222763
**/


class gyerp {

	public $server_url = 'http://api.guanyierp.com/rest/erp_open';
    public $appkey = '197316';
    public $secret = '78face58245a42a88f2149336e87417d';
    public $sessionKey = '69439a2999ca41e093a0c2634e222763';
    public $shop_code = '25boy';//卖家账号
	
    /*
    * 请求ERP服务器
    * post方式
    */
    public function update_erp_post($post_data){

        //设置post数据
        if(empty($post_data['method'])) return 'method error!';

        // json编码
        $data_string = json_encode($post_data);

        // curl实例
        $curl = curl_init($this->server_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT,60);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length:'.strlen($data_string)
            )
        );
        // 执行命令
        $output = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //关闭URL请求
        curl_close($curl);

        // 警报
        // self::erp_server_warning($httpcode,$post_data['method'],true);

        //显示获得的数据
        return json_decode($output, TRUE);
    }


    // 添加订单
    public function add_order($post_data=array())
    {
        $parameter = array();
        $parameter['appkey']            = $this->appkey;
        $parameter['sessionkey']        = $this->sessionKey;
        $parameter['method']            = 'gy.erp.trade.add';
        $parameter['order_type_code']   = 'Sales';
        $parameter['refund']            = '0';
        $parameter['shop_code']    = $this->shop_code;
        $parameter['express_code']    = empty($post_data['logistics_type']) ? 'YTO' : strtoupper($post_data['logistics_type']);
        $parameter['warehouse_code']    = '001';
        $parameter['vip_code']    = $post_data['mail'];
        $parameter['vip_name']    = $post_data['mail'];
        $parameter['platform_code']    = $post_data['outer_tid'];
        $parameter['receiver_name']    = $post_data['receiver_name'];
        $parameter['receiver_province']    = $post_data['receiver_state'];
        $parameter['receiver_city']    = $post_data['receiver_city'];
        $parameter['receiver_district']    = $post_data['receiver_district'];
        $parameter['receiver_address']    = $post_data['receiver_state'].' '.$post_data['receiver_city'].' '.$post_data['receiver_district'].' '.$post_data['receiver_address'];
        $parameter['receiver_zip']    = isset($post_data['receiver_zip'])?$post_data['receiver_zip']:'';
        $parameter['receiver_mobile'] = isset($post_data['receiver_mobile'])?$post_data['receiver_mobile']:'';//手机。( receiver_phone 、receiver_mobile其中任意一项必填 )
        $parameter['receiver_phone'] = isset($post_data['receiver_phone'])?$post_data['receiver_phone']:'';//固定电话。( receiver_phone 、receiver_mobile其中任意一项必填 )
        $parameter['deal_datetime'] = isset($post_data['deal_datetime']) ? $post_data['deal_datetime'] : date('Y-m-d H:i:s',time());
        $parameter['pay_datetime'] = isset($post_data['pay_datatimes']) ? $post_data['pay_datatimes'] : date('Y-m-d H:i:s',time());
        $parameter['post_fee'] = isset($post_data['logistics_fee'])?$post_data['logistics_fee']:0;
        $parameter['discount_fee'] = isset($post_data['total_discount_fee'])?$post_data['total_discount_fee']:0;
        $parameter['buyer_memo'] = isset($post_data['buyer_message'])?$post_data['buyer_message']:'';
        $parameter['seller_memo'] = isset($post_data['trade_memo'])?$post_data['trade_memo']:'';
        $parameter['payments'] = isset($post_data['payments']) ? $this->filter_null($post_data['payments']) : array();
        // 商品信息
        $details = array();
        if( !empty($post_data['details']) ){
            foreach ($post_data['details'] as $key => $val) {
                $details[] = array(
                    'item_code' => $val['sku_sn'],
                    'sku_code' => $val['sku_prop'],
                    'price' => $val['price'],
                    'qty' => $val['num'],
                    'refund' => 0,
                    'oid' => 'T'.substr($post_data['outer_tid'],-6,6),
                );
            }
        }
        $parameter['details'] = $details;
        // 过滤空值，防止签名错误
        $parameter = array_filter($parameter);
        // 加入签名
        $parameter['sign'] = $this->getSign($parameter);
        // print_r($parameter);exit;
        $result = $this->update_erp_post($parameter);
        // print_r($result);
        
        $this->set_log(1,'add_order()新增订单，已发送去ERP端口',$post_data['outer_tid']);

        if(!empty($result['success'])){
            global $DB;
            $query = $DB->query("SELECT s.id,s.seller_level_id,o.user_id FROM seller s LEFT JOIN orders o ON s.user_id=o.user_id WHERE o.order_sn = '{$post_data['outer_tid']}'");
            $seller = $DB->fetch_array($query);
            if(!empty($seller)){
                $this->tradeMemo($post_data['outer_tid'],'（分销）');
            }
        }

        return $result;
    }


    /*
    * 查询订单状态
    * 返回数组 Array()
    */
    public function get_order_status($order_sn=null, $findAll = false){
        if(!$order_sn) return false;
        
        // 查询erp订单状态
        $parameter = array();
        $parameter['appkey'] = $this->appkey;
        $parameter['sessionkey'] = $this->sessionKey;
        $parameter['method'] = 'gy.erp.trade.get';
        $parameter['platform_code'] = $order_sn;
        $parameter['sign'] = $this->getSign($parameter);
        $result = $this->update_erp_post($parameter);
        // print_r($result);exit;
        $tradeState = array('shenhe'=>0);
        if(!empty($result['orders']) && $result['total'] > 0){
            foreach ($result['orders'] as $key => $val) {
                // 跳过无效的订单
                if( !empty($val['cancle']) ){
                    continue;
                }

                if($findAll){
                    $tradeState = $val;
                }else{
                    $tradeState['shenhe'] = intval($val['approve']);
                }
            }
        }

        return $tradeState;
    }

    // 订单退款状态更新
    public function trade_refund_update($post_data){
        if(empty($post_data['outer_tid'])) return false;

        $parameter = array();
        $parameter['appkey'] = $this->appkey;
        $parameter['sessionkey'] = $this->sessionKey;
        $parameter['method'] = 'gy.erp.trade.refund.update';
        $parameter['shop_code'] = $this->shop_code;
        $parameter['tid'] = $post_data['outer_tid'];                                // 平台单号
        $parameter['oid'] = $post_data['outer_refundid'];                           // 子订单号
        $parameter['refund_state'] = $post_data['refund_state'];                   // 0、取消退款 1、标识退款
        $parameter['sign'] = $this->getSign($parameter);
        $result = $this->update_erp_post($parameter);
        // print_r($result);

        if(isset($result['success']) && $result['success']==1){
            $this->set_log(2,'trade_refund_update()更新退款状态，已发送去ERP端口',$post_data['outer_tid']);
        }
        return $result;
    }


    /**
    * 2017-05-18
    * 添加退款单
    * 云erp不用全退款单，直接更新订单退款状态
    **/
    public function drawback($post_data)
    {

        // 先查询erp销售订单号
        $erp = $this->get_order_status($post_data['outer_tid'], 'all');
        $trade_code = isset($erp['code']) ? $erp['code'] : $post_data['outer_tid'];

        $parameter = array();
        $parameter['appkey'] = $this->appkey;
        $parameter['sessionkey'] = $this->sessionKey;
        $parameter['method'] = 'gy.erp.trade.refund.add';
        $parameter['shop_code'] = $this->shop_code;
        $parameter['refund_code'] = $post_data['outer_refundid'];    // 退款单号
        $parameter['type_code'] = '001';
        $parameter['trade_code'] = $trade_code;                 // 关联销售订单号
        $parameter['vip_code'] = $post_data['mail'];            // 关联的erp销售订单号
        $parameter['payment_type_code'] = isset($post_data['pay_codes'])?$post_data['pay_codes']:'';
        $parameter['amount'] = $post_data['pay_moneys'];
        $parameter['note'] = $post_data['trade_memo'];
        // 退款商品列表 (refund_type为1)必填
        if( empty($post_data['item_detail']) ){
            $parameter['refund_type'] = '0';
        }else{
            $parameter['refund_type'] = '1';
            $parameter['item_detail'] = $this->filter_null($post_data['item_detail']);
        }
        $parameter = $this->filter_null($parameter);
        $parameter['sign'] = $this->getSign($parameter);
        // print_r($parameter);exit;
        $result = $this->update_erp_post($parameter);
        // print_r($result);

        if(isset($result['success']) && $result['success'] && !empty($result['code'])){
            $this->set_log(2,'drawback()新增退款单，已发送去ERP端口',$data['outer_tid']);
        }

        return $result;
    }


    // 添加记录
	public function set_log($erp_type,$erp_text,$order_sn)
    {
    	// include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
		global $DB;
        $data['erp_type'] = $erp_type;
        $data['erp_text'] = $erp_text;
        $data['order_sn'] = $order_sn;
        $data['erp_date'] = date("Y-m-d H:i:s");
        $DB->Add('erp_log', $data);
        return $last_id=$DB->insert_id();
    }


    /**
    * 返回适用于ERP的订单数据
    **/
    public function getOrderForErp($order_id){
        global $DB,$Base;

        // 商品信息
        $goods = $items = array();
        $query = $DB->query("SELECT oi.*,o.`receiver_name`,o.`location`,o.`receiver_phone`,o.`pay_total`,o.business_code,o.client,o.is_issuing FROM order_items oi LEFT JOIN orders o  ON o.order_id = oi.order_id WHERE o.order_id = '{$order_id}'");
        while ($v = $DB->fetch_array($query)) {
            $prop = $DB->GetRs("products","sku_sn","WHERE product_id = '{$v['product_id']}'");
            $row['sku_sn'] = $prop['sku_sn'];
            $row['price'] = $v['price'];
            $row['sku_prop'] = $v['sku_prop'];
            $row['num'] = $v['num'];
            $goods[] = $row;
            $items[] = $v;
        }

        // 不同步线下自提订单
        if(!empty($items[0]['business_code']) && $items[0]['client'] == 'o2o' && $items[0]['is_issuing']==0) {
            return false;
        }

        // 整理商品信息
        $itemsns='';
        $prices='';
        $skusns='';
        $nums='';
        foreach ($goods as $k => $v) {
            $itemsns .= $goods[$k]['sku_sn'];
            $prices .= $goods[$k]['price'];
            $skusns .= $goods[$k]['sku_prop'];
            $nums .= $goods[$k]['num'];
            if (array_key_exists($k + 1, $goods)) {
                $itemsns .= ',';
                $prices .= ',';
                $skusns .= ',';
                $nums .= ',';
                continue;
            } else {
                break;
            }
        }


        // 订单信息
        $sql = 'SELECT o.`user_id`,o.`order_sn`, o.`receiver_name`,o.`location`,o.`receiver_phone`,o.pay_method,o.`pay_total`,o.pay_status,o.order_date,o.pay_date,o.`ship_price`, o.`discount`, o.`seller_note`, o.`buyer_note`, d.`delivery_code`,u.nickname 
                            FROM `orders` AS o 
                            LEFT JOIN `users` as u ON o.user_id=u.user_id
                            LEFT JOIN `delivery` d ON o.delivery_id = d.delivery_id
                            WHERE `order_id`="' . $order_id . '"';
        $query = $DB->query($sql);
        $order = $DB->fetch_array($query);

        // 没有收货地址返回false
        if(empty($order['location'])){
            return false;
        }

        // 支付信息，新版云erp用到
        $payments = array();
        // 2017-06-09停用读取钱包流水记录作为支付明细，因为有的订单同步erp后没有支付明细
        /*if($order['pay_status'] == 1){
            $condition = "WHERE pay_sn='{$order['order_sn']}' AND user_id={$order['user_id']} AND pay_status='paid' AND type='goods'";
            $bag = $DB->GetRs("bag","bag_id,method,pay_date,money,transaction_id,pay_business",$condition);
            if( !empty($bag) ){
                $payments[] = array(
                    'pay_type_code' => $bag['method'],
                    'paytime' => strtotime($bag['pay_date'])*1000,
                    'payment' => abs($bag['money']),
                    'pay_code' => $bag['transaction_id'],
                    'account' => $bag['pay_business']
                );
            }
        }*/

        // 使用订单金额信息作为支付明细
        if($order['pay_method'] == 5){
            $pay_type_code = 'weixin';
        }else if($order['pay_method'] == 1){
            $pay_type_code = 'alipay';
        }else{
            $pay_type_code = 'bag';
        }
        $payments[] = array(
            'pay_type_code' => $pay_type_code,
            'paytime' => strtotime($order['pay_date'])*1000,
            'payment' => $order['pay_total'],
            'pay_code' => $order['order_sn'],
            'account' => ''
        );

        // 整理erp所需数据
        $erp = array();
        $erp['mail'] = $order['nickname'];//买家账号
        $erp['itemsns'] = $itemsns;//商品编码列表：以半角逗号(,)分隔。 //通过订单号获取
        $erp['prices'] = $prices;//商品价格列表：以半角逗号(,)分隔。商品价格个数必须与商品编码个数一致。 //通过订单号获取
        $erp['skusns'] = $skusns;//商品规格：以半角逗号(,)分隔。 //通过订单号获取
        $erp['nums'] = $nums;//商品数量：以半角逗号(,)分隔。商品数量个数必须与商品编码个数一致。 //通过订单号获取
        $erp['receiver_name'] = $order['receiver_name'];//收货人 //通过订单号获取
        $address = explode(',', $order['location']);
        $erp['receiver_address'] = isset($address[3])?$address[3]:'';//收货地址 //通过订单号获取
        $erp['receiver_state'] = $address[0];//省 //通过订单号获取
        $erp['receiver_city'] = $address[1];//市 //通过订单号获取
        $erp['receiver_district'] = isset($address[2])?$address[2]:'';//区 //通过订单号获取
        if($Base->is_phone_number($order['receiver_phone'])){
          $erp['receiver_mobile'] = $order['receiver_phone'];//手机。( receiver_phone 、receiver_mobile其中任意一项必填 )
        }else{
          $erp['receiver_phone'] = $order['receiver_phone'];//电话。( receiver_phone 、receiver_mobile其中任意一项必填 )
        }
        //$erp['receiver_zip'] = 1;//邮编 //通过订单号获取
        $erp['logistics_fee'] = isset($order['ship_price'])?$order['ship_price']:0;   //运费

        $erp['outer_tid'] = $order['order_sn'];//外部订单号
        $erp['buyer_message'] = $order['buyer_note'];//买家留言
        $erp['ticket_no'] = $order['order_sn'];//交易单号
        $erp['pay_codes'] = '004';//支付代码
        $erp['pay_moneys'] = $order['pay_total'];//支付金额
        $erp['total_discount_fee'] = $order['discount'];//让利金额
        $erp['logistics_type'] = empty($order['delivery_code']) ? 'YTO' : $order['delivery_code']; // 配送方式
        $erp['pay_datatimes'] = $order['pay_date'];//支付时间
        $erp['pay_trade_ids'] = $order['order_sn'];     //交易号数组
        $erp['pay_accounts'] = $order['order_sn']; //账号数组
        $erp['pay_memos'] = $order['order_sn']; //支付明细备注数组
        $erp['trade_memo'] = $order['seller_note']; //卖家备注

        // 新版云erp需要参数
        $erp['details'] = $goods;
        $erp['payments'] = $payments;
        $erp['deal_datetime'] = $order['order_date'];

        return $erp;
    }


    /**
     * 2018-01-10 andy 新增
    * 返回适用于o2o代发订单同步ERP的订单数据
    **/
    public function getO2oOrderForErp($order_id){
        global $DB,$Base;

        // 商品信息 （只限于代发订单） o2o_order_join.order_type = issuing
        $goods = $items = array();
        $query = $DB->query("SELECT oi.*,o.`receiver_name`,oj.`location`,o.`receiver_phone`,oj.`pay_total`,o.business_code,oj.client FROM o2o_order_item oi LEFT JOIN o2o_order o ON o.order_id = oi.order_id JOIN o2o_order_join oj ON o.order_id = oj.order_id WHERE o.order_id = '{$order_id}' AND oj.order_type = 'issuing'");
        while ($v = $DB->fetch_array($query)) {
            $prop = $DB->GetRs("products","sku_sn","WHERE product_id = '{$v['product_id']}'");
            $row['sku_sn'] = $prop['sku_sn'];
            $row['price'] = $v['price'];
            $row['sku_prop'] = $v['sku_prop'];
            $row['num'] = $v['quantity'];
            $goods[] = $row;
            $items[] = $v;
        }

        // 如果没有订单数据
        if(empty($goods)){
            return false;
        }

        // 整理商品信息
        $itemsns='';
        $prices='';
        $skusns='';
        $nums='';
        foreach ($goods as $k => $v) {
            $itemsns .= $goods[$k]['sku_sn'];
            $prices .= $goods[$k]['price'];
            $skusns .= $goods[$k]['sku_prop'];
            $nums .= $goods[$k]['num'];
            if (array_key_exists($k + 1, $goods)) {
                $itemsns .= ',';
                $prices .= ',';
                $skusns .= ',';
                $nums .= ',';
                continue;
            } else {
                break;
            }
        }


        // 订单信息
        $sql = 'SELECT o.`user_id`,o.`order_sn`, o.`receiver_name`,oj.`location`,o.`receiver_phone`,oj.pay_method,oj.`pay_total`,oj.pay_status,o.create_date AS order_date,o.pay_date, oj.`discount`, oj.`seller_note`, oj.`buyer_note`, d.`delivery_code`,u.nickname 
                            FROM `o2o_order` AS o 
                            JOIN `o2o_order_join` AS oj ON o.order_id = oj.order_id
                            LEFT JOIN `users` as u ON o.user_id=u.user_id
                            LEFT JOIN `delivery` d ON o.delivery_id = d.delivery_id
                            WHERE o.`order_id`="' . $order_id . '"';
        $query = $DB->query($sql);
        $order = $DB->fetch_array($query);

        // 没有收货地址返回false
        if(empty($order['location'])){
            return false;
        } else {
            // 转换收货地址
            $location = json_decode($order['location'], TRUE);

            if (!is_array($location)) {
                return false;
            }

            // 检测是否缺失地址参数
            $locationKeys = array_keys($location);
            if(array_diff(array('district','address','city','state'), $locationKeys)){
                return false;
            }
        }

        // 支付信息，新版云erp用到
        $payments = array();
        // 2017-06-09停用读取钱包流水记录作为支付明细，因为有的订单同步erp后没有支付明细
        /*if($order['pay_status'] == 1){
            $condition = "WHERE pay_sn='{$order['order_sn']}' AND user_id={$order['user_id']} AND pay_status='paid' AND type='goods'";
            $bag = $DB->GetRs("bag","bag_id,method,pay_date,money,transaction_id,pay_business",$condition);
            if( !empty($bag) ){
                $payments[] = array(
                    'pay_type_code' => $bag['method'],
                    'paytime' => strtotime($bag['pay_date'])*1000,
                    'payment' => abs($bag['money']),
                    'pay_code' => $bag['transaction_id'],
                    'account' => $bag['pay_business']
                );
            }
        }*/

        // 使用订单金额信息作为支付明细
        if($order['pay_method'] == 5){
            $pay_type_code = 'weixin';
        }else if($order['pay_method'] == 1){
            $pay_type_code = 'alipay';
        }else{
            $pay_type_code = 'bag';
        }
        $payments[] = array(
            'pay_type_code' => $pay_type_code,
            'paytime' => strtotime($order['pay_date'])*1000,
            'payment' => $order['pay_total'],
            'pay_code' => $order['order_sn'],
            'account' => ''
        );

        // 整理erp所需数据
        $erp = array();
        $erp['mail'] = $order['nickname'];//买家账号
        $erp['itemsns'] = $itemsns;//商品编码列表：以半角逗号(,)分隔。 //通过订单号获取
        $erp['prices'] = $prices;//商品价格列表：以半角逗号(,)分隔。商品价格个数必须与商品编码个数一致。 //通过订单号获取
        $erp['skusns'] = $skusns;//商品规格：以半角逗号(,)分隔。 //通过订单号获取
        $erp['nums'] = $nums;//商品数量：以半角逗号(,)分隔。商品数量个数必须与商品编码个数一致。 //通过订单号获取
        $erp['receiver_name'] = $order['receiver_name'];//收货人 //通过订单号获取
        $erp['receiver_address'] = $location['address'];//收货地址 //通过订单号获取
        $erp['receiver_state'] = $location['state'];//省 //通过订单号获取
        $erp['receiver_city'] = $location['city'];//市 //通过订单号获取
        $erp['receiver_district'] = $location['district'];//区 //通过订单号获取
        if($Base->is_phone_number($order['receiver_phone'])){
          $erp['receiver_mobile'] = $order['receiver_phone'];//手机。( receiver_phone 、receiver_mobile其中任意一项必填 )
        }else{
          $erp['receiver_phone'] = $order['receiver_phone'];//电话。( receiver_phone 、receiver_mobile其中任意一项必填 )
        }
        //$erp['receiver_zip'] = 1;//邮编 //通过订单号获取
        $erp['logistics_fee'] = isset($order['ship_price'])?$order['ship_price']:0;   //运费

        $erp['outer_tid'] = $order['order_sn'];//外部订单号
        $erp['buyer_message'] = $order['buyer_note'];//买家留言
        $erp['ticket_no'] = $order['order_sn'];//交易单号
        $erp['pay_codes'] = '004';//支付代码
        $erp['pay_moneys'] = $order['pay_total'];//支付金额
        $erp['total_discount_fee'] = $order['discount'];//让利金额
        $erp['logistics_type'] = empty($order['delivery_code']) ? 'YTO' : $order['delivery_code']; // 配送方式
        $erp['pay_datatimes'] = $order['pay_date'];//支付时间
        $erp['pay_trade_ids'] = $order['order_sn'];     //交易号数组
        $erp['pay_accounts'] = $order['order_sn']; //账号数组
        $erp['pay_memos'] = $order['order_sn']; //支付明细备注数组
        $erp['trade_memo'] = $order['seller_note']; //卖家备注

        // 新版云erp需要参数
        $erp['details'] = $goods;
        $erp['payments'] = $payments;
        $erp['deal_datetime'] = $order['order_date'];

        return $erp;
    }

    // 添加o2o订单
    public function add_o2o_order($post_data=array())
    {
        $parameter = array();
        $parameter['appkey']            = $this->appkey;
        $parameter['sessionkey']        = $this->sessionKey;
        $parameter['method']            = 'gy.erp.trade.add';
        $parameter['order_type_code']   = 'Sales';
        $parameter['refund']            = '0';
        $parameter['shop_code']    = $this->shop_code;
        $parameter['express_code']    = empty($post_data['logistics_type']) ? 'YTO' : strtoupper($post_data['logistics_type']);
        $parameter['warehouse_code']    = '001';
        $parameter['vip_code']    = $post_data['mail'];
        $parameter['vip_name']    = $post_data['mail'];
        $parameter['platform_code']    = $post_data['outer_tid'];
        $parameter['receiver_name']    = $post_data['receiver_name'];
        $parameter['receiver_province']    = $post_data['receiver_state'];
        $parameter['receiver_city']    = $post_data['receiver_city'];
        $parameter['receiver_district']    = $post_data['receiver_district'];
        $parameter['receiver_address']    = $post_data['receiver_state'].' '.$post_data['receiver_city'].' '.$post_data['receiver_district'].' '.$post_data['receiver_address'];
        $parameter['receiver_zip']    = isset($post_data['receiver_zip'])?$post_data['receiver_zip']:'';
        $parameter['receiver_mobile'] = isset($post_data['receiver_mobile'])?$post_data['receiver_mobile']:'';//手机。( receiver_phone 、receiver_mobile其中任意一项必填 )
        $parameter['receiver_phone'] = isset($post_data['receiver_phone'])?$post_data['receiver_phone']:'';//固定电话。( receiver_phone 、receiver_mobile其中任意一项必填 )
        $parameter['deal_datetime'] = isset($post_data['deal_datetime']) ? $post_data['deal_datetime'] : date('Y-m-d H:i:s',time());
        $parameter['pay_datetime'] = isset($post_data['pay_datatimes']) ? $post_data['pay_datatimes'] : date('Y-m-d H:i:s',time());
        $parameter['post_fee'] = isset($post_data['logistics_fee'])?$post_data['logistics_fee']:0;
        $parameter['discount_fee'] = isset($post_data['total_discount_fee'])?$post_data['total_discount_fee']:0;
        $parameter['buyer_memo'] = isset($post_data['buyer_message'])?$post_data['buyer_message']:'';
        $parameter['seller_memo'] = isset($post_data['trade_memo'])?$post_data['trade_memo']:'';
        $parameter['payments'] = isset($post_data['payments']) ? $this->filter_null($post_data['payments']) : array();
        // 商品信息
        $details = array();
        if( !empty($post_data['details']) ){
            foreach ($post_data['details'] as $key => $val) {
                $details[] = array(
                    'item_code' => $val['sku_sn'],
                    'sku_code' => $val['sku_prop'],
                    'price' => $val['price'],
                    'qty' => $val['num'],
                    'refund' => 0,
                    'oid' => 'T'.substr($post_data['outer_tid'],-6,6),
                );
            }
        }
        $parameter['details'] = $details;
        // 过滤空值，防止签名错误
        $parameter = array_filter($parameter);
        // 加入签名
        $parameter['sign'] = $this->getSign($parameter);
        // print_r($parameter);exit;
        $result = $this->update_erp_post($parameter);
        // print_r($result);
        
        $this->set_log(1,'add_o2o_order()新增订单，已发送去ERP端口',$post_data['outer_tid']);

        if(!empty($result['success'])){
            global $DB;
            $query = $DB->query("SELECT s.id,s.seller_level_id,o.user_id FROM seller s LEFT JOIN o2o_order o ON s.user_id=o.user_id WHERE o.order_sn = '{$post_data['outer_tid']}'");
            $seller = $DB->fetch_array($query);
            if(!empty($seller)){
                $this->tradeMemo($post_data['outer_tid'],'（分销）');
            }
        }

        return $result;
    }


    /**
     * 修改订单备注
     */
    public function tradeMemo($order_sn, $memo = '')
    {
        if(empty($order_sn)) return false;

        $parameter = array();
        $parameter['appkey'] = $this->appkey;
        $parameter['sessionkey'] = $this->sessionKey;
        $parameter['method'] = 'gy.erp.trade.memo.update';
        $parameter['tid'] = $order_sn;                                // 平台单号
        $parameter['memo'] = $memo;                           // 子订单号
        $parameter['sign'] = $this->getSign($parameter);
        $result = $this->update_erp_post($parameter);
        // print_r($result);

        if(isset($result['success']) && $result['success']==1){
            $this->set_log(2,'tradeMemo()更新退款状态，已发送去ERP端口',$order_sn);
        }
        return $result;
    }


    /**
    * 生成sign签名
    * 首先将请求参数拼接成标准的 json 格式字符串（此时应不包含sign字段及其内容。
    * 建议：先将字符串转为 json 对象，再由对象转为字符串，以此得到格式化后的标准形式），
    * 接着在字符串的首尾加上用户的secret（可在ERP的“控制面板-应用授权-云ERP授权”页面中获取），
    * 最后对其进行MD5（32位大写）加密，得到最终的签名。
    * 签名必须是UTF-8编码。
    * @return string 32位大写MD5加密
    **/
    public function getSign($data) {
        if (empty($data)) {
            return "";
        }
        unset($data['sign']); //可选，具体看传参
        $data = $this->json_encode_ch($data);
        $sign = strtoupper(md5($this->secret . $data . $this->secret));
        return $sign;
    }
    // 编码处理
    function json_encode_ch($arr) {
        return urldecode(json_encode($this->url_encode_arr($arr)));
    }
    // 编码处理
    function url_encode_arr($arr) {
        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
                $arr[$k] = $this->url_encode_arr($v);
            }
        } elseif (!is_numeric($arr) && !is_bool($arr)) {
            $arr = urlencode($arr);
        }
        return $arr;
    }
    // 过滤数组空值
    public function filter_null($arr = array()){
        $newarr = array();
        foreach($arr as $key=>$val){
            if( is_array($val) ){
                $newarr[$key] = $this->filter_null($val);
            } elseif ( $val != '' ){
                $newarr[$key] = $val;
            }
        }
        return $newarr;
    }

}