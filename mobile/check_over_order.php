<?php
require_once ('../include/share.php');
require_once ('../include/order.php');
// DB连接
$db = connectDB ();
$order_id = getQueryData ( "order_id" );

$order_info = get_order_info ( $db, $order_id );
if ($order_info ['status'] == 0) {
 responseData ( false, '请您先支付' );
} else if ($order_info ['is_open'] == 1) {
 responseData ( false, '已经弹出' );
} else {
 responseData ( true, '发送命令成功' );
 // 发送http请求开门
 if (sendOpenBox ( $db, $order_info ['box_id'] )) {
  // 更改订单状态
  $sql = "update vem_order_list set is_open = 1 where order_id =" . correctSQL ( $order_id );
  executeSQL ( $db, $sql );
 }
}

?>