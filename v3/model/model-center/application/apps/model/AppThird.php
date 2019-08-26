<?php
/**
 * 网站参数设置模型
 */
namespace app\apps\model;

use think\Db;
use app\common\model\CommonModel;

class AppThird extends CommonModel
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	protected $table = 'app_third';
}