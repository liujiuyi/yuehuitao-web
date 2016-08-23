<?php
function get_admin_user_info($db, $admin_user_id) {
 $sql = "SELECT *  FROM vem_admin_user where id = " . $admin_user_id;
 $result = querySQL ( $db, $sql );
 return mysql_fetch_assoc ( $result );
}
?>