<?php
require_once ('share.php');
function get_order_info($db, $order_id) {
 $sql = "select * from vem_order_list where order_id =" . correctSQL ( $order_id );
 $result = querySQL ( $db, $sql );
 return mysql_fetch_assoc ( $result );
}
function create_order($db, $order_id, $order_price) {
 $sql = "insert into vem_order_list (order_id, order_price, create_date) values (" . correctSQL ( $order_id ) . ", " . correctSQL ( $order_price ) . ", now())";
 executeSQL ( $db, $sql );
}
function get_order_goods_list($db, $order_id) {
 $sql = "select g.*, b.goods_image from vem_order_goods g, vem_device_box b where g.box_id = b.id and g.order_id =" . correctSQL ( $order_id );
 $result = querySQL ( $db, $sql );
 $data = array ();
 while ( $row = mysql_fetch_assoc ( $result ) ) {
  $data [] = $row;
 }
 return $data;
}
function get_order_price($db, $order_id) {
 $sql = "select sum(goods_price) order_price from vem_order_goods where order_id =" . correctSQL ( $order_id );
 return executeScalar ( $db, $sql );
}
function create_order_goods($db, $order_id, $device_id, $box_id, $goods_name, $goods_price) {
 $sql = "insert into vem_order_goods (order_id, device_id, box_id, goods_name, goods_price) values (" . correctSQL ( $order_id ) . ", " . correctSQL ( $device_id ) . ", " . correctSQL ( $box_id ) . ", " . correctSQL ( $goods_name ) . ", " . correctSQL ( $goods_price ) . ")";
 executeSQL ( $db, $sql );
}
function notify_order($db, $out_trade_no) {
 $order_info = get_order_info ( $db, $out_trade_no );
 if ($order_info != null && $order_info ['status'] != 1) {
  // 更改订单状态
  $sql = "update vem_order_list set status = 1 where order_id =" . correctSQL ( $out_trade_no );
  executeSQL ( $db, $sql );
  
  // 查找所有盒子信息
  $sql = "select * from vem_order_goods where order_id = " . correctSQL ( $out_trade_no );
  $result = querySQL ( $db, $sql );
  while ( $box = mysql_fetch_assoc ( $result ) ) {
   // 更改盒子状态
   $sql = "update vem_device_box set status = 0 where id =" . $box ['box_id'];
   executeSQL ( $db, $sql );
   
   // 发送http请求开门
   if (sendOpenBox ( $db, $box ['box_id'], $out_trade_no )) {
    // 更改订单状态
    $sql = "update vem_order_list set is_open = 1 where order_id =" . correctSQL ( $out_trade_no );
    executeSQL ( $db, $sql );
   }
  }
 }
}
?>