<?php
// 图片空间
class Space {
	var $_table_space = 'space';
	public function __construct(){
	}

	/**
	 * 上传文件
	 * @param string    $file_id  文件名md5 id
	 * @param string    $filename  文件名
	 * @param string    $tree_id  空间目录
	 * @param string    $text  显示名
	 */
	public function addFile($file_id,$filename,$tree_id,$text,$file_type,$file_width,$file_height)
	{
		global $DB;
		$formdata = array(
			"file_id"=>$file_id,
		    "tree_id"=>(int)$tree_id,
		    "filename"=>$filename,
		    "text"=>$text,
		    "file_type"=>$file_type,
		    "file_width"=>$file_width,
		    "file_height"=>$file_height,
		    "status"=>"1",
		    "user_id"=>intval(@$_SESSION["user_id"]),
		    "create_date"=>date('Y-m-d H:i:s')
		);

		$result = $DB->Add($this->_table_space,$formdata);
		return $lasid = $DB->insert_id();

	}

	public function chkFile($file_id) {
		global $DB;

		$Condition = "where file_id='".$file_id."'";
		$row = $DB->GetRs($this->_table_space,"*",$Condition);
		if(empty($row['file_id'])){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 删除
	 * @param string    $file_ids
	 */
	public function rmClear($file_ids) {
		global $DB;
		return $DB->Del($this->_table_space,"","","file_id='".$file_ids."'");
	}

	// 取文件信息
	public function getFile($file_id,$tree_id=null){
		global $DB;

		$Condition = "where file_id='".$file_id."'";
		if((int)$tree_id > 0){
			$Condition .= " AND tree_id=".$tree_id;
		}
		$row = $DB->GetRs($this->_table_space,"*",$Condition);
		return $row;
	}
	

}
?>