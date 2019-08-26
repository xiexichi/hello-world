<?php
class Common {
	public function __construct(){}


	// 发放代金券
	function used_coupon($coupon_id,$user_id){
	    GLOBAL $DB;
	    // 查询代金券
	    $nowday = date('Y-m-d',time());
	    $Table="coupon";
	    $Condition = "where coupon_id=".(int)$coupon_id." AND start_date<='".$nowday."' AND exp_date>='".$nowday."'";
	    $coupon = $DB->GetRs($Table,'coupon_id',$Condition);
	    if(!empty($coupon['coupon_id'])){
		    //从数据库取出一张可用代金券
		    $Table="coupon_user";
		    $Condition = "where coupon_id=".(int)$coupon_id." AND user_id=-1 AND coupon_active=0 AND get_date IS NULL";
		    $Fileds = "coupon_user_id,coupon_pws";
		    $rs = $DB->GetRs($Table,$Fileds,$Condition);
		    //如果有代金券，就发给用户
		    if(!empty($rs['coupon_pws'])){
		        $Condition .= " AND coupon_pws='".$rs['coupon_pws']."'";
		        $result = $DB->Set($Table,array("user_id"=>$user_id,'get_date'=>date('Y-m-d H:i:s',time())),$Condition);
		        if($result){
		            return $rs['coupon_pws'];
		        }
		    }
		}
		return false;
	}


	/* 
    * 计算运费模板
    * @varchar      $product_ids    商品ID，多个以,分隔，例: 58x2,59x1
    * @int/array    $address_id     (int)收货人地址ID，(array)收货省/市/区
    * @float        $price          购物车总价
    */
    function get_ship_fee($product_ids, $address_id, $price,$delivery_id = 0){

		global $DB;

		$is_free = 1;

	    /******************************************************************
	        配送方式:顺丰到付不收运费
	    *******************************************************************/
	    if($delivery_id > 0) {
			$row = $DB->GetRs('delivery', '*',"where delivery_id = ".$delivery_id);
			if(isset($row['is_there']) && $row['is_there']) {
				return 0;//配送方式为：到付	
			}
	    }

	    /******************************************************************
	        区分用户(会员、分销商),来判断是否需要运费
	    *******************************************************************/
		$is_seller = 0;
		$seller = $DB->GetRs('seller', '*',"where user_id=" . $_SESSION["user_id"]);
		if($seller) $is_seller = 1;

		//分销商
		if($is_seller) {
			$seller_level = $DB->GetRs('seller_level', '*',"where id=" .$seller['seller_level_id']);
			if($seller_level['is_free']) {
				return 0;  //分销商免运费的情况
			}else {
				$is_free = 0;
			}
		}else {

		    //获取用户资料
		    $Table="users";
		    $Fileds = "level";
		    $Condition = "where user_id=" . $_SESSION["user_id"];
		    $user = $DB->GetRs($Table,$Fileds,$Condition);

		    // 普通用户绑定微信帐号免运费
		    if($user['level'] == 0 && self::bind_weixin($_SESSION['user_id'])===true){
		        return 0;
		    }

		    //vip用户设定是否免运费
		    $vip = $DB->GetRs("level",'*',"where id=" . $user['level']);

		    if(isset($vip)) {
		    	if($vip['is_free']) return 0;	//VIP用户免运费的情况
		    	else $is_free = 0;
		    }

		}


    /******************************************************************
        运费入口
    *******************************************************************/

	    // 查找收货人地址
	    if(is_array($address_id)){
	        $user_area = $address_id;
	    }else{
	        $Table="v_address";
	        $Fileds = "state_id,city_id,district_id";
	        $Condition = "where address_id=".(int)$address_id;
	        $user_area = $DB->GetRs($Table,$Fileds,$Condition);
	        if(!isset($user_area['state_id'])){
	            return -10;
	        }
	    }
	    // print_r($user_area);

	    // 查找商品运费模板
	    $proids = explode(',',$product_ids);
	    $products = array();
	    $total_number = 0;
	    $Table2="products";
	    $Fileds2 = "product_id,delivery_id,weight";
	    foreach ($proids as $val) {
	        $pi = explode('x', $val);
	        $product_id = $pi[0];
	        $Condition2 = "where product_id=".(int)$product_id;
	        $products[$product_id] = $DB->GetRs($Table2,$Fileds2,$Condition2);
	        $products[$product_id]['num'] = $pi[1];
	        $total_number += $pi[1];    // 商品数量
	    }
	    // print_r($products);


	    // 查询运费模板设置
	    $shipSet = array();
	    $total_weight = 0;
	    foreach ($products as $key => $pro) {
	        $total_weight += $pro['weight'];    // 商品重量

	        $sql = "SELECT da.* FROM delivery_area as da 
	            LEFT JOIN delivery_area_region as dar ON da.delivery_areaid=dar.delivery_areaid 
	            WHERE da.delivery_id='".$pro['delivery_id']."' AND dar.area_id IN(".implode(',', $user_area).") LIMIT 1";
	        $query = $products[$product_id] = $DB->query($sql);
	        $shipSet[$pro['delivery_id']] = $DB->fetch_assoc($query);
	    }

	    // 计算运费
	    $price = (int)$price;
	    foreach ($shipSet as $delivery_id => $set) {
	    	$set['free_money'] = (int)$set['free_money'];
	        if(isset($set['fee_mode'])){
	            switch ($set['fee_mode']) {
	                case 'by_weight':   // 按重量：起步价+(进一取整(重量-首重)*续重价)
	                    if(!$is_free || $set['free_money']==0 || $price<$set['free_money']){
	                        $fee['by_weight'] = $set['base_fee']+(ceil($total_weight-1)*$set['step_fee']);
	                    }else{
	                        $fee['by_weight'] = 0;
	                    }
	                    break;
	                
	                case 'by_number':   // 按件数：起步价+进一取整(续件数/总件数)*续件价
	                    if(!$is_free || $set['free_money']==0 || $price<$set['free_money']){
	                    	if($total_number>$set['base_num']){
	                    		$number = $total_number - $set['base_num'];
	                    		$fee['by_number'] = $set['base_num_fee']+ceil($number/$set['step_num'])*$set['step_num_fee'];
	                    	}else{
	                    		$fee['by_number'] = $set['base_num_fee'];
	                    	}
	                    }else{
	                        $fee['by_number'] = 0;
	                    }
	                    break;

	                default:
	                    # code...
	                    break;
	            }
	        }else{
				$fee = 0;
			}

	    }

	    if(is_array($fee)){
	        return min($fee);// 返回最小费用
	    }else{
	        return 0;
	    }
	}


	// 是否绑定微信
	function bind_weixin($user_id){
		if(!$user_id){
			return false;
		}

		global $DB;
		$Table="users";
        $Fileds = "user_id,openid";
        $Condition = "where user_id=".(int)$user_id;
        $data = $DB->GetRs($Table,$Fileds,$Condition);
        if(isset($data['openid']) && $data['openid']){
        	return true;
        }
        return false;
	}



	/**
	 * 判断分类是否是父类,
	 * @param  int $category_id 分类ID
	 * @return   是的 => 查找返回所有子类id  不是 => 直接返回分类ID 
	 */
	function checkCategoryLevle($category_id,$type='article')
	{
		global $DB;

		if($type=='category'){
			$Table = 'category';
			$Condition = " WHERE status=1 AND category_id=".(int)$category_id;
			$Field = 'category_id';
		}else{
			$Table = 'article_cat';
			$Condition = " WHERE status=1 AND article_cid=".(int)$category_id;
			$Field = 'article_cid';
		}
		$Row = $DB->Get($Table,'*',$Condition);
		$Row = $DB->result;
		if($DB->num_rows($Row) > 0 ){
			while($result = $DB->fetch_assoc($Row)){
				$sub_category_query = $DB->Get($Table,$Field,"where status=1 AND parent = ".(int)$category_id." order by sort");
				$sub_category = $DB->result;
				$data = array($category_id);
				while($result = $DB->fetch_assoc($sub_category)){
					$data[] = $result[$Field]; 
				}
			}
			if(empty($data)){
			    return 0;
			}else{
			    return join(',',$data);
			}

		}else{
			return $category_id;
		}
	}

	/**
	 * 检查一个产品是否正在进行秒杀中
	 * @param  int $product_id 产品ID
	 * @return array
	 */
	function check_miao($product_id)
	{
		global $DB;
		$Table="v_miao";
	    $Fileds = "*";
	    $Condition = "where start_date < NOW() AND end_date > NOW() and product_id =".(int)$product_id;
	    $row = $DB->GetRs($Table,$Fileds,$Condition);
	    return $row;
	}

	/**
	 * 检查一个产品是否在进行活动中
	 * @param  int $product_id 产品ID
	 * @return array
	 */
	function check_event($product_id)
	{
		global $DB;
	    $sql = "SELECT * FROM `events` WHERE start_date < NOW() AND end_date > NOW() AND event_id IN (SELECT event_id FROM event_item WHERE product_id = $product_id)";
	    $result = $DB->query($sql);
	    return $result ? $DB->fetch_assoc($result) : '';
	}

	/**
	 * 检查一个产品是否在进行多个活动中
	 * @param  int $product_id 产品ID
	 * @return array
	 */
	function check_events($product_id)
	{
		global $DB;
	    $sql = "SELECT * FROM `events` WHERE start_date < NOW() AND end_date > NOW() AND event_id IN (SELECT event_id FROM event_item WHERE product_id = $product_id)";
	    $result = $DB->query($sql);
		$events = array();
		while($row = $DB->fetch_assoc($result)) {
		    $events[] = $row;
		}
	    return $events;
	}


	// 查询产品是否参加活动
	function isEvent($product_id, $start=false)
	{
		global $DB;
		$Table="v_events";
		$Fileds = "*";
		$Condition = "where end_date > NOW() AND product_id=".(int)$product_id;
		if($start == true){
			$Condition .= " AND start_date < NOW()";
		}
		$sql .= " ORDER BY end_date ASC ";
		$Row = $DB->Get($Table,$Fileds,$Condition,0);
		$events = array();
		while($result = $DB->fetch_assoc($Row)) {
		    $events[] = $result;
		}
	    return $events;
	}

	/*
    * 把活动附到每个单品里
    * $del_product_ids array 踢出活动的产品ID
    */
    function get_product_events($carts, $del_product_ids=array()){
        global $DB;

        $data = array();
        foreach ($carts as $key => $val) {
        	if(!in_array($val['product_id'], $del_product_ids)){
	            $miao_products_ids  = self::get_miao_products(0,true);
	            if($miao_products_ids && in_array($val['product_id'], $miao_products_ids)){
	                $data[$key] = $val;
	                continue;
	            }
	            $event = self::isEvent($val['product_id'],true);
	            $price = $val['miao_price']?$val['miao_price']:$val['product_price'];
	            $val['event'] = isset($event[0])?$event:null;
	        }
            $data[$key] = $val;
        }
        return $data;
    }

    /*
      按活动分组购物车商品
    */
    function event_group($carts){
        $miao_products_ids = self::get_miao_products(0,true);
        $event_group = array();
        foreach ($carts as $cart) {
        	if(isset($cart['prize'])){
                continue; // 排除参与奖励活动的商品
            }
            if(isset($cart['event']) && !empty($cart['event'])){
              if($miao_products_ids && in_array($cart['product_id'], $miao_products_ids)){
                continue; // 排除秒杀不允许叠加商品
              }

              $item = array(
                  'product_id' => $cart['product_id'],
                  'quantity' => $cart['quantity'],
                  'product_price' => $cart['product_price'],
                  'miao_price' => $cart['miao_price'],
              );

              foreach ($cart['event'] as $k => $v) {
                $event_group[$v['event_id']]['event'] = $v;
                $event_group[$v['event_id']]['items'][] = $item;
              }

            }
        }
        return $event_group;
    }


    /*
    * 当前用户所有代金券
    * $total 订单支付总额
    */
    public function get_coupon($total,$cart=null,$hasevents = ''){
    	global $DB;

        $coupon_array = array();
        $today = date('Y-m-d',time());

        //当前用户的所有代金卷 , 不包含无密码的
        $sql = "SELECT c.price,c.price_limit,c.coupon_type ,c.coupon_id,c.coupon_title,c.exp_date,c.not_top FROM coupon c LEFT JOIN coupon_user cu ON cu.coupon_id = c.coupon_id WHERE cu.coupon_active = 0 AND  c.start_date <= '".$today."' AND c.exp_date >= '".$today."' AND cu.user_id = '".(int)$_SESSION['user_id']."' group by c.coupon_id ORDER BY c.price_limit desc";
        $has_pwd_coupon_query = $DB->query($sql);
		$Row = $DB->result;
		while($has_pwd_coupon = $DB->fetch_assoc($Row)){
              if($total >= $has_pwd_coupon['price_limit'] ){
                $coupon_array[] =  array(
                    'coupon_price' => $has_pwd_coupon['price'],
                    'coupon_id'    => $has_pwd_coupon['coupon_id'],
                    'price_limit'  => $has_pwd_coupon['price_limit'],
                    'coupon_type'  => $has_pwd_coupon['coupon_type'],
                    'coupon_title'  => $has_pwd_coupon['coupon_title'],
                    'not_top'  => $has_pwd_coupon['not_top'],
                    'exp_date'  => date('Y-m-d',strtotime($has_pwd_coupon['exp_date'])),
                 );
              }
		}

         //无密码的优惠券
        $sql = "SELECT price,price_limit,coupon_type,quota,coupon_id,coupon_title,exp_date,not_top FROM coupon WHERE start_date <= '".$today."' AND exp_date >= '".$today."' AND is_pws = 0 ";
        $no_pwd_query = $DB->query($sql);
		$Row = $DB->result;
        while($no_pwd = $DB->fetch_assoc($Row)){
            //判断优惠劵的张数是否用完
            $no_pwd_count_query = $DB->query("SELECT coupon_id FROM coupon_user WHERE coupon_id = '".$no_pwd['coupon_id']."'");
            $no_pwd_count  = $DB->num_rows($DB->result);
            if($no_pwd['quota'] > $no_pwd_count && $total >= $no_pwd['price_limit']){
                 $coupon_array[] =  array(
                    'coupon_price' => $no_pwd['price'],
                    'price_limit'  => $no_pwd['price_limit'],
                    'coupon_id' => $no_pwd['coupon_id'],
                    'coupon_type'  => $no_pwd['coupon_type'],
                    'coupon_title'  => $no_pwd['coupon_title'],
                    'not_top'  => $no_pwd['not_top'],
                    'exp_date'  => date('Y-m-d',strtotime($no_pwd['exp_date'])),
                 );
            }
        }

        $coupon_arr = $this->array_sort($coupon_array,'price_limit','desc');


        // 排除商品
        foreach ($coupon_arr as $key => $coupon) {
          $excgoods = self::get_coupon_excgoods($coupon['coupon_id']);
          $excgoods_price = 0;
          foreach ($cart as $val) {
            if(in_array($val['product_id'], $excgoods)){
              $amount = ($val['miao_price']?$val['miao_price']:$val['product_price'])*$val['quantity'];
              if(isset($val['event']['event_id'])){
                $group = self::get_event_price_one($val['event']['event_id'],$amount,$val['quantity']);
                $excgoods_price += $amount-$group['price'];
              }else{
                $excgoods_price += $amount;
              }
            }
          }
          if($coupon['price_limit'] > $total-$excgoods_price){
            unset($coupon_arr[$key]);
          }
        }



        $new_coupon = array();

        foreach ($coupon_arr as $coupon) {
        	if($hasevents == 'yes' && $coupon['coupon_type'] == 'C') {
        		//有活动时C代金券不可用
        	}else {
     			$new_coupon[$coupon['coupon_id']] = $coupon;
        	}
   
        }
        
        return $new_coupon;
    }

    /*
      取秒杀中的单品
      $plus=0 不可叠加 
      $plus=1 可叠加 
    */
    function get_miao_products($plus=0, $return_array=false){
    	global $DB;

		$sql = "SELECT mi.product_id FROM miao_item mi LEFT JOIN miao m ON m.miao_id = mi.miao_id WHERE mi.plus=".(int)$plus." AND m.start_date < NOW() AND m.end_date > NOW() ";
		$query = $DB->query($sql);
		$Row = $DB->result;
		$id_array = array();
		while($result = $DB->fetch_assoc($Row)){
			$id_array[] = $result;
		}

		$ids = array();
		foreach ($id_array as $id) {
		   array_push($ids, $id['product_id']);
		}
		if(empty($ids)){
		    return 0;
		}else{
		  if($return_array == true){
		    return $ids;
		  }else{
		    return join(',',$ids);
		  }
		}
    }

    function array_sort($arr, $keys, $type = 'asc') {

        $keysvalue = $new_array = array();

        foreach ($arr as $k => $v) {

            $keysvalue[$k] = $v[$keys];
        }

        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }

        reset($keysvalue);

        foreach ($keysvalue as $k => $v) {

            $new_array[] = $arr[$k];
        }

        return $new_array;
    }


    // 计算代金券后价格
    function get_coupon_price($coupon_id, $cart_ids, $sub_total){
    	global $DB;

    	$result = array('ms_status'=>'error','cut_price'=>0);
    	$today = date('Y-m-d',time());

    	$cart_product = self::carts($cart_ids);
	    $cart_product = self::get_product_events($cart_product);
    	$products_num = 0;
    	$total_price = 0;
    	foreach ($cart_product as $key => $res) {
    		$products_num += $res["quantity"];
		    $total_price += $res['price']*$res["quantity"];
    	}

	    $sql = "SELECT * FROM coupon WHERE start_date<='".$today."' AND exp_date>='".$today."' AND coupon_id=".(int)$coupon_id;
		$query = $DB->query($sql);
	    $coupon = $DB->fetch_assoc($query);
		if(!isset($coupon['coupon_id'])){
			return $result;
		}


		// 排除商品
		$excgoods = self::get_coupon_excgoods($coupon_id);
		$has_events = false;
		$excgoods_price = 0;
		foreach ($cart_product as $val) {
			if(in_array($val['product_id'], $excgoods)){
			  $amount = ($val['miao_price']?$val['miao_price']:$val['product_price'])*$val['quantity'];
			  if(isset($val['event']['event_id'])){
			    $group = self::get_event_price_one($val['event']['event_id'],$amount,$val['quantity']);
			    $excgoods_price += $amount-$group['price'];
			  }else{
			    $excgoods_price += $amount;
			  }
			}
			// 是否有满减活动
			if(isset($val['event']) && $val['event']){
			  $has_events = true;
			}
		}
		$sub_total = $sub_total-$excgoods_price;


		// 必须满代金券的金额
		if(isset($coupon['price']) && $coupon['price']>$sub_total){
			return array('ms_status'=>'minprice','cut_price'=>0);
		}


		// 是否使用
		if($coupon['is_pws']==1){
			$query = $DB->query("SELECT * FROM coupon_user WHERE coupon_active=0 AND user_id='".$_SESSION['user_id']."' AND coupon_id=".(int)$coupon_id);
		    $res = $DB->fetch_assoc($query);
			if(!isset($res['coupon_id'])){
			  $result['ms_status'] = 'useed';
			  return $result;
			}
		}

		// 是否允许叠加
		if($has_events === true && $coupon['coupon_type'] == 'C'){
		  $result['ms_status'] = 'notype';
		  return $result;
		}

		// 计算优惠
		if((float)$sub_total >= $coupon['price_limit']){
			$result['ms_status'] = 'ok';
			$result['coupon_id'] = $coupon['coupon_id'];
			$result['coupon_title'] = $coupon['coupon_title'];
			// 无上限
			if($coupon['not_top'] == 1){
			  $cut_price = $coupon['price']*(int)($sub_total/$coupon['price_limit']);
			}else{
			  $cut_price = $coupon['price'];
			}
			$result['cut_price'] = $cut_price;
		}

		return $result;

    }


    /**
     * 添加订单优惠信息
     * @param int $order_id  订单ID
     * @param array $data   订单优惠信息
     */
    function add_order_discount($order_id ,$data)
    {
    	global $DB;

    	$order_sn = isset($data['order_sn'])?$data['order_sn']:NULL;
    	
    	$table = "order_discounts";
	    $order_discounts = array(
	        "order_id"=>$order_id,
	        "title"=>$data["title"],
	        "type"=>$data["type"],
	        "discount"=>$data["discount"],
	        "event_coupon_id"=>$data["event_coupon_id"],
	    );
	    $result = $DB->Add($table, $order_discounts);

	    // 使用有代金券
		/*if($data['type'] == 2){
			$Condition = "where coupon_id = ".(int)$data['event_coupon_id'];
    		$coupon = $DB->GetRs('coupon',"is_pws",$Condition);
			if($coupon['is_pws']  == 0){
			// $DB->query("UPDATE  $this->_table_coupon SET  quota = quota-1 WHERE  coupon_id = '".(int)$data['event_coupon_id']."'");
			}else{
				$result = $DB->Set('coupon_user',array("coupon_active"=>1,'used_date'=>date('Y-m-d H:i:s',time()),'order_sn'=>$order_sn),"WHERE coupon_active=0 AND coupon_id = ".(int)$data['event_coupon_id'] ." AND user_id=".(int)$data['user_id'] . " LIMIT 1");
			}
		}*/

		// 领取奖品
		if($data['type'] == 3){
			$result = $DB->Set('prize_users',array("complete"=>1,'used_date'=>date('Y-m-d H:i:s',time()),'order_sn'=>$order_sn),"WHERE `complete`=0 AND prize_id = ".(int)$data['event_coupon_id'] ." AND user_id=".(int)$data['user_id'] . " LIMIT 1");
		}
    }


    /**
    * 当前正在进行活动根据活动组合得出 打折减掉价钱
    * @return array
    */
    public function get_event_price_one($event_id, $sub_total, $quantity, $three) {

		global $DB;

		$Condition = "where event_id = ".(int)$event_id." AND start_date < NOW() AND end_date > NOW() ORDER BY event_value desc ";
		$event = $DB->GetRs('events',"*",$Condition);

      /******************************************************************
          限制会员组参加活动,即限制VIP会员是否使用专属折扣
      *******************************************************************/

      //会员限制默认为false
      $vip_confine = false;    

      //获取用户资料
		$Table="users";
		$Fileds = "level";
		$Condition = "where user_id=" . $_SESSION["user_id"];
		$row = $DB->GetRs($Table,$Fileds,$Condition);

      if(is_array($row) && $row['level'] > 0) {

		$Table="level";
		$Fileds = "*";
		$Condition = "where id=" . $row['level'];
		$vip = $DB->GetRs($Table,$Fileds,$Condition);

      }

      //判断是否要限制活动参加的会员列表
      if(isset($vip) && !empty($event['level'])) {
        $clear_level_ids = explode(',', $event['level']);
        //如果该会员等级是属于活动限制等级会员内，则将会员限制设置为true
        if(in_array($row['level'], $clear_level_ids)) {
          $vip_confine = true;
        }

      }


      /******************************************************************
          计算活动优惠，并进行择优
      *******************************************************************/

       $total = array();
       $title = '';
       $event_price = unserialize($event['event_price']);
       $event_value = unserialize($event['event_value']);

       for($i=0;$i<count($event_price);$i++){

          $value = $event_value[$i];
          $price = $event_price[$i];

          if((int)$price == 0){
              continue;
          }
          
           //满元减 折扣后的价钱
           //==============================================================================
           if($event['event_index'] == 1){
                $current_price  = (float)$sub_total;
                if($current_price >= $value){
                    if($event['not_top'] ==1){
                      $total[$i] = $price*((int)($current_price/$value));
                    }else{
                      $total[$i] = $price;
                    }
                    $title = '满'.$value.'元,减'.$price.'元';
                }
                //vip使用专属折扣来进行择优，如果为会员限制则不进行择优
                if(isset($vip) && !$vip_confine) { 
                  $discount_price = $current_price * (1 - $vip['discount'] / 10);
                  if($discount_price > $total[$i]) {
                    $total[$i] = $discount_price;
                    $title = $vip['level'].'专享'.$vip['discount'].'折';
                  }
                }
           }

           //满元折 折扣后的价钱
           //==============================================================================
           if($event['event_index'] == 2){
                $current_price  = (float)$sub_total;
                if($current_price >= $value){
                    $total[$i] = $current_price*(1-$price/10);
                    $title = '满'.$value.'元,打'.$price.'折';
                }
                //vip使用专属折扣来进行择优，如果为会员限制则不进行择优
                if(isset($vip) && !$vip_confine) { 
                  $discount_price = $current_price * (1 - $vip['discount'] / 10);
                  if($discount_price > $total[$i]) {
                    $total[$i] = $discount_price;
                    $title = $vip['level'].'专享'.$vip['discount'].'折';
                  }
                }
           }
           //满件减 折扣后的价钱
           //==============================================================================
           if($event['event_index'] == 3){
                $current_quantity = $quantity;
                if($current_quantity >= $value){
                    if($event['not_top'] ==1){
                      $total[$i] = $price*((int)($current_quantity/$value));
                    }else{
                      $total[$i] = $price;
                    }
                    $title = '满'.$value.'件,减'.$price.'元';
                }
                //vip使用专属折扣来进行择优，如果为会员限制则不进行择优
                if(isset($vip) && !$vip_confine) { 
                  $discount_price = $current_price * (1 - $vip['discount'] / 10);
                  if($discount_price > $total[$i]) {
                    $total[$i] = $discount_price;
                    $title = $vip['level'].'专享'.$vip['discount'].'折';
                  }
                }               
           }
           //满件折 折扣后的价钱
           //==============================================================================
           if($event['event_index'] == 4){
                $current_quantity = $quantity;
                $current_price = (float)$sub_total;
                if($current_quantity >= $value){
                    $total[$i] = $current_price*(1-$price/10);
                    $title = '满'.$value.'件,打'.$price.'折';
                }
                //vip使用专属折扣来进行择优，如果为会员限制则不进行择优
                if(isset($vip) && !$vip_confine) { 
                  $discount_price = $current_price * (1 - $vip['discount'] / 10);
                  if($discount_price > $total[$i]) {
                    $total[$i] = $discount_price;
                    $title = $vip['level'].'专享'.$vip['discount'].'折';
                  }
                }      
           }

           //满件免 折扣后的价钱
           //==============================================================================
           if($event['event_index'] == 5){
                $current_quantity = $quantity;
                $current_price  = (float)$sub_total;
                $total[$i] = 0;

                if($current_quantity >= $value){
                    if($event['not_top'] ==1){
                      $b = intval($current_quantity / $value) * $price; //上不封顶应免的件数
                      $sum = 0;
                      for ($a=0; $a < $b; $a++) { 
                        $sum += $three[$a]; 
                      }
                      $total[$i] = $sum;
                      $title = '满'.$value.'件,免'.$price.'件';
                    }else{
                      $sum = 0;
                      for ($a=0; $a < $price; $a++) { 
                        $sum += $three[$a]; 
                      }
                      $total[$i] = $sum;
                      $title = '满'.$value.'件,免'.$price.'件';
                    }
                }
                //vip使用专属折扣来进行择优，如果为会员限制则不进行择优
                if(isset($vip) && !$vip_confine) { 
                  $discount_price = $current_price * (1 - $vip['discount'] / 10);
                  if($discount_price > $total[$i]) {
                    $total[$i] = $discount_price;
                    $title = $vip['level'].'专享'.$vip['discount'].'折';
                  }
                }
           }



        }

        // 返回最大优惠值
        return array(
            'price' => (float)@max($total),
            'title' => $title,
        );
    }  


    // 获取购物车商品
	function carts($cart_ids=0, $seller=array()){
	    global $DB;
	    global $SITECONFIGER;

	    $cart_product = array();
		$sql = "SELECT DISTINCT(c.cart_id),c.quantity,c.product_id,c.sku_prop,c.color_prop,c.size_prop,p.product_name,p.market_price,p.price,p.total_quantity,p.sale,p.hot,p.stock,p.presale_date,p.sku_sn
		        FROM cart c LEFT JOIN products p ON c.product_id=p.product_id
		        WHERE c.user_id=".(int)$_SESSION["user_id"];
		if(!empty($cart_ids)){
			$sql .= " AND c.cart_id IN(".$cart_ids .")";
		}
		$sql .= " ORDER BY c.create_date DESC";
		$Row = $DB->query($sql);
		while($cart = $DB->fetch_assoc($Row))
		{
			// 分销供货价
			if(isset($seller['seller_level_id']) && $seller['seller_level_id'])
			{
	            //取得产品的折扣
	            $item = $DB->GetRs('seller_item', '*', "WHERE product_id = ".$cart["product_id"]);
	            if($item) {
	                $discounts = unserialize($item['discounts']);
	                $seller_discount = $discounts[$seller['seller_level_id']];
	                if(isset($seller_discount['type']) && $seller_discount['type']=='price'){
	                    // 设置分销供货价
	                    $seller_price = number_format($seller_discount['value'], 2, '.', '');
	                }else{
	                    // 按折扣计算分销供货价
	                    if(isset($seller_discount["value"]) && $seller_discount["value"] > 0 && $seller_discount["value"] < 10 ){
	                        $seller_price = round($seller_discount["value"]*$cart['price']/10, 2);
	                    }
	                }
	            }
	        }


			$miao = self::check_miao($cart["product_id"]);
		    if(empty($cart['sku_prop'])){
		        $where = " AND color_prop='".$cart['color_prop']."' AND size_prop='".$cart['size_prop']."'";
		    }else{
		        $where = " AND sku_prop='".$cart['sku_prop']."'";
		    }
		    $stock = $DB->GetRs("stock","sku_prop,photo_prop,color_prop,size_prop,sync,quantity as siglequantity","WHERE depot_id=".$SITECONFIGER['sys']['default_depot_id']." and sku_sn='".$cart['sku_sn']."' {$where}");
		    // 预售时间小于当前，改为非预售
		    $presale = empty($stock["sync"])?1:0;
	        if(strtotime($cart['presale_date'])<time()){
	            $presale=0;
	        }
		    $arr = array(
                "cart_id"=>$cart["cart_id"],
                "sku_sn"=>$cart["sku_sn"],
                "product_id"=>$cart["product_id"],
                "product_name"=>$cart["product_name"],
                "market_price"=>$cart["market_price"],
                // "product_price"=>ceil($cart['price']),
                "miao_price"=>$miao['miao_price']?$miao['miao_price']:0,
                "total_quantity"=>$cart["total_quantity"],
                "size_prop"=>isset($stock["size_prop"])?$stock["size_prop"]:NULL,
                "quantity"=>$cart["quantity"],
                "color_prop"=>isset($stock["color_prop"])?$stock["color_prop"]:NULL,
                "stock"=>$cart["stock"],
                "siglequantity"=>isset($stock["siglequantity"])?$stock["siglequantity"]:NULL,
                "thumb"=>isset($stock["photo_prop"])?$stock["photo_prop"]:NULL,
                "sku_prop"=>isset($stock["sku_prop"])?$stock["sku_prop"]:NULL,
                "order_time"=>isset($miao['order_time'])?$miao['order_time']:NULL,
                "event"=>isset($cart['event'])?$cart['event']:NULL,
                "presale"=>$presale,
                "presale_date"=>empty($cart['presale_date'])?'':date('Y-m-d',strtotime($cart['presale_date'])),
            );
            if(isset($seller_price) && $seller_price>0){
				$arr['orig_price'] = $cart['price'];
				$arr['product_price'] = number_format($seller_price, 2, '.', '');
			}else{
				$arr['product_price'] = $cart['price'];
			}
			$cart_product[] = $arr;
		}

	    return  $cart_product;
	}


	// 自动关闭超时未付款的订单
    function auto_close_notpay_order($user_id){
    	global $DB;

        if(!$user_id)
            return false;

        $cart_product = array();
	    $Table = "orders";
	    $Condition = "where user_id=" . $user_id . " AND status=0 AND exp_date<NOW()";
	    $Row = $DB->Get($Table, 'order_id', $Condition, 0);
	    $RowCount = $DB->num_rows($Row);
	    if($RowCount > 0){
	        while($result = $DB->fetch_assoc($Row)){
	        	$rs = $DB->Set($Table,array("status"=>"-1",'reout'=>"timeout"),"WHERE status = 0 AND exp_date < NOW() AND order_id=".(int)$result['order_id']);
	        	if($rs){
	        		$data = array(
                        'status'    =>  -1,
                        'content'   =>  '支付超时自动关闭订单',
                    );
                    self::add_order_history($result['order_id'],$data); // 添加订单历史
	        	}
	        }
	    }
    }

    // 自动关闭超时未寄回订单
    function auto_close_exchange_order($user_id){
        global $DB,$SITECONFIGER;

        if(!$user_id)
            return false;

        $exchange_timeout = $SITECONFIGER['order']['exchange_timeout'];

        $Table = "orders";
	    $Condition = "where user_id=" . $user_id . " AND ((status=5 AND return_num=4) OR (status=7 AND return_num=2))";
	    $Row = $DB->Get($Table, 'order_id,status,order_sn,reout', $Condition, 0);
	    $RowCount = $DB->num_rows($Row);
	    if($RowCount > 0){
	        while($order = $DB->fetch_assoc($Row)){
	        	// 商品已经寄回状态不执行
            	if($order['reout']!='expback' && $order['reout']!='repeat'){
		            $history = self::get_order_history($order['order_id'],$order['status']);
		            if(isset($history['create_date'])){
		                $pastday = date('Y-m-d H:i:s',strtotime($history['create_date'])+86400*$exchange_timeout);
		                // 超时完成交易
		                if(strtotime($pastday)-time() <= 0){
		                    $data = array(
		                        'status' => 8,
		                        'reout'  => 're_timeout',
		                        'content' => '7天未寄回，超时自动完成交易'
		                    );
		                    self::add_order_history($order['order_id'],$data);
		                    if($order['status']==7){
		        				$relation_order = $DB->GetRs('orders','order_id,order_sn,pay_status,pay_date,pay_total',"WHERE relation_order = '".$order['order_sn']."' order by order_id desc");
		                        if(isset($relation_order['order_id'])){
		                            if($relation_order['pay_status']==1 && $relation_order['pay_total']>0){
		                                // 转入退款
		                                $redata = array(
		                                    'status' => 3,
		                                    'content' => '未寄回商品，退回运费'
		                                );
		                                self::add_order_history($relation_order['order_id'],$redata);
		                            }else{
		                                // 关闭订单
		                                $redata = array(
		                                    'status' => -1,
		                                    'reout'  => 're_timeout',
		                                    'content' => '未寄回商品，超时自动关闭订单'
		                                );
		                                self::add_order_history($relation_order['order_id'],$redata);
		                            }
		                        }
		                    }
		                }
		            }	// endif create_date;
		        }	// endif reout;
		    }	// endwhile;
        }
    }

    /**
     * 添加订单历史信息
     * @param int $order_id  订单ID
     * @param array $data   订单历史信息
     */
     public function add_order_history($order_id ,$data) {
     	global $DB;

        $condition = isset($data['condition'])?$data['condition']:null;
        if(isset($data['img_url'])){
            $img_url = $data['img_url'];
        }else{
            $img_url = NULL;
        }
        if(isset($data['note'])){
            $note = $data['note'];
        }else{
            $note = NULL;
        }

        // 查询订单信息
        $Table="orders";
        $Fileds = "order_id,order_sn,user_id ,pay_total,relation_order";
        $Condition = "where order_id=".(int)$order_id;
        $order = $DB->GetRs($Table,$Fileds,$Condition);
        if(!isset($order['order_sn'])){
          return false;
        }

        // 添加订单历史
        $res = $DB->Add("order_history",array(
	        "order_id"=>$order_id,
	        "condition"=>$condition,
	        "content"=>$data['content'],
	        "status"=>(int)$data['status'],
	        "img_url"=>$data['img_url'],
	        "note"=>$data['note'],
	        "create_date"=>date('Y-m-d H:i:s'),
	    ));
        if($res){
        	$pay_sql = array('status' => (int)$data['status']);
            if(!empty($data['reout'])){
              $pay_sql['reout'] = $data['reout'];
            }
        	// 更新支付状态与支付时间
            if((int)$data['status'] == 1){
              $pay_sql['pay_status'] = 1;
              $pay_sql['pay_date'] = date('Y-m-d H:i:s',time());
              if(isset($data['pay_method'])){
                 $pay_sql['pay_method'] = $data['pay_method'];
              }
            }
        	$result = $DB->Set($Table,$pay_sql, "WHERE order_id =".(int)$order_id);
        }

        // 释放库存
        if($data['status'] == -1){
	        $Fileds = "product_id, size_prop,color_prop,num";
	        $Condition = "where order_id=".(int)$order_id;
	        $Row = $DB->Get('order_items',$Fileds,$Condition);
	        $Row = $DB->result;
			if($DB->num_rows($Row) > 0 ){
				while($product = $DB->fetch_assoc($Row)){
		        	//$DB->Set('prop', "quantity=quantity+".(int)$product['num'], "WHERE product_id = '".(int)$product['product_id']."' AND color_prop = '".$product['color_prop']."'");
		        	$DB->Set('products', "sale = sale-".(int)$product['num'].", total_quantity=total_quantity+".(int)$product['num'], "WHERE product_id = '".(int)$product['product_id']."'");
	            }
	        }
        }

        // 积分变动，如果是售后订单，将记录原订单积分
        if($data['status'] == 8){
          if(!empty($order['relation_order']) && $order['relation_order']!=null){
	        $Fileds = "user_id ,pay_total";
	        $Condition = "WHERE user_id='".$order['user_id']."' AND order_sn = '".$order['relation_order']."'";
	        $order = $DB->GetRs($Table,$Fileds,$Condition);
          }
          self::users_integral_modify($order['user_id'],$order['pay_total']);
        }

        return $result;
    }

    /**
     * 获取订单某状态的历史
     * @param  int $order_id 订单ID
     * @param  int $status 订单状态
     * @return array
     */
    public function get_order_history($order_id,$status) {
    	global $DB;
        $Condition = "WHERE order_id = '".(int)$order_id."' and status = '".(int)$status."' order by history_id desc";
        return $DB->GetRs('order_history','*',$Condition);
    }

    /**
     *改变用户积分
     *@param string $user_id 用户id
     *@return array 返回数据
     */
    public function users_integral_modify($user_id,$integral_value){
    	global $DB;

        $integral_value = (int)$integral_value;
        if($integral_value<0){
            $context = '退款扣除';
        }else if($integral_value>0){
            $context = '消费积分';
        }else{
            return false;
        }

        $res = $DB->Add("integral",array(
	        "user_id"=>$user_id,
	        "integral_value"=>$integral_value,
	        "context"=>$context,
	        "create_date"=>date('Y-m-d H:i:s'),
	    ));
	    $result = $DB->Set('users','integral_total=integral_total+'.$integral_value, "WHERE user_id =".(int)$user_id);
        return $result;
    }


    /*
    * 寄回快递信息
    */
    public function send_back($order_id,$msg,$status)
    {
    	global $DB;
    	
	    $result = $DB->Set('orders','`reout`="expback", return_num=6', "WHERE order_id =".(int)$order_id);
        if($result){
	        // 添加订单历史
	        $res = $DB->Add("order_history",array(
		        "order_id"=>$order_id,
		        "content"=>$msg,
		        "status"=>(int)$status,
		        "create_date"=>date('Y-m-d H:i:s'),
		    ));
        }
        return $result;
    }


    /**
	 * 根据活动ID 获取单个信息
	 * @param  int  $prize_id 活动ID
	 * @return array          
	 */
	public function getPrize($prize_id)
	{
		global $DB;
		$Table="prizes";
        $Fileds = "*";
        $Condition = "where prize_id=".(int)$prize_id;
       	return $DB->GetRs($Table,$Fileds,$Condition);
	}

	/**
	 * 获取进行中的奖品列表
	 * @return array          
	 */
	public function getPrizes($user_id)
	{
		global $DB;
		$Table="v_prizes";
        $Fileds = "*";
        $Condition = "where complete= 0 AND start_date <= NOW() and end_date > NOW() AND user_id='".(int)$user_id."' ORDER BY start_date desc";
       	$DB->Get($Table,$Fileds,$Condition);
       	$Row = $DB->result;
       	$rows = array();
		if($DB->num_rows($Row) > 0 ){
			while($result = $DB->fetch_assoc($Row)){
	        	$rows[] = $result;
            }
        }
        return $rows;
	}


	/**
	 * 当前活动下的产品列表
	 * @param  integer  $prize_id 活动ID
	 * @param  array   $data      过滤跟分页的数组信息
	 * @return array              
	 */
	public function getProductFromPrize($prize_id)
	{
		global $DB;
		$Table="prize_products";
        $Fileds = "*";
        $Condition = "where prize_id='".(int)$prize_id."'";
       	$DB->Get($Table,$Fileds,$Condition);
       	$Row = $DB->result;
       	$rows = array();
		if($DB->num_rows($Row) > 0 ){
			while($result = $DB->fetch_assoc($Row)){
	        	$rows[] = $result;
            }
        }
        return $rows;
	}


	/*
	* 计算奖励价格，得出减去的价格
	*/
	public function getPrizePrice($product_price, $price_type, $prize_price, $max_quantity){
		switch ($price_type) {
            case 'cash':
                $prize_total = ($product_price-$prize_price)*$max_quantity;
                break;
            case 'fold':
                $prize_total = ($product_price-$product_price*($prize_price/10))*$max_quantity;
                break;
            default:
                $prize_total = 0;
                break;
        }

        return $prize_total;
	}


    /*
    * 把奖品附到每个单品里
    * $carts array 购物车商品
    * $split bool 当大于领取数量时是否拆分显示
    */
    public function get_product_prizes($carts, $user_id=null, $split=true){

    	if(!$user_id){
    		$user_id = $_SESSION['user_id'];
    	}
        // 当前用户可领取产品奖品
        $prizes = self::getPrizes($user_id);
        $prize_product_ids = array();
        $prize_cut_total = 0;
        if(!empty($prizes)){
            foreach ($prizes as $key=>$prize) {
                $prize_count_quantity = 0;
                $prize_products = self::getProductFromPrize($prize['prize_id']);
                foreach ($carts as $ck=>$cv) {
                    foreach ($prize_products as $pk => $pv) {
                        if($cv['product_id'] == $pv['product_id'] && !in_array($pv['product_id'],$prize_product_ids)){
                            if($split==true && $cv['quantity'] > $prize['quantity']){
                                $carts[$ck]['quantity'] = $prize['quantity'];
                                // 拆分生成新的item在购物车显示
                                $newItem = $cv;
                                $newItem['quantity'] = $cv['quantity']-$prize['quantity'];
                                $carts[] = $newItem;
                            }
                            $price = $cv['miao_price']?$cv['miao_price']:$cv['product_price'];
                            $prize_quantity = $cv['quantity']>$prize['quantity']?$prize['quantity']:$cv['quantity'];
                            $item_price = self::getPrizePrice($price, $prize['price_type'], $prize['item_price'], $prize_quantity);
                            $carts[$ck]['prize'] = array(
                                'prize_id' => $prize['prize_id'],
                                'prize_title' => $prize['title'],
                                'prize_desc' => $prize['desc'],
                                'prize_price' => $item_price,
                            );
                            $prize_count_quantity += $carts[$ck]['quantity'];
                            $prize_cut_total += $item_price;
                            $prize_product_ids[] = $pv['product_id'];
                        }
                    }
                }
                $prizes[$key]['count_num'] = $prize_count_quantity;
            }
        }

        $data['carts'] = $carts;
        $data['prizes'] = $prizes;
        $data['prize_cut_total'] = $prize_cut_total;
        $data['prize_product_ids'] = $prize_product_ids;

        return $data;
    }


	// 取一级数据
    public function get_picshow($pos_id,$num=0){
    	// 读取缓存
    	global $Cache;
    	$CKey = 'picshow_'.$pos_id.'_'.$num;
		$resultCache = $Cache -> get($CKey);
		if (is_null($resultCache)){

	    	global $DB;
			$Table="v_picshow";
			$Fileds = "*";
			$Condition = "where find_in_set($pos_id,pos_ids) AND (start_date<NOW() OR start_date IS NULL) AND (end_date>NOW() OR end_date IS NULL)";
			$Condition .= " ORDER BY sort ASC,ad_id desc ";
			if(!empty($num)){
	            $Condition .= " LIMIT $num ";
	        }
			$Row = $DB->Get($Table,$Fileds,$Condition,0);
			$data = array();
			while($result = $DB->fetch_assoc($Row)) {
				// 排除下架商品
				if($result['type']=='product'){
					$rs = $DB->GetRs('products','stock',"WHERE product_id=".(int)$result['product_id']);
					if($rs['stock']==0){
					    continue;
					}
				}
				$data[] = $result;
			}

			$ad_data = array(
				'exptime' => isset($data[0]['end_date'])?$data[0]['end_date']:'',
				'data' => $data,
			);
			$Cache->set($CKey, $ad_data);

	    }else{
	    	$ad_data = $resultCache;
	    }

	    return self::check_adexptime($ad_data,$num);
    }

    // 取二级数据,ext_id 排除的ID
    public function get_picshow_set($pos_id,$ext_id=array()){
        if(empty($pos_id)) return false;
        global $DB;
    	$Table="position";
		$Fileds = "pos_id,posname";
		$Condition = "where parent=$pos_id ORDER BY sort ASC";
		$rs = $DB->Get($Table,$Fileds,$Condition,0);
		$result = array();
		while($row = $DB->fetch_assoc($rs)) {
            if(!in_array($row['pos_id'],$ext_id)){
            	$row['list'] = self::get_picshow($row['pos_id']);
            	$result[] = $row;
            }
		}
        return $result;
    }


    // 判断广告是否过期
    public function check_adexptime($ad_data,$num=null){
    	$data = '';
    	if(empty($ad_data['exptime']) || strtotime($ad_data['exptime']) > time() ){
	    	if($num == 1){
	            $row = $ad_data['data'][0];
	            switch ($row['type']) {
	                case 'flash':
	                    $html = '<embed src="'.$row['srcurl'].'" type="application/x-shockwave-flash" width="550" height="400" quality="high" />';
	                    break;
	                case 'image':
	                    $html = '<a href="/?m=call&go='.$row['ad_id'].'" target="_blank" class="img_box"><img src="'.$row['srcurl'].'" alt="'.$row['adname'].'" /></a>';
	                    break;
	                case 'code':
	                    $html = htmlspecialchars_decode($row['contxt']);
	                    break;
	                case 'product':
	                    $html = '<a href="/?m=call&go='.$row['ad_id'].'" target="_blank" class="img_box"><img src="'.$row['product_img'].'" alt="'.$row['product_name'].'" /></a>';
	                    break;
	                default:
	                    $html = '';
	                    break;
	            }
	            $data = $html;
	        }else{
	        	$data = $ad_data['data'];
	        }
	    }
        return $data;
    }


	/*
	* 记录访问
	* 2016-05-23 停用
	* 改为 页面 ajax调用 /stats/v.php 记录访问
	*/
	public function add_chart($item_id=null,$type=null){
	  return false;
	}


	/**
	 * 获取产品的属性
	 * @param  intval $product_id 产品ID
	 * @return array
	 */
	public function getProductProp($product_id,$in = FALSE, $where=null)
	{
		global $DB;

		if($where){
			$where = " AND " . $where;
		}
		if($in){
			$Condition = " WHERE quantity > 0 AND  product_id =".(int)$product_id.$where;
		}else{
			$Condition = "WHERE product_id=".(int)$product_id.$where;
		}
		return $DB->GetRs('prop',"*", $Condition);
	}
	/*
	* 取单条规格
	*/
	public function get_one_prop($sku_sn, $color_prop, $size_prop, $depot_id=NULL){
		global $DB,$SITECONFIGER;
		if(empty($depot_id)){
            $depot_id = $SITECONFIGER['sys']['default_depot_id'];
        }
        $Condition = "WHERE `sku_sn`='".$sku_sn."' AND `color_prop`='".$color_prop."' AND `size_prop`='".$size_prop."' AND depot_id = ".(int)$depot_id;
        return $DB->GetRs('stock',"*", $Condition);
	}


	/*
    * 获取代金券中排除的商品
    * return array()
    */
    public function get_coupon_excgoods($coupon_id){
    	global $DB;
        $today = date('Y-m-d',time());
        $data = array();

        $Condition = "where start_date<='".$today."' AND exp_date>='".$today."' AND coupon_id=".(int)$coupon_id;
		$coupon = $DB->GetRs('coupon',"*",$Condition);
        if(!isset($coupon['coupon_id'])){
            return $data;
        }

        $Condition = "where coupon_id=".(int)$coupon_id;
		$Row = $DB->Get('coupon_excgoods',"*",$Condition);
		$Row = $DB->result;
		if($DB->num_rows($Row) > 0 ){
			while($result = $DB->fetch_assoc($Row)){
	        	$data[] = $result['product_id'];
            }
        }

        return $data;
    }


    /*
    * 获取品牌 缓存
    */
    public function getBrands($num=null){
	    global $Cache;
	    $CKey = 'index_brands'.$num;
	    $resultCache = $Cache -> get($CKey);
	    if (is_null($resultCache)){
	        global $DB;
	        $limit = '';
	        if($num > 0){
	        	 $limit = " limit $num";
	        }
	        $Table="brands";
	        $Fileds = "*";
	        $Condition = "  Order by sort ASC,brand_id asc $limit";
	        $Row = $DB->Get($Table,$Fileds,$Condition);
	        $brands = array();
	        while($result = $DB->fetch_assoc($Row)){
	        	$brands[$result['brand_id']] = $result;
	            // $brands[] = $result;
	        }
	        $Cache->set($CKey, $brands);
	    }else{
	        $brands = $resultCache;
	    }
	    return $brands;
	}


	/*
	* 获取分类 缓存
	*/
	public function get_category(){
		$returnjson = array();
		global $Cache;
	    $CKey = 'category';
	    $resultCache = $Cache -> get($CKey);
	    if (IS_NULL($resultCache)){
			global $DB;
			$Table = "category";
			$Condition = "where parent=0 order by sort asc";
			$Row = $DB->Get($Table,$Fileds,$Condition,0);
			$Row = $DB->result;
			$RowCount = $DB->num_rows($Row);
			if($RowCount!=0){
			    while($result = $DB->fetch_assoc($Row)){
			        $ChildrenArray = array();
			        $RowChildren = $DB->Get($Table,"*","where parent=".$result["category_id"]." order by sort asc");
			        $RowChildren = $DB->result;
			        $RowChildrenCount = $DB->num_rows($RowChildren);
			        if($RowChildrenCount!=0){
			            while($resultChildren = $DB->fetch_assoc($RowChildren)){
			                array_push($ChildrenArray, array(
			                    "category_name"=>$resultChildren["category_name"],
			                    "category_id"=>$resultChildren["category_id"],
			                    "img_url"=>$resultChildren["img_url"],
			                    "status"=>$resultChildren["status"]
			                ));
			            }
			        }
			        array_push($returnjson, array(
			            "category_name"=>$result["category_name"],
			            "category_id"=>$result["category_id"],
			            "img_url"=>$result["img_url"],
			            "status"=>$result["status"],
			            "childrens"=>$ChildrenArray
			        ));
			    }
			}
			$Cache->set($CKey, $returnjson);
	    }else{
	        $returnjson = $resultCache;
	    }
	    return $returnjson;

	}

	/*
	* 检查会员资料是否为空
	*/
	public function check_user_info($user_id,$finds = array()){
		// 没有参数通过，前台不显示提示信息
		if(!$finds || !$user_id){
			return true;
		}

		global $DB;
		$check = true;
		$Table="users";
        $Fileds = implode(',', $finds);
        $Condition = "where user_id=".(int)$user_id;
        $data = $DB->GetRs($Table,$Fileds,$Condition);
        if( $data ){
	        foreach ($data as $field) {
	        	if(empty($field)){
	        		$check = false;
	        	}
	        }
	    }
        return $check;
	}


	/*
    * 判断会员ID是否已有抽奖码
    */
    public function exist_user_lottery_code($user_id,$activity){
    	// 没有参数通过，前台不显示提示信息
		if(empty($user_id) || empty($activity)){
			return false;
		}

    	global $DB;
        $Condition = "WHERE user_id='".(int)$user_id."' AND activity='".$activity."'";
        $Row = $DB->Get('lottery_code','code_id',$Condition);
		$Row = $DB->result;
		return $DB->num_rows($Row);
    }


    // 生成唯一抽奖码，数据库已有将重新生成
    function get_lottery_code($activity=null,$limit=6){
    	global $DB;
    	// 根据limit补9
    	$max = str_pad('9', $limit, '9', STR_PAD_LEFT);
        $code = str_pad(mt_rand(1, $max), $limit, '0', STR_PAD_LEFT);

        $Condition = "WHERE code='".$code."' AND activity='".$activity."'";
        $row = $DB->GetRs('lottery_code','code_id',$Condition);
        if(!empty($row['code_id'])){
            $code = self::get_lottery_code($activity,$limit);
        }
        return $code;
    }


    /*
    * 保存抽奖码
    */
    public function save_lottery_code($data){
    	global $DB;

        if(!empty($data['code']) && !empty($data['user_id'])){
	        $DB->Add("lottery_code",$data);
	        return  $DB->insert_id();
        }
    }


	/* *********************************************************************
	 * 获取 可用的推广计划下面的所属指定推广者的商品列表 
	 * *********************************************************************/
	public function get_beyond_product_list($promote_id,$searchArr = array('searchCategory'=>'','searchKeywords'=>'')) {
		GLOBAL $DB;
		/************************* 获取主推商品 *************************/
		$promote_product_list = $promote_category_list = $products = array();

		$sql = "SELECT ppc.pcontent_id,pp.pplan_id,p.product_id,p.sku_sn,p.product_name,p.price,p.sale,p.date_added,ppc.commission_rate,DATEDIFF(CURDATE(),p.date_added) as datediff,
		        (SELECT pi.url FROM product_img pi WHERE ppc.item_id = pi.product_id LIMIT 1) as url 
		        FROM promote_plan_content ppc
		        LEFT JOIN promote_plan pp ON ppc.pplan_id = pp.pplan_id
		        LEFT JOIN promote_plan_target ppt ON ppt.pplan_id = ppc.pplan_id
		        LEFT JOIN products p ON ppc.item_id = p.product_id
		        WHERE ppc.content_type = 0 AND pp.start_time <= now() AND pp.end_time >= now() 
		        AND (pp.plan_type = 'public' OR ppt.promote_id = $promote_id)
		        ORDER BY pp.plan_priority DESC,pp.plan_type DESC,ppc.commission_rate DESC";

		$result = $DB->query($sql);
		while ($row = $DB->fetch_array($result)) {
		    if(!in_array($row['product_id'], $products)) {
		        array_push($products, $row['product_id']);
		        if($row['commission_rate'] > 0) {
		            $promote_product_list[$row['product_id']] = $row;
		        }
		    }
		}

		/************************* 获取类目下的商品 *************************/
		//获取可推广类目
		$promote_category_list = $this->get_beyond_category_list($promote_id);
		// print_r($promote_category_list);exit();

		//获取类目下的商品
		foreach ($promote_category_list as $key => $value) {
		    $category_id = $key;
		    //DATEDIFF(CURDATE(),p.date_added) as datediff 表示商品从创建到现在已过了多久(天)
		    $sql = "SELECT p.product_id,p.product_name,p.sku_sn,p.price,p.sale,p.date_added,DATEDIFF(CURDATE(),p.date_added) as datediff,(SELECT pi.url FROM product_img pi WHERE ptc.product_id = pi.product_id LIMIT 1) as url
		            FROM product_to_category ptc
		            LEFT JOIN products p ON ptc.product_id = p.product_id
		            WHERE category_id = $category_id AND p.stock = 1";

		    $result = $DB->query($sql);
		    while ($row = $DB->fetch_array($result)) {
		      if(!in_array($row['product_id'], $products)) {
		            array_push($products, $row['product_id']);
		            $row['commission_rate'] = $value['commission_rate'];
		            $row['pplan_id'] = $value['pplan_id'];
		            $promote_product_list[$row['product_id']] = $row;
		        }
		    }

		}

		//查询类目,剔除商品
		if(!empty($searchArr['searchCategory'])) {
	        $sql = "SELECT p.product_id FROM product_to_category ptc
	                LEFT JOIN products p ON ptc.product_id = p.product_id
	                WHERE category_id = {$searchArr['searchCategory']} AND p.stock = 1";
		    $result = $DB->query($sql);
		    $product_ids = array();
		    while ($row = $DB->fetch_array($result)) {
                $product_ids[] = $row['product_id'];
		    }
            foreach ($promote_product_list as $key => $value) {
                if(!in_array($value['product_id'], $product_ids)) {
                    unset($promote_product_list[$key]);
                }
            }
        }

		//搜索关键字
		if(!empty($searchArr['searchKeywords'])) {
		    foreach ($promote_product_list as $key => $value) {
		        if(strpos($value['product_name'],$searchArr['searchKeywords']) === false && strpos($value['product_name'],strtoupper($searchArr['searchKeywords'])) === false && strpos($value['sku_sn'],$searchArr['searchKeywords']) === false && strpos($value['sku_sn'],strtoupper($searchArr['searchKeywords'])) === false) {
		           unset($promote_product_list[$key]); 
		        }
		    }
		}
		// print_r($promote_product_list);
		return $promote_product_list;
	}


	/* *********************************************************************
	 * 获取 可用的推广计划下面的所属指定推广者的类目列表 
	 * *********************************************************************/
    public function get_beyond_category_list($promote_id) {
    	GLOBAL $DB;

		$promote_category_list = $item = array();
        //----------------------------------获取主推类目--------------------------------------

        $sql = "SELECT ppc.pcontent_id,ppc.item_id,ppc.commission_rate,ppc.pplan_id,(SELECT category_name FROM category c WHERE ppc.item_id = c.category_id) AS category_name 
                FROM promote_plan_content ppc
                JOIN promote_plan pp ON ppc.pplan_id = pp.pplan_id
                LEFT JOIN promote_plan_target ppt ON ppt.pplan_id = ppc.pplan_id
                WHERE ppc.content_type = 1 AND pp.start_time <= now() AND pp.end_time >= now() 
                AND (pp.plan_type = 'public' OR ppt.promote_id = $promote_id)
                ORDER BY pp.plan_priority DESC,pp.plan_type DESC,ppc.commission_rate DESC";

		$result = $DB->query($sql);
		while ($row = $DB->fetch_array($result)) {
		    if(!in_array($row['item_id'], $item)) {
		        array_push($item, $row['item_id']);
		        if($row['commission_rate'] > 0) {
	            	$promote_category_list[$row['item_id']] = $row;
                	$promote_category_list[$row['item_id']]['category_id'] = $row['item_id'];
		        }
		    }
		}

		//----------------------------------获取其余类目--------------------------------------
        //获取后续购物(全站)
        $promote_website = $this->get_beyond_website($promote_id);
        //如果没有后续购物或者后续购物设置空比率，则其余类目不推广
        if(!empty($promote_website)) {
            $websiteCommissionRate = $promote_website['commission_rate'];
            $websitePlanId         = $promote_website['pplan_id'];
            //获取全部二级类目
            $sql = "SELECT * FROM category WHERE parent <> 0 AND status = 1"; 
			$result = $DB->query($sql);
			while ($row = $DB->fetch_array($result)) {
				$all_secondly_category[] = $row;
			}

            //去除重复,去除空比率类目
            foreach ($all_secondly_category as $key => $value) {
                if(!in_array($value['category_id'], $item)) {
                    array_push($item, $value['category_id']);
                    $promote_category_list[$value['category_id']] = $value;
                    $promote_category_list[$value['category_id']]['commission_rate'] = $websiteCommissionRate;
                    $promote_category_list[$value['category_id']]['pplan_id']        = $websitePlanId;
                }
            }
            
        }

		return $promote_category_list;
    }

    /* *********************************************************************
     * 获取 首次消费优惠
     * 推广内容类型/0主推商品 /1主推类目 /2后续购物(全站) /3后续充值 /4首次购物(全站) /5首次充值
     * *********************************************************************/
    public function get_beyond_first($promote_id,$content_type) {
    	GLOBAL $DB;
        $sql = "SELECT ppc.pcontent_id,ppc.pplan_id,ppc.commission_rate FROM promote_plan_content ppc
                JOIN promote_plan pp ON ppc.pplan_id = pp.pplan_id
                LEFT JOIN promote_plan_target ppt ON ppt.pplan_id = ppc.pplan_id
                WHERE ppc.content_type = $content_type AND pp.start_time <= now() AND pp.end_time >= now() 
                AND (pp.plan_type = 'public' OR ppt.promote_id = $promote_id)
                ORDER BY pp.plan_priority DESC,pp.plan_type DESC,ppc.commission_rate DESC LIMIT 1";
		$result = $DB->query($sql);
		$promote_first = $DB->fetch_array($result);
        if(empty($promote_first) || $promote_first['commission_rate'] == 0) {
            $promote_first = array();
        }
        return $promote_first;
    }

	/* *********************************************************************
	 * 获取 可用的推广计划下面的所属指定推广者的网站推广 
	 * *********************************************************************/
    public function get_beyond_website($promote_id) {
    	GLOBAL $DB;
        $sql = "SELECT ppc.pcontent_id,ppc.pplan_id,ppc.commission_rate FROM promote_plan_content ppc
                JOIN promote_plan pp ON ppc.pplan_id = pp.pplan_id
                LEFT JOIN promote_plan_target ppt ON ppt.pplan_id = ppc.pplan_id
                WHERE ppc.content_type = 2 AND pp.start_time <= now() AND pp.end_time >= now() 
                AND (pp.plan_type = 'public' OR ppt.promote_id = $promote_id)
                ORDER BY pp.plan_priority DESC,pp.plan_type DESC,ppc.commission_rate DESC LIMIT 1";

		$result = $DB->query($sql);
		$promote_website = $DB->fetch_array($result);
        if(empty($promote_website) || $promote_website['commission_rate'] == 0) {
            $promote_website = array();
        }
        return $promote_website;
    }


	/* *********************************************************************
	 * 获取 可用的推广计划下面的所属指定推广者的充值返 
	 * *********************************************************************/
    public function get_beyond_recharge($promote_id) {
    	GLOBAL $DB;
        $sql = "SELECT ppc.pcontent_id,ppc.pplan_id,ppc.commission_rate FROM promote_plan_content ppc
                JOIN promote_plan pp ON ppc.pplan_id = pp.pplan_id
                LEFT JOIN promote_plan_target ppt ON ppt.pplan_id = ppc.pplan_id
                WHERE ppc.content_type = 3 AND pp.start_time <= now() AND pp.end_time >= now() 
                AND (pp.plan_type = 'public' OR ppt.promote_id = $promote_id)
                ORDER BY pp.plan_priority DESC,pp.plan_type DESC,ppc.commission_rate DESC LIMIT 1";

		$result = $DB->query($sql);  
		$promote_recharge = $DB->fetch_array($result);
        if(empty($promote_recharge) || $promote_recharge['commission_rate'] == 0) {
            $promote_recharge = array();
        }
        return $promote_recharge;
    }

	/* *********************************************************************
	 * 获取链接 
	 * *********************************************************************/
    public function get_beyond_link($promote_id,$type,$item_id) {
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

    //取得搜索时间的sql
    public function getWhenSql($time_field,$when) {
        switch ($when) {
            case 'this_week':
                $when = " YEARWEEK($time_field) = YEARWEEK(NOW())";
                break;
            case 'last_week':
                $when = " YEARWEEK($time_field) = YEARWEEK(NOW()) - 1";
                break;
            case 'this_month':
                $when = " date_format($time_field,'%Y-%m') = date_format(curdate(),'%Y-%m')";
                break;
            case 'last_month':
                $when = " date_format($time_field,'%Y-%m') = date_format(DATE_SUB(curdate(), INTERVAL 1 MONTH),'%Y-%m')";
                break;
            case 'yesterday':
                $when = " TO_DAYS($time_field) = TO_DAYS(now()) - 1";
                break;
            default://今天
                $when = " TO_DAYS($time_field) = TO_DAYS(now())";
                break;
        }
        return $when;
    }

    /*
    * 线下消费来源记录
    */
    public function getCodeFromBusiness(){
    	return;
    	global $DB;
    	$business_code = '';
    	if(!empty($_SESSION['qrcode_fr']) && !empty($_SESSION['qrcode_ch'])) {
    	    $fr = base64_decode($_SESSION['qrcode_fr']);
    	    $ch = base64_decode($_SESSION['qrcode_ch']);
    	    if($fr == 'business') {
    	        $business = $DB->GetRs("business","business_id","WHERE business_code = '{$ch}'");
    	        if(!empty($business)) $business_code = $ch;
    	    }
    	}
    	return $business_code;
    }


	/**
	 * 注册来源备注
	 * @return  返回来源的中文信息(如果存在ch,则返回fr-ch)
	 */
    public function getQrcodeFrom(){
    	global $DB;
    	$qrcode_name = "";
	    if(!empty($_SESSION['qrcode_fr'])) {
	        $fr = base64_decode($_SESSION['qrcode_fr']);
	        if($fr == 'business'){
				$qrcode_name = "来源：".self::getBusinessCodeFrom('business_name');
	        }else{
		        $ch = !empty($_SESSION['qrcode_ch'])?base64_decode($_SESSION['qrcode_ch']):'';
		        $qrcode = $DB->GetRs("qrcode","name","WHERE code = '{$fr}'");
		        if(!empty($qrcode)) {
		            $qrcode_name = "来源：".$qrcode['name'];
		            if(!empty($ch)) $qrcode_name .= "-".$ch;
		        }
		    }
	    }
    	return $qrcode_name;
    }

	/**
	 * 线下注册来源记录
	 * @return  返回线下商户代码
	 */
    public function getBusinessCodeFrom($field = 'business_code'){
    	global $DB;
    	$business_code = "";
	    if(!empty($_SESSION['qrcode_fr']) && !empty($_SESSION['qrcode_ch'])) {
	        $fr = base64_decode($_SESSION['qrcode_fr']);
	        $ch = mysql_real_escape_string(base64_decode($_SESSION['qrcode_ch']));
	        if($fr == 'business') {
	        	$business = $DB->GetRs("business","business_name,business_code","WHERE business_code = '{$ch}'");
	        	$business_code = isset($business['business_code'])?$business['business_code']:'';
	        }
	    }
	    if($field == 'business_code'){
	    	return $business_code;
	    }else{
	    	return isset($business[$field]) ? $business[$field] : '';
	    }
    }

    /**
      * 计算充值赠送金额
      * @param $user_id 	  用户id
      * @param $total_fee 	  充值金额
      * @param $business_code 商户代码
      * @return 充值赠送金额
      **/
    public function getRechargePlus($user_id,$total_fee,$business_code = '') {
    	global $DB;
    	//优惠金额初始化为0
    	$plus_price = 0;
    	//获取用户信息
        $sql = "SELECT u.*,s.seller_level_id FROM users u
                LEFT JOIN seller s ON u.user_id = s.user_id
                WHERE u.user_id = $user_id LIMIT 1";
		$result = $DB->query($sql);  
		$user = $DB->fetch_array($result);
	    //查找属于哪个充值优惠组
	    if(empty($user['seller_level_id'])) {
	    	//会员
	    	$recharge = self::_findBeyondRecharge($user['level']);
	    	if(!empty($business_code)) {
	    		$_recharge = self::_findBeyondRecharge($user['level'],'user',$business_code);
	    		//如果存在商户充值优惠组(用户等级)，则返回优惠组
	    		if(!empty($_recharge)) $recharge = $_recharge;
	    	}
	    }else {
	    	//分销
	    	$recharge = self::_findBeyondRecharge($user['seller_level_id'],'seller');
	    	if(!empty($business_code)) {
	    		$_recharge = self::_findBeyondRecharge($user['seller_level_id'],'seller',$business_code);
	    		//如果存在商户充值优惠组(分销等级)，则返回优惠组
	    		if(!empty($_recharge)) $recharge = $_recharge;
	    	}
	    }

	    //存在充值优惠
	    if(!empty($recharge)){
	    	$recharge['recharge_value'] = unserialize($recharge['recharge_value']);
	    	$recharge['recharge_price'] = unserialize($recharge['recharge_price']);

	    	//优惠类别
	    	if($recharge['not_top']) {
	    	    //上不封顶
	    	    $prepaid_plus_full = (float)$recharge['recharge_value'][0];
	    	    $prepaid_plus_plus = (float)$recharge['recharge_price'][0];
	    	    if($prepaid_plus_full>0 && $total_fee>=$prepaid_plus_full) {
	    	        $plus_price = $prepaid_plus_plus*floor($total_fee/$prepaid_plus_full);
	    	    }
	    	}else {
	    	    //分层优惠
	    	    $layer = count($recharge['recharge_value']);
	    	    $recharge_value = $recharge['recharge_value'];
	    	    $recharge_price = $recharge['recharge_price'];
	    	    $total = array();//优惠
	    	    $current_price = (float)$total_fee;//充值金额
	    	    for ($i=0; $i < $layer; $i++) { 
	    	        $value = $recharge_value[$i];
	    	        $price = $recharge_price[$i];
	    	        if((int)$value == 0 || (int)$price == 0) continue;
	    	        if($current_price >= $value) $total[$i] = $price;
	    	    }

	    	    // 返回最大优惠值
	    	    $plus_price = (float)@max($total);
	    	} 
	    }
	   	//充值赠送金额
	    return $plus_price;
    }

    /**
      * 获取充值优惠组
      * @param $user_id  	  用户id
      * @param $total_fee 	  充值金额
      * @param $business_code 商户代码
      * @return array
      **/
    public function getRecharge($user_id,$business_code = '') {
    	global $DB;
    	//优惠金额初始化为0
    	$plus_price = 0;
    	//充值优惠初始化
    	$recharge = array();
    	//获取用户信息
        $sql = "SELECT u.*,s.seller_level_id FROM users u
                LEFT JOIN seller s ON u.user_id = s.user_id
                WHERE u.user_id = $user_id LIMIT 1";
		$result = $DB->query($sql);  
		$user = $DB->fetch_array($result);
	    //查找属于哪个充值优惠组
	    if(empty($user['seller_level_id'])) {
	    	//会员
	    	$recharge = self::_findBeyondRecharge($user['level']);
	    	if(!empty($business_code)) {
	    		$_recharge = self::_findBeyondRecharge($user['level'],'user',$business_code);
	    		//如果存在商户充值优惠组(用户等级)，则返回优惠组
	    		if(!empty($_recharge)) $recharge = $_recharge;
	    	}
	    }else {
	    	//分销
	    	$recharge = self::_findBeyondRecharge($user['seller_level_id'],'seller');
	    	if(!empty($business_code)) {
	    		$_recharge = self::_findBeyondRecharge($user['seller_level_id'],'seller',$business_code);
	    		//如果存在商户充值优惠组(分销等级)，则返回优惠组
	    		if(!empty($_recharge)) $recharge = $_recharge;
	    	}
	    }

	    //存在充值优惠
	    if(!empty($recharge)){
	    	$recharge['recharge_value'] = unserialize($recharge['recharge_value']);
	    	$recharge['recharge_price'] = unserialize($recharge['recharge_price']);
	    }
	   	//充值优惠信息
	    return $recharge;
    }

    /**
      * 取出用户所在的充值组
      * @param $level_id 	  用户类型等级
      * @param $type     	  用户类型/user/seller
      * @param $business_code 线下所属店铺
      * @return array
      **/
    private function _findBeyondRecharge($level_id,$type = 'user',$business_code = '') {
    	global $DB;
        $sql = "SELECT r.* FROM recharge r JOIN recharge_object o ON r.recharge_id = o.recharge_id";
        //专注线下
        if(!empty($business_code)) {
            $sql .= " JOIN recharge_business b ON r.recharge_id = b.recharge_id";
        }
    	//搜索条件
        $sql .= " WHERE o.type = '$type' AND o.level_id = $level_id AND date(r.start_date) <= CURDATE() AND date(r.end_date) >= CURDATE()";
        //区分线上线下
        if(empty($business_code)) {
            $sql .= " AND r.recharge_id NOT IN (SELECT DISTINCT recharge_id FROM recharge_business)";
        }else {
            $sql .= " AND b.business_code = '$business_code'";
        }
        //排序并取出一条
        $sql .= " ORDER BY r.start_date DESC,r.end_date DESC,r.recharge_id DESC LIMIT 1";
        // echo $sql;exit();
        $result = $DB->query($sql);  
        return $DB->fetch_array($result);
    }

    /**
     * 是否允许首次充值返现10%
     * 注册时间 2018-12-22 之后
     * 传入total_fee 计算赠送金额
     @return array 充值返现规则
     */
    public function firstRecharge($user_id, $total_fee = 0)
    {
    	global $DB;
    	$query = $DB->query("select user_id, `level`, create_date, (select id from seller where user_id={$user_id} limit 1) as seller_id from users where user_id={$user_id}");
		$user = $DB->fetch_array($query);
    	$result = [
    		'code' => -1,
    		'msg' => ''
    	];
    	try {
    		$query = $DB->query("SELECT * FROM recharge_firstset WHERE date(start_date) <= CURDATE() AND date(end_date) >= CURDATE()");
    		$set = $DB->fetch_array($query);
	    	if(empty($set)){
	    		throw new Exception('无活动');
	    	}
	    	// 只有普通会员可参与
	    	if($set['only_user'] == 1 && ($user['level']>0 || $user['seller_id']>0)){
	    		throw new Exception('只有普通会员可参与');
	    	}
	    	// 限制注册时间
	    	if(strtotime($user['create_date']) >= strtotime($set['reg_time'])){
		    	$query = $DB->query("SELECT bag_id FROM bag WHERE user_id={$user_id} AND type='prepaid' AND pay_status='paid' AND date(pay_date) >= '{$set['start_date']}' AND (LOCATE('合并付款', note)=0 or note IS NULL)");
		    	$bag = $DB->fetch_array($query);
		    	if(empty($bag)){
					$result['code'] = 0;
					$result['title'] = $set['title'];
					$result['money'] = $set['money'];
					$result['step'] = $set['step'];
					$result['ratio'] = $set['return_ratio'];
					$result['plus_price'] = 0;
					$result['tips'] = "新用户首次每充{$set['money']}返现".$set['money']*($set['return_ratio']/100)."，仅1次机会！";
		    	}
		    }
    	} catch (Exception $e) {
    		$result['code'] = -1;
            $result['msg']  = $e->getMessage();
    	}

    	// 计算赠送金额
    	if(isset($result['ratio']) && $result['ratio'] > 0 && $total_fee > 0) {
    		$money = $result['money'];
    		$step = $result['step'];
    		$ratio = $result['ratio'];
			$plus = floatval($money*($ratio/100));
    		if($total_fee >= $money){
    			$result['plus_price'] = intval(($total_fee - $money) / $step) * $plus + $plus;
    		}
    	}
		return $result;
    }
}
