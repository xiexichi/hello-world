<?php
/*
* 2016-03-29
* 取消使用curl获取，已经放到 common.php function get_category()
*/
require $_SERVER['DOCUMENT_ROOT']."/config.php";
$returnjson = array();
$Table = "category";
$Condition = "where status=1 AND parent=0 order by sort asc";
$Row = $DB->getPage($Table,$Fileds,$Condition,0);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);
if($RowCount!=0){
    while($result = $DB->fetch_assoc($Row)){
        $ChildrenArray = array();
        $RowChildren = $DB->getPage($Table,"*","where status=1 AND parent=".$result["category_id"]." order by sort asc",0);
        $RowChildren = $DB->result;
        $RowChildrenCount = $DB->num_rows($RowChildren);
        if($RowChildrenCount!=0){
            while($resultChildren = $DB->fetch_assoc($RowChildren)){
                array_push($ChildrenArray, array(
                    "category_name"=>$resultChildren["category_name"],
                    "category_id"=>$resultChildren["category_id"],
                    "img_url"=>$resultChildren["img_url"]
                ));
            }

        }
        array_push($returnjson, array(
            "category_name"=>$result["category_name"],
            "category_id"=>$result["category_id"],
            "img_url"=>$result["img_url"],
            "childrens"=>$ChildrenArray
        ));
    }

}
die(json_encode($returnjson));