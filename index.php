<?php

//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('view/RegisterView.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//CREATE OBJECTS OF THE VIEWS
$v = new LoginView();
$rv = new RegisterView();
$dtv = new DateTimeView();
$lv = new LayoutView();

 $sql = 'SELECT username FROM users';
         $result = mysqli_query($conn, $sql);

         if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
               echo "Username: " . $row["username"]. "<br>";
            }
         } else {
            echo "0 results";
         }
         mysqli_close($conn);

 if (isset($_GET['register'])){
    $lv->setLinkGoBack();
    $lv->render(false, $rv, $dtv);
} else {
    $lv->setLinkRegister();
    $lv->render(false, $v, $dtv);
}




