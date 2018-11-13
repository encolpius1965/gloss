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

    $db_host = 'localhost';
	$db_username = 'mysql';
	$db_password = 'mysql';
	$db_name = 'gloss1';
	$db_charset = 'utf8';

    
    
	$is_connected = @mysql_connect($db_host, $db_username, $db_password);
	$is_db_selected = $is_connected ? @mysql_select_db($db_name) : FALSE; 
 
	$errors = array();
 
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