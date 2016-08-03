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
  case 'box_list' :
   $device_id = getQueryData ( "device_id" );
   $sql = "SELECT *  FROM vem_device_box where device_id = " . $device_id . " ORDER BY id ASC";
   
   $listsql = "SELECT * FROM (" . $sql . ") AS t";
   
   $result = querySQL ( $db, $listsql );
   
   $data = array ();
   while ( $row = mysql_fetch_assoc ( $result ) ) {
    $data [] = $row;
   }
   
   responseData ( true, null, $data );
   
   break;
  case 'box_update' :
   $box_id = getQueryData ( 'box_id' );
   $goods_name = getQueryData ( 'goods_name' );
   $goods_price = getQueryData ( 'goods_price' );
   $status = getQueryData ( 'status' );
   $sql = "UPDATE vem_device_box SET status = " . $status . ", goods_name = " . correctSQL ( $goods_name ) . ", goods_price = " . $goods_price . " WHERE id = " . $box_id;
   
   $res = executeSQL ( $db, $sql );
   if (! isset ( $res )) {
    responseData ( false, "更新失败" );
    $logger->error ( $sql );
    break;
   }
   responseData ( true );
   
   break;
  case 'box_info' :
   $box_id = getQueryData ( 'box_id' );
   $sql = "SELECT * FROM vem_device_box WHERE id = " . $box_id;
   
   $result = querySQL ( $db, $sql );
   $row = mysql_fetch_assoc ( $result );
   
   $data = array ();
   $data [] = $row;
   
   echo '{ "data": ';
   
   if ($data != null) {
    $first = true;
    foreach ( $data as $item ) {
     if ($first)
      $first = false;
     else
      echo ",";
     
     echo json_encode ( $item );
    }
   }
   
   echo ',"success": "true"}';
   break;
  
  default :
   
   responseData ( false, '无效的请求' );
   break;
 }
} catch ( Exception $e ) {
 responseData ( false, $e->getMessage () );
}
?>