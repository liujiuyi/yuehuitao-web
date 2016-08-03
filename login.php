<?php
require_once ('include/share.php');

$faileMessage = '';

if (isset ( $_GET ["func"] )) {
 if ($_GET ["func"] == "login") {
  $db = connectDB ();
  
  $username = $_POST ["username"];
  $password = $_POST ["password"];
  if ($username == "") {
   $faileMessage = '帐号不能为空！';
  } else if ($password == "") {
   $faileMessage = '密码不能为空！';
  } else {
   $userinfo = login ( $db, $username, $password );
   
   if ($userinfo != null) {
    setSessionData ( "userinfo", $userinfo );
    saveLoginInfo ( $userinfo ['id'] );
    gotoURL ( 'index.php' );
   } else {
    $faileMessage = '帐号或密码错误！';
   }
  }
 }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>悦惠淘自动售卖机管理系统</title>
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<script type="text/javascript">
		function onCompletedLoad()
		{
			document.getElementById('username').focus();
			document.getElementById('username').select();
		}
	</script>
</head>
<body onload="javascript : onCompletedLoad();" style="font-size: 13px">
 <table border="0" cellspacing="0" cellpadding="0"
  style="width: 100%; height: 600px;">
  <tr>
   <td align="center" valign="middle" height="100%">
    <table width="60%" border="0" cellspacing="0" cellpadding="0"
     class="main_box" style="margin: 20px auto;">
     <tr>
      <td class="foot_info"> <?php
      
      if (isset ( $expireMessage ) && $expireMessage != '') {
       echo $expireMessage;
      } else {
       echo $faileMessage;
      }
      ?></td>
     </tr>
     <tr>
      <td valign="top" align="center">
       <table width="100%" border="0" cellspacing="3" cellpadding="25"
        style="border: 1px solid #eee;">
        <tr>
         <td style="background: #f8f8f8;" align="center">
          <form action="login.php?func=login" method="post">
           <table border="0" cellspacing="5" cellpadding="0">
            <tr>
             <td align="right">用户ID ：</td>
             <td align="left"><input type="text" id="username"
              name="username" class="input_1" value="admin" /></td>
            </tr>
            <tr>
             <td align="right">密码 ：</td>
             <td align="left"><input type="password" id="password"
              name="password" class="input_1" value="admin123" /></td>
            </tr>
            <tr>
            </tr>
            <tr>
             <td>&nbsp;</td>
             <td align="left">
              <!-- <a href="#" class="a_login" onclick="javascript: submit();">login</a></a> -->
              <input type="submit" class="a_login" value="Login" />
             </td>
            </tr>
           </table>
          </form>
         </td>
        </tr>
       </table>
      </td>
     </tr>
    </table>
   </td>
  </tr>
 </table>
</body>
</html>