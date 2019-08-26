<?php
function creat_area_array($DB){
    $area_list = array();
    $Table = "area";
    $Fileds = "*";
    $Condition = "where parent_id=1 order by area_id asc";
    $Row = $DB->Get($Table, $Fileds, $Condition, 0);
    $RowCount = $DB->num_rows($Row);
    if($RowCount!=0){
        while($result = $DB->fetch_assoc($Row)){

            $city_array = array();
            $Condition_city = "where parent_id=".$result["area_id"]." order by area_id asc";
            $Row_city = $DB->Get($Table, $Fileds, $Condition_city, 0);
            $RowCount_city = $DB->num_rows($Row_city);
            if($RowCount_city!=0){
                while($result_city = $DB->fetch_assoc($Row_city)){

                    $district_array = array();
                    $Condition_district= "where parent_id=".$result_city["area_id"]." order by area_id asc";
                    $Row_district = $DB->Get($Table, $Fileds, $Condition_district, 0);
                    $RowCount_district = $DB->num_rows($Row_district);
                    if($RowCount_district!=0){
                        while($result_district = $DB->fetch_assoc($Row_district)){
                            array_push($district_array, array(
                                "areaname"=>$result_district["area_name"],
                                "area_id"=>$result_district["area_id"],
                                "parent_id"=>$result_district["parent_id"],
                                "area_type"=>$result_district["area_type"]
                            ));
                        }

                    }

                    array_push($city_array, array(
                        "areaname"=>$result_city["area_name"],
                        "area_id"=>$result_city["area_id"],
                        "parent_id"=>$result_city["parent_id"],
                        "area_type"=>$result_city["area_type"],
                        "sub"=>$district_array,
                    ));
                }

            }


            array_push($area_list, array(
                "areaname"=>$result["area_name"],
                "area_id"=>$result["area_id"],
                "parent_id"=>$result["parent_id"],
                "area_type"=>$result["area_type"],
                "sub"=>$city_array,
            ));


        }

    }

    return $area_list;
}

$CKey = 'area';
$resultCache = $Cache -> get($CKey);
if (is_null($resultCache)){
    $AREA = creat_area_array($DB);
    $Cache -> set($CKey, $AREA, 100000);
}else{
    $AREA = $resultCache;
}
