<?php
require_once ('../include/share.php');
$delivery_userinfo = getSessionData ( "delivery_userinfo" );

if (isset ( $_REQUEST ["logout"] )) {
 if ($_REQUEST ['logout'] != null) {
  unset ( $_SESSION ['delivery_userinfo'] );
 }
}

if ($delivery_userinfo == null) {
 require_once ("login.php");
} else {
 if (checkDeliveryLoginInfo ( $delivery_userinfo ['id'] )) {
  require_once ("login.php");
 } else {
  saveDeliveryLoginInfo ( $delivery_userinfo ['id'] );
  gotoURL ( "device_list.php" );
 }
}
?>