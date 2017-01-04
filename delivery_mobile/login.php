<?php
require_once ('../include/share.php');
if (! isset ( $smarty )) {
 require '../smarty/Smarty.class.php';
 $smarty = new Smarty ();
 $smarty->compile_check = true;
}
$faileMessage = '';
if (isset ( $_GET ["func"] )) {
 if ($_GET ["func"] == "login") {
  $db = connectDB ();
  
  $username = $_POST ["username"];
  $password = $_POST ["password"];
  if ($username == "") {
   $faileMessage = '帐号不能为空！';
  } else if ($password == "") {
   $faileMessage = '密码不能为空！';
  } else {
   $delivery_userinfo = delivery_login ( $db, $username, $password );
   if ($delivery_userinfo != null) {
    setSessionData ( "delivery_userinfo", $delivery_userinfo );
    saveDeliveryLoginInfo ( $delivery_userinfo ['id'] );
    gotoURL ( 'index.php' );
   } else {
    $faileMessage = '帐号或密码错误！';
   }
  }
 } else if ($_GET ["func"] == "logout") {
  $db = connectDB ();
  
  $username = $_POST ["username"];
  $password = $_POST ["password"];
  if ($username == "") {
   $faileMessage = '帐号不能为空！';
  } else if ($password == "") {
   $faileMessage = '密码不能为空！';
  } else {
   $delivery_userinfo = delivery_login ( $db, $username, $password );
   if ($delivery_userinfo != null) {
    setSessionData ( "delivery_userinfo", $delivery_userinfo );
    saveDeliveryLoginInfo ( $delivery_userinfo ['id'] );
    gotoURL ( 'index.php' );
   } else {
    $faileMessage = '帐号或密码错误！';
   }
  }
 }
}

if (isset ( $faileMessage ) && $faileMessage != '') {
 echo "<script>window.alert('" . $faileMessage . "');</script>";
}
if (isset ( $expireMessage ) && $expireMessage != '') {
 $smarty->assign ( "expire_message", $expireMessage );
}
$smarty->display ( 'login.tbl' );
?>