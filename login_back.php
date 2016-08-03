<?php
require_once ('include/share.php');

// DB连接
$db = connectDB();

try {
	$func = $_REQUEST["func"];

	switch ($func) {
		case 'login' :

			$username = urldecode($_REQUEST["username"]);
			$password = $_REQUEST["password"];

			$userinfo = login($db, $username, $password);
	
			if ($userinfo != null) {
				$tx = base64_encode ( $username . ',' . $password);
				
				$userinfo['tx'] = $tx;
				responseData(true, null, null, $userinfo);
			} else {
				responseData(false, "帐号或密码错误");
			}
			break;
		default :
			responseData(false, '无效的请求');
			break;
	}
} catch (Exception $e) {
	responseData(false, $e->getMessage());
}
?>