<?php

header('Content-Type: text/html; charset=utf-8');
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // наш код
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email']; 
    $message =$_POST['message'];
    echo "Получено $name $surname $email";
}

 ?>
 
 