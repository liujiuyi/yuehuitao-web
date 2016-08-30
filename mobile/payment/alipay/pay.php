<?php
ini_set ( 'date.timezone', 'Asia/Shanghai' );

require_once ('../../../include/share.php');
require_once ('../../../include/device.php');
require_once ('../../../include/order.php');

// 订单id
$order_id = getMillisecond ();

// DB连接
$db = connectDB ();
// 检索商品信息
$box_id_array = getQueryData ( "box_id" );

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
     <button class="wxpay_button" type="submit" onclick="callpay()">立即支付</button>
     <button class="return_button" type="button" onclick="history.go(-1)">返回上一页</button>
    </div>
   </div>
   <input type=hidden id="WIDout_trade_no" name="WIDout_trade_no" value="<?php echo $order_id;?>"> 
   <input type=hidden id="WIDsubject" name="WIDsubject" value="<?php echo $order_goods_name;?>"> 
   <input type=hidden id="WIDtotal_fee" name="WIDtotal_fee" value="<?php echo $order_price;?>">
   <input type=hidden id="WIDshow_url" name="WIDshow_url" value="">
   <input type=hidden id="WIDbody" name="WIDbody" value="">
  </form>
  <div class="footer">
   <div class="gezifooter">
    <p>2005-2016 悦惠淘 版权所有，并保留所有权利</p>
   </div>
  </div>

</body>
</html>