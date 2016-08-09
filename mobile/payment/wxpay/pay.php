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
$sql = "insert into vem_order_list (order_id, device_id, box_id, goods_name, order_price, create_date) values ('$order_id', '$device_id', '$box_id', '$goods_name', '$goods_price', now())";
executeSQL ( $db, $sql );
?>

<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>微信支付</title>
<link href="../../styles/style.css" rel="stylesheet" />
<script src="../../js/jquery-1.11.3.min.js" type="text/javascript"></script>
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
	

	
	function openBox(val){
		val.setAttribute("disabled", true); 
	  $.ajax({
    	type:'post',
    	url:'../../check_over_order.php',
      data:{ order_id : '<?php echo $order_id;?>' },
      dataType:'json',
      success:function(data){
        alert(decodeURI(data.msg));
        if(data.success == 'false'){
          val.removeAttribute("disabled");    
        }
      },
      error:function(){}
	  }); 
	}
	</script>
</head>
<body>
 <div class="container">
  <div class="header">
   <img src="../../images/mlogo.png" style="margin: 10px 0px 0px 15px" />
  </div>
  <div class="wxpay_content">
   <font><b class="wxpay_title">购买的商品为<span class="wxpay_value"><?php echo $goods_name;?></span></b></font>
   <br /> <br /> <font><b class="wxpay_title">支付金额为<span
     class="wxpay_value"><?php echo $goods_price;?></span>元钱
   </b></font> <br /> <br />
   <div align="center">
    <button class="wxpay_button" type="button" onclick="callpay()">立即支付</button>
    <button class="return_button" type="button" onclick="history.go(-1)">返回上一页</button>
    <input class="open_button" type="button" value="弹开格子" onclick="openBox(this)"/>
   </div>
  </div>
  <div class="footer">
   <div class="gezifooter">
    <p>2005-2016 悦惠淘 版权所有，并保留所有权利</p>
   </div>
  </div>

</body>
</html>