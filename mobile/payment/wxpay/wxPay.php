<?php
ini_set ( 'date.timezone', 'Asia/Shanghai' );
require_once "lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";

require_once ('../../../include/share.php');
require_once ('../../../include/device.php');
require_once ('../../../include/order.php');

$order_id = getMillisecond ();

// DB连接
$db = connectDB ();

// 检索商品信息
$box_id_array = explode(',', getQueryData ( "box_id" ));

foreach ( $box_id_array as $box_id ) {
 $box_info = get_device_box_info ( $db, $box_id );
 if ($box_info ['status'] != 1) {
  continue;
 }
 
 // 设备id
 $device_id = $box_info ['device_id'];
 // 商品名称
 $goods_name = $box_info ['goods_name'];
 // 商品价格
 $goods_price = $box_info ['goods_price'];
 // 商品图片
 $goods_image = $box_info ['goods_image'];
 
 if (empty ( $device_id ) || empty ( $goods_name ) || empty ( $goods_price )) {
  continue;
 }
 // 创建订单商品记录
 create_order_goods ( $db, $order_id, $device_id, $box_id, $goods_name, $goods_price );
}

$order_goods_list = get_order_goods_list ( $db, $order_id );
if (count ( $order_goods_list ) > 0) {
 // 创建订单
 $order_price = 0;
 $order_price = get_order_price ( $db, $order_id );
 create_order ( $db, $order_id, $order_price );
} else {
 echo "<script>alert('商品参数错误');history.go(-1);</script>";
 return;
}
foreach ( $order_goods_list as $order_goods ) {
 $order_goods_name .= $order_goods ['goods_name'];
}
// ①、获取用户openid
$tools = new JsApiPay ();
$openId = $tools->GetOpenid ();

// ②、统一下单
$input = new WxPayUnifiedOrder ();
$input->SetBody ( $order_goods_name );
$input->SetAttach ( "自动售货机" );
$input->SetOut_trade_no ( $order_id );
$input->SetTotal_fee ( $order_price * 10 * 10 );
$input->SetTime_start ( date ( "YmdHis" ) );
$input->SetTime_expire ( date ( "YmdHis", time () + 600 ) );
$input->SetNotify_url ( "http://www.yuehuitao.com/vending_machine/mobile/payment/wxpay/notify.php" );
$input->SetTrade_type ( "JSAPI" );
$input->SetOpenid ( $openId );
$order = WxPayApi::unifiedOrder ( $input );
$jsApiParameters = $tools->GetJsApiParameters ( $order );
// ③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
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
        } else {
          settime(val);
        }
      },
      error:function(){}
	  }); 
	}
	
	var countdown=60; 
	function settime(val) { 
  	if (countdown == 0) { 
  		val.removeAttribute("disabled");    
  		val.value="弹开格子"; 
  		countdown = 60; 
  		return;
  	} else { 
  		val.setAttribute("disabled", true); 
  		val.value="重新弹开(" + countdown + ")"; 
  		countdown--; 
  	} 
  	setTimeout(function() { 
  		 settime(val) 
  	},1000) 
	} 
	</script>
</head>
<body>
 <div class="container">
  <div class="header">
   <img src="../../images/mlogo.png" style="margin: 10px 0px 0px 15px" />
  </div>
  <div class="wxpay_content">
   <?php foreach($order_goods_list as $order_goods){?>
    <hr/>
    <font>
    <b class="wxpay_title">商品名称：
    <span class="wxpay_value"><?php echo $order_goods['goods_name'];?></span>
    </b>
    </font>
    <br />
    <font>
    <b class="wxpay_title">商品金额：
    <span class="wxpay_value"><?php echo $order_goods['goods_price']?></span>元钱</b></font> 
    <br /> 
    <?php if (!empty($order_goods['goods_image'])){ ?>
      <img class="wxpay_image" alt="" src="../../../<?php echo PHOTO_URL_PREFIX. $order_goods['goods_image']?>" />
   <?php 
       }
       $order_goods_name .= $order_goods['goods_name'];
     }
   ?>
   <hr/>
   <font>
   <b class="wxpay_title">合计金额：
   <span class="wxpay_value"><?php echo $order_price?></span>元钱</b></font> 
   <br /> 
   <br />
   <div align="center">
    <button class="wxpay_button" type="button" onclick="callpay()">立即支付</button>
    <button class="return_button" type="button" onclick="history.go(-1)">返回上一页</button>
    <input class="open_button" type="button" value="弹开格子" onclick="openBox(this)" />
   </div>
  </div>
  <div class="footer">
   <div class="gezifooter">
    <p>2005-2016 悦惠淘 版权所有，并保留所有权利</p>
   </div>
  </div>
</body>
</html>