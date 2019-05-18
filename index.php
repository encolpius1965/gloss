<?php require_once( "util/util.php" );// echo("<p> NOS");		
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
 
 
