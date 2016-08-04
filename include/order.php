<?php
function get_order_info($db, $order_id) {
 $sql = "select * from vem_order_list where order_id =" . correctSQL ( $order_id );
 $result = querySQL ( $db, $sql );
 return mysql_fetch_assoc ( $result );
}
?>