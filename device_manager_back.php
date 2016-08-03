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
  case 'device_list' :
   $sql = "SELECT *  FROM vem_device ORDER BY id ASC";
   
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
     'totalCount' => $totalCount 
   ) );
   
   break;
  case 'device_create' :
   $device_code = getQueryData ( 'device_code' );
   $device_name = getQueryData ( 'device_name' );
   $device_address = getQueryData ( 'device_address' );
   $box_number = getQueryData ( 'box_number' );
   
   $sql = "INSERT INTO vem_device(device_code, device_name, device_address ) VALUES( " . correctSQL ( $device_code ) . ", " . correctSQL ( $device_name ) . ", " . correctSQL ( $device_address ) . ")";
   
   $res = executeSQL ( $db, $sql );
   if (! isset ( $res )) {
    responseData ( false, "添加失败" );
    $logger->error ( $sql );
    break;
   }
   
   $device_id = mysql_insert_id ();
   
   for($i = 1; $i <= $box_number; $i ++) {
    $sql = "INSERT INTO vem_device_box(box_number, device_id ) VALUES(" . $i . ", " . $device_id . ")";
    
    $res = executeSQL ( $db, $sql );
   }
   responseData ( true );
   
   break;
  
  case 'device_update' :
   $device_id = getQueryData ( 'device_id' );
   $device_name = getQueryData ( 'device_name' );
   $device_address = getQueryData ( 'device_address' );
   $device_code = getQueryData ( 'device_code' );
   $sql = "UPDATE vem_device SET device_code = " . correctSQL ( $device_code ) . ", device_name = " . correctSQL ( $device_name ) . ", device_address = " . correctSQL ( $device_address ) . " WHERE id = " . $device_id;
   
   $res = executeSQL ( $db, $sql );
   if (! isset ( $res )) {
    responseData ( false, "更新失败" );
    $logger->error ( $sql );
    break;
   }
   responseData ( true );
   
   break;
  case 'device_info' :
   $device_id = getQueryData ( 'device_id' );
   $sql = "SELECT * FROM vem_device WHERE id = " . $device_id;
   
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
  
  case 'device_delete' :
   $id = getQueryData ( 'id' );
   
   $sql = "DELETE FROM vem_device WHERE id = " . $id;
   
   $res = executeSQL ( $db, $sql );
   if (! isset ( $res )) {
    responseData ( false, "删除失败" );
    $logger->error ( $sql );
    break;
   }


   $sql = "DELETE FROM vem_device_box WHERE device_id = " . $id;
    
   $res = executeSQL ( $db, $sql );
   
   responseData ( true );
   break;
  default :
   
   responseData ( false, '无效的请求' );
   break;
 }
} catch ( Exception $e ) {
 responseData ( false, $e->getMessage () );
}
?>