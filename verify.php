<?php
 // открываем сессию
 session_start();
  header('Content-Type: text/html; charset=utf-8');
  /*
            echo "Сессии <pre>";
                print_r($_SESSION);
                echo "</pre>";
 
                echo "Куки <pre>";
                print_r($_COOKIE);
            echo "</pre>";
            */

/*
if (!isset($_SESSION['UserId']))  
                header('Location: concept.php');   
                // $UserId = 1;
        else 
               $UserId=$_SESSION['UserId'];

*/

  
  
  
  
  
 // данные были отправлены формой?
 require_once( "classes/connection.php" );
 $conn = new Connection();
 // 
 //  
if (!isset($_SESSION['UserId'])) 
               $UserId=0;    
        else 
               $UserId=$_SESSION['UserId'];

           
           
           
if( ($UserId==0)  ||  ($_POST['f_logout'])){


            $_SESSION['password'] ='';
            $_SESSION['login'] ="Логин";
            $_SESSION['UserId'] = 0;
            
            
} 

 if($_POST['f_lost']){
           header("Location: lost.php");
          exit;
 }



 if($_POST['f_edit']){
           header("Location: registry.php");
          exit;
 }

 
 if($_POST['f_new']){
           header("Location: registry.php");
          exit;
 }

 if($_POST['f_enter']){
     
     // проверяем данные на правильность... в данном случае я
     // вписал имя пользователя и пароль прямо в код, целесообразней
     // было бы проверить логин/пароль в базе данных и при сов-
     // падении дать доступ пользователю...
    $_SESSION['UserId'] = 0;
    $UserId=$_SESSION['UserId']; 
    $login=$_POST['UserName'];
    $password=$_POST['UserPassword'];
    
    
    $UserId=$conn->vFunc("GetUserId", Array( $login, $password));  
    
    
    
    $_SESSION['UserId'] = $UserId;
 
         // запоминаем имя пользователя
         if ($_SESSION['UserId'] > 0)
         {
           ;
         
           $_SESSION['login'] = $login;
           $_SESSION['password'] = $password;
           setcookie("UserId",$UserId,  time()+10*365*24*60*60 );          
        
           header("Location: index.php");
          exit;
         }
        else 
        {
          $_SESSION['password'] ='';
        } 
    
 }
 // если что-то было не так, то пользователь получит
 // сообщение об ошибке.
 
      $login = $_SESSION['login'];
      $password = $_SESSION['password']; 
      $UserId=$_SESSION['UserId'];
      
     
/*     
echo "<p>!LOGIN:  $login"; 
            echo "<p>Сессии <pre>";
                print_r($_SESSION);
                echo "</pre>";


                echo "Куки <pre>";
                print_r($_COOKIE);
            echo "</pre>";

*/
      
 ?>
 <html><body>
 <html>
 <head>
     <title>Авторизация</title>
      <link rel="stylesheet" href="style.css" type="text/css">
 </head>
 <body>
 <form action="verify.php" method="post">
     <fieldset    class="editor" >
     <legend> Учетные данные  </legend>
     <br>
     <input type="text" name="UserName"  value=<?=$login ?>><br><br>
     Пароль: <input type="password" name="UserPassword"   value=<?=$password ?>     ><br><br>
     <input type="Submit" name="f_enter" value="Войти"  <?=$UserId>0 ? "": "" ?>   >
     <input type="Submit" name="f_new" value="Регистрация"  <?=$UserId>0 ? "hidden": "" ?>   >
     <input type="Submit" name="f_edit" value="Смена учетных данных"  <?=$UserId>0 ? "": "hidden" ?>              >
     <input type="Submit" name="f_lost" value="Забыли пароль?"  <?=$UserId>0 ? "hidden": "" ?>    >
     <input type="Submit" name="f_logout" value="Разлогиниться"  <?=$UserId>0 ? "": "hidden" ?>    >
     </fieldset>
 </form>
 </body>
 </html>
  </body></html>