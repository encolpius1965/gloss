CREATE DEFINER=`root`@`%` PROCEDURE `GetLexTarget`(LexSource varchar(255),  ConceptSourceId int, ConceptTargetId int   )
BEGIN
/*
На входе лексема, которую ввел пользователь UserId отвечая на вопрос USER.DegreId
В рамках данной процедуры мы получим ответ мы 
- найдем множество верных ответов.
- определим, верно ли ответил пользователь
- Запишем ответы в USMESS.
- выполним пересчет очков и запишем данные в USER и DEGREE   

*/

SET @UserId = (SELECT USER_ID FROM CONCEPT WHERE CONCEPT_ID=ConceptSourceId);
SET @LexSourceId = SetLex(LexSource);

 CALL LexTarget(@LexSourceId,ConceptSourceId,ConceptTargetId);    
/* внутри этой процедуры будет определена таблица tmpLexTarget) */

SET @Type=-1;
   
/* требуется уборка */
    DELETE FROM USMESS 
    WHERE USER_ID=@UserId  AND USMESS.LTYPE=@Type; 

    INSERT INTO USMESS ( USER_ID,  LEX_ID, TXT, LTYPE) 
    SELECT  @UserId, TmpLexTarget.LEX_ID, TmpLexTarget.TXT, @Type  
    FROM TmpLexTarget
    ;

       
    
    
END