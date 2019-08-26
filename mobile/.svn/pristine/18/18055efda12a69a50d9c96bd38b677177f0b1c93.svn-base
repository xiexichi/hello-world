<?php
class Activity {
	private $db;

	public function __construct(){
		global $DB;
		$this->db = $DB;
	}

	/**
     * 获取用户可参加的活动
     */
    public function getUserActivity($business_id, $user)
    {
    	// if(empty($user) || empty($business_id)) return [];
        // 正在开展的活动,使用场景，可用店铺，可用会员，可用分销
        if ( ! empty($user['seller_level_id'])) {
            $assoTable = 'activity_seller';
            $level_id = $user['seller_level_id'];
        }else {
            $assoTable = 'activity_user';
            $level_id = isset($user['level']) ? $user['level'] : 0;
        }

        $sql = "select a.*,ax.activity_id,ax.level_id 
        		from activity a 
        		join activity_business ab on a.id = ab.activity_id
        		join {$assoTable} ax on a.id = ax.activity_id AND ax.level_id = '{$level_id}'
        		where a.start_date <= CURDATE() AND a.end_date >= CURDATE() AND scenes <> 1 AND ab.business_id ='{$business_id}'
        		order by priority,id desc";
        $query = $this->db->query($sql);
        $activitys = [];
        while ($row = $this->db->fetch_array($query)) {
        	$activitys[] = $row;
     	}

        // 获取使用规则
		if ( ! empty($activitys)) {
			foreach ($activitys as $key => $activity) {
				$query = $this->db->query("select * from activity_rule where activity_id={$activity['activity_id']}");
		        $rules = [];
		        while ($row = $this->db->fetch_array($query)) {
		        	$rules[] = $row;
		     	}
				if ( ! empty($rules))
					$activitys[$key]['rules'] = $rules;	

				// 活动商品
				// 根据模式来获取不同商品数据
				$table = $activity['goods_mode'] == 1 ? 'activity_goods' : 'activity_exgoods';
				$query = $this->db->query("select * from activity_rule where activity_id={$activity['activity_id']}");
		        $rows = [];
		        while ($row = $this->db->fetch_array($query)) {
		        	$rows[] = $rows;
		     	}
				$goods = [];
				if ( ! empty($rows)) {
					foreach ($rows as $k => $v) {
						$goods[] = $v['product_id'];
					}
				}
				$activitys[$key][$table] = $goods;	

			}
		}
        return (array)$activitys;
    }


    /**
     * 根据商户获取活动
     */
    public function carts_join_activitys($carts, $activitys, $level = [])
    {
		// 计算新的实付价
		if ( ! empty($activitys)) {
			$activityData = [];
			$activityDiscount = 0;
			foreach ($activitys as $key => $activity) {
				$groupTotal = $this->getActivityGroup($activity, $carts);
				// print_r($groupTotal);
				$this->getActivityDiscount($activity, $groupTotal, $carts);
			}
		}

		return $carts;
    }

    /**
	 * 购物车满足活动条件的商品数量
	 * activity_type_id: 1=满元折，2=满元减，3=满件折
	 */
	private function getActivityGroup($activity, $carts)
	{
		$num = 0;
		$total = 0;
		foreach ($carts as $key => $value) {
			switch ($activity['activity_type_id']) {
				case '1':	// 满元折
				case '2':	// 满元减
					$pass = $activity['goods_mode'] == 1 ? 
							in_array($value['product_id'], $activity['activity_goods']) : 
							!in_array($value['product_id'], $activity['activity_exgoods']);

					if($pass){
						$total += $value['product_price']*$value['quantity'];
						$num += $value['quantity'];
					}
					break;
				// 满元减
				case '3':
					$pass = $activity['goods_mode'] == 1 ? 
							in_array($value['product_id'], $activity['activity_goods']) : 
							!in_array($value['product_id'], $activity['activity_exgoods']);

					if($pass){
						$num += $value['quantity'];
						$total += $value['product_price']*$value['quantity'];
					}
					break;
			}
		}

		return [
			'total' => $total,
			'num' => $num,
		];
	}

	/**
	 * 计算活动规则优惠
	 */
	private function getActivityDiscount($activity, $groupTotal, &$carts)
	{
		$result = [
			'discount' => 0,
			'rate' => 0
		];

		$pay_total = $groupTotal['total'];
		$num_total = $groupTotal['num'];
		if($this->payTotal == 0) {
			$this->payTotal = $pay_total;
		}
		if (empty($activity['rules']) || empty($pay_total)) return $result;

		$discountTotal = 0;

		// 计算不同活动优惠
		switch($activity['activity_type_id']) {
			// 满元折
			case 1:
				foreach ($activity['rules'] as $key => $value) {
					// 满足金额
					$full = $value['full'];
					// 优惠数值
					$discount = $value['discount'];
					if (empty($full) || $pay_total >= $full) {
						$discountTotal = $pay_total * (10 - $discount) * 0.1;
					}
				}
				$discountTotal = round($discountTotal, 2);
				$this->discountTotal += $discountTotal;
				break;

			// 满元减
			case 2:
				foreach ($activity['rules'] as $key => $value) {
					// 满足金额
					$full = $value['full'];
					// 优惠数值
					$discount = $value['discount'];
					if (empty($full) || $pay_total >= $full) {
						$discountTotal = $discount;
					}
					// 上不封顶,full不能为0，不然死循环
					if ( ! empty($activity['is_top']) && ! empty($full)) {
						// 从第二遍开始走起
						$i = 2;
						while($pay_total >= ($full * $i)) {
							// 优惠数值
							$discountTotal = $value['discount'] * $i;
							$i++;
						}
						break;
					}
				}
				$discountTotal = round($discountTotal, 2);
				$this->discountTotal += $discountTotal;
				break;

			case '3':
				// 满件折
				foreach ($activity['rules'] as $key => $value) {
					// 满足金额
					$full = $value['full'];
					// 优惠数值
					$discount = $value['discount'];
					if ($num_total >= $full) {
						$discountTotal = $pay_total - ($pay_total * ($discount/10));
					}
				}
				// 总优惠金额
				$discountTotal = round($discountTotal, 2);
				$this->discountTotal += $discountTotal;
				break;

			default:
				// pe('其他折扣方式还没写呢！');
				break;
		}

		// echo ' discountTotal='.$this->discountTotal;

		// 计算折扣加入购物车re_price
		if($discountTotal > 0) {
			foreach ($carts as $key => $val) {
				if($this->inActivityProductIds($activity, $val['product_id'])) {
					$carts[$key]['re_price'] = $val['product_price'] * (($pay_total-$discountTotal)/$pay_total);
					$carts[$key]['activity_id'] = $activity['id'];
					$carts[$key]['activity_title'] = $activity['title'];
					// echo "{$val['product_id']} - {$activity['title']} = ";
					// echo "{$val['product_price']} * (({$pay_total}-{$discountTotal})/{$pay_total})";
					// echo "\n";
				}
			}
		}
	}

	/**
	 * 是否参与活动商品
	 */
	private function inActivityProductIds($activity, $product_id)
	{
		return $activity['goods_mode'] == 1 ? 
			in_array($product_id, $activity['activity_goods']) : 
			!in_array($product_id, $activity['activity_exgoods']);
	}


	/**
	 * 会员组折扣
	 * @param $carts [array] 购物车数据
	 * @param $user [array] 会员信息
	 * @param $org_pay_total [float] 购物车商品总价
	 * @param $total_event_price [float] 店铺活动减免价格
	 */
	public function userLevelDiscount(&$carts, $user, $org_pay_total, $total_event_price)
	{
		$activitys_title = '';

		// 会员组折扣
		if(!empty($user['level'])) {
		    $query = $this->db->query("select * from `level` where id={$user['level']}");
		    $level = $this->db->fetch_array($query);
	        $exgoods = [];
		    if(!empty($level['id'])) {
		        $query = $this->db->query("select * from user_level_exgoods where level_id = {$level['id']}");
		        while ($row = $this->db->fetch_array($query)) {
		            $exgoods[] = $row['product_id'];
		        }
		    }
		    $discount = $level['discount'] / 10;
		    $user_pay_total = $org_pay_total * $discount;
		    // 当会员折扣大于活动时，优先使用会员折扣
		    if($user_pay_total > 0 && $user_pay_total < ($org_pay_total-$total_event_price)) {
		        foreach ($carts as $key => $val)
		        {
		        	if(in_array($val['product_id'], $exgoods))
		        		continue;
		    		$activitys_title = "[{$level['level']}]专享{$level['discount']}折！";
		            $val['re_price'] = $val['product_price'] * $discount;
		            $carts[$key]['re_price'] = sprintf('%.2f', $val['re_price']);
		            $carts[$key]['subtotal'] = sprintf('%.2f', $val['re_price']*$val['quantity']);
		            $carts[$key]['org_subtotal'] = sprintf('%.2f', $val['product_price']*$val['quantity']);
		            $carts[$key]['activity_title'] = $activitys_title;
		        }
		    }
		}

		return $activitys_title;
	}
}
