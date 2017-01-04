<?php
require '../smarty/Smarty.class.php';
$smarty = new Smarty ();
$smarty->compile_check = true;

require_once ('../include/share.php');
require_once ('../include/device.php');
// DB连接
$db = connectDB ();

$device_id = getQueryData ( "device_id" );
$result = get_device_box_list ( $db, $device_id );
$data1 = array ();
$data2 = array ();

$index = 1;
while ( $row = mysql_fetch_assoc ( $result ) ) {
 if ($index <= 40) {
  $data1 [] = $row;
 } else {
  $data2 [] = $row;
 }
 $index ++;
}
if (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MicroMessenger' ) !== false) {
 $smarty->assign ( "submit_url", "payment/wxpay/pay.php" );
} else {
 $smarty->assign ( "submit_url", "payment/alipay/pay.php" );
}
$smarty->assign ( "box_list1", $data1 );
$smarty->assign ( "box_list2", $data2 );
$smarty->display ( 'device_mobile.tbl' );
?>