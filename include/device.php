<?php
function get_device_info($db, $device_id) {
 $sql = "SELECT *  FROM vem_device where id = " . $device_id;
 $result = querySQL ( $db, $sql );
 return mysql_fetch_assoc ( $result );
}
function get_device_box_list($db, $device_id) {
 $sql = "SELECT *  FROM vem_device_box where device_id = " . $device_id . " ORDER BY id ASC";
 return querySQL ( $db, $sql );
}
function get_device_box_info($db, $box_id) {
 $sql = "SELECT *  FROM vem_device_box where id = " . $box_id;
 $result = querySQL ( $db, $sql );
 return mysql_fetch_assoc ( $result );
}
?>