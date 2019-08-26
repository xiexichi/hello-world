<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require $_SERVER['DOCUMENT_ROOT'].'/model/public/build.area.cache.php';

$depth = 0;
$area_id_path = isset($_GET["path"]) ? $_GET["path"] : "";
$is_state = isset($_GET["state"]) ? $_GET["state"] : 'Y';
$return_area = array();
if($area_id_path!=""){
    $aip = explode(",",$area_id_path);
    $depth = count($aip);

    //print_r($aip);
    if(isset($AREA[$aip[0]]["sub"])){
        switch($depth){
            case 1:
                foreach($AREA[$aip[0]]["sub"] as $Key){
                    array_push($return_area,array(
                        "areaname"=>$Key["areaname"],
                        "area_id"=>$Key["area_id"]
                    ));
                }
                break;
            case 2:
                foreach($AREA[$aip[0]]["sub"][$aip[1]]["sub"] as $Key){
                    array_push($return_area,array(
                        "areaname"=>$Key["areaname"],
                        "area_id"=>$Key["area_id"]
                    ));
                }
                break;
        }
    }

}else if($is_state == 'Y'){
    foreach($AREA as $Key){
        array_push($return_area,array(
            "areaname"=>$Key["areaname"],
            "area_id"=>$Key["area_id"]
        ));
    }
}

$return_area = array(
    "status"=>"success",
    "depth"=>$depth,
    "data"=>$return_area
);

echo json_encode($return_area);