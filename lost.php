<?php
 // открываем сессию
 session_start();
  header('Content-Type: text/html; charset=utf-8');
 // данные были отправлены формой?
 require_once( "classes/connection.php" );
 require_once( "util/util.php" );
 $conn = new Connection();
 $root_email =  $conn->GetRootEmail();
    $lOK=1;
 $msg= array();
 if (!isset($_SESSION['UserId'])) 
               $UserId=0;    
        else 
        {
              $UserId=$_SESSION['UserId'];
      
             $sql = "SELECT   email FROM USER WHERE USER_ID=$UserId";
		     $result = mysql_query($sql) 
				  or die('Query error: <code>'.$sql.'</code>');
                if ( is_resource($result) ) 
                {
						while ( $row = mysql_fetch_assoc($result) )
                    {
                            $_SESSION['email']= $row[email];
                            $email = $_SESSION['email'];
                    }		
                }        
        }       

if($_POST['f_ok']){
      $lOK=1;    // лишняя операция что характерно
}  

 if($_POST['f_enter']){
     
     
      $lOK=0;  
      $email=$_POST['Email'];
      $sql = " SELECT USER_ID, LOGIN FROM USER WHERE EMAIL= '"."$email"."'";
                       $result = mysql_query($sql) 
                                or die('Query error: <code>'.$sql.'</code>');
//    echo $sql;
       if ( is_resource($result) ) 
                                    {
			
                                        while ( $row = mysql_fetch_assoc($result) )
                                        {
                                                $_SESSION['UserId'] =$row[UserId];
                                                $_SESSION['login'] =$row[login];
                                                $UserId=$row[UserId]; 
                                                $login=$row[login];
                                                $password = $_SESSION['password'] = RandDigitStr(4);
                                                $lOK=1;
                                         }    
                                    }    ;  
       if ($lOK==0)
       {
         $msg[] = "Нет пользователя с таким почтовым ящиком.  Зарегистрируйтесь заново";
         $msg[] = "или отправьте письмо на адрес $root_email";
         $msg[] = "с описанием учетных данных, какими вы их запомнили. ";  
       }                              

      else 
      {
        $lOK = mail( "$email", "Lost Data", " Ваш логин $login \n Новый пароль $password"); 
        $lEcho = 
        $lEcho= $OK ? 0 : 1;
        $sql = " CALL  TmpPasswordApply("."'$password',"."'$email',$lEcho)";
        // echo $sql ;
        
                  $result = mysql_query($sql) 
                                or die('Query error: <code>'.$sql.'</code>');
        
      
       if ($lOK==0)
       {
         
         $msg[] = "Письмо с учетными данными не может быть отправлено на ваш почтовый почтовый ящик по техническим причинам";
         $msg[] = "Оно сохранено и будет отправлено позже. ";  
       }           
       else 
       {   $msg[] = "Письмо отправлено";
           $lOK=0;  }   
      } 
      
 }
 
     
 // если что-то было не так, то пользователь получит
 // сообщение об ошибке.
 ?>
 <html><body>
 <html>
 <head>
     <title>Забыли пароль?</title>
      <link rel="stylesheet" href="style.css" type="text/css">
 </head>
 <body>
  <form action="?" method="post">
     <fieldset    class="editor" >
     <legend>Восстановление учетных данных через email</legend>
     <br>Введите email, указанный при регистрации:<br>
     <input type="email" required name="Email" title="Введите актуальный email" size="40" value=<?=$email?>  ><br><br>
     <input type=submit name="f_enter" value="Отправить" <?=$lOK>0 ? "" :"hidden"?>><br><br>
     </fieldset>      

<fieldset    class="message" <?=$lOK>0 ? "hidden" : "" ?>   >    
              

<?
foreach($msg as $indx => $text)
{ ?><br><label><?=$text?></label>
<?
}
?>
<br><br>
     <input type=submit name="f_ok" value="OK"><br><br> 
     </fieldset>
 </form>
 </body>
 </html>
  </body></html>