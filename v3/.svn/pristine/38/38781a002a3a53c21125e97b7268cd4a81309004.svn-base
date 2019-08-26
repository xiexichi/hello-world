<?php

/**
 * 
 */

namespace app\common\model;

use think\Model;
use think\Db;

class SystemLog extends Model
{

	// 添加参数
	public $params;


	public function add(){

		$request = Request();
		$data = array();
		$data['admin_id'] = 1; //临时写入，需要修改
		$data['log_ip'] = $request->ip();
		$data['log_url'] = $request->url();
		$data['log_info'] = set_init($params['log_info'], '');
		$data['extra'] = set_init($params['extra'], '');
		

		// 除了修改，其他可以不用记录参数
		if (humpToLine($request->action()) == 'edit') {
			$data['params'] = json_encode($request->param(), JSON_UNESCAPED_UNICODE);
		}

		$data['module'] = humpToLine($request->module());
		$data['controller'] = humpToLine($request->controller());
		$data['action'] = humpToLine($request->action());
		$data['create_time'] = date('Y-m-d H:i:s');
		
		pe($data);
	}

}