<?php
require_once ('../include/share.php');

$delivery_userinfo = getSessionData ( "delivery_userinfo" );
$expireMessage = "";
if ($delivery_userinfo == NULL) {
 $expireMessage = "会话已过期，请重新登录";
 require_once ("index.php");
 exit ();
}

if (checkDeliveryLoginInfo ( $delivery_userinfo ['id'] )) {
 $expireMessage = "登录已超时，请重新登录";
 require_once ("index.php");
 exit ();
} else {
 saveDeliveryLoginInfo ( $delivery_userinfo ['id'] );
}

$db = connectDB ();

try {
 $func = $_REQUEST ["func"];
 
 switch ($func) {
  case 'box_open' :
   $box_id = getQueryData ( 'box_id' );
   if (sendOpenBox ( $db, $box_id, 'system' )) {
    responseData ( true );
   } else {
    responseData ( false, '发送命令失败' );
   }
   break;
  
  case 'box_over' :
   $box_id = getQueryData ( 'box_id' );
   
   $sql = "UPDATE vem_device_box SET status = 1 WHERE id = " . $box_id;
   $res = executeSQL ( $db, $sql );
   if (! isset ( $res )) {
    responseData ( false, "更新失败" );
    break;
   }
   responseData ( true, "操作成功" );
   
   break;
  default :
   
   responseData ( false, '无效的请求' );
   break;
 }
} catch ( Exception $e ) {
 responseData ( false, $e->getMessage () );
}
?>