<?php
require_once ('../include/share.php');
if (! isset ( $smarty )) {
 require '../smarty/Smarty.class.php';
 $smarty = new Smarty ();
 $smarty->compile_check = true;
}

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
$sql = "SELECT d.* FROM vem_device d, vem_delivery_user u WHERE find_in_set(d.id, u.device_ids) AND u.id = " . $delivery_userinfo ['id'];
$result = querySQL ( $db, $sql );
$data = array ();
while ( $row = mysql_fetch_assoc ( $result ) ) {
 $data [] = $row;
}
$smarty->assign ( "device_list", $data );
$smarty->display ( 'device_list.tbl' );
?>