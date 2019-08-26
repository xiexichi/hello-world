<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
$id = isset($_GET["id"]) ? $_GET["id"] : 0;

if($id==0){
    echo "";
    exit;
}

$Table="products";
$Fileds = "`content`,`sku_sn`";
$Condition = "where product_id=".$id;
$product = $DB->GetRs($Table,$Fileds,$Condition);
if(empty($product)){
    echo "";
    exit;
}else{

	// 商品属性
	$sql = "select a.attr_id,a.attr_value,b.attr_name from product_attr as a left join product_attribute as b on a.attr_id=b.attr_id where product_id='{$id}'";
	$query = $DB->query($sql);
	$attribute = array();
	while ($row = $DB->fetch_array($query)) {
		$attribute[] = $row;
	}
	// 在数组开头加入sku_sn
	array_unshift($attribute,array('attr_id'=>0,'attr_value'=>$product['sku_sn'],'attr_name'=>'编号'));
	// 分割数组
	// $attribute = array_chunk($attribute, 2);

	$html = '';
    $html .= "<div class='leftcontent'>";
	$html .= '<ul class="product_attribute">';
	foreach($attribute as $attr){
		$html .= '<li><span>'.$attr['attr_name'].'：'.$attr['attr_value'].'</span></li>';
	}
	if(count($attribute)%2 == 1){
		$html .= '<li><span></span></li>';
	}
	$html .= '</ul>';
    $html .= "</div>";
    $html .= '
	    <ul class="product_attribute2">
			<li><span><img src="http://www.25boy.cn/images/tips_01.png" width="80%"></span></li>
			<li><span><img src="http://www.25boy.cn/images/tips_02.png" width="80%"></span></li>
			<li><span><img src="http://www.25boy.cn/images/tips_03.png" width="80%"></span></li>
			<li><span><img src="http://www.25boy.cn/images/tips_04.png" width="80%"></span></li>
	    </ul>';
    $html .= "<div>".$product["content"]."</div>";

    echo $html;
}