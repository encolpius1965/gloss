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

?>