<?php
require_once ('include/share.php');

$userinfo = getSessionData ( "userinfo" );

$expireMessage = "";

if ($userinfo == NULL) {
 $expireMessage = "会话已过期，请重新登录";
 require_once ("index.php");
 exit ();
}

if (checkLoginInfo ( $userinfo ['id'] )) {
 $expireMessage = "登录已超时，请重新登录";
 require_once ("index.php");
 exit ();
} else {
 saveLoginInfo ( $userinfo ['id'] );
}

date_default_timezone_set ( "PRC" );
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0  Strict//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>悦惠淘自动售卖机管理系统</title>
<!-- Include Ext and app-specific scripts: -->
<script type="text/javascript" src="ext-js/adapter/ext/ext-base.js"></script>
<script type="text/javascript" src="ext-js/ext-all.js"></script>

<!-- Include Ext stylesheets here: -->
<link rel="stylesheet" type="text/css"
 href="ext-js/resources/css/ext-all.css">

<!-- Common Styles for the examples -->
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<link rel="stylesheet" type="text/css" href="css/float_message.css" />
<script type="text/javascript" src="js/main.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/float_message.js"></script>
<script type="text/javascript" src="js/change_password.js"></script>

<?php globalJavascript(); ?>
<?php

foreach ( $ScriptFiles as $file ) {
 echo ("<script type='text/javascript' src='js/$file'></script>");
}
?>
</head>
<body oncontextmenu="return false" class="body">
 <div id="banner" class="banner">
  <!--<div class="logo"><img src="images/banner_logo.png" /></div>-->
  <div class="top_menu">
   <dl class="top_menu_c">
<?php
if ($CurPage == 'device_manager.php')
 echo "<dd>设备管理</dd>";
else
 echo "<dd><a href='device_manager.php'>设备管理</a></dd>";

if ($CurPage == 'order_manager.php')
 echo "<dd>订单管理</dd>";
else
 echo "<dd><a href='order_manager.php'>订单管理</a></dd>";

?>
<dd>
     <a id="ch_password" href='#'>修改密码</a>
    </dd>
    <dd>
     <a href='index.php?logout=1'>注销</a>
    </dd>
   </dl>
  </div>
  <div class='top_info'>
   <div id="user_info_bar" class="user_info_bar"><?php
   if ($userinfo ["type"] == USER_TYPE_2_PROJECT) {
    echo "项目部,";
    echo $userinfo ["department_name"] . ",";
   }
   if ($userinfo ["type"] == USER_TYPE_3_PURCHASE) {
    echo "采购部,";
   }
   if ($userinfo ["type"] == USER_TYPE_4_DEPOT) {
    echo "库存部,";
    echo $userinfo ["department_name"] . ",";
   }
   if ($userinfo ["type"] == USER_TYPE_5_AUDIT) {
    echo "审计部,";
   }
   echo $userinfo ["username"];
   ?></div>
  </div>
 </div>
<?php
echo $PageContents;
?>
<?php

require_once ("foot.html");
?>
</body>
</html>