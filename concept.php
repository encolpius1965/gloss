<?php
require_once( "util/util.php" );
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


$UserId = GetUserId()  ;
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
     $row=$conn->GetRow("USER", "USER_ID=$UserId");
			      $lAppend = $row[LAPPEND];
                  $lEdit = $row[LEDIT];
                  $lDelete=$row[LDELETE];
                  $lMessage=$row[LMESSAGE];
                  $selectConceptId=$row[CONCEPT_ID];

        
if  (!empty($_POST['f_delete']))  // отработка
    {    
      $selectConceptId= empty($_POST['SelectConcept']) ?  0 : $_POST['SelectConcept']; 
      $lDelete=1;
        $conn->UpdateTable("USER",
                              Array("LDELETE"=>$lDelete),
                           "USER_ID=$UserId");                              

  
         $conn->Proc("DeleteConcept", Array( $UserId, $selectConceptId));            
 
         $row=$conn->GetRow("USER", "USER_ID=$UserId");  
         $DelRes = $row[DELRES];

            //    echo "lDelRes=$lDelRes";
            // а теперь так - если что-то пошло не так, включаем режим сообщений
                if ($DelRes < 0)
                {                     
                   $lMessage = 1;
                  $Message =  ($DelRes == -1)     ?  "Удаление невозможно. Не выделен элемент для удаления"  : 
                                                       "Удаление невозможно. Имеются лексемы, ассоциированные с данным понятием. "           ;  
                   
                   
                   
                }
                
                
    }    
        
        

if  (!empty($_POST['f_append']))  // отработка 
   
 {
      $lAppend = 1;
      $conn->UpdateTable("USER", Array("LAPPEND"=>$lAppend), "USER_ID=$UserId");
 }

 
 if  (!empty($_POST['f_edit'])) 
 {
      
      $selectConceptId= empty($_POST['SelectConcept']) ?  0 : $_POST['SelectConcept']; 
      
      
       if  ($selectConceptId>0)
       { 
              $lEdit = 1; 
              $conn->UpdateTable("USER",Array("LEDIT"=>$lEdit,  "CONCEPT_ID"=>$selectConceptId),"USER_ID=$UserId");           

              $row=$conn->GetRow("CONCEPT","USER_ID=$UserId AND CONCEPT_ID=$selectConceptId");  
             $newLine = $row[NAME];
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
      
    $conn->UpdateTable("USER", Array ("LAPPEND"=>$lAppend, "LEDIT"=>$lEdit,"LDELETE"=>$lDelete), "USER_ID=>$UserId");				
    
}    



 
 if  (!empty($_POST['f_ok']))  // отработка 
   
 {
   if ($lDelete==1)
   {
     $lDelete=0;
     $lMessage=0;
             $conn->UpdateTable("USER",Array("LDELETE"=>$lDelete,"LMESSAGE"=>$lMessage),"USER_ID=$UserId");

   }       


   if ($lAppend==1) 
      {
       $newLine= empty($_POST['newLine']) ?  $newLine : $_POST['newLine'];    
//       
                 $conn->Proc("AddNewConcept", Array( $UserId, $newLine));                      
//
      // добавим новую строку     
       $lAppend = 0;
       $conn->UpdateTable("USER",Array("LAPPEND"=>$lAppend),"USER_ID=$UserId");
       $row=$conn->GetRow("USER", "USER_ID=$UserId");  
 			      $lAppend = $row[LAPPEND];
                  $lEdit = $row[LEDIT];
                  $selectConceptId=$row[CONCEPT_ID];
    
                        

          
      }
    
      if ($lEdit==1) 
      {
        $lEdit = 0;  
        $conn->UpdateTable("USER",Array("LEDIT"=>$lEdit),"USER_ID=$UserId");

  
          $newLine= empty($_POST['newLine']) ?  $newLine : $_POST['newLine']; 
          $conn->UpdateTable("CONCEPT",Array("NAME"=>$newLine),"USER_ID=$UserId AND CONCEPT_ID=$selectConceptId");
  
  
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
      $aConcept=$conn->GetColumn("CONCEPT", "CONCEPT_ID", "NAME", "USER_ID=$UserId");  
      $count = count($aConcept);
      
// echo("<p> UserId=$UserId");    
// print_r($aConcept);      
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
 
 
