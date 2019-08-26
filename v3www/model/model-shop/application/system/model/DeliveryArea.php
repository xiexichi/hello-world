<?php
/**
 * 网站参数设置模型
 */
namespace app\system\model;

use think\Db;
use app\common\model\CommonModel;
use app\system\model\DeliveryRegion;

class DeliveryArea extends CommonModel
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	// 一对多关联配送地区信息表
	public function deliveryRegion()
  {
		return $this->hasMany('DeliveryRegion')->join('region', 'region_id=region.id');
  }

  // 修改后置
	public function editAfter($data)
	{
		$regions = explode(',', $data['regions']);
		$postData = [];
		foreach ($regions as $key => $value) {
			$postData[] = [
				'region_id' => $value,
				'delivery_area_id' => $data['id'],
			];
		}

		// 删除后添加
		$deliveryRegionModel = new DeliveryRegion();
		$deliveryRegionModel->destroy(['delivery_area_id' => $data['id']]);
		return $deliveryRegionModel->saveAll($postData);
	}

  // 添加后置
	public function addAfter($data)
	{
		$regions = explode(',', $data['regions']);
		$postData = [];
		foreach ($regions as $key => $value) {
			$postData[] = [
				'region_id' => $value,
				'delivery_area_id' => $data['id'],
			];
		}
		// 删除后添加
		$deliveryRegionModel = new DeliveryRegion();
		return $deliveryRegionModel->saveAll($postData);
	}

  // 删除后置
	public function deleted($ids)
	{
		// 开启事务
		$this->startTrans();
    // 删除
		if($res = $this->where('id','in', $ids)->delete()) {
			$deliveryRegionModel = new DeliveryRegion();
			$res = $deliveryRegionModel->where('delivery_area_id','in', $ids)->delete();
		}
		// 执行后置方法
		if (!$res) {
      // 回滚
			$this->rollback();
			return false;
		}
    // 提交事务
		$this->commit();
		return $res;
	}

}