<?php
// --------------- Database connection information -----------------------------------

define ( "DB_HOSTNAME", 'localhost:3306' );
define ( "DB_NAME", 'yuehuitao-service' );
define ( "DB_USER", 'root' );
define ( "DB_PASSWORD", '123456' );

// define ( "DB_HOSTNAME", 'main_db:3306' );
// define ( "DB_NAME", 'newshop' );
// define ( "DB_USER", 'yhtdbuser' );
// define ( "DB_PASSWORD", 'yhtdbuser0303' );

// --------------- User login timeout -----------------------------------
define ( "USER_LOGIN_TIMEOUT", 300000 );

//Page count
define ( "PAGE_COUNT", 50 );

define ( 'LOG4PHP_DIR', "/include/log4php" );

//Code Url
define ( 'CODE_URL', "http://www.yuehuitao.com/vending_machine/mobile/device_mobile.php" );
?>