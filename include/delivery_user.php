<?php
function get_delivery_user_info($db, $delivery_user_id) {
 $sql = "SELECT *  FROM vem_delivery_user where id = " . $delivery_user_id;
 $result = querySQL ( $db, $sql );
 return mysql_fetch_assoc ( $result );
}
?>