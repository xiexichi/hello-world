<?php
/*
* 广告链接
* $ad_id 广告
*/
if(isset($_GET['go'])){
    $_GET['go'] = htmlentities($_GET['go']);
	$ad_id = (int)$_GET['go'];
	if($ad_id > 0){
		$row = get_row($ad_id);
		$url = 'http://'.$base_url;
		if(!empty($row)){
            switch ($row['type']) {
                case 'image':
                    $url = $row['url'];
                    break;
                case 'product':
                    $url = '/?m=category&a=product&id='.$row['product_id'];
                    break;
                default:
                    $url = 'http://'.$base_url;
                    break;
            }
        }
		header('Location: '.$url);
        exit;
	}
}


function get_row($ad_id){
	global $DB;
	$Condition = "WHERE `ad_id`=".(int)$ad_id." AND (start_date<NOW() OR start_date IS NULL) AND (end_date>NOW() OR end_date IS NULL)";
	return $DB->GetRs('picshow',"*",$Condition);
}