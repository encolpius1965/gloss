<?php

class ConceptEdit
{
 private $UserId;   
 
 function __construct($_UserId)   
 {
     $UserId = $_UserId;
 }   
}
session_start();
header('Content-Type: text/html; charset=utf-8');
require_once( "classes/connection.php" );
$conn = new Connection();


$UserId =    $conn->GetUserId()  ;
$o = new ConceptEdit ($UserId);


$lAppend = 0;
$lEdit = 0;
$lDelete = 0;
$lMessage = 0;
$selectConceptId=0;
$lIntercactive=0;

$prompt="";
$Message="nothing";

$newLine="";  // сделать подсказку 


// разбираемся с текущим режимом
  // заполним справочник концептов
     $sql = "SELECT * FROM USER WHERE USER_ID=$UserId"; 
		$result = mysql_query($sql) 
				  or die('Query error: <code>'.$sql.'</code>');
		if ( is_resource($result) ) 
		{
			
			while ( $row = mysql_fetch_assoc($result) )
			{
			      $lAppend = $row[LAPPEND];
                  $lEdit = $row[LEDIT];
                  $lDelete=$row[LDELETE];
                  $lMessage=$row[LMESSAGE];
                  $selectConceptId=$row[CONCEPT_ID];
            }
        }

        
if  (!empty($_POST['f_delete']))  // отработка
    {    
      $selectConceptId= empty($_POST['SelectConcept']) ?  0 : $_POST['SelectConcept']; 
      $lDelete=1;
      
                    $sql = "UPDATE USER SET ".
                 " LDELETE=$lDelete ".
                 "WHERE USER_ID=$UserId";				

            $result = mysql_query($sql) 
            or die('Query error: <code>'.$sql.'</code>');

            // echo "UserId=$UserId, selectConceptId=$selectConceptId ";
 
            
            $sql = " CALL DeleteConcept($UserId, $selectConceptId)  ";
                  $result = mysql_query($sql) 
                                or die('Query error: <code>'.$sql.'</code>');
 

            $sql = "SELECT * FROM USER WHERE USER_ID=$UserId"; 
                $result = mysql_query($sql) 
                        or die('Query error: <code>'.$sql.'</code>');
                if ( is_resource($result) ) 
                {
			
                        while ( $row = mysql_fetch_assoc($result) )
                        {
                            $DelRes = $row[DELRES];
                        }
                }

            //    echo "lDelRes=$lDelRes";
            // а теперь так - если что-то пошло не так, включаем режим сообщений
                if ($DelRes < 0)
                {                     
                   $lMessage = 1;
                   /*
                    $sql = "UPDATE USER SET ".
                     " LMESSAGE=$lMessage ".
                     "WHERE USER_ID=$UserId";				

                  $result = mysql_query($sql) 
                  or die('Query error: <code>'.$sql.'</code>');
*/                   
                  $Message =  ($DelRes == -1)     ?  "Удаление невозможно. Не выделен элемент для удаления"  : 
                                                       "Удаление невозможно. Имеются лексемы, ассоциированные с данным понятием. "           ;  
                   
                   
                   
                }
                
                
    }    
        
        

if  (!empty($_POST['f_append']))  // отработка 
   
 {
      $lAppend = 1;
              $sql = "UPDATE USER SET ".
                 " LAPPEND=$lAppend ".
                 "WHERE USER_ID=$UserId";				

            $result = mysql_query($sql) 
            or die('Query error: <code>'.$sql.'</code>');
 }

 
 if  (!empty($_POST['f_edit'])) 
 {
      
      $selectConceptId= empty($_POST['SelectConcept']) ?  0 : $_POST['SelectConcept']; 
      
      
       if  ($selectConceptId>0)
       { 
              $lEdit = 1;   
              $sql = "UPDATE USER SET ".
                 " LEDIT=$lEdit ".
                 " ,CONCEPT_ID=$selectConceptId ".
                 "WHERE USER_ID=$UserId";				
            $result = mysql_query($sql) 
            or die('Query error: <code>'.$sql.'</code>');
            
        $sql = "SELECT * FROM CONCEPT WHERE USER_ID=$UserId AND CONCEPT_ID=$selectConceptId"; 
		$result = mysql_query($sql) 
				  or die('Query error: <code>'.$sql.'</code>');
		if ( is_resource($result) ) 
		{
			
			while ( $row = mysql_fetch_assoc($result) )
			{
			      $newLine = $row[NAME];
            }
        }
       } 
       else 
       {
           $lEdit = 0; 
           $lMessage=1;
           $Message="Не выделен элемент для редактирования";
       }           
       
 }

if  (!empty($_POST['f_reset']))  // отработка
{

    
   if ($lAppend==1) 
          $lAppend=0;

   if ($lEdit==1)
          $lEdit=0;

   $sql = "UPDATE USER SET ".
                 " LAPPEND=$lAppend ".
                 " ,LEDIT=$lEdit ".
                 " ,LDELETE=$lDelete ".
                 "WHERE USER_ID=$UserId";				
            $result = mysql_query($sql) 
                        or die('Query error: <code>'.$sql.'</code>');
   
    
}    



 
 if  (!empty($_POST['f_ok']))  // отработка 
   
 {
   if ($lDelete==1)
   {
     $lDelete=0;
     $lMessage=0;
              $sql = "UPDATE USER SET ".
                 " LDELETE=$lDelete ".
                 " ,LMESSAGE=$lMessage ".
                 "WHERE USER_ID=$UserId";				
            $result = mysql_query($sql) 
                        or die('Query error: <code>'.$sql.'</code>');

   }       


   if ($lAppend==1) 
      {
       $newLine= empty($_POST['newLine']) ?  $newLine : $_POST['newLine'];    
//       
                   
                    $sql = " CALL AddNewConcept($UserId,  '".$newLine."'  )  ";
                  $result = mysql_query($sql) 
                                or die('Query error: <code>'.$sql.'</code>');
                                /*
                                        $sql = "SELECT * FROM USER WHERE USER_ID=$UserId";
                                        $result = mysql_query($sql) 
                                        or die('Query error: <code>'.$sql.'</code>');
                                */        
//
      // добавим новую строку     
       $lAppend = 0;
              $sql = "UPDATE USER SET ".
                 " LAPPEND=$lAppend ".
                 "WHERE USER_ID=$UserId";				
            $result = mysql_query($sql) 
                        or die('Query error: <code>'.$sql.'</code>');
                        
                        
                        $sql = "SELECT * FROM USER WHERE USER_ID=$UserId"; 
		$result = mysql_query($sql) 
				  or die('Query error: <code>'.$sql.'</code>');
		if ( is_resource($result) ) 
		{
			
			while ( $row = mysql_fetch_assoc($result) )
			{
			      $lAppend = $row[LAPPEND];
                  $lEdit = $row[LEDIT];
                  $selectConceptId=$row[CONCEPT_ID];
            }
        }
    
                        

          
      }
    
      if ($lEdit==1) 
      {
        $lEdit = 0;  
              $sql = "UPDATE USER SET ".
                 " LEDIT=$lEdit ".
                 "WHERE USER_ID=$UserId";				
            $result = mysql_query($sql) 
                        or die('Query error: <code>'.$sql.'</code>');
  
  
          $newLine= empty($_POST['newLine']) ?  $newLine : $_POST['newLine']; 
  
              $sql = "UPDATE  CONCEPT SET ".
                 " NAME= '".$newLine."' ".    
                 "WHERE USER_ID=$UserId AND CONCEPT_ID=$selectConceptId";				
            $result = mysql_query($sql) 
                        or die('Query error: <code>'.$sql.'</code>');
  
      }    
      
 }

 
 
    if ($lAppend==1 OR $lEdit==1)
          $lModifying=1;
    else 
          $lModifying=0;  
 
 
    if ($lAppend==1)
    {
        $prompt="Новое понятие:";
    } 

    if ($lEdit==1)
    {
        $prompt="Текущее понятие:";
    } 
    
 
 
  // заполним справочник концептов
     $sql = "SELECT * FROM CONCEPT WHERE USER_ID=$UserId"; 
		$result = mysql_query($sql) 
				  or die('Query error: <code>'.$sql.'</code>');
		if ( is_resource($result) ) 
		{
			
			while ( $row = mysql_fetch_assoc($result) )
			{
			      $key = $row[CONCEPT_ID];
                  $value =$row[NAME];
                  $aConcept[$key]=$value;
            }
        }

        $count = count($aConcept);
        
        $lInteractive  =  (   ($lModifying==0 and $lMessage==0) ? 0 : 1);
?>
<html>
<head>
    <title>Редактирование справочника понятий</title>
    <link rel="stylesheet" href="style.css" type="text/css">

</head>
<body>
     <form action="?" method="post">  
 
           <fieldset    class="editor" >
              
<legend>Редактирование </legend>

 
<br><br>




<table border = "0" width="100%"> <!-- начало содержимого таблицы-->
   <col width="40%">
   <col width= <?=($lInteractive==0)     ? "5%": "40%" ?>           >
   <col width= <?=($lInteractive==0)     ? "55%": "20%" ?>           >
   
		
		<tr> <!--описываем первую строку-->
		</tr>
		<tr><!--описываем вторую строку-->
			<td>
            <select name="SelectConcept"  size=<?='"'."$count".'"'?>        >
                    <?
                        foreach($aConcept as $indx => $text)
                            { ?><option  value=<?=$indx?>
                                    <?=($indx==$selectConceptId)? " selected": ""?>
                                >
                                <?=$text?>
   
                                </option><?
                        }
                            ?>
                    </select>
            </td>
			<td>
                         <table border="0"  >
                         <tr>
                         </tr>        
                         <tr><td>
                          <?=$prompt?><br> 
                         </td></tr>
                         <tr><td>
                          <input type=text name="newLine"  size="100"  "autofocus" <?=$lModifying==0 ? "hidden": "" ?>   value=<?=$newLine?>     >  <br>
                          <label     <?=$lMessage==0 ? "hidden": "" ?> id="txt_wrong"  >         <?=$Message?></label><br>
                          <br> 
                         </td></tr>    
                         <tr><td>
                          <input type=submit name="f_ok"   value="   ОK    "  <?=($lInteractive==0)     ? "hidden": "" ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;          
                          <input type=submit name="f_reset"   value="Отмена" <?=$lModifying==0 ? "hidden": "" ?> >
                          <br><br><br> 
                         </td></tr>    
                         <tr><td>
                          <?=$message?><br><br><br> 
                         </td></tr>    


                         </table>                         
            </td>
      
			<td>
                 <table border="0">
                         <tr>
                         </tr>        
                         <tr><td>
                         <input type=submit name="f_append"   value="Добавить" <?=$lInteractive>0 ? "hidden": "" ?>>
                          <br><br><br> 
                         </td></tr>
                         <tr><td>
                          <input type=submit name="f_edit"      value="Редактировать"   <?=$lInteractive>0 ? "hidden": ""  ?> >
                          <br><br><br> 
                         </td></tr>                                                  
                         <tr><td>
                          <input type=submit name="f_delete"   value="Удалить"  <?=$lInteractive>0 ? "hidden": "" ?> >
                          <br><br><br> 
                         </td></tr>                                                  
                         <tr><td>
                          <a href="index.php">На главную страницу</a><br><br> 
                         </td></tr                                                  
 
                 </table>
            </td>
		</tr>
	</table> <!--конец таблицы-->


 
 
    </form>        
 
	</body>
 <?php
 
 
 ?>
 
 
