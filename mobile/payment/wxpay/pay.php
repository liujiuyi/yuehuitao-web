<?php
require_once ('../../../include/share.php');
$box_id_array = getQueryData ( "box_id" );

$param = "";
foreach ( $box_id_array as $box_id ) {
 $param .= $box_id . ",";
}

$param = rtrim ( $param, ',' );

Header ( "Location:wxPay.php?box_id=" . $param );
?>