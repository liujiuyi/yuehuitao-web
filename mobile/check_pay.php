<?php
require_once ('../include/share.php');
$box_id = getQueryData ( "box_id" );

if (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MicroMessenger' ) !== false) {
 Header ( "Location:payment/wxpay/pay.php?box_id=" . $box_id );
} else {
 Header ( "Location:payment/alipay/pay.php?box_id=" . $box_id );
}
?>