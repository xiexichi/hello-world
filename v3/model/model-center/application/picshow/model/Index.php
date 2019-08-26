<?php
/**
 * 25boy v3 广告列表管理
 * 2019-02-26 张文杰
 */

namespace app\picshow\model;

use think\Db;
use app\common\model\CommonModel;

class Index extends CommonModel
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	protected $table = 'ad';

	// 一对一关联内容模块表
	public function module()
  {
		return $this->hasOne('app\picshow\model\Module', 'id', 'module_id');
  }

	// 一对一关联广告位表
	public function position()
  {
		return $this->hasOne('app\picshow\model\Position', 'id', 'position_id');
  }

  // 将parameter限制为json类型
  public function setParameterAttr($value)
  {
		$parameter = new \stdClass();
    if(!empty($value)) {
			parse_str($value, $parameter);
			$parameter = array_filter($parameter);
		}
		return json_encode($parameter);
  }
}