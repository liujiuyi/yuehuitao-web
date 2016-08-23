<?php
ini_set ( 'date.timezone', 'Asia/Shanghai' );

require_once ('../../../include/share.php');
require_once ('../../../include/device.php');
require_once ('../../../include/order.php');

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
// 商品图片
$goods_image = $box_info ['goods_image'];

if (empty ( $device_id ) || empty ( $goods_name ) || empty ( $goods_price )) {
 echo "<script>alert('商品参数错误');history.go(-1);</script>";
 return;
}

$order_id = getMillisecond();
// 创建订单
create_order($db, $order_id, $device_id, $box_id, $goods_name, $goods_price);
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>支付宝支付</title>
<link href="../../styles/style.css" rel="stylesheet" />
</head>
<body>
 <div class="container">
  <div class="header">
   <img src="../../images/mlogo.png" style="margin: 10px 0px 0px 15px" />
  </div>
  <form name=alipayment action=alipayapi.php method=post target="_blank">
  <div class="wxpay_content">
    <font><b class="wxpay_title">购买的商品为<span class="wxpay_value"><?php echo $goods_name;?></span></b></font>
    <br /> 
    <br /> 
    <font><b class="wxpay_title">支付金额为<span class="wxpay_value"><?php echo $goods_price;?></span>元钱</b></font> 
    <br /> 
    <br />
    <?php if (!empty($goods_image)){ ?>
      <img class="wxpay_image" alt=""
     src="../../../<?php echo PHOTO_URL_PREFIX. $goods_image?>" />
    <?php } ?>
    <div align="center">
     <button class="wxpay_button" type="submit" onclick="callpay()">立即支付</button>
     <button class="return_button" type="button" onclick="history.go(-1)">返回上一页</button>
    </div>
  </div>
  <input type=hidden id="WIDout_trade_no" name="WIDout_trade_no" value="<?php echo $order_id;?>">
  <input type=hidden id="WIDsubject" name="WIDsubject" value="<?php echo $goods_name;?>">
  <input type=hidden id="WIDtotal_fee" name="WIDtotal_fee" value="<?php echo $goods_price;?>">
  </form>
  <div class="footer">
   <div class="gezifooter">
    <p>2005-2016 悦惠淘 版权所有，并保留所有权利</p>
   </div>
  </div>

</body>
</html>