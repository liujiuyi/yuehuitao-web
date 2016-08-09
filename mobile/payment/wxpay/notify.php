<?php
ini_set ( 'date.timezone', 'Asia/Shanghai' );
error_reporting ( E_ERROR );

require_once "lib/WxPay.Api.php";
require_once 'lib/WxPay.Notify.php';
require_once 'log.php';

require_once ('../../../include/share.php');
require_once ('../../../include/order.php');

// 初始化日志
$logHandler = new CLogFileHandler ( "logs/" . date ( 'Y-m-d' ) . '.log' );
$log = Log::Init ( $logHandler, 15 );
class PayNotifyCallBack extends WxPayNotify {
 // 查询订单
 public function Queryorder($transaction_id) {
  $input = new WxPayOrderQuery ();
  $input->SetTransaction_id ( $transaction_id );
  $result = WxPayApi::orderQuery ( $input );
  Log::DEBUG ( "query:" . json_encode ( $result ) );
  if (array_key_exists ( "return_code", $result ) && array_key_exists ( "result_code", $result ) && $result ["return_code"] == "SUCCESS" && $result ["result_code"] == "SUCCESS") {
   return true;
  }
  return false;
 }
 
 // 重写回调处理函数
 public function NotifyProcess($data, &$msg) {
  Log::DEBUG ( "call back:" . json_encode ( $data ) );
  $notfiyOutput = array ();
  
  if (! array_key_exists ( "transaction_id", $data )) {
   $msg = "输入参数不正确";
   return false;
  }
  // 查询订单，判断订单真实性
  if (! $this->Queryorder ( $data ["transaction_id"] )) {
   $msg = "订单查询失败";
   return false;
  }
  // 根据 $data["out_trade_no"] 订单号 更新订单状态
  // 执行更新
  // DB连接
  $out_trade_no = $data ["out_trade_no"];
  $db = connectDB ();
  $order_info = get_order_info ( $db, $out_trade_no );
  if ($order_info != null && $order_info ['status'] != 1) {
   // 更改订单状态
   $sql = "update vem_order_list set status = 1 where order_id =" . correctSQL ( $out_trade_no );
   executeSQL ( $db, $sql );
   
   // 更改盒子状态
   $sql = "update vem_device_box set status = 0 where id =" . $order_info ['box_id'];
   executeSQL ( $db, $sql );
   
   // 发送http请求开门
   if (sendOpenBox ( $db, $order_info ['box_id'] )) {
    // 更改订单状态
    $sql = "update vem_order_list set is_open = 1 where order_id =" . correctSQL ( $out_trade_no );
    executeSQL ( $db, $sql );
   }
  }
  return true;
 }
}

Log::DEBUG ( "begin notify" );
$notify = new PayNotifyCallBack ();
$notify->Handle ( false );