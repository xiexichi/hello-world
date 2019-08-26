<?php
/** 
* 由于25boy帐户余额没有区分基本帐户与赠送余额
* 此类通过流水计算拆分余额，并保存在user_bag表
* @author   文杰
* @version  20180327

规则：
    1. 充值直接累加余额
    2. 消费按期末余额计算消费比率，并保存比率给退款用
    3. 退款必须关联原订单号pay_sn，找回原订单的比率计算退款金额
    4. 旧数据没有关联原订单，则退入基本余额
    5. 扣款按期末余额比例扣除，允许出现负数
    6. 第三方消费goods不处理（充值除外）
    7. 未支付的流水不处理
*/

class Balance {

	private $db;

	public function __construct(){
		// 连接数据库
    	global $DB;
		$this->db = $DB;
	}

	/**
     * 计算期末基本余额
     * @param $bag [array] 单条流水
     * @return [array.base] 期末基本余额
     * @return [array.plus] 期末赠送余额
     * @return [array.ratio] 基本帐户退款比率
     * @return [array.ratio2] 赠送帐户退款比率
     */
    public function ending_balance($bag)
    {
        $ratio = $ratio2 = 0;
        $base = 0;
        $plus = 0;
        $pay_sn = '';    // 多次退款使用，因为是根据user_bag表的pay_sn找出退款比率
        // 由于找回订单时流水信息可能发生变化，所以这个是下面的时间条件
        $find_date = isset($bag['pay_date']) ? $bag['pay_date'] : 0;
        
        // 找回原订单
        $bag = $this->find_origin_order($bag);

        // 期末记录
        $result = $this->db->query("select * from user_bag where user_id={$bag['user_id']} AND create_date<='{$find_date}' order by id desc limit 1");
        $last = $this->db->fetch_array($result);
        isset($last['base_over']) || $last['base_over']=0;
        isset($last['plus_over']) || $last['plus_over']=0;
        isset($last['ratio']) || $last['ratio']=0;
        isset($last['ratio2']) || $last['ratio2']=0;

        // 第三方消费取上一笔期末余额
        if( ($bag['method'] == 'alipay' || $bag['method'] == 'weixin')
            && ($bag['type'] == 'goods' || $bag['type'] == 'ship_fee') )
        {
            return [
                'base' => $last['base_over'],
                'plus' => $last['plus_over'],
                'ratio' => $last['ratio'],
                'ratio2' => $last['ratio2'],
                'pay_sn' => $bag['pay_sn'],
            ];
        }
        
        // 逻辑处理
        switch ($bag['type']) {
            case 'prepaid':
                // 基本帐户余额 = 期末基本金额+发生金额
                $base = $bag['money'] + $last['base_over'];
                // 赠送帐户余额 = 赠送金额+期末赠送余额
                $plus = $bag['plus_price'] + $last['plus_over'];
                // 基本帐户余额 = (期末基本金额/(期末基本余额+期末赠送余额))
                $ratio = @($base/($base+$plus));
                // 赠送帐户余额 = (期末赠送余额/(期末基本余额+期末赠送余额))
                $ratio2 = @($plus/($base+$plus));
                break;

            case 'goods':
            case 'ship_fee':
                $bag['money'] = -abs($bag['money']);    //流水正负不统一
                // 基本帐户退款比率
                // 基本帐户余额 = (期末基本金额/(期末基本余额+期末赠送余额))*发生金额+期末基本金额
                $ratio = @($last['base_over']/($last['base_over']+$last['plus_over']));
                // echo "({$ratio}*{$bag['money']}+{$last['base_over']});";
                $base = ($ratio*$bag['money']+$last['base_over']);
                // 赠送帐户退款比率
                // 赠送帐户余额 = (期末赠送余额/(期末基本余额+期末赠送余额))*发生金额+期末赠送余额
                $ratio2 = @($last['plus_over']/($last['base_over']+$last['plus_over']));
                $plus = $ratio2*$bag['money']+$last['plus_over'];
                break;

            case 'refund':
            	$bag['money'] = abs($bag['money']);

                // 第三方退款
                if( $bag['method'] == 'weixin' || $bag['method'] == 'alipay' ){
                    // o2o分别原路退款，找回订单号
                    $result = $this->db->query("SELECT b.money,ub.* FROM bag b JOIN user_bag ub ON b.pay_sn=ub.pay_sn WHERE b.transaction_id='{$bag['pay_sn']}' ORDER BY bag_id ASC");
                    $payBag = $this->db->fetch_array($result);

                    // 如果原订单为空，则是线上退款
                    if(empty($payBag))
                    {
                        $base = $last['base_over'];
                        $plus = $last['plus_over'];
                        $ratio = $last['ratio'];
                        $ratio2 = $last['ratio2'];
                        $pay_sn = $bag['pay_sn'];
                        break;
                    }

                    $result = $this->db->query("SELECT SUM(abs(money)) as money FROM bag WHERE type='refund' AND pay_sn='{$bag['pay_sn']}' AND pay_date <='{$bag['pay_date']}'");
                    $refunds = $this->db->fetch_array($result);
                 
                    // $a = 减去第三方退款后的期末基本余额
                    // $b = 减去第三方退款后的期末赠送余额
                    // $a_ratio = 减去第三方退款后的基本比率
                    // $b_ratio = 减去第三方退款后的赠送比率
                    // $pay_sn 下次退款以此索引找出退款比率
                    $a = $payBag['money']*$payBag['ratio']-$refunds['money'];
                    $b = $payBag['money']*$payBag['ratio2'];
                    $ratio = ($a/($a+$b));
                    $ratio2 = ($b/($a+$b));
                    $base = $last['base_over'];
                    $plus = $last['plus_over'];
                    $pay_sn = $payBag['pay_sn'];
                }
                else
                {
                    // 取出退款比率
                    $result = $this->db->query("select ratio,ratio2 from user_bag where pay_sn='{$bag['pay_sn']}' order by id desc limit 1");
                    $rs = $this->db->fetch_array($result);
                    $ratio = isset($rs['ratio']) ? $rs['ratio'] : 0;
                    $ratio2 = isset($rs['ratio2']) ? $rs['ratio2'] : 0;
                    // echo "{$bag['money']}*{$ratio}+{$last['base_over']}";
                    if($ratio == 0){
                        // 旧数据退款没有关联订单，退入期末余额
                        $base = $bag['money']+$last['base_over'];
                        $plus = $last['plus_over'];
                    }else{
                        // 基本帐户余额 = 发生金额*退款比率+期末基本余额
                        $base = $bag['money']*$ratio+$last['base_over'];
                        // 赠送帐户余额 = 发生金额*退款比率+期末赠送余额
                        $plus = $bag['money']*$ratio2+$last['plus_over'];
                    }
                }
                break;

            case 'deduct':
            	// 20180510 文杰修改分开扣除基本帐户与赠送帐户余额
                $bag['money'] = abs($bag['money']);
                $bag['plus_price'] = abs($bag['plus_price']);
                $base = @($last['base_over']-$bag['money']);
                $plus = @($last['plus_over']-$bag['plus_price']);
                $ratio = $base/($base+$plus);
                $ratio2 = $plus/($base+$plus);

                /*$bag['money'] = abs($bag['money']+$bag['plus_price']);
                // 扣款按期末余额计算比例
                $ratio = $last['base_over']/($last['base_over']+$last['plus_over']);
                $ratio2 = $last['plus_over']/($last['base_over']+$last['plus_over']);
                $base = @($last['base_over']-$ratio*$bag['money']);
                $plus = @($last['plus_over']-$ratio2*$bag['money']);*/
            	break;

            default:
                # code...
                break;
        }

        return [
            'base' => $base,
            'plus' => $plus,
            'ratio' => $ratio,
            'ratio2' => $ratio2,
            'pay_sn' => $pay_sn,
        ];
    }


    /**
     * 查询记录已经存在
     * @param $bag_id [int] 流水ID
     * @return [bool]
     */
    public function exist_user_bag($bag_id)
    {
        $result = $this->db->query("SELECT * FROM user_bag WHERE bag_id={$bag_id}");
        $count = $this->db->num_rows($result);
        return $count ? true : false;
    }


    /**
     * 添加记录
     * 退款类型必须要有原订单号
     * @param $bag [array] 单条流水记录，必需字段：bag_id,user_id,pay_sn,type,method,money,plus_price,pay_date,pay_status
     * @return [bool]
     */
    public function save_user_bag($bag)
    {
        try {
        	// 钱包变动、第三方退款、第三方充值，以外不需要处理！
            // 2018-04-09取消判断，第三方变动在计算期末余额时取上一笔期末
            /*if( ($bag['method'] == 'alipay' || $bag['method'] == 'weixin') 
                 && $bag['type'] != 'prepaid' && $bag['type'] != 'refund' ){
                return false;
            }*/
            // 未支付不处理
            if( $bag['pay_status'] != 'paid' ){
                return false;
            }

        	// 不能重复添加，已经有记录返回true
            if( $this->exist_user_bag($bag['bag_id']) ){
                return true;
            }

        	// 计算期末余额
            $total = $this->ending_balance($bag);

            // 插入数据库
            $insData = [
            	'base_over' => $total['base'],
                'plus_over' => $total['plus'],
                'ratio' => $total['ratio'],
                'ratio2' => $total['ratio2'],
                'bag_id' => $bag['bag_id'],
                'user_id' => $bag['user_id'],
                'pay_sn' => $total['pay_sn'] ? $total['pay_sn'] : $bag['pay_sn'],
                'create_date' => $bag['pay_date'],
            ];
            $this->db->Add('user_bag', $insData);
            return $this->db->affected_rows();

        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * 获取发票金额 
     * @param $bag [array] 单条流水记录
     * @return $invoiceMoney [float] 发票金额
     */
    public function get_invoice_price($bag_id)
    {
        // 找回原订单
        $bag = $this->find_origin_order($bag_id);
        // 目前知道测试数据不完整，返回0（或者有其他未知情况？）
        if(empty($bag['bag_id'])) return 0;

        // 初始为正数
        $invoiceMoney = isset($bag['money']) ? abs($bag['money']) : 0;

        // 非商户组计算发票金额
        if( $bag['level'] != 12 )
        {
            // 订单指定类型计算发票金额，其他不需要计算
            if( in_array($bag['method'], ['alipay','weixin','cash','card']) || $bag['ratio']==0 ){
                // 第三方支付不需要计算发票金额
                $invoiceMoney = $bag['money'];
            }else if($bag['type'] == 'deduct' ){
                $invoiceMoney = $bag['money'];
            }else{
                $invoiceMoney = $bag['money']*$bag['ratio'];
            }
        }

        // 正负数转换
        if($bag['type'] == 'goods' ||  $bag['type'] == 'ship_fee' || $bag['type'] == 'deduct'){
            $invoiceMoney = -abs($invoiceMoney);
        }else{
            $invoiceMoney = abs($invoiceMoney);
        }


        return round($invoiceMoney, 2);
    }


    /**
     * 线下流水以T开头的退款订单
     * 找回原订单
     **/
    public function find_origin_order($bag)
    {
        // 取流水
        if( !is_array($bag)){
            // 取发票金额
            $bag = $this->db->find("SELECT b.bag_id,b.user_id,b.pay_sn,b.pay_date,b.type,b.money,b.plus_price,b.method,b.transaction_id,ub.base_over,ub.plus_over,ub.ratio,ub.ratio2,u.`level` 
                    FROM bag b LEFT JOIN user_bag ub ON b.bag_id=ub.bag_id 
                    LEFT JOIN users u ON u.user_id=b.user_id
                    WHERE b.bag_id={$bag} LIMIT 1");
        }

        // T 开头的订单，找回原订单
        if( stripos($bag['pay_sn'], 'T')===0 )
        {
            $result = $this->db->query("SELECT * FROM o2o_reorder WHERE reorder_sn='{$bag['pay_sn']}' LIMIT 1");
            $reorder = $this->db->fetch_array($result);
            // o2o退货单，找回原订单
            $result = $this->db->query("SELECT b.bag_id,b.user_id,b.pay_sn,b.pay_date,b.type,b.money,b.plus_price,b.method,b.transaction_id,u.`level` 
                FROM bag b 
                -- LEFT JOIN user_bag ub ON b.bag_id=ub.bag_id 
                LEFT JOIN users u ON u.user_id=b.user_id
                LEFT JOIN o2o_order o ON o.order_sn=b.pay_sn
                WHERE o.order_id={$reorder['order_id']} LIMIT 1");
            $newbag = $this->db->fetch_array($result);
            // 找出最新比率，因为多次退款会有多条记录
            $userBag = array();
            if(!empty($newbag)){
                $result = $this->db->query("SELECT base_over,plus_over,ratio,ratio2 FROM user_bag WHERE pay_sn='{$newbag['pay_sn']}' ORDER BY id DESC LIMIT 1");
                $userBag = $this->db->fetch_array($result);
            }
            // 退款类型
            $newbag['type'] = 'refund';
            $newbag['money'] = $bag['money'];
            $bag = array_merge($newbag, (array)$userBag);
        }

        return $bag;
    }
}

