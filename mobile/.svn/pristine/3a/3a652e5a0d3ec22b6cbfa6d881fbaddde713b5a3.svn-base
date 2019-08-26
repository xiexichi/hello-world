<?php
$CKey = 'category';
$resultCache = $Cache -> get($CKey);
if (is_null($resultCache)){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $_SERVER['HTTP_HOST']."/model/system/get.category.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $category = json_decode(curl_exec($ch),true);
    curl_close($ch);
    $Cache -> set($CKey, $category);
}else{
    $category = $resultCache;
}