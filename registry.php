<?php
 // открываем сессию
 session_start();
  header('Content-Type: text/html; charset=utf-8');
 // данные были отправлены формой?
 require_once( "classes/connection.php" );
 require_once( "util/util.php" );
 $conn = new Connection();
    $lOK=1;
 $msg= array();
 if (!isset($_SESSION['UserId'])) 
               $UserId=0;    
        else 
        {
              $UserId=$_SESSION['UserId'];
              $row=$conn->GetRow("USER","USER_ID=$UserId");
              $_SESSION['email']= $row['EMAIL'];
              $email = $_SESSION['email'];
        }       

 
 $login = $_SESSION['login'];
 
 $password = $_SESSION['password'];
 $password2 = $password;
 /* 
            echo "Сессии <pre>";
                print_r($_SESSION);
                echo "</pre>"
 
                echo "Куки <pre>";
                print_r($_COOKIE);
            echo "</pre>";
 */
  

 if($_POST['f_ok']){
      $lOK=1;    // лишняя операция что характерно
 }
 if($_POST['f_enter']){
     // проверяем данные на правильность... в данном случае я
     // вписал имя пользователя и пароль прямо в код, целесообразней
     // было бы проверить логин/пароль в базе данных и при сов-
     // падении дать доступ пользователю...
  
 
  
    $login=$_POST['UsName'];
    $password=$_POST['UsPass'];
    $password2=$_POST['UsPass2'];
    $email=$_POST['Email'];
    
    $_SESSION['login'] = $login;
    $_SESSION['email'] = $email;

    if ($password!=$password2)
    {
        $lOK=0;
        $msg[] = "Пароли не совпадают";
    }
    
    if (empty($login))
    {
        $lOK=0;
        $msg[] = "Логин не может быть пустым";
    }
    else 

        {
         
         $num_rows=$conn->GetCount("USER", "USER_ID != $UserId AND LOGIN='"."$login"."'");   
         

                        
        if ( $num_rows > 0 )
        {
                $lOK=0;
                $msg[] = "В базе уже есть пользователь с таким логином. Подберите другой.";
        }       
            
            
    }   
    if (!CorrectMail($email))
    {
        $lOK=0;
        $msg[] = "Некорректный email";
    }        
 
    
    
    if ($lOK)
    {
       // регистрируем нового пользователя, даем ему базовый набор и уводим на главную страницу переводить с английского на русский
       
    $conn->Proc("UpdateUserInstall", Array($UserId,$login,$password,$email));
    $_SESSION['UserId']=$conn->vFunc("GetUserId", Array($login,$password));


 
           $_SESSION['login'] = $login;
           $_SESSION['password'] = $password;     
          setcookie("UserId",$UserId,  time()+10*365*24*60*60 ); 
           header("Location: index.php");
          exit;
          
    }

 }    
 // если что-то было не так, то пользователь получит
 // сообщение об ошибке.
 ?>
 <html><body>
 <html>
 <head>
     <title>Авторизация</title>
      <link rel="stylesheet" href="style.css" type="text/css">
 </head>
 <body>
  <form action="?" method="post">
     <fieldset    class="editor" >
     <legend> Учетные данные  </legend>
     <br>
     Логин      : <input type="text" required  name="UsName" <?=$lOK>0 ? "autofocus" :"" ?>  title="Не менее 3 латинских букв"  value=<?=$login?>   ><br><br>
     Пароль     : <input type="password"   name="UsPass"  value=<?=$password ?>   > <br><br>
     Подтвердите: <input type="password"   name="UsPass2" value=<?=$password2 ?>  title="Подтвердите введенный пароль">     <br><br>
     E-mail     : <input type="email" required name="Email" title="Введите актуальный email"  value=<?=$email?>  ><br><br>
     <input type=submit name="f_enter" value="Отправить" <?=$lOK>0 ? "" :"hidden"?>><br><br>
     </fieldset>      

<fieldset    class="message" <?=$lOK>0 ? "hidden" : "" ?>   >    
              

<?
foreach($msg as $indx => $text)
{ ?><br><label id=txt_wrong><?=$text?></label>
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