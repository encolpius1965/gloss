<?php

session_start();
header('Content-Type: text/html; charset=utf-8');

require_once( "classes/connection.php" );

 




$conn = new Connection();

$UserId =    $conn->GetUserId()  ;  

$TypeComparUsMess = -1;

$lEdit= 0;
 	
 
    // считываем с сервера базовые параметры от
        $row=$conn->GetRow("USER", "USER_ID=$UserId");
                  $ConceptSourceId=$row[CONC_SOURCE_ID];
                  $ConceptTargetId=$row[CONC_TARGET_ID];
                  $lCompar=$row[LCOMPAR];
                  $lexSource=$row[COMP_SOURCE];
                  $lexTarget=$row[COMP_TARGET];

      
        
 // обработчик кнопки если кнопка была нажата, данные надо записатт на сервер из форм
 //
 // 
 //  четыре  взаимоисключающих режима
 //  - привязываем новое слово-target
 //    - отвязываем слово--target
//     - фикируем новое  - Source
//      - переходим к новому слову 
// это нужно чтобы раз

//  $lexSource=empty($_POST['lexSource']) ?  $lexSource : $_POST['lexSource'];   
$lSetTarget= !empty($_POST['f_put_target']) ? 1 : 0;
$lClearTarget= !empty($_POST['f_clear_target']) ? 1 : 0;

$lSetSource= !empty($_POST['f_put_source']) ? 1 : 0;;
$lClearSource= !empty($_POST['f_new_source'])  ? 1 : 0   ;  

 // это была базовая логика. но на нее накладываются игры с клавишей Enter
  
  
if (  ($lSetSource==1) && ($lCompar==1))    // т.е. нажата кнопка которая на самом деде не видна
{
      $lSetSource = 0;
      $lSetTarget = 1;
}    
  
   
 
  if  ($lSetTarget) // отработка 
  {
      
     if  (!empty($_POST['lexTarget'])) 
    {        
      $lEdit=1;
      
      $ConceptSourceId=empty($_POST['SelectConceptSource']) ?  $ConceptSourceId : $_POST['SelectConceptSource'];
      $ConceptTargetId=empty($_POST['SelectConceptTarget']) ?  $ConceptTargetId : $_POST['SelectConceptTarget'];
      
      $lexSource=empty($_POST['lexSource']) ?  $lexSource : $_POST['lexSource'];
      $lexTarget=empty($_POST['lexTarget']) ?  $lexTarget : $_POST['lexTarget'];

      $conn->Proc("SetDegree", Array( $lexSource, $ConceptSourceId, $lexTarget, $ConceptTargetId));  
     
                          
      $lexTarget='';
    }       
  }    
  

  
  if  ( $lClearTarget) // отработка 
  {
      $selectTarget = empty($_POST['aTarget']) ?  '' : $_POST['aTarget'];
   
              $lEdit=1;
              $ConceptSourceId=empty($_POST['SelectConceptSource']) ?  $ConceptSourceId : $_POST['SelectConceptSource'];
              $ConceptTargetId=empty($_POST['SelectConceptTarget']) ?  $ConceptTargetId : $_POST['SelectConceptTarget'];
      
              $lexSource=empty($_POST['lexSource']) ?  $lexSource : $_POST['lexSource'];
              
      $conn->Proc("UnSetDegree", Array($lexSource,  $ConceptSourceId, $selectTarget, $ConceptTargetId)); 
   
    
  }   

 
 if  ($lClearSource)  
 {
     $lEdit=1;
             //  очищаем. готовим новый цикл.
         $lexSource='';
         $lCompar=0;
 
 }    

     
  if  ($lSetSource) // отработка 
  {
    
    $lEdit=1;

         // начало. размещаем новую базовую лексему, получаем актуальный перечень целевых лексем, переключаем $lCompar   
         $ConceptSourceId=empty($_POST['SelectConceptSource']) ?  $ConceptSourceId : $_POST['SelectConceptSource'];
         $ConceptTargetId=empty($_POST['SelectConceptTarget']) ?  $ConceptTargetId : $_POST['SelectConceptTarget'];
 
 // и запись предусмотреть...!!! 
       if  (!empty($_POST['lexSource']) )
       {   
        $lexSource=empty($_POST['lexSource']) ?  $lexSource : $_POST['lexSource'];

         $lCompar=1;
       } 
      //  echo "Выходим  lCompar=$lCompar  $countTarget =countTarget ";
  } 



    if ($lCompar==1)
    {
            $conn->Proc("ArrangeLexSource", Array( $lexSource,  $ConceptSourceId,$ConceptTargetId));
                                
         // заполним массив "таргетов"
            $aTarget = $conn->GetColumn("USMESS", "LEX_ID", "TXT", "USER_ID=$UserId AND LTYPE=$TypeComparUsMess");
            $countTarget = max(count($aTarget),4); 
    
    }   
  
    // заполним справочник концептов
        $aConceptSource=$conn->GetColumn("CONCEPT","CONCEPT_ID","NAME");
        $aConceptTarget=$conn->GetColumn("CONCEPT","CONCEPT_ID","NAME");        
    

    if ($lEdit>0)
    {

        $conn->UpdateTable("USER",Array("CONC_SOURCE_ID"=>$ConceptSourceId,   "CONC_TARGET_ID"=>$ConceptTargetId, 
                                       "LCOMPAR"=>$lCompar,
                                       "COMP_SOURCE"=>$lexSource,  COMP_TARGET=>$lexTarget),
                 "USER_ID=$UserId");				

        
    }    

   
?>
<html>
<head>
    <title>Пополнение словарей</title>
    <link rel="stylesheet" href="style.css" type="text/css">

</head>
<body>
     <form action="?" method="post">  
 
 Базовое понятие:
<select name="SelectConceptSource" <?=$lCompar>0 ? "disabled": "" ?> >
<?
foreach($aConceptSource as $indx => $text)
{ ?><option  value=<?=$indx?>
            <?=($indx==$ConceptSourceId)? " selected": ""?>
   >
<?=$text?>
   
</option><?
}
?>
</select><br><br>

Целевое понятие:
<select name="SelectConceptTarget" <?=$lCompar>0 ? "disabled": "" ?> >
<?
foreach($aConceptTarget as $indx => $text)
{ ?><option  value=<?=$indx?>
            <?=($indx==$ConceptTargetId)? " selected": ""?>
   >
<?=$text?>
   
</option><?
}
?>
</select><br><br>
          Базовая лексема: 
          <input type=text name="lexSource" size="100"  <?=$lCompar>0 ? "disabled": "autofocus" ?>      value=<?=$lexSource?>
          >
          <input type=submit name="f_put_source"   <?=$lCompar>0 ? "hidden": "" ?>    value="Фиксировать"     >
           <br><br>
          <label> <?=$lCompar>0 ? "Целевая лексема:": "" ?> </label>
          <input type=text name="lexTarget" size="100"  <?=$lCompar>0 ? "autofocus": "hidden" ?>      value=<?=$lexTarget?>
          >
           <input type=submit name="f_put_target"   <?=$lCompar>0 ? "": "hidden" ?>   value="Привязать"     >
           <input type=submit name="f_new_source"   <?=$lCompar>0 ? "": "hidden" ?>   value="Новая базовая лексема"     >  
           <br><br>
           <select name="aTarget"   <?=$lCompar>0 ? "": "hidden" ?> size=<?='"'."$countTarget".'"'?>       >    
                        <?
                        foreach($aTarget as $indx => $text)
                              { ?><option  value=<?='"'."$text".'"'?>
                            >
                    <?=$text?>
   
                </option><?
                }
                    ?>
            </select>
            
            <input type=submit name="f_clear_target" value="Отвязать"  <?=$lCompar>0 ? "": "hidden" ?> >  
           <br><br><br><br>
           <a href="index.php">На главную страницу</a>
           <br><br>
    </form>        
 
	</body>
 <?php
 
 
 
 ?>
 
 
