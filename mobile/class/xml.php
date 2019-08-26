<?php 

class xml extends Error{
	private $filepath;  
	private $xmldoc;

	public function __construct($filepath){
        $this->filepath=$filepath;
    }
    public function GetData(){
		if(!file_exists($this->filepath)){
			if($this->filepath == "library/xml/config.xml"){
				$errorArray = array(
					"title"=>"网站配置文件错误!",
					"description"=>"网站配置文件可能已经损坏或被删除，请联系管理员 (info@25boy.com) 重建...",
					"footer"=>"发生时间：".date("Y-m-d H:i:s")
				);
				parent::OutputError($errorArray);
				exit();
			}
		}else{

			$this->xmldoc = simplexml_load_file($this->filepath);
			return json_decode(json_encode($this->xmldoc),TRUE);

		}
    }
} 
?>