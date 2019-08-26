<?php
/**
 * 广告内容模型
 */
namespace app\picshow\model;

use think\Db;
use app\common\model\CommonModel;

class Module extends CommonModel
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	protected $table = 'ad_module';
}