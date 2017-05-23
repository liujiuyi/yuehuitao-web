<?php
require_once ('../include/share.php');
require_once ('../include/order.php');
// DB连接
$db = connectDB ();
$order_id = getQueryData ( "order_id" );

$order_info = get_order_info ( $db, $order_id );
if ($order_info ['status'] == 0) {
 responseData ( false, '请您先支付' );
} else {
 // 检查是否超时
 $sql = "SELECT TIMESTAMPDIFF(MINUTE, " . correctSQL ( $order_info['create_date'] ) . " ,now());";
 $minute = executeScalar ( $db, $sql );
 if ($minute > OPEN_TIME_OUT) {
  responseData ( false, '已超时，不能弹开' );
 } else {
  responseData ( true, '发送命令成功' );
  // 查找所有格子编号
  $box_ids = executeScalar ( $db, "SELECT GROUP_CONCAT(box_id)  AS box_ids FROM vem_order_goods WHERE order_id = " . correctSQL ( $order_id ));
  // 更改盒子状态
  $sql = "update vem_device_box set status = 0 where id in (" . $box_ids . ")";
  executeSQL ( $db, $sql );
  // 发送http请求开门
  $sql = "select Group_concat(b.box_number) as box_number, d.device_code from vem_device d, vem_device_box b where d.id = b.device_id and b.id in (" . $box_ids . ")";
  $result = mysql_fetch_assoc (querySQL( $db, $sql ));
  
  if (sendOpenBox2($conn, $result['device_code'], $result['box_number'])) {
   // 更改订单状态
   $sql = "update vem_order_list set is_open = 1 where order_id =" . correctSQL ( $order_id );
   executeSQL ( $db, $sql );
  }
 }
}

?>