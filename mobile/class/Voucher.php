<?php
/**
 * 优惠券类
 */
class Voucher
{

	private $_connport = 'wap';
	protected $user_id;
	// 不需要验证登录状态的方法
	protected $unCheckLoginMethods = ['getVouchers'];
	// 数据库实例
	private $db;

	public function __construct(){
		//验证登录权限
		$this->checkLogin();

		global $DB;
		$this->db = $DB;
	}

	/**
	 * 获取优惠券
	 */
	public function getVoucher()
	{
		$data = $_POST;

		// 测试
		// $this->user_id = 7183;
		// $data = ['voucher_id' => 24];
		// $data = ['code' => 'ogZQYmBi'];
		// pe($data);

		// 获取优惠券信息
		if ( ! empty($data['code'])) {
			$sql = "SELECT *,(CURDATE() > v.end_date) is_end
					FROM voucher_use vu
					JOIN voucher v ON vu.voucher_id = v.voucher_id
					WHERE vu.code = '{$data['code']}' AND vu.voucher_id = '{$data['voucher_id']}'
					LIMIT 1
			";
			// pe($sql);
			$voucher = $this->db->find($sql);
			// pe($voucher);
		}elseif ( ! empty($data['voucher_id'])) {
			$voucher = $this->model->getVoucher($data['voucher_id']);
		}
		if (empty($voucher)) {
			$this->missingParameter(-101, '找不到优惠券信息！');
		}

		if ( ! empty($voucher['code']) && ! empty($voucher['user_id'])) {
			$this->missingParameter(-101, '优惠券已被领取！');
		}
		// pe($voucher);

		// 条件
		// 1.判断用户身份是否可以获取优惠券
		$rs = $this->model->isAllowUserGetVoucher($voucher['voucher_id'], $this->user_id);
		// pe($rs);
		if ($rs['status'] != 'succ') {
			$this->missingParameter(-101, $rs['msg']);
		}

		// 2.判断优惠券是否结束
		if ( ! empty($voucher['is_end'])) {
			$this->missingParameter(-101, '优惠券已结束！');
		}

		// 3.判断优惠券剩余可获取数量
		if ( ! empty($voucher['quantity']) && empty($voucher['code']) && $voucher['received_quantity'] >= $voucher['quantity']) {
			$this->missingParameter(-101, '优惠券数量已被领取完！');
		}
		// pe(111);

		// 4.判断用户是否已到达优惠券规定的每人限领数量
		$qty = $this->model->getUserReceivedQuantity($voucher['voucher_id'], $this->user_id);
		if ($qty >= $voucher['limited_quantity']) {
			$this->missingParameter(-101, "优惠券每人限领{$voucher['limited_quantity']}张，您已全部领取完！");
		}
		// pe($qty);

		// 获取优惠券期限日期
		$term_date = $voucher['term_type'] == 2 ? changeDate($voucher['term_day']) : $voucher['term_date'];

		$insData = [
			'voucher_id' => $voucher['voucher_id'],
			'user_id' => $this->user_id,
			'term_date' => $term_date,
			'get_type' => $this->_connport,
			'create_time' => gettime(),
		];
		// pe($insData);

		if (empty($data['code'])) {
			$rs = $this->model->add('voucher_use', $insData);
		}else {
			$rs = $this->db->update('voucher_use', $insData, ['id' => $voucher['id']]);
		}

		if ( ! $rs) {
			$this->missingParameter(-102, "领取优惠券失败，请稍后重试！");
		}

	    echoAPIJsonData([
	    	'code' => 0,
	    	'msg' => '领取优惠券成功！',
	    	'rs' => [],
	    ]);
	}

	/**
	 * 获取未到期的优惠券列表
	 */
	public function getVouchers()
	{
		$data = $_POST;

		$voucher_id = !empty($data['voucher_id']) ? $data['voucher_id'] : '';
		// $voucher_id = 24;

		$vouchers = $this->model->getVouchers($voucher_id, false);
		echoAPIJsonData([
			'code' => 0,
			'msg' => '',
			'rs' => $vouchers,
		]);
	}

	/**
	 * 获取用户优惠券列表
	 */
	public function getUserVouchers()
	{
		$result = array();
		//订单状态
		$data['status'] = isset($_GET['status']) ? intval($_GET['status']) : '';
		//分页数据
		$data['current_page'] = isset($_GET['pageNo']) ? $_GET['pageNo'] : 1;
		$data['pageSize'] = isset($_GET['rowNo']) ? $_GET['rowNo'] : 10;
		// 获取用户优惠券列表
		$pageData = $this->model->getUserVouchers($this->user_id, $data);

		//返回数据
		$result = array();
		if(count($pageData['data']) > 0){
			$result['code'] = 0;
			$result['msg']  = 'success';	
			$result['rs']   = $pageData;
		}else{
			$result['code'] = -2;
			$result['msg']  = '暂无数据';
		}
		echoAPIJsonData($result);
		exit;
	}

	/**
	 * 以每种券为一组，计算各组能使用的优惠券的优惠组合
	 * 小程序线上订单专用的
	 */
	public function getAvailableVoucherGroup()
	{
		$data = $_POST;
		$pay_total = isset($data['pay_total']) ? $data['pay_total'] : 0;
		$total_quantity = isset($data['total_quantity']) ? $data['total_quantity'] : 0;
		$goods_total = isset($data['goods_total']) ? $data['goods_total'] : 0;

		$vouchers = $this->getAvailableVoucherGroupModel($this->user_id, $pay_total, $total_quantity, $goods_total);

		// print_r($vouchers);exit;
		return[
			'code' => 0,
			'msg' => '',
			'rs' => $vouchers
		];
	}

	/**
	 * 获取优惠券信息，仿model
	 */
	public function getAvailableVoucherGroupModel($user_id, $pay_total, $total_quantity, $goods_total)
	{
		// 条件
		// 1.使用场景,这里肯定是拿关于线下的
		// 2.可用店铺
		// 3.未到期
		// 4.未使用
		$vouchers = $this->getUserAvailableVouchers($user_id);
		// print_r($vouchers);exit;
		if (empty($vouchers)) return [];

		// 将优惠券分组，以voucher_id来分
		$voucherGroup = [];
		foreach ($vouchers as $key => $value) {
			$voucher_id = $value['voucher_id'];
			$voucher_use_id = $value['id'];
			if (empty($voucherGroup[$voucher_id]['items'][$voucher_use_id]))
				$voucherGroup[$voucher_id]['items'][$voucher_use_id] = $value;
			if (empty($voucherGroup[$voucher_id]['rules'])) {
				// 获取优惠券使用规则
				$query = $this->db->query("SELECT * FROM voucher_rule WHERE voucher_id = '{$voucher_id}'");
				$voucherGroup[$voucher_id]['rules'] = [];
				while ($row = $this->db->fetch_array($query)) {
	      	$voucherGroup[$voucher_id]['rules'][] = $row;
	     	}
			}
			if (empty($voucherGroup[$voucher_id]['info'])) {
				// 排除商品
				$query = $this->db->query("SELECT * FROM voucher_goods WHERE voucher_id = '{$voucher_id}'");
				$voucherGroup[$voucher_id]['goods'] = [];
				while ($row = $this->db->fetch_array($query)) {
	      	$voucherGroup[$voucher_id]['goods'][] = $row;
	     	}
				// 券信息
				$voucherGroup[$voucher_id]['info'] = [
					'voucher_type_id' => $value['voucher_type_id'],
					'label' => $value['voucher_label'],
					'title' => $value['title'],
					'voucher_id' => $value['voucher_id'],
					'use_mode' => $value['use_mode'],
					'product_mode' => $value['product_mode'],
				];
			}
		}

		// print_r($voucherGroup);exit;
		$vouchers = [];
		// 计算每个优惠券组别的优惠
		foreach ($voucherGroup as $key => $group) {
			switch ($group['info']['voucher_type_id']) {
				// 满元用券
				case 1:
					$voucher = $this->getType1Vouchers($group, $pay_total);
					break;
				// 现金购物券
				case 2:
					$voucher = $this->getType2Vouchers($group, $pay_total);
					break;
				// 满件折券
				case 3:
					$voucher = $this->getType3Vouchers($group, $goods_total, $total_quantity);
					break;
				default:
					break;
			}
			if ( ! empty($voucher)){
				$goods = [];
				foreach ($group['goods'] as $v) {
					$goods[] = $v['product_id'];
				}
				$voucher['goods'] = $goods;
				$voucher['use_mode'] = $group['info']['use_mode'];
				$voucher['product_mode'] = $group['info']['product_mode'];
				$voucher['voucher_type_id'] = $group['info']['voucher_type_id'];
				$voucher['label'] = $group['info']['label'];
				$voucher['title'] = $group['info']['title'];
				$voucher['voucher_id'] = $group['info']['voucher_id'];
				$vouchers[] = $voucher;
			}
		}
		return $vouchers;
	}

	/**
	 * 检查登录
	 */
	private function checkLogin()
	{
		$m = isset($_GET["m"]) ? trim($_GET["m"]) : '';
		$this->user_id = isset($_SESSION["user_id"]) ? intval($_SESSION["user_id"]) : 0;
		if(!in_array($m, $this->unCheckLoginMethods)) {
			if(empty($this->user_id)) {
				return [
					'code' => -1,
					'msg' => 'nologin',
				];
				exit;
			}
		}
	}

	/**
	 * 获取用户可用的优惠券
	 */
	private function getUserAvailableVouchers($user_id = '') {
		$sql = "
			SELECT
				vu.*, v.title,
				v.not_top,
				v.amount,
				v.full_amount,
				v.use_mode,
				v.product_mode,
				vt.type AS voucher_type,
				concat('券', vu.voucher_id) AS voucher_label,
				concat('#', vu.id) AS voucher_use_label,
				vt.voucher_type_id
			FROM
				voucher_use vu
			JOIN voucher v ON vu.voucher_id = v.voucher_id
			JOIN voucher_type vt ON v.voucher_type_id = vt.voucher_type_id
			WHERE
				vu.term_date >= CURDATE()
				AND v.start_date <= CURDATE()
			AND vu.`status` = 0
			AND v.scenes IN (1,3)
			AND vu.user_id = $user_id
			GROUP BY vu.id
		";
		$query = $this->db->query($sql);
		$result = [];
		while ($row = $this->db->fetch_array($query)) {
    	$result[] = $row;
   	}

		// 获取现金购物券 status=1 但是还有额度的
		$sql = "
			SELECT
				vu.*, v.title,
				v.not_top,
				v.amount,
				v.full_amount,
				vt.type AS voucher_type,
				concat('券', vu.voucher_id) AS voucher_label,
				concat('#', vu.id) AS voucher_use_label,
				vt.voucher_type_id,
				IFNULL(SUM(vud.discount_price), 0) used_quota
			FROM
				voucher_use vu
			JOIN voucher v ON vu.voucher_id = v.voucher_id
			JOIN voucher_type vt ON v.voucher_type_id = vt.voucher_type_id
			LEFT JOIN voucher_use_detail vud ON vu.id = vud.voucher_use_id
			WHERE
				vu.term_date >= CURDATE()
			# 这里一定要搜索已使用的
			AND vu.`status` = 1
			# 这里一定要搜索类型为2
			AND v.voucher_type_id = 2
			AND v.scenes IN (1,3)
			AND vu.user_id = $user_id
			GROUP BY vu.id
			HAVING v.`amount` > used_quota
		";
		$query = $this->db->query($sql);
		$result2 = [];
		while ($row = $this->db->fetch_array($query)) {
    	$result2[] = $row;
   	}
		return array_merge($result, $result2);
	}

	/**
	 * 获取满元用券的优惠信息
	 */
	protected function getType1Vouchers($group = '', $pay_total = '')
	{
		// 累积的实付价
		$payTotal = $pay_total;
		// 累积优惠金额
		$discountTotal = 0;
		// 可用最大数量
		$maxQuantity = 0;
		// 用了多少张券
		$numTotal = 0;
		// 所使用的券标识voucher_use_id
		$voucherUseIds = [];
		// 要返回的数据
		$voucher = [];

		// 获取最大数量
		foreach ($group['rules'] as $k => $rule) {
			if ($payTotal >= $rule['full_amount'])
				$maxQuantity = $rule['max_quantity'];
			else 
				break;
		}

		// 已有优惠券
		foreach ($group['items'] as $k => $voucher_use) {
			if ($numTotal >= $maxQuantity) break;

			$voucherUseIds[] = [
				'voucher_use_id' => $voucher_use['id'],
				'discount_price' => $voucher_use['amount'],
			];
			$numTotal++;
			$discountTotal += $voucher_use['amount'];
			$payTotal -= $voucher_use['amount'];
		}
		if ( ! empty($numTotal)) {
			$voucher = [
				'pay_total' => $payTotal,
				'discount_total' => $discountTotal,
				'num_total' => $numTotal,
				'voucher_use_ids' => $voucherUseIds,
				'original_pay_total' => $pay_total
			];
		}

		return $voucher;
	}

	/**
	 * 获取现金购物券的优惠信息
	 */
	protected function getType2Vouchers($group = '', $pay_total = '')
	{
		// 累积的实付价
		$payTotal = $pay_total;
		// 累积优惠金额
		$discountTotal = 0;
		// 可用最大数量
		$maxQuantity = 1;
		// 用了多少张券
		$numTotal = 0;
		// 所使用的券标识voucher_use_id
		$voucherUseIds = [];
		// 要返回的数据
		$voucher = [];

		// 已有优惠券
		foreach ($group['items'] as $k => $voucher_use) {
			if ($numTotal >= $maxQuantity) break;

			// 获取优惠券剩下可用的额度
			$leftQuota = $this->getLeftAvailableQuota($voucher_use);
			// 如果可用额度为零，则跳过
			if (empty($leftQuota)) continue;

			// 计算优惠金额
			$discount_price = min($pay_total, $leftQuota);

			$voucherUseIds[] = [
				'voucher_use_id' => $voucher_use['id'],
				'discount_price' => $discount_price,
			];
			$numTotal++;
			$discountTotal += $discount_price;
			$payTotal -= $discount_price;
		}

		// pe($numTotal);
		if ( ! empty($numTotal)) {
			$voucher = [
				'pay_total' => $payTotal,
				'discount_total' => $discountTotal,
				'num_total' => $numTotal,
				'voucher_use_ids' => $voucherUseIds,
				'original_pay_total' => $pay_total
			];
		}

		// pe($vouchers);
		return $voucher;
	}

	/**
	 * 获取满件折券
	 */
	protected function getType3Vouchers($group = '', $pay_total = '', $total_quantity = 0)
	{
		// 累积的实付价
		$payTotal = $pay_total;
		// 累积优惠金额
		$discountTotal = 0;
		// 可用最大折扣
		$maxDiscount = 0;
		// 用了多少张券
		$numTotal = 0;
		// 所使用的券标识voucher_use_id
		$voucherUseIds = [];
		// 要返回的数据
		$voucher = [];
		// 打折券只取第一张
		$item = current($group['items']);

		// 获取可用最大折扣
		foreach ($group['rules'] as $k => $rule) {
			if ($total_quantity >= $rule['full_amount']){
				$maxDiscount = floatval($rule['max_quantity']);
				break;
			}
		}

		// 已有优惠券
		if(!empty($maxDiscount)) {
			$payTotal = ($maxDiscount/10)*$payTotal;
			$numTotal = 1;
			$discountTotal = $pay_total-$payTotal;
			$voucherUseIds[] = [
				'voucher_use_id' => $item['id'],
				'discount_price' => $discountTotal,
			];
		}

		if ( ! empty($numTotal)) {
			$voucher = [
				'pay_total' => $payTotal,
				'discount_total' => $discountTotal,
				'num_total' => $numTotal,
				'voucher_use_ids' => $voucherUseIds,
				'original_pay_total' => $pay_total,
			];
		}

		return $voucher;
	}

	/**
	 * 获取现金购物券剩下可用的的额度
	 */
	private function getLeftAvailableQuota($data)
	{
		$sql = "SELECT sum(abs(discount_price)) total 
				FROM voucher_use_detail
				WHERE voucher_use_id = '{$data['id']}'
				AND status = 1";
		$query = $this->db->query($sql);
		$row = $this->db->fetch_array($query);
		$usedQuota = !empty($row['total']) ? floatval($row['total']) : 0;
		return floatval($data['amount']) - $usedQuota;
	}


	/**
	 * 使用优惠券
	 */
	public function useVouchers($voucher = [], $order_sn = '')
	{
		if ( ! empty($voucher['voucher_use_ids'])) {
			foreach ($voucher['voucher_use_ids'] as $key => $value) {
				$voucher_use_id = $value['voucher_use_id'];
				$set = [
					'status' => 1,
					'use_time' => date('Y-m-d H:i:s'),
					'order_sn' => $order_sn,
					'discount_price' => $value['discount_price'],
				];
				$rt = $this->db->Set("voucher_use",$set,"where id=".$voucher_use_id);
				if ( ! $rt) return false;

				// 新增新的记录
				$ins = array(
					'voucher_use_id' => $voucher_use_id,
					'order_sn' => $order_sn,
				    'discount_price' => $value['discount_price'],
				    'create_time' => date('Y-m-d H:i:s')
				);
				// pe($set);
				$this->db->Add("voucher_use_detail",$ins);
				return intval($rt);
			}
		}
		return true;
	}

}
