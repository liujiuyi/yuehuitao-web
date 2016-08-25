<?php
require '../smarty/Smarty.class.php';
require_once ('../include/share.php');
$smarty = new Smarty ();
$smarty->compile_check = true;

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

$device_id = getQueryData ( "device_id" );
if (empty ( $device_id )) {
 echo "<script>alert('参数错误');history.go(-1);</script>";
 return;
}

$db = connectDB ();
$sql = "SELECT * FROM vem_device_box WHERE device_id = " . $device_id;
$result = querySQL ( $db, $sql );
$data = array ();
while ( $row = mysql_fetch_assoc ( $result ) ) {
 $data [] = $row;
}
$smarty->assign ( "box_list", $data );
$smarty->display ( 'box_list.tbl' );
?>