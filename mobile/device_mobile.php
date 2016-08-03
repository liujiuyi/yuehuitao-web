<?php
require 'smarty/Smarty.class.php';
$smarty = new Smarty ();
$smarty->compile_check = true;

require_once ('../include/share.php');
// DB连接
$db = connectDB ();

$device_id = getQueryData ( "device_id" );
$sql = "SELECT *  FROM vem_device_box where device_id = " . $device_id . " ORDER BY id ASC";
$result = querySQL ( $db, $sql );
$data1 = array ();
$data2 = array ();

$index = 1;
while ( $row = mysql_fetch_assoc ( $result ) ) {
 if ($index <= 48) {
  $data1 [] = $row;
 } else {
  $data2 [] = $row;
 }
 $index ++;
}
$smarty->assign ( "box_list1", $data1 );
$smarty->assign ( "box_list2", $data2 );
$smarty->display ( 'device_mobile.tbl' );
?>