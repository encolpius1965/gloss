<?php
 session_start();
header ("Content-Type: text/html; charset=utf-8");


if (!isset($_SESSION['UserId'])     OR ($_SESSION['UserId']==0)        )       
                header('Location: verify.php');   
                // $UserId = 1;
        else 
               $UserId=$_SESSION['UserId'];


// require_once( "classes/connection.php" );




 
?>
