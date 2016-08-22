<?php
require_once ('share.php');
function get_order_info($db, $order_id) {
 $sql = "select * from vem_order_list where order_id =" . correctSQL ( $order_id );
 $result = querySQL ( $db, $sql );
 return mysql_fetch_assoc ( $result );
}

function notify_order($db, $out_trade_no) {
 $order_info = get_order_info ( $db, $out_trade_no );
 if ($order_info != null && $order_info ['status'] != 1) {
  // 更改订单状态
  $sql = "update vem_order_list set status = 1 where order_id =" . correctSQL ( $out_trade_no );
  executeSQL ( $db, $sql );
  
  // 更改盒子状态
  $sql = "update vem_device_box set status = 0 where id =" . $order_info ['box_id'];
  executeSQL ( $db, $sql );
  
  // 发送http请求开门
  if (sendOpenBox ( $db, $order_info ['box_id'], $out_trade_no )) {
   // 更改订单状态
   $sql = "update vem_order_list set is_open = 1 where order_id =" . correctSQL ( $out_trade_no );
   executeSQL ( $db, $sql );
  }
 }
}
?>