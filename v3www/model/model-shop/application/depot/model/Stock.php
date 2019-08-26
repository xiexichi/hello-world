<?php

/**
 *
 */

namespace app\depot\model;

class Stock extends Base
{

	/**
	 * [getItems description]
	 * @param  [type] $item_code        [description]
	 * @param  [type] $product_color_id [description]
	 * @return [type]                   [description]
	 */
	public function getItems($item_code, $product_color_id, $shop_id = NULL, $key_type = NULL, $shop_depot_pre_select_id = NULL){

		// 是否进货标记
		$isPurchase = empty($this->data['is_purchase']) ? false : $this->data['is_purchase'];

		// 如果有规格
		if (!empty($this->data['sku_code'])) {
			$skuCode = trim(str_replace('，', ',', $this->data['sku_code']));
			$this->where('a.sku_code', 'IN', $skuCode);
		}

		// 2019-02-25 判断是否需要查找商品销量
		if (!empty($this->data['sales_volume'])) {
			$whereShopId = '';
			if ($shop_id) {
				$whereShopId = " AND oi.shop_id = {$shop_id}";
			}
			// 销量
			$sales = "(SELECT SUM(ogi.num) FROM order_goods_info ogi JOIN order_info oi ON ogi.order_id = oi.order_id WHERE oi.finished_status = 1 AND ogi.erp_code = CONCAT(a.item_code,',',a.sku_code) {$whereShopId} ) sales";

			$this->field($sales);
		}

		// pe($this->connection);
		// 将对象转换为数组方法：collection()->toArray()
		$this->alias('a')
			 ->field('a.*,a.id stock_id,IFNULL(sd.quantity, 0) shop_quantity,"" pre_select_quantity')
			 ->join("shop_depot sd", "a.id = sd.stock_id", 'LEFT')
			 ->where('a.item_code', $item_code)
			 ->where('a.product_color_id', $product_color_id);

		// 如果有店铺id并且不是进货类型
		if ($shop_id && !$isPurchase) {
			$this->where("sd.shop_id", $shop_id);
		}

		// 如果有预选标签id
		if ($shop_depot_pre_select_id) {
			$this->field('sdpsi.quantity pre_select_quantity')
				 ->join('shop_depot_pre_select sdps', "sdps.id = {$shop_depot_pre_select_id}", 'LEFT')
				 ->join('shop_depot_pre_select_item sdpsi', "sdpsi.shop_depot_pre_select_id = sdps.id AND a.id = sdpsi.stock_id", 'LEFT');
		}

		// 查找数据
		$items = collection($this->select());

		// pe($this->getLastSql());

        if( !empty($items) && $key_type = 'size' ){
            $items = $items->toArray();
            $newItems = [];
            foreach ($items as $k => $v) {

                // 拆分码数
                $skuNameArr = explode(' ', $v['sku_name']);

                if (count($skuNameArr) > 1) {
                    $v['size'] = trim($skuNameArr[1]);
                } else {
                    $v['size'] = trim($skuNameArr[0]);
                }

                // 添加数据
                $newItems[$v['size']] = $v;
            }

            return sizeKeySort($newItems);
        }


		return $items;
	}

    /**
     * 获取商品sku可售库存数
     * @param string $item_code
     * @param string $sku_code
     */
	public function getItemStock($item_code='',$sku_code='',$shop_id){
	    $stock = 0;
	    if( empty($item_code) || empty($shop_id) ){
            return $stock;
        }
        $where['sd.type'] = 1;
        $where['s.item_code'] = $item_code;
        $where['s.sku_code'] = $sku_code;
        $where['sd.shop_id'] = $shop_id;
        $stock = $this->alias('s')
            ->join('shop_depot sd',"sd.stock_id = s.id")
            ->where($where)->value('sd.availavle_quantity');
        if(is_null($stock)){
            $stock = 0;
        }
        return $stock;
    }

    /**
     * 检查是否超出库存
     * @param string $item_code     erp 商品code
     * @param string $sku_code      erp sku code
     * @param int $num              数量
     * @param $shop_id              店铺id
     * @return bool
     */
    public function checkStockOver($item_code='',$sku_code='',$num=0,$shop_id){
        if( empty($item_code) || empty($shop_id) ){
            return false;
        }
        $stock = $this->getItemStock($item_code,$sku_code,$shop_id);
        if( $stock == 0 || $stock < $num ){
            return false;
        }
        return true;
    }

    /**
     * [getStockField 获取库存字段]
     * @param  [type] $stock_id   [库存id]
     * @param  [type] $field_name [字段名称]
     * @return [type]             [description]
     */
    public function getStockField($stock_id, $field_name){
        $stock = $this->where('id', $stock_id)->find();
        if ($stock) {
            if (empty($stock[$field_name])) {
                return '';
            }
            return $stock[$field_name];
        }

        return '';
    }

}
