<?php
require_once ('include/share.php');
require_once ('include/admin_user.php');
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
  case 'admin_user_list' :
   $sql = "SELECT *  FROM vem_admin_user ORDER BY id ASC";
   
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
  case 'admin_user_create' :
   $username = getQueryData ( 'username' );
   $password = getQueryData ( 'password' );
   $type = getQueryData ( 'type' );
   
   $sql = "INSERT INTO vem_admin_user(username, password, type, create_date) VALUES( " . correctSQL ( $username ) . ", " . correctSQL ( $password ) . ", " . $type . ", now())";
   
   $res = executeSQL ( $db, $sql );
   if (! isset ( $res )) {
    responseData ( false, "添加失败" );
    $logger->error ( $sql );
    break;
   }
   responseData ( true );
   
   break;
  
  case 'admin_user_update' :
   $admin_user_id = getQueryData ( 'id' );
   $username = getQueryData ( 'username' );
   $password = getQueryData ( 'password' );
   $type = getQueryData ( 'type' );
   $sql = "UPDATE vem_admin_user SET username = " . correctSQL ( $username ) . ", password = " . correctSQL ( $password ) . ", type = " . correctSQL ( $type ) . " WHERE id = " . $admin_user_id;
   
   $res = executeSQL ( $db, $sql );
   if (! isset ( $res )) {
    responseData ( false, "更新失败" );
    $logger->error ( $sql );
    break;
   }
   responseData ( true );
   
   break;
  case 'admin_user_info' :
   $admin_user_id = getQueryData ( 'admin_user_id' );
   $row = get_admin_user_info ( $db, $admin_user_id );
   
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
  
  case 'admin_user_delete' :
   $id = getQueryData ( 'id' );
   
   $sql = "DELETE FROM vem_admin_user WHERE id = " . $id;
   
   $res = executeSQL ( $db, $sql );
   if (! isset ( $res )) {
    responseData ( false, "删除失败" );
    $logger->error ( $sql );
    break;
   }
   
   responseData ( true );
   break;
  
  case 'admin_device_user_list' :
   $sql = "SELECT id, username FROM vem_admin_user WHERE type = 2";
   
   if ($userinfo ["type"] == 2) {
    $sql .= " AND id = " . $userinfo ["id"];
   }
   
   $result = querySQL ( $db, $sql );
   $data = array ();
   if ($userinfo ["type"] == 1) {
    $data [] = array (
      "id" => "",
      "username" => "无" 
    );
   }
   while ( $row = mysql_fetch_assoc ( $result ) ) {
    $data [] = $row;
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