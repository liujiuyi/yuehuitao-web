<?php
error_reporting ( 0 );
ini_set ( 'display_errors', '0' );

session_start ();
ob_start ( "mb_output_handler" );

header ( "Content-Type: text/html; charset=UTF-8" );

date_default_timezone_set ( "PRC" );

// Storage API
include_once ("config.inc.php");
// Log4PHP
include_once ('log4php/Logger.php');
Logger::configure ( dirname ( __FILE__ ) . "/log4php.properties" );
$logger = Logger::getRootLogger ();

// -------------------------
// 1. 数据库相关
// -------------------------

$g_transaction = false;

// 连接
function connectDB() {
 $conn = mysql_pconnect ( DB_HOSTNAME, DB_USER, DB_PASSWORD ) or die ( "Database Connection Failed" );
 
 mysql_select_db ( DB_NAME, $conn ) or die ( "Could not select database" );
 
 $sql = "SET NAMES utf8";
 @mysql_query ( $sql, $conn );
 
 $sql = "SET GLOBAL time_zone = '+8:00'";
 @mysql_query ( $sql, $conn );
 
 $sql = "SET time_zone = '+8:00'";
 @mysql_query ( $sql, $conn );
 
 return $conn;
}

// SQL执行 (SELECT)
function querySQL($conn, $sql) {
 global $g_transaction;
 
 global $logger;
 
 $logger->debug ( "SQL:" . $sql );
 
 $result = mysql_query ( $sql, $conn );
 
 if (! $result) {
  if ($g_transaction)
   return null;
  else
   die ( "querySQL failed. " . mysql_error () );
 }
 
 return $result;
}

// SQL执行 (SELECT COUNT, MAX)
function executeScalar($conn, $sql) {
 global $g_transaction;
 
 global $logger;
 
 $result = mysql_query ( $sql, $conn );
 
 $logger->debug ( "SQL:" . $sql . "\nCOUNT:" . mysql_result ( $result, 0, 0 ) );
 
 if (! $result) {
  if ($g_transaction)
   return null;
  else
   die ( "executeScalar failed. " . mysql_error () );
 }
 
 if (mysql_num_rows ( $result ) != 1)
  return null;
 
 return mysql_result ( $result, 0, 0 );
}

// SQL执行 (INSERT, UPDATE, DELETE)
function executeSQL($conn, $sql) {
 global $g_transaction;
 
 global $logger;
 
 $result = mysql_query ( $sql, $conn );
 
 $logger->debug ( "SQL:" . $sql . "\nAFFECTED ROWS:" . mysql_affected_rows ( $conn ) );
 
 if (! $result) {
  if ($g_transaction)
   return null;
  else
   die ( "executeSQL failed. " . mysql_error () );
 }
 
 return mysql_affected_rows ( $conn );
}

// Begin transaction
function beginSQL($conn) {
 global $g_transaction;
 
 $result = mysql_query ( "begin", $conn ) or die ( "beginSQL failed. " . mysql_error () );
 
 $g_transaction = true;
}

// Commit transaction
function commitSQL($conn) {
 global $g_transaction;
 
 $result = mysql_query ( "commit", $conn ) or die ( "commitSQL failed. " . mysql_error () );
 
 $g_transaction = false;
}

// Rollback transaction
function rollbackSQL($conn) {
 global $g_transaction;
 
 $result = mysql_query ( "rollback", $conn ) or die ( "rollbackSQL failed. " . mysql_error () );
 
 $g_transaction = false;
}

// ---------------------
// 2. HTTP关系
// ---------------------

// Get query data
function getQueryData($name) {
 $ret = getPostData ( $name );
 if ($ret != null)
  return $ret;
 
 return getGetData ( $name );
}

// Get transaction data
function getPostData($txt, $key = null) {
 global $_POST;
 
 if ($key == null)
  $ret = $_POST [$txt];
 else
  $ret = $_POST [$txt] [$key];
 
 if (! isset ( $ret ))
  return $ret;
 
 $ret = str_replace ( "\\\\", "\\", $ret );
 $ret = str_replace ( "\\\"", "\"", $ret );
 $ret = str_replace ( "\\'", "'", $ret );
 
 // &#8722;处理成这样的代码
 // $ret = html_entity_decode($ret, ENT_QUOTES, 'UTF-8');
 return $ret;
}

// Get get data
function getGetData($name) {
 global $_GET;
 
 return $_GET [$name];
}

// Get session data
function getSessionData($name) {
 global $_SESSION;
 
 if (! array_key_exists ( $name, $_SESSION ))
  return null;
 
 return $_SESSION [$name];
}

// Session data 设定
function setSessionData($name, $value) {
 global $_SESSION;
 
 $_SESSION [$name] = $value;
}

// Clear session
function clearSession() {
 global $_SESSION;
 $_SESSION = array ();
}

// 跳转
function gotoURL($url) {
 ob_clean ();
 header ( 'Location: ' . $url );
 exit ();
}

// ---------------------
// 3. 文字处理相关
// ---------------------

// 将$txt转换成有效的SQL字符串
function toStringForSql($txt) {
 $txt = str_replace ( "'", "''", $txt );
 
 return $txt;
}

// ------------------ 公用函数定义结束 -------------------

/**
 * 检查数字参数的数据
 *
 * @param string $val
 *         检查的文字列
 * @return result bool 结果
 */
function isValidNumber($val) {
 if (intval ( $val ) == NULL)
  return false;
 
 return true;
}

/**
 * 检查日期参数的数据
 *
 * @param string $val
 *         检查的文字列
 * @return result bool 结果
 */
function isValidDate($val) {
 $ret = date_parse ( $val );
 
 if ($ret ["error_count"] > 0)
  return false;
 
 return true;
}

/**
 * 有效的SQL文
 *
 * @param string $sql
 *         SQL文字列
 * @param int $type
 *         0: string, 1: integer
 * @return result string 结果
 */
function correctSQL($sql, $type = 0, $quot = true) {
 if ($type == 0) {
  if ($sql == NULL) {
   $sql = "NULL";
  } else if ($sql == "") {
   $sql = "''";
  } else {
   $sql = str_replace ( "'", "''", $sql );
   $sql = str_replace ( "\\", "\\\\", $sql );
   
   if ($quot)
    $sql = "'" . $sql . "'";
  }
 } else if ($type == 1) {
  if ($sql === NULL || $sql === "")
   $sql = "NULL";
 }
 return $sql;
}
function htmlescape($str, $replace_linefeed = false) {
 if ($replace_linefeed) {
  $str = str_replace ( "\n", " ", $str );
  $str = str_replace ( "\r", " ", $str );
 }
 
 return htmlspecialchars ( $str );
}

// Send response for Ext-JS
function responseData($success, $msg, $data = null, $prop = null) {
 global $logger;
 
 if ($success) {
  if ($msg == null)
   $msg = '';
  
  echo '{	"success": "true", "msg": "' . urlencode ( $msg ) . '"';
  
  if ($prop != null) {
   foreach ( $prop as $key => $val ) {
    echo ', "' . $key . '": "' . urlencode ( $val ) . '"';
   }
  }
  
  echo ', "data": [';
  
  if ($data != null) {
   $first = true;
   foreach ( $data as $item ) {
    if ($first)
     $first = false;
    else
     echo ",";
    
    echo json_encode ( $item );
   }
  }
  
  echo "]";
  
  echo "}";
 } else {
  echo '{	"success": "false", "msg": "' . urlencode ( $msg ) . '"}';
  $logger->warn ( '{	"success": "false", "msg": "' . $msg . '"}' );
 }
}
function globalJavascript() {
 echo "<script type='text/javascript'>";
 
 echo "var CODE_URL = '" . CODE_URL . "';";
 echo "var PAGE_COUNT = " . PAGE_COUNT . ";";
 echo "var PHOTO_URL_PREFIX = '" . PHOTO_URL_PREFIX . "';";
 echo "var SYSDATE_UNIX = " . time () . ";";
 echo "var SYSDATE_FORMAT = '" . date ( 'Y-m-d', time () ) . "';";
 echo "var SYSDATE_SUB_FORMAT = '" . date ( 'Y-m-d', strtotime ( '-3 month' ) ) . "';";
 echo "var SYSDATE_YEAR = " . date ( 'Y', time () ) . ";";
 
 $userinfo = getSessionData ( "userinfo" );
 if ($userinfo != null) {
  echo "var LOGINUSER_USERNAME = '" . $userinfo ["username"] . "';";
 }
 
 echo "</script>";
}

// Send socket request
function SendSocketRequest($ip, $port, $request) {
 $address = gethostbyname ( $ip );
 // Create a tcp/ip socket
 $socket = @socket_create ( AF_INET, SOCK_STREAM, SOL_TCP );
 if ($socket === false)
  return null;
 
 $result = @socket_connect ( $socket, $address, $port );
 if ($result === false)
  return null;
  
  // Set timeout read 1s
 $timeout = array (
   'sec' => 0,
   'usec' => 1000000 
 );
 socket_set_option ( $socket, SOL_SOCKET, SO_SNDTIMEO, $timeout );
 socket_set_option ( $socket, SOL_SOCKET, SO_RCVTIMEO, $timeout );
 
 $in = "<data ";
 foreach ( $request as $key => $val ) {
  $in .= " " . $key . "='" . $val . "'";
 }
 $in .= "/>";
 
 // $in = json_encode($request);
 
 $result = socket_write ( $socket, $in, strlen ( $in ) );
 if ($result === false)
  return null;
 
 $out = '';
 $str = '';
 while ( $out = socket_read ( $socket, 2048 ) ) {
  $str .= $out;
 }
 
 socket_close ( $socket );
 
 // $result = json_decode($str, true);
 
 $ret = null;
 
 foreach ( $val as $tag ) {
  if ($tag ['tag'] == 'DATA' && $tag ['attributes'] != null) {
   $ret = array ();
   $data = &$ret;
  } else if ($tag ['tag'] == 'CHANNEL') {
   if ($ret ["channel"] == null)
    $ret ["channel"] = array ();
   
   $n = count ( $ret ["channel"] );
   
   $ret ["channel"] [] = array ();
   $data = &$ret ["channel"] [$n];
  } else
   continue;
  
  $attr = $tag ['attributes'];
  
  foreach ( $attr as $k => $v ) {
   $data [strtolower ( $k )] = $v;
  }
 }
 
 return $ret;
}

// Timeout : 12.345 (in seconds)
function getSessionSaveData($dataname, $timeout) {
 $session_lasttime = getSessionData ( "session_lasttime_" . $dataname );
 $session_data = getSessionData ( "session_" . $dataname );
 
 if ($session_lasttime == null || $session_data == null || microtime ( true ) - $session_lasttime > $timeout)
  return null;
 
 return $session_data;
}
function randomPassword($length) {
 $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
 $pass = "";
 
 for($x = 0; $x < $length; $x ++) {
  $i = rand ( 0, 62 );
  $pass .= substr ( $chars, $i, 1 );
 }
 return $pass;
}

// User login
function login($conn, $username, $password) {
 global $logger;
 
 $sql = "SELECT *
		        FROM vem_admin_user
		       WHERE username = " . correctSQL ( $username ) . " AND password = " . correctSQL ( $password );
 
 $result = querySQL ( $conn, $sql );
 $row = mysql_fetch_assoc ( $result );
 
 if ($row != null) {
  $sql = "UPDATE vem_admin_user SET last_login_ip = " . correctSQL ( $_SERVER ['REMOTE_ADDR'] ) . ", last_login_date = " . correctSQL ( time (), 1 ) . " WHERE id = " . $row ["id"];
  
  executeSQL ( $conn, $sql );
  $logger->info ( $username . " 于 " . date ( 'Y-m-d H:i:s', time () ) . " 登录" );
 }
 
 return $row;
}
function loginTX($conn, $tx) {
 $tx = base64_decode ( $tx );
 list ( $username, $password ) = split ( ',', $tx );
 
 return login ( $conn, $username, $password );
}
function checkInBack($userinfo) {
 if ($userinfo == null) {
  echo '{	"success": "false", "timeout": "true", "msg": "会话无效，请重新登录" }';
  exit ();
 }
}

// Check if already user login with macid
function checkLoginInfo($userid) {
 global $logger;
 
 $user_login_key = 'user_login_' . $userid;
 $user_login_data = getSessionData ( $user_login_key );
 
 if (time () - $user_login_data ["time"] > USER_LOGIN_TIMEOUT) {
  $userinfo = getSessionData ( "userinfo_archive" );
  $logger->info ( $userinfo ['username'] . " 于 " . date ( 'Y-m-d H:i:s', time () ) . " 登录超时" );
  
  unset ( $_SESSION ['userinfo_archive'] );
  return true;
 }
 
 return false;
}

// Save user login information
function saveLoginInfo($userid) {
 $user_login_key = 'user_login_' . $userid;
 
 $user_login_data = array ();
 $user_login_data ["time"] = time ();
 
 setSessionData ( $user_login_key, $user_login_data );
}
function sendOpenBox($conn, $box_id, $order_id) {
 $sql = "select b.box_number, d.device_code from vem_device d, vem_device_box b where d.id = b.device_id and b.id = " . $box_id;
 $result = querySQL ( $conn, $sql );
 $open_info = mysql_fetch_assoc ( $result );
 if ($open_info != null) {
  $url = OPEN_DEVICE_URL . 'command.action?action=01&index=' . $open_info ['box_number'] . '&device=' . $open_info ['device_code'] . '&order_id=' . $order_id . '';
  global $logger;
  $logger->debug ( "send url: " . $url);
  $ch = curl_init ();
  $timeout = 5;
  curl_setopt ( $ch, CURLOPT_URL, $url );
  curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
  curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
  curl_exec ( $ch );
  if (curl_errno ( $ch )) {
   return false;
  }
  curl_close ( $ch );
  return true;
 }
}

function sendOpenBox2($conn, $device_code, $box_number) {
 if (!empty($device_code) && !empty($box_number)) {
  $url = OPEN_DEVICE_URL . 'command.action?action=01&index=' . $box_number . '&device=' . $device_code;
  global $logger;
  $logger->debug ( "send url: " . $url);
  $ch = curl_init ();
  $timeout = 5;
  curl_setopt ( $ch, CURLOPT_URL, $url );
  curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
  curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
  curl_exec ( $ch );
  if (curl_errno ( $ch )) {
   return false;
  }
  curl_close ( $ch );
  return true;
 }
}
function savePhoto($savePath) {
 global $logger;
 $logger->debug ( "savePhoto." );
 if (! isset ( $_FILES ["photo"] )) {
  return null;
 }
 $logger->debug ( "Type: " . $_FILES ["photo"] ["type"] );
 $logger->debug ( "Size: " . $_FILES ["photo"] ["size"] );
 
 if (($_FILES ["photo"] ["size"] < 8000000)) {
  if ($_FILES ["photo"] ["error"] > 0) {
   $logger->debug ( "Return Code: " . $_FILES ["photo"] ["error"] );
   return null;
  } else {
   $logger->debug ( "Upload: " . $_FILES ["photo"] ["name"] );
   $logger->debug ( "Type: " . $_FILES ["photo"] ["type"] );
   $logger->debug ( "Size: " . ($_FILES ["photo"] ["size"] / 1024) . " Kb<br />" );
   $logger->debug ( "Temp file: " . $_FILES ["photo"] ["tmp_name"] );
   
   $suffix = end ( explode ( '.', $_FILES ["photo"] ["name"] ) );
   $photo_name = time () . '.' . $suffix;
   move_uploaded_file ( $_FILES ["photo"] ["tmp_name"], $savePath . $photo_name );
   
   $logger->debug ( $_FILES ["photo"] ["tmp_name"] . " move to: " . $savePath . $_FILES ["photo"] ["name"] );
   return $photo_name;
  }
 } else {
  $logger->debug ( "Invalid file" );
  return null;
 }
}
function getMillisecond() {
 list ( $t1, $t2 ) = explode ( ' ', microtime () );
 return ( float ) sprintf ( '%.0f', (floatval ( $t1 ) + floatval ( $t2 )) * 1000 );
}

// Device User login
function delivery_login($conn, $username, $password) {
 global $logger;

 $sql = "SELECT *
		        FROM vem_delivery_user
		       WHERE username = " . correctSQL ( $username ) . " AND password = " . correctSQL ( $password );

 $result = querySQL ( $conn, $sql );
 $row = mysql_fetch_assoc ( $result );

 if ($row != null) {
  $sql = "UPDATE vem_delivery_user SET last_login_ip = " . correctSQL ( $_SERVER ['REMOTE_ADDR'] ) . ", last_login_date = " . correctSQL ( time (), 1 ) . " WHERE id = " . $row ["id"];

  executeSQL ( $conn, $sql );
  $logger->info ( $username . " 于 " . date ( 'Y-m-d H:i:s', time () ) . " 登录" );
 }

 return $row;
}

function saveDeliveryLoginInfo($userid) {
 $delivery_user_login_key = 'delivery_user_login_' . $userid;

 $delivery_user_login_data = array ();
 $delivery_user_login_data ["time"] = time ();

 setSessionData ( $delivery_user_login_key, $delivery_user_login_data );
}

function checkDeliveryLoginInfo($userid) {
 global $logger;
 
 $delivery_user_login_key = 'delivery_user_login_' . $userid;
 $delivery_user_login_data = getSessionData ( $delivery_user_login_key );
 
 if (time () - $delivery_user_login_data ["time"] > USER_LOGIN_TIMEOUT) {
  $delivery_userinfo = getSessionData ( "delivery_userinfo_archive" );
  $logger->info ( $delivery_userinfo ['username'] . " 于 " . date ( 'Y-m-d H:i:s', time () ) . " 登录超时" );
  
  unset ( $_SESSION ['delivery_userinfo_archive'] );
  return true;
 }
 
 return false;
}
?>