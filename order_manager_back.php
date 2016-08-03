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
   
   $sql = "SELECT o.*, d.device_name FROM vem_order_list o, vem_device d where o.device_id = d.id and status = 1 and o.create_date like '" . $current . "%' ORDER BY o.id DESC";
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