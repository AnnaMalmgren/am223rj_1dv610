<?php

//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('view/RegisterView.php');
require_once('controller/RegisterController.php');
// require_once('dbproduction.php');
require('dbsettings.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//CREATE OBJECTS OF THE VIEWS
$v = new LoginView();
$rv = new RegisterView();
$dtv = new DateTimeView();
$lv = new LayoutView();

$registerController = new RegisterController($rv, $v);
$registerController->registerUser();

 if (isset($_GET['register'])){
    $lv->setLinkGoBack();
    $lv->render(false, $rv, $dtv);
} else {
    $lv->setLinkRegister();
    $lv->render(false, $v, $dtv);
}







