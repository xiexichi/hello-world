<?php
class Error {
	private $BaseCode = "<h1>{title}</h1><p>{description}</p><small>{footer}</small>";

	public function __construct(){}

    public function OutputError($errorArray){
    	$tempErrorPageHtml = $this->BaseCode;
		$tempErrorPageHtml = str_replace("{title}", $errorArray["title"], $tempErrorPageHtml);
		$tempErrorPageHtml = str_replace("{description}", $errorArray["description"], $tempErrorPageHtml);
		$tempErrorPageHtml = str_replace("{footer}", $errorArray["footer"], $tempErrorPageHtml);
    	echo $tempErrorPageHtml;
    	//var_dump($tempErrorPageHtml);
    }


    public function show($msg,$title='',$gourl=''){
        if(empty($gourl)){
            $errorhtml = '';
            if($title){
                $errorhtml .= '<h3>'.$title.'</h3>';
            }
            $errorhtml .= '<p>'.$msg.'</p>';
            echo $errorhtml;
        }else{
            echo '<script>alert("'.$msg.'");window.location.href="'.$gourl.'";</script>';
        }
        exit;            

    }


}
?>