<?php
require_once ('../include/share.php');
require_once ('../include/order.php');
// DB连接
$db = connectDB ();
$order_id = getQueryData ( "order_id" );

$order_info = get_order_info ( $db, $order_id );
if ($order_info['status'] != 1) {
 responseData ( false, '请您先支付' );
} else {
 responseData ( true, '发送命令成功' );
 // 发送http请求开门
 sendOpenBox ( $db, $order_info ['box_id'] );
}

?>