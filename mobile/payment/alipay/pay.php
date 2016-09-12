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
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>悦惠淘自动贩卖机-购物车</title>
<link href="../../styles/style.css" rel="stylesheet" />
<link href="../../styles/trade.css" rel="stylesheet" type="text/css">
</head>
<body>
 <div class="container">
  <div class="header">
   <img src="../../images/mlogo.png" style="margin: 0px 0px 10px 0px" />
  </div>
  
	<form name=alipayment action=alipayapi.php method=post target="_blank">
		<div class="cart">
			<div class="cart-brand-list">
			  <header>
				<p class="title util-ellipsis"><a class="cart-martshow-go">订单信息</a></p>
				<div class="countdown"></div>
			  </header>
			  <!--商品列表begin-->
			<?php foreach($order_goods_list as $order_goods){?>
			  <div class="row-3 cart-item-list">
				<div class="cart-item-wrapper">
				  <div class="cart-item-img">
					<img src="../../../<?php echo PHOTO_URL_PREFIX. $order_goods['goods_image']?>" alt="<?php echo $order_goods['goods_name'];?>">
				  </div>
				  <div class="cart-item-info">
					<a href="goods.php?id=1576">
					  <p class="title"><?php echo $order_goods['goods_name'];?></p></a>
					<p class="description util-ellipsis"></p>
					<p class="cart-control J_cart-control">
					  <?php if (!empty($order_goods['goods_url'])){ ?>
						 <font>
							<a href="<?php echo $order_goods['goods_url']?>" target="_blank"><b class="wxpay_title">
							<span class="wxpay_value">查看详情</span></b></a>
						 </font> 
						 <br /> 
						 <?php } if (!empty($order_goods['goods_image'])){ ?>
					</p>
				  </div>
				  <div class="cart-item-price">
					<p class="discount"><?php echo $order_goods['goods_price']?></p>
					<p class="original">x1</p></div>
				</div>
			  </div>
			  <?php 
							}
							$order_goods_name = $order_goods['goods_name'];
						  }
						?>
			  <!--  -->
			  <footer class="cart-item-sum">
				<section class="row-1 cart-preferential-status">
					<div class="row-1 cart-count">
						<p>小计：<span id="goods_subtotal_31159" class="price"><?php echo $order_price?>元</span></p>
					</div>
				</section>
			  </footer>
			  
			<div align="center">
				 <button class="wxpay_button" type="submit">立即支付</button>
				 <button class="return_button" type="button" onclick="history.go(-1)">返回上一页</button>
			</div>
			<input type=hidden id="WIDout_trade_no" name="WIDout_trade_no" value="<?php echo $order_id;?>"> 
			<input type=hidden id="WIDsubject" name="WIDsubject" value="<?php echo $order_goods_name;?>"> 
			<input type=hidden id="WIDtotal_fee" name="WIDtotal_fee" value="<?php echo $order_price;?>">
			<input type=hidden id="WIDshow_url" name="WIDshow_url" value="">
			<input type=hidden id="WIDbody" name="WIDbody" value="">
			</div>
		</div>
	</form> 
	
	<div class="footer">
		<div class="gezifooter">
			<p>2005-2016 悦惠淘 版权所有，并保留所有权利</p>
		</div>
	</div>
</div>
</body>
</html>