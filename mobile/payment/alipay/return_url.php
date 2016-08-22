<?php
/*
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 *
 * ************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 */
require_once ("alipay.config.php");
require_once ("lib/alipay_notify.class.php");

//商户订单号
$out_trade_no = $_GET['out_trade_no'];
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>支付宝支付</title>
<link href="../../styles/style.css" rel="stylesheet" />
<script src="../../js/jquery-1.11.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
	function openBox(val){
		val.setAttribute("disabled", true); 
	  $.ajax({
    	type:'post',
    	url:'../../check_over_order.php',
      data:{ order_id : '<?php echo $out_trade_no;?>' },
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
   <div align="center">
    <input class="open_button" type="button" value="弹开格子"
     onclick="openBox(this)" />
   </div>
  </div>
  <div class="footer">
   <div class="gezifooter">
    <p>2005-2016 悦惠淘 版权所有，并保留所有权利</p>
   </div>
  </div>

</body>
</html>