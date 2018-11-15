<?php

 session_start();



header('Content-Type: text/html; charset=utf-8');





if (!isset($_SESSION['UserId'])     OR ($_SESSION['UserId']==0)        )       
                header('Location: verify.php');   
                // $UserId = 1;
        else 
               $UserId=$_SESSION['UserId'];


require_once( "classes/connection.php" );

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
$conn = new Connection();

   





$TypeSingleUsMess=0;
$TypeSelectUsMess=1;
 
 	
 
 
    // считываем с сервера базовые параметры от
        $sql = "SELECT * FROM USER WHERE USER_ID=$UserId";
		$result = mysql_query($sql) 
				  or die('Query error: <code>'.$sql.'</code>');
		if ( is_resource($result) ) 
		{
			
			while ( $row = mysql_fetch_assoc($result) )
			{
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
                  // $SessionTime = $row[SESSION_TIME];   неочевидно. зачем нам на фронтенде эта переменная.
             } 
        }        

      
        
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
         
          $sql = "UPDATE USER SET ".
                 " SCOREMAX=$ScoreMax, SCORESTEP=$ScoreStep, MSHRINKAGE=$MonthlyShrinkage, LSELECT=$lSelect , LSTARTED=$lStarted ".
                 ", CONC_SOURCE_ID=$ConceptSourceId,   CONC_TARGET_ID=$ConceptTargetId, SESSION_ID=$SessionId, SESSION_TIME=NOW() ".
                 ", SUMSCORE=$SumScore, SUMFAIL=$SumFail,   KPD=$Kpd,   LRESULT=$lResult, SEL_SIZE=$SelectSize ".
                 "WHERE USER_ID=$UserId";				

            $result = mysql_query($sql) 
				  or die('Query error: <code>'.$sql.'</code>');
//         echo "<p>Записали lStarted=$lStarted";

        

  }      
  
  

   if   ( ((!empty($_POST['f_start']))&&  $lStarted    )  ||  (    !empty($_POST['f_affirm'])       ) ) 
  {
      
            //этот кусок мы потом перенесем в фунцию. это вывод Source-параметров   
                  $sql = " CALL GetSourceRec($UserId)  ";
                  $result = mysql_query($sql) 
                                or die('Query error: <code>'.$sql.'</code>');
                                
                                //??????????????? разбираться и видимо убрать то что выше
                                
                                
                                        $sql = "SELECT * FROM USER WHERE USER_ID=$UserId";
                                        $result = mysql_query($sql) 
                                        or die('Query error: <code>'.$sql.'</code>');
                                if ( is_resource($result) ) 
                                    {
			
                                        while ( $row = mysql_fetch_assoc($result) )
                                        {
                                                $lexSource= $row[LEX_SOURCE];
                                                $Count = $row[COUNT];
                                                $Average = $row[AVERAGE];

                                        }    
                                    }   
  // а если у нас стиль - выбор из, то нам надо и с массивом возможного выбора разбираться
                if ($lSelect == 1)     
                {
                    $sql = " CALL GetSelectionArray($UserId)  ";
                    $result = mysql_query($sql) 
                                or die('Query error: <code>'.$sql.'</code>');
                } 
  // очистим LexTarget и lResult и запишем lexSource
                $lexTarget = "";    // '".$name."    '".$name."    '".$name."'
                $lResult =0;  //  '".$lexTarget."'
                $sql = "UPDATE USER SET ".
                        "LEX_TARGET='".$lexTarget."'      ,LRESULT=$lResult ".
                        "WHERE USER_ID=$UserId";				
                 $result = mysql_query($sql) 
                                or die('Query error: <code>'.$sql.'</code>');
            
		
                        
  }   
  
 
  if ($lStarted && ($lSelect == 1))
  {
                                        $sql = "SELECT LEX_ID, TXT FROM USMESS WHERE USER_ID=$UserId AND LTYPE=$TypeSelectUsMess";
                                        $result = mysql_query($sql);
                                        while ( $row = mysql_fetch_assoc($result) )
                                        {
                                                $indx = $row[LEX_ID];
                                                $aSelTarget[$indx]=   $row[TXT];
                                                
                                        }
                            $countSelA = count($aSelTarget);

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
 
               
               $sql = " CALL PutTargetRec($UserId,  '".$lexTarget."'  )  ";                 
               $result = mysql_query($sql) 
                                or die('Query error: <code>'.$sql.'</code>');
            
  

  } 

    
    if ($lStarted)            // здесь аккуратно считывается информация из трех баз и выводится в условно-мадальное окно
    {
               // конечно это окно методами тупого html не очень красиво. надо почитаь матчасть.
                    $sql = "SELECT * FROM USER WHERE USER_ID=$UserId";
                    $result = mysql_query($sql) 
                        or die('Query error: <code>'.$sql.'</code>');
                    if ( is_resource($result) ) 
                        {
			
                            while ( $row = mysql_fetch_assoc($result) )
                                {
                                    $SumScore = $row[SUMSCORE];
                                    $Kpd = $row[KPD];
                                    $lResult=$row[LRESULT];
                                    $Score= $row[SCORE];
                                }   
                  // $SessionTime = $row[SESSION_TIME];   неочевидно. зачем нам на фронтенде эта переменная.
                         } 
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
                    $sql = "SELECT * FROM USMESS WHERE USER_ID=$UserId AND LTYPE=$TypeSingleUsMess";
                    $result = mysql_query($sql) 
                                or die('Query error: <code>'.$sql.'</code>');
                                
                    $RightAnswerTitle = (lResult==1) ? "Правильный ответ" : "Дополнительные варианты";             
                                
                     if ( is_resource($result) ) 
                    {
                        while ( $row = mysql_fetch_assoc($result) )
                        {
                            
                              if ( ($lResult==1) || ($row[LAUXILIARY]==1) ) 
                                {
                                  $aRightAnswer[]=$row[TXT];
                                } 
                        }
                        
                    } 
                    
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
          
   $sql = "SELECT * FROM CONCEPT WHERE USER_ID=$UserId"; 
		$result = mysql_query($sql) 
				  or die('Query error: <code>'.$sql.'</code>');
		if ( is_resource($result) ) 
		{
			
			while ( $row = mysql_fetch_assoc($result) )
			{
			      $key = $row[CONCEPT_ID];
                  $value =$row[NAME];
                  $aConceptSource[$key]=$value;
                  $aConceptTarget[$key]=$value;
            }
        }
        
//           <style type="text/css">
//	      body {background-color: Beige;}
//          fieldset {background-color: beige;}
//          fieldset.manager  {background-color: beige ;}
//          fieldset.lexems  {background-color: beige ;}
//          #txt_right { color: green ; }
//          #txt_wrong { color: red ; }
//          #txt_thinking {  background-color:beige ; color: beige ; }
//          #txt_good { color: green ; } 
//          #txt_disclaimer { color: green ; } 
//          #txt_stats { background-color: lavender; text-align: center   }
//	    </style>
        
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
             <a href="compar.php">        Пополнить словари</a>
              <a href="verify.php">        Учетная запись</a>

   </p>
    </form>        
 	</body>
 <?php
 
 
 
 ?>
 
 
