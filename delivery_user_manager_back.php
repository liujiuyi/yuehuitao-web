<?php
require_once ('include/share.php');
require_once ('include/delivery_user.php');
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
  case 'delivery_user_list' :
   $sql = "SELECT * FROM vem_delivery_user where admin_user_id = " . $userinfo ["id"] . " ORDER BY id ASC";
   
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
  case 'delivery_user_create' :
   $username = getQueryData ( 'username' );
   $password = getQueryData ( 'password' );
   $type = getQueryData ( 'type' );
   
   $sql = "INSERT INTO vem_delivery_user(admin_user_id, username, password, create_date) VALUES( " . $userinfo ["id"] . ", " . correctSQL ( $username ) . ", " . correctSQL ( $password ) . ", now())";
   
   $res = executeSQL ( $db, $sql );
   if (! isset ( $res )) {
    responseData ( false, "添加失败" );
    $logger->error ( $sql );
    break;
   }
   responseData ( true );
   
   break;
  
  case 'delivery_user_update' :
   $delivery_user_id = getQueryData ( 'id' );
   $username = getQueryData ( 'username' );
   $password = getQueryData ( 'password' );
   $type = getQueryData ( 'type' );
   $sql = "UPDATE vem_delivery_user SET username = " . correctSQL ( $username ) . ", password = " . correctSQL ( $password ) . " WHERE id = " . $delivery_user_id;
   
   $res = executeSQL ( $db, $sql );
   if (! isset ( $res )) {
    responseData ( false, "更新失败" );
    $logger->error ( $sql );
    break;
   }
   responseData ( true );
   
   break;
  case 'delivery_user_info' :
   $delivery_user_id = getQueryData ( 'delivery_user_id' );
   $row = get_delivery_user_info ( $db, $delivery_user_id );
   
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
  
  case 'delivery_user_delete' :
   $id = getQueryData ( 'id' );
   
   $sql = "DELETE FROM vem_delivery_user WHERE id = " . $id;
   
   $res = executeSQL ( $db, $sql );
   if (! isset ( $res )) {
    responseData ( false, "删除失败" );
    $logger->error ( $sql );
    break;
   }
   
   responseData ( true );
   break;
  
  case 'push_device' :
   $delivery_user_id = getQueryData ( 'delivery_user_id' );
   $devices = getQueryData ( 'devices' );
   for($i = 0; $i < count ( $devices ); $i ++) {
    if ($i == 0)
     $device_ids = $devices [$i];
    else
     $device_ids = $device_ids . "," . $devices [$i];
   }
   $sql = "UPDATE vem_delivery_user SET device_ids = " . correctSQL ( $device_ids ) . " WHERE id = " . $delivery_user_id;
   
   $res = executeSQL ( $db, $sql );
   if (! isset ( $res )) {
    responseData ( false, "更新失败" );
    $logger->error ( $sql );
    break;
   }
   responseData ( true );
   
   break;
  
  case 'delivery_list' :
   $delivery_user_id = getQueryData ( 'delivery_user_id' );
   $sql1 = "SELECT id, device_name FROM vem_device WHERE admin_user_id = " . $userinfo ["id"];
   $sql2 = "SELECT d.id, d.device_name FROM vem_device d, vem_delivery_user u WHERE find_in_set(d.id, u.device_ids) AND u.id = " . $delivery_user_id . " AND d.admin_user_id = " . $userinfo ["id"];
   
   $result1 = querySQL ( $db, $sql1 );
   $data1 = array ();
   while ( $row1 = mysql_fetch_assoc ( $result1 ) ) {
    $data1 [] = $row1;
   }
   
   $result2 = querySQL ( $db, $sql2 );
   $data2 = array ();
   while ( $row2 = mysql_fetch_assoc ( $result2 ) ) {
    $data2 [] = $row2;
   }
   
   $data = array ();
   for($i = 0; $i < count ( $data1 ); $i ++) {
    $data1 [$i] ['check'] = false;
    for($j = 0; $j < count ( $data2 ); $j ++) {
     if ($data1 [$i] ['id'] == $data2 [$j] ['id']) {
      $data1 [$i] ['check'] = true;
     }
    }
    $data [] = $data1 [$i];
   }
   
   responseData ( true, null, $data );
   
   break;
  default :
   
   responseData ( false, '无效的请求' );
   break;
 }
} catch ( Exception $e ) {
 responseData ( false, $e->getMessage () );
}
?>