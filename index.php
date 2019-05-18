<?phpecho("<p> NOS");require_once( "util/util.php" );

 session_start();


header('Content-Type: text/html; charset=utf-8');


$UserId =  GetUserId();
// echo "<p>UserId=$UserId";

if     ($UserId==0)   
                header('Location: verify.php');   
                // $UserId = 1;

                      
require_once( "classes/connection.php" );




/*
if (!isset($_SESSION['UserId']))  
                header('Location: concept.php');   
                // $UserId = 1;
        else 
               $UserId=$_SESSION['UserId'];

*/
$conn = new Connection();

   




$TypeSingleUsMess=0;
$TypeSelectUsMess=1;
 
$row = $conn->GetRow("USER", "USER_ID=$UserId"); 	
 
 
			      $ScoreMax= $row[SCOREMAX];
                
                  $ScoreStep=$row[SCORESTEP];
                  $MonthlyShrinkage=$row[MSHRINKAGE];
                  $lSelect=$row[LSELECT];
                  $lStarted=$row[LSTARTED];
                  $ConceptSourceId=$row[CONC_SOURCE_ID];
                  $ConceptTargetId=$row[CONC_TARGET_ID];
                  $SessionId = $row[SESSION_ID];
                  $SumScore = $row[SUMSCORE];
                  $Count = $row[COUNT];
                  $Average = $row[AVERAGE];
                  $SumFail = $row[SUMFAIL];
                  $Kpd = $row[KPD];
                  $lResult=$row[LRESULT];
                  $lexTarget=$row[LEX_TARGET];
                  $lexSource=$row[LEX_SOURCE];
                  $SelectSize=$row[SEL_SIZE];

      
        
 // обработчик кнопки если кнопка была нажата, данные надо записатт на сервер из форм
 // 
 if  (!empty($_POST['f_start']))  // отработка 
 {
   //     $lStarted=_SwitchStarted($lHeaderInvocation=false);
     
         $ConceptSourceId=empty($_POST['SelectConceptSource']) ?  $ConceptSourceId : $_POST['SelectConceptSource'];
         $ConceptTargetId=empty($_POST['SelectConceptTarget']) ?  $ConceptTargetId : $_POST['SelectConceptTarget'];
         $ScoreMax= empty($_POST['ScoreMax']) ? $ScoreMax : $_POST['ScoreMax'];
         $ScoreStep= empty($_POST['ScoreStep']) ? $ScoreStep : $_POST['ScoreStep'];
         $MonthlyShrinkage= empty($_POST['MonthlyShrinkage']) ? $MonthlyShrinkage : $_POST['MonthlyShrinkage'];
         $lSelect= ($_POST['FillingStyle'])=="Select" ? 1: 0;     
         $SelectSize=empty($_POST['SelectSize']) ?  $SelectSize : $_POST['SelectSize'];         
 //                         echo "<p>перед переключением lStarted=$lStarted";
         $lStarted = ($lStarted==1) ? 0 : 1;
          
         
 //                         echo "<p>после переключения lStarted=$lStarted";
         if ($lStarted)
         {
                $SessionId++;
                $SumScore=0;
                $SumFail=0;
                $lResult=0;
                $Kpd=0;
         }      
         
         
        $conn->UpdateTable("USER",
                            Array(    
                 "SCOREMAX"=>$ScoreMax, "SCORESTEP"=>$ScoreStep, "MSHRINKAGE"=>$MonthlyShrinkage, "LSELECT"=>$lSelect , "LSTARTED"=>$lStarted,                                   "CONC_SOURCE_ID"=>$ConceptSourceId,   "CONC_TARGET_ID"=>$ConceptTargetId, "SESSION_ID"=>$SessionId,                 "SUMSCORE"=>$SumScore, "SUMFAIL"=>$SumFail,   "KPD"=>$Kpd,   "LRESULT"=>$lResult, "SEL_SIZE"=>$SelectSize                 ), 
                 "USER_ID=$UserId");				

                 /*
                 ,"CONC_SOURCE_ID"=>$ConceptSourceId,   "CONC_TARGET_ID"=>$ConceptTargetId, "SESSION_ID"=>$SessionId, "SESSION_TIME"=>NOW(),
                 "SUMSCORE"=>$SumScore, "SUMFAIL"=>$SumFail,   "KPD"=>$Kpd,   "LRESULT"=>$lResult, "SEL_SIZE"=$SelectSize
                 */
        

  }      
  
  

   if   ( ((!empty($_POST['f_start']))&&  $lStarted    )  ||  (    !empty($_POST['f_affirm'])       ) ) 
  {
      
            //этот кусок мы потом перенесем в фунцию. это вывод Source-параметров   
                 $conn->Proc("GetSourceRec", Array($UserId));
                                
                                //??????????????? разбираться и видимо убрать то что выше
                 $row=$conn->GetRow("USER", "USER_ID=$UserId");                                
                                                $lexSource= $row[LEX_SOURCE];
                                                $Count = $row[COUNT];
                                                $Average = $row[AVERAGE];

                        
  // а если у нас стиль - выбор из, то нам надо и с массивом возможного выбора разбираться
                if ($lSelect == 1)     
                {
                    $conn->Proc("GetSelectionArray", Array($UserId));
                } 
  // очистим LexTarget и lResult и запишем lexSource
                $lexTarget = "";    // '".$name."    '".$name."    '".$name."'
                $lResult =0;  //  '".$lexTarget."'
                
                $conn->UpdateTable("USER",
                                    Array("LEX_TARGET"=>$lexTarget,  "LRESULT"=>$lResult),
                                           "USER_ID=$UserId");
		
                        
  }   
  
 
  if ($lStarted && ($lSelect == 1))
  {
                                $aSelTarget=$conn->GetColumn("USMESS","LEX_ID", "TXT", "USER_ID=$UserId AND LTYPE=$TypeSelectUsMess");                                              
                                $countSelA = count($aSelTarget);   //                             print_r($aSelTarget);

  }

 
  
  if  (!empty($_POST['f_put']))  // отработка 
  {
      if ($lSelect == 0)
      {
            $lexTarget = $_POST['lexTarget'];    // '".$name."    '".$name."    '".$name."'
  
 
      }     
      else
      {   
            $lexTarget=  empty($_POST['aSelTarget']) ? "" :     $_POST['aSelTarget'];
             /*
             echo $lexTargetId;
            $sql = "SELECT * FROM LEX WHERE LEX_ID=$lexTargetId";
            $result = mysql_query($sql) 
                                or die('Query error: <code>'.$sql.'</code>');
                                if ( is_resource($result) ) 
                                    {
			
                                        while ( $row = mysql_fetch_assoc($result) )
                                        {
                                                $lexTarget=  $row[TXT];

                                        }    
                                    }   

            */
            
            
      }
 
               $conn->Proc("PutTargetRec", Array($UserId, $lexTarget));
  

  } 

    
    if ($lStarted)            // здесь аккуратно считывается информация из трех баз и выводится в условно-мадальное окно
    {
               // конечно это окно методами тупого html не очень красиво. надо почитаь матчасть.
                    $row = $conn->GetRow("USER", "USER_ID=$UserId"); 	
                           $SumScore = $row[SUMSCORE];
                           $Kpd = $row[KPD];
                           $lResult=$row[LRESULT];
                           $Score= $row[SCORE];
                  // $SessionTime = $row[SESSION_TIME];   неочевидно. зачем нам на фронтенде эта переменная.
                         
                        $aResult[0] =  "";         
                        $aResult[1] = "Ошибка";
                        $aResult[2] = "Верно";
                        $aResult[3] = "<b>Верно</b>. Лексема выведена из активного набора";
                        
                        $aResultClass[0]="thinking";
                        $aResultClass[1]="wrong";
                        $aResultClass[2]="right";
                        $aResultClass[3]="good";
                        
                        $curClassResult  = $aResultClass[$lResult]; 
         // заполним справочник концептов
         
                    $RightAnswerTitle = (lResult==1) ? "Правильный ответ" : "Дополнительные варианты";      
                    
//                    echo "<p>aRightAnswer";
//                    echo "<p> TypeSingleUsMess=$TypeSingletUsMess";
                    $aRightAnswer=$conn->GetOnlyColumn("USMESS", "TXT", "USER_ID=$UserId AND LTYPE=$TypeSingleUsMess AND ( ($lResult=1) OR (LAUXILIARY=1)           )");                                              
                    $countRA = count($aRightAnswer);
                    $lIsAnswerArray = (($lResult>0) AND ($countRA>0));
                    $RightAnswerTitle =
                               ($lIsAnswerArray) ?
                                                (($lResult==1) ? "Правильный ответ" : "Дополнительные варианты")
                                                :
                                                "";
                    
  }  
  
  //   print_r($aConceptSource);
 /*      <?=($key==$ConceptTargetId)? " selected": ""?>*/
 
       $lShowLexems = (($lStarted ==1) and ($Count >0)) ? 1 : 0;
       $lCountDisclaimer = (($lStarted ==1) and ($Count ==0 )) ? 1 : 0;
       
       $lShowTargetString = ($lSelect==0) || (($lSelect==1) && ($lResult>0)) ? 1: 0;
       $lShowTargetArrray =(($lSelect==1) && ($lResult==0))  ? 1: 0;
       
       
       $StartStopStr = $lStarted ? "Остановить тестирование" : "Начать тестирование";
        $StartStopStr = $lCountDisclaimer ?"       OK       "  : $StartStopStr;
       
       
       $CheckedSelect= $lSelect ? "checked":"";
       $CheckedFilling= $lSelect ? "": "checked";

  // заполним справочник концептов
   $aConceptSource=$conn->GetColumn("CONCEPT","CONCEPT_ID", "NAME", "USER_ID=$UserId");                                              
   $aConceptTarget=$conn->GetColumn("CONCEPT","CONCEPT_ID", "NAME", "USER_ID=$UserId");       // в PHP нет нормальной функции клонирования массива. свою писать лень.
   $sql = "SELECT * FROM CONCEPT WHERE USER_ID=$UserId"; 
		
?>
<html>
<head>
    <title>Запоминание</title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
     <form action="?" method="post">  
    <label id="txt_disclaimer" <?=$lCountDisclaimer ? "": "hidden" ?>    >Активный набор исчерпан. Измените параметры, либо пополните словари. </label><br> 
 
   <fieldset  <?=$lShowLexems ? "": "hidden" ?> class="lexems" >
          Базовая лексема: <input type=text name="lexSource" disabled size="100" value=<?='"'."$lexSource".'"'?>> 
              Вес <label  id="txt_stats">  <?=$Score?> </label>      
           <br><br>
          Целевая лексема: 
          <input type=text name="lexTarget" size="100"  <?=$lResult>0 ? "disabled": "autofocus" ?>    <?=$lShowTargetString  ? "" : "hidden"?>      value=<?='"'."$lexTarget".'"'?>> 
                         
      
                      
 
         <select name="aSelTarget"   <?=$lShowTargetArrray ? "": "hidden" ?> size=<?='"'."$countSelA".'"'?>       >    
                        <?
                        foreach($aSelTarget as $indx => $text)
                            { ?><option  value=<?='"'."$text".'"'?>
                            >
                    <?=$text?>
   
                </option><?
                }
                    ?>
            </select>
            
            <input type=submit name="f_put" value="Отправить"  <?=$lResult>0 ? "hidden": "" ?> >
            
            <br><br>
 
 
          <label  id="txt_<?=$aResultClass[$lResult]?>"  >       <?= $aResult[$lResult]?></label><br>        
          <label><?= $RightAnswerTitle?><br></label>
                <select name="AnswerList1"   <?=$lIsAnswerArray ? "": "hidden" ?> size=<?='"'."$countRA".'"'?>       >
                        <?
                        foreach($aRightAnswer as $indx => $text)
                            { ?><option  value=<?='"'."$indx".'"'?>
                            >
                    <?=$text?>
   
                </option><?
                }
                    ?>
            </select><br><br>
            <input type=submit name="f_affirm" value="         OK            "  <?=$lResult>0 ? "": "hidden" ?>    <?=$lResult>0 ? "autofocus": "" ?> > <br><br>
        
            <table border = "0"  align="center"> <!--начало содержимого таблицы-->
                        <tr> <!--описываем первую строку-->
		</tr> 
        		<tr> <!--описываем вторую строку-->
			<td>Активный набор</td>
			<td>Средний вес</td>
			<td>КПД</td>
            <td>Индекс запоминания</td>
		</tr>

		<tr id="txt_stats"> <!--описываем вторую строку-->
			<td> <?=$Count?></td>
			<td><?=$Average?></td>
			<td><?=$Kpd?></td>
            <td><?=$SumScore?></td>
		</tr>
	</table> <!-- конец таблицы-->
  </fieldset>
 
        <br><br>
        <input type=submit name="f_start"   value="<?=$StartStopStr?>" >
        <br><br>
 
           <fieldset  <?=$lStarted ? "hidden" : ""?>  class="manager" >
              
<legend>Управление </legend>

 Базовое понятие:
<select name="SelectConceptSource"  >
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
<select name="SelectConceptTarget" >
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


         Порог запоминания: <input type=text name="ScoreMax" value=<?=$ScoreMax?>> <br><br>
         Шаг запоминания: <input type=text name="ScoreStep" value=<?=$ScoreStep?>> <br><br>
         Месячная усушка: <input type=text name="MonthlyShrinkage" value=<?=$MonthlyShrinkage?>> <br><br>
         Стиль заполнения:<br>
                Набор текста<input type=radio name="FillingStyle" value="Filling" <?=$CheckedFilling ?>>
                Выбор<input type=radio name="FillingStyle" value="Select" <?=$CheckedSelect ?>  >
        Размерность выбора (для одноименного стиля): <input type=text name="SelectSize" value=<?=$SelectSize?>> <br><br>      
        </fieldset>
   <p><a href="concept.php">Модифицировать список понятий</a>
             <a href="compar.php?idd=3">        Пополнить словари</a>
              <a href="verify.php">        Учетная запись</a>

   </p>
    </form>        
 	</body>
 <?php
 
 
 
 ?>
 
 
