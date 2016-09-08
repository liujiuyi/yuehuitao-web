<?php
require_once ('include/share.php');
require_once ('include/device.php');
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
   $result = get_device_box_list ( $db, $device_id );
   
   $data = array ();
   while ( $row = mysql_fetch_assoc ( $result ) ) {
    $data [] = $row;
   }
   
   responseData ( true, null, $data );
   
   break;
  case 'box_update' :
   $goods_image = savePhoto ( PHOTO_URL_PREFIX );
   $box_id = getQueryData ( 'box_id' );
   $box_no = getQueryData ( 'box_no'); 
   $goods_name = getQueryData ( 'goods_name' );
   $goods_price = getQueryData ( 'goods_price' );
   $goods_url = getQueryData ( 'goods_url' );
   $status = getQueryData ( 'status' );
   
   $sql = "UPDATE vem_device_box SET status = " . $status . ", box_no = " . correctSQL ( $box_no ) . ", 
     goods_name = " . correctSQL ( $goods_name ) . ", goods_price = " . $goods_price . ", goods_url = " . correctSQL ( $goods_url );
   
   if($goods_image && !empty($goods_image)){
     $sql .= " ,goods_image = " . correctSQL ( $goods_image );
   }
   
   $sql .= " WHERE id = " . $box_id;
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
   $row = get_device_box_info ( $db, $box_id );
   
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
  
  case 'box_open' :
   $box_id = getQueryData ( 'id' );
   if (sendOpenBox ( $db, $box_id, 'system' )) {
    responseData ( true );
   } else {
    responseData ( false, '发送命令失败' );
   }
   break;
  default :
   
   responseData ( false, '无效的请求' );
   break;
 }
} catch ( Exception $e ) {
 responseData ( false, $e->getMessage () );
}
?>