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
  // 查找所有盒子信息
  $sql = "select * from vem_order_goods where order_id = " . correctSQL ( $order_id );
  $result = querySQL ( $db, $sql );
  while ( $box = mysql_fetch_assoc ( $result ) ) {
   // 更改盒子状态
   $sql = "update vem_device_box set status = 0 where id =" . $box ['box_id'];
   executeSQL ( $db, $sql );
    
   // 发送http请求开门
   sendOpenBox ( $db, $box ['box_id'], $order_id );
   sleep(2);//等待一秒执行下次循环
  }
 }
}

?>