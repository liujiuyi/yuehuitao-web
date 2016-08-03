<?php
require_once ('include/share.php');
$userinfo = getSessionData ( "userinfo" );

if (isset ( $_REQUEST ["logout"] )) {
 if ($_REQUEST ['logout'] != null) {
  global $logger;
  $logger->info ( $userinfo ['username'] . " 于 " . date ( 'Y-m-d H:i:s', time () ) . " 注销" );
  unset ( $_SESSION ['userinfo'] );
 }
}

$userinfo = getSessionData ( "userinfo" );
if ($userinfo == null) {
 require_once ("login.php");
} else {
 if (checkLoginInfo ( $userinfo ['id'] )) {
  require_once ("login.php");
 } else {
  saveLoginInfo ( $userinfo ['id'] );
  gotoURL ( "device_manager.php" );
 }
}
?>