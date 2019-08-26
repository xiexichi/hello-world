<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$sql = "SELECT * FROM promote_category pc
        LEFT JOIN category c ON pc.category_id = c.category_id
        ORDER BY c.sort ASC,pc.category_id ASC";   

$result = $DB->query($sql);
$data = array();
while ($row = $DB->fetch_array()) {
    array_push($data,$row);
}
echo json_encode($data);
exit;
?>
