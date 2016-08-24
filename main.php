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
<script type="text/javascript"
 src="http://file.tttalk.org/public/ext-3.4.1/adapter/ext/ext-base.js"></script>
<script type="text/javascript"
 src="http://file.tttalk.org/public/ext-3.4.1/ext-all.js"></script>
<script type="text/javascript"
 src="http://file.tttalk.org/public/ext-3.4.1/examples/ux/fileuploadfield/FileUploadField.js"></script>

<!-- Include Ext stylesheets here: -->
<link rel="stylesheet" type="text/css"
 href="http://file.tttalk.org/public/ext-3.4.1/resources/css/ext-all.css">
<link rel="stylesheet" type="text/css"
 href="http://file.tttalk.org/public/ext-3.4.1/examples/ux/fileuploadfield/css/fileuploadfield.css" />

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

if ($userinfo ["type"] == 1) {
 if ($CurPage == 'admin_user_manager.php')
  echo "<dd>管理员管理</dd>";
 else
  echo "<dd><a href='admin_user_manager.php'>管理员管理</a></dd>";
} else {
 if ($CurPage == 'delivery_user_manager.php')
  echo "<dd>配送员管理</dd>";
 else
  echo "<dd><a href='delivery_user_manager.php'>配送员管理</a></dd>";
}
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