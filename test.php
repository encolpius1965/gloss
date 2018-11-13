<?php

session_start();
header('Content-Type: text/html; charset=utf-8');

require_once( "classes/connection.php" );

 


$conn = new Connection();

$UserId =    $conn->GetUserId()  ;  



if (!isset($_SESSION['test_login'])) 
{
 $login="noah";
}    
else 
{
 $login=$_SESSION['test_login'];
}

$lEdit= 0;
 	
     
    
    if  ($_POST['f_logout']){
        $login="";
        $_SESSION['test_login'] = "";
} 

    if  ($_POST['f_login']){
         $_SESSION['test_login'] = $_POST['login'];
        $login=  $_POST['login'];
} 


    
?>
<html>
<head>
    <title>Тестовая страница</title>
    <link rel="stylesheet" href="style.css" type="text/css">

</head>
<body>
     <form action="?" method="post">  
           Имя пользователя: 
          <input type=text name="login"   value=<?=$login?>
          >
          <br><br>
          
          Пароль: 
          <input type=password name="password"   
          >
          <br><br>
 
          
            <input type=submit name="f_logout" value="Разлогиниться"   >  
           <br><br><br><br>
                       <input type=submit name="f_login" value="Фиксировать"   >  
           <br><br><br><br>

           <a href="index.php">На главную страницу</a>
           <br><br>
    </form>        
 
	</body>
 <?php
 
 
 
 ?>
 
 
