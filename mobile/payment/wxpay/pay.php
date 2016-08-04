<?php
ini_set ( 'date.timezone', 'Asia/Shanghai' );
require_once "lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";

require_once ('../../../include/share.php');
require_once ('../../../include/device.php');
// DB连接
$db = connectDB ();
// 检索商品信息
$box_id = getQueryData ( "box_id" );
$box_info = get_device_box_info ( $db, $box_id );

if ($box_info ['status'] != 1) {
 echo "<script>alert('该商品缺货');history.go(-1);</script>";
 return;
}

// 设备id
$device_id = $box_info ['device_id'];
// 商品名称
$goods_name = $box_info ['goods_name'];
// 商品价格
$goods_price = $box_info ['goods_price'];

if (empty ( $device_id ) || empty ( $goods_name ) || empty ( $goods_price )) {
 echo "<script>alert('商品参数错误');history.go(-1);</script>";
 return;
}

$order_id = WxPayConfig::MCHID . date ( "YmdHis" );

// ①、获取用户openid
$tools = new JsApiPay ();
$openId = $tools->GetOpenid ();

// ②、统一下单
$input = new WxPayUnifiedOrder ();
$input->SetBody ( $goods_name );
$input->SetAttach ( "自动售货机" );
$input->SetOut_trade_no ( $order_id );
$input->SetTotal_fee ( $goods_price * 10 * 10 );
$input->SetTime_start ( date ( "YmdHis" ) );
$input->SetTime_expire ( date ( "YmdHis", time () + 600 ) );
$input->SetNotify_url ( "http://www.yuehuitao.com/vending_machine/mobile/payment/wxpay/notify.php" );
$input->SetTrade_type ( "JSAPI" );
$input->SetOpenid ( $openId );
$order = WxPayApi::unifiedOrder ( $input );
$jsApiParameters = $tools->GetJsApiParameters ( $order );
// ③、在支持成功回调通知中处理成功之后的事宜，见 notify.php

// 创建订单
$sql = "insert into vem_order_list (order_id, device_id, box_id, goods_name, order_price, create_date) values ('$order_id', '$device_id',  '$box_id', '$goods_name', '$goods_price', now())";
executeSQL ( $db, $sql );
?>

<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>微信支付</title>
<script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				alert(res.err_code+res.err_desc+res.err_msg);
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
</head>
<body>
 <br />
 <font color="#9ACD32"><b>该笔订单支付金额为<span
   style="color: #f00; font-size: 50px"><?php echo $goods_price;?></span>元钱
 </b></font>
 <br />
 <br />
 <div align="center">
  <button
   style="width: 210px; height: 50px; border-radius: 15px; background-color: #FE6714; border: 0px #FE6714 solid; cursor: pointer; color: white; font-size: 16px;"
   type="button" onclick="callpay()">立即支付</button>
 </div>
</body>
</html>