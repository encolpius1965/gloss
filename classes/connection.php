<?php


class Connection 
{
 private $errors ;   
 
function __construct()
{ 

# Запуск сессии
// session_start();
# Служит для отладки, показывает все ошибки, предупреждения и т.д.
// error_reporting(E_ALL);

// локальный MySQL
    $db_host = 'localhost';
	$db_username = 'mysql';
	$db_password = 'mysql';
	$db_name = 'gloss1';
	$db_charset = 'utf8'; 

// локальная Хероку    
    $db_host = 'localhost';
	$db_username = 'mysql';
	$db_password = 'mysql';
	$db_name = 'heroku_846065d530579e0';
	$db_charset = 'utf8';

// Реальная хероку
    $db_host = 'eu-cdbr-west-02.cleardb.net';
	$db_username = 'b0f439327ec632';
	$db_password = 'bf8363b2';
	$db_name = 'heroku_846065d530579e0';
	$db_charset = 'utf8';

  
    
    echo "<p> 2__Before  Connection";
    
	// $is_connected = @mysql_connect($db_host, $db_username, $db_password);
// !    $is_connected = mysql_connect($db_host, $db_username, $db_password);
//    echo "<p> After MySQL Connection. is_connected=$is_connected";
    
 $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name); // подключаемся к базе MySQL   

   echo "<p> After DB Connection. ";

 
if (mysqli_connect_errno()) { // проверяем подключение
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}    
    
// !	$is_db_selected = $is_connected ? @mysql_select_db($db_name) : FALSE; 
 
 
	$errors = array();
 /*
	if (!$is_connected) $errors[] = 'Не могу соединиться с базой данных';
	if (!$is_db_selected) $errors[] = 'Не могу найти базу данных ';
 
 
     if (!empty($errors))
	{
		echo '<hr /><ul class="errors">';
		foreach ($errors as $err)
		{
			echo '<li>'.htmlspecialchars($err).'</li>';
		}
		echo '</ul>';
    }    
 */
}

function GetUserId()
   {
       return 1;
  
   } 

function GetRootEmail()
   {
       return "andrey.noskoff@gmail.com";
   } 

   
}


 
 ?>