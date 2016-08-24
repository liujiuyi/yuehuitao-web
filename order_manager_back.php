<?php
require_once ('include/share.php');
// DB连接
$db = connectDB ();

// login user info
$userinfo = getSessionData ( "userinfo" );
if ($userinfo == null) {
 $tx = $_REQUEST ["tx"];
 if ($tx != null)
  $userinfo = loginTX ( $db, urldecode ( $tx ) );
}

try {
 $func = $_REQUEST ["func"];
 
 switch ($func) {
  case 'order_list' :
   $x_pos = getQueryData ( "pos" );
   $x_pos = isset ( $x_pos ) ? $x_pos : 0;
   $params = array ();
   
   $sql = "select date_format(date_add(CURDATE(),interval " . $x_pos . " day), '%Y-%m-%d')";
   $current = executeScalar ( $db, $sql ); // 返回日期
   
   $searchinfo = getQueryData ( 'searchinfo' );
   $admin_user_id = getQueryData ( 'admin_user_id' );
   
   $sql = "SELECT o.*, d.device_name, a.username FROM vem_order_list o LEFT JOIN vem_device d ON o.device_id = d.id LEFT JOIN vem_admin_user a ON a.id = d.admin_user_id WHERE o.status = 1 AND o.create_date like '" . $current . "%'";
   
   if ($userinfo ["type"] == 2) {
    $sql .= " AND d.admin_user_id = " . $userinfo ["id"];
   } else if ($admin_user_id != '' && isset ( $admin_user_id )) {
    $sql .= " AND d.admin_user_id = " . $admin_user_id;
   }
   
   if ($searchinfo != '' && isset ( $searchinfo ))
    $sql .= " AND (o.order_id = " . correctSQL ( $searchinfo ) . " or d.device_name LIKE '%" . $searchinfo . "%')";
   
   $sql .= "  ORDER BY o.id DESC";
   
   $listsql = "SELECT * FROM (" . $sql . ") AS t";
   
   $start = getQueryData ( 'start' );
   $limit = getQueryData ( 'limit' );
   if ($start != null && $limit != null) {
    $listsql .= " LIMIT " . $limit;
    $listsql .= " OFFSET " . $start;
   } else {
    $listsql .= " LIMIT " . PAGE_COUNT . " OFFSET 0";
   }
   
   $result = querySQL ( $db, $listsql );
   
   $data = array ();
   while ( $row = mysql_fetch_assoc ( $result ) ) {
    $data [] = $row;
   }
   
   $totalCount = executeScalar ( $db, "SELECT COUNT(*) FROM (" . $sql . ") AS tc " );
   
   responseData ( true, null, $data, array (
     'totalCount' => $totalCount,
     'current' => $current 
   ) );
   
   break;
  default :
   
   responseData ( false, '无效的请求' );
   break;
 }
} catch ( Exception $e ) {
 responseData ( false, $e->getMessage () );
}
?>