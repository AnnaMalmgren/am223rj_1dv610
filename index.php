<?php

require_once('App.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Session settings
ini_set('session.use_only_cookies', 'On');
ini_set('session.use_strict_mode', 'On');
ini_set('session.cookie_httponly', 'On');
//ini_set('session.cookie_secure', 'On');
ini_set('session.cookie_samesite', 'Strict');
ini_set( 'session.use_trans_sid', FALSE );

//create and run app.
$app = new App();

$app->runApp();







