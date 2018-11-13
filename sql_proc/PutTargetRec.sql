CREATE DEFINER=`root`@`%` PROCEDURE `PutTargetRec`(in UserId int, in LexTarget varchar(255))
BEGIN
/*
На входе лексема, которую ввел пользователь UserId отвечая на вопрос USER.DegreId
В рамках данной процедуры мы получим ответ мы 
- найдем множество верных ответов.
- определим, верно ли ответил пользователь
- Запишем ответы в USMESS.
- выполним пересчет очков и запишем данные в USER и DEGREE   

*/

SET @DegreeId = (SELECT DEGREE_ID FROM USER WHERE USER_ID=UserId);
SET @MShipSourceId = (SELECT MSHIP_SOURCE_ID FROM DEGREE WHERE DEGREE_ID=@DegreeId);
SET @LexSourceId = (SELECT LEX_ID FROM MSHIP WHERE MSHIP_ID=@MShipSourceId);
SET @ConceptSourceId = (SELECT CONC_SOURCE_ID FROM USER WHERE USER_ID=UserId);
SET @ConceptTargetId = (SELECT CONC_TARGET_ID FROM USER WHERE USER_ID=UserId);
SET @ScoreStep = (SELECT SCORESTEP FROM USER WHERE USER_ID=UserId);
SET @ScoreMax =  (SELECT SCOREMAX FROM USER WHERE USER_ID=UserId);
SET @SumScore = (SELECT SUMSCORE FROM USER WHERE USER_ID=UserId);
SET @SumFail = (SELECT SUMFAIL FROM USER WHERE USER_ID=UserId);
SET @Score =  (SELECT SCORE FROM DEGREE WHERE DEGREE_ID=@DegreeId);

 CALL LexTarget(@LexSourceId,@ConceptSourceId,@ConceptTargetId);    
/* внутри этой процедуры будет определена таблица tmpLexTarget) */


SET @LexTargetId = (SELECT LEX_ID FROM tmpLexTarget WHERE tmpLexTarget.TXT=LexTarget)   ;


   
				  if  (@LexTargetId>0) then
                            SET @SumScore = @SumScore + @ScoreStep;
							SET @Score=  @Score + @ScoreStep;
							if (@Score >=@ScoreMax)
									then SET @lResult = 3;   /* верно, лексема уходит из активного набора */      
									else SET @lResult = 2;
                            end if;
                            
                    else 
                            SET @SumFail = @SumFail + @ScoreStep;
                            SET @Score =   @Score - @ScoreStep;
                            SET @lResult=1;        /* ответ неверен */
 end if; 
    /* в DEGREE и USER заносим учетные параметры, в USMESS - подробный ответ    */
    UPDATE DEGREE 
      SET SCORE=@Score
     WHERE DEGREE_ID=@DegreeId;
    /*            */
    UPDATE USER
      SET SUMSCORE=@SumScore,
          SUMFAIL=@SumFail,
          KPD =  (100.0*@SumScore)/(@SumScore  + @SumFail),
          SCORE = @Score, 
          LRESULT = @lResult,
          LEX_TARGET=LexTarget
    WHERE USER_ID=UserId;

SET @Type = 0;  -- режим вывода полного ответа по слову  при введенном слове в режиме запоминания
   
/* требуется уборка */
    DELETE FROM USMESS 
    WHERE USER_ID=UserId AND LTYPE=@Type; 



    INSERT INTO USMESS ( USER_ID, DEGREE_ID, LEX_ID, TXT, LAUXILIARY,LTYPE) 
    SELECT  UserId, @DegreeId, TmpLexTarget.LEX_ID, TmpLexTarget.TXT,  
                 IF( TmpLexTarget.LEX_ID=@LexTargetId,0,1), @Type
    
    FROM TmpLexTarget
    ;

    
    
    
    
END