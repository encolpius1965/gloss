<?php
function CorrectMail($mail)
{
    if (!preg_match('~^[a-zA-Z0-9_\.\-]+@([a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,6}$~', $mail))
    {
        return 0;
    }

    return 1;
}
function RandDigitStr ($length)
{
/* генерируем случайную строку длины $length, состоящую из цифр.*/
 $ret = "";
  for($i=0; $i<$length; $i++) { 
        $dgt=rand(0,9);
        $ret= $ret."$dgt";        
 } 
 return $ret;
}    
function GetUserId()
{
// проверяем наличие корректного UserId по массивам SESSION и COOKIES   
    $ret=0;
    
    if (isset($_SESSION['UserId']))
                  $ret=$_SESSION['UserId'];
    
    if ($ret==0)
    {
        if  (isset($_COOKIE['UserId']))
                  $ret=$_COOKIE['UserId'];
    }
    
    return $ret;    
}

?>