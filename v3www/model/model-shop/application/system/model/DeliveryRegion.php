<?php
/**
 * 网站参数设置模型
 */
namespace app\system\model;

use think\Db;
use app\common\model\CommonModel;

class DeliveryRegion extends CommonModel
{
	// 一对一关联地区表
	public function region()
  {
      return $this->hasOne('Region');
  }
}