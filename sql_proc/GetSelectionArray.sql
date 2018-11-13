CREATE DEFINER=`root`@`%` PROCEDURE `GetSelectionArray`( UserId int)
sp: BEGIN
Declare TrueTargetLexId int;
Declare TrueTargetLexTxt varchar(255); 
Declare jCount0, jCount int;   /* требуемая и раброчвя размерность массива выбора */
Declare jTrue int;     /* позиция верного ответа в ряду от  1 до JCount */


SET @DegreeId = (SELECT DEGREE_ID FROM USER WHERE USER_ID=UserId);
SET @MShipSourceId = (SELECT MSHIP_SOURCE_ID FROM DEGREE WHERE DEGREE_ID=@DegreeId);
SET @LexSourceId = (SELECT LEX_ID FROM MSHIP WHERE MSHIP_ID=@MShipSourceId);
SET @ConceptSourceId = (SELECT CONC_SOURCE_ID FROM USER WHERE USER_ID=UserId);
SET @ConceptTargetId = (SELECT CONC_TARGET_ID FROM USER WHERE USER_ID=UserId);


SET @Type=1;       -- режим выбора верного значения из нескольких вариантов  
    
    DELETE FROM USMESS 
    WHERE USER_ID=UserId AND LTYPE=@Type;


/* определим набор ответов */
 CALL LexTarget(@LexSourceId,@ConceptSourceId,@ConceptTargetId);    
/* внутри этой процедуры будет определена таблица tmpLexTarget) */
/*        LEX_ID int,    */
/*        TXT nvarchar(255)  */
Set @nCountTmp = (SELECT COUNT(*) FROM tmpLexTarget);

if  (@nCountTmp = 0)  
          then 
           leave sp;
            -- SET @nCountTmp = 0;
end if;            


if (@nCountTmp = 1 ) 
    then
       begin
          SET TrueTargetLexId = ( SELECT LEX_ID FROM tmpLexTarget);
       end;
     else
       begin
         /* если ответов несколько, придется подзаморочиться со случайной выборкой */
           SET @minId = (SELECT MIN( LEX_ID) FROM tmpLexTarget);
           SET @maxId = (SELECT MAX( LEX_ID) FROM tmpLexTarget); 
           SET @id = @minId + Floor ( rand()* (@maxId - @minId));
           SET TrueTargetLexId = 
                 ( SELECT LEX_ID FROM tmpLexTarget 
                   WHERE LEX_ID>=@id
                   ORDER BY LEX_ID
                   limit 1);
       end;
  end if;   
  SET TrueTargetLexTxt = (SELECT TXT FROM LEX WHERE LEX.LEX_ID=TrueTargetLexId);
  /* Позиционируем правильный ответ в выходном массиве */ 
   SET jCount = (SELECT SEL_SIZE FROM USER WHERE USER_ID=UserId);
       SET jCount0 = jCount;
       SET jCount = 2*jCount0;
   
   
   SET jTrue = 1 + Floor ( rand()* jCount);
   
   /* цикл записи в usmess от 1 до jCount*. */
    

    
    
   SET @j =1;
    SET @TabCount=1;
  
    
   /* надо предусмотреть ситуацию, когда будет преждевременно исчерпан список. кривовато.-= сдедать красивее. */
   WHILE @j<=jCount AND @TabCount>0 
     DO
          SET @TabCount = ( 
                          SELECT COUNT(*)
                         FROM MSHIP
                         JOIN DEGREE ON DEGREE.MSHIP_SOURCE_ID=MSHIP.MSHIP_ID AND DEGREE.CONCEPT_TARGET_ID=@ConceptSourceId
                         LEFT JOIN USMESS ON ( USMESS.USER_ID=UserId AND (USMESS.LTYPE=@Type) AND  USMESS.LEX_ID=MSHIP.LEX_ID)  
                         WHERE MSHIP.CONCEPT_ID=@ConceptTargetId 
                             AND USMESS.LEX_ID IS NULL 
                        );
  
          case 
            when (@j = jTrue) OR (@TabCount = 0)
                   then 
                                begin
                                        INSERT INTO USMESS (USER_ID, LEX_ID, TXT, DELTA, LTYPE)
										SELECT UserId, TrueTargetLexId, TrueTargetLexTxt, 0, @Type;
                                 end; 
                                       
                         when (@TabCount > 0) 
                   then 
                     begin
                                     SET @minId = (
										SELECT MIN(MSHIP.LEX_ID) 
										FROM MSHIP
                                        JOIN DEGREE ON DEGREE.MSHIP_SOURCE_ID=MSHIP.MSHIP_ID AND DEGREE.CONCEPT_TARGET_ID=@ConceptSourceId
										LEFT JOIN USMESS ON ( USMESS.USER_ID=UserId AND USMESS.LTYPE=@Type AND USMESS.LEX_ID=MSHIP.LEX_ID)   
										WHERE MSHIP.CONCEPT_ID=@ConceptTargetId 
										AND USMESS.LEX_ID IS NULL 
											);

										SET @maxId = (
												 SELECT MAX(MSHIP.LEX_ID) 
												 FROM MSHIP
                                                 JOIN DEGREE ON DEGREE.MSHIP_SOURCE_ID=MSHIP.MSHIP_ID AND DEGREE.CONCEPT_TARGET_ID=@ConceptSourceId
												 LEFT JOIN USMESS ON ( USMESS.USER_ID=UserId AND USMESS.LTYPE=@Type AND USMESS.LEX_ID=MSHIP.LEX_ID)   
												 WHERE MSHIP.CONCEPT_ID=@ConceptTargetId
													 AND USMESS.LEX_ID IS NULL 
												);
                                         SET @id = @minId + Floor ( rand()* (@maxId - @minId));
                                         

                                       
                                      INSERT INTO USMESS (USER_ID, LEX_ID, TXT, DELTA, LTYPE)
                                        SELECT UserId, MSHIP.LEX_ID, LEX.TXT, MSHIP.LEX_ID-@id, @Type 
										FROM MSHIP
                                        JOIN DEGREE ON DEGREE.MSHIP_SOURCE_ID=MSHIP.MSHIP_ID AND DEGREE.CONCEPT_TARGET_ID=@ConceptSourceId
										LEFT JOIN USMESS ON  ( USMESS.USER_ID=UserId AND USMESS.LTYPE=@Type AND  USMESS.LEX_ID=MSHIP.LEX_ID)   
                                        JOIN LEX ON LEX.LEX_ID=MSHIP.LEX_ID
										WHERE MSHIP.CONCEPT_ID=@ConceptTargetId 
										AND USMESS.LEX_ID IS NULL
                                        AND MSHIP.LEX_ID>=@id
                                        ORDER BY MSHIP.LEX_ID
                                        LIMIT 1
											;
                                         
                                         
                      end;                          
        
                     
  
         
             
           end case;  
              
        
          
          
          					SET @j = @j+1;
 
      END WHILE;
     
     
     /* срезаем потенциальные "зоны излишней кучности"  */
     SET jCount = (SELECT COUNT(*) FROM USMESS WHERE USMESS.USER_ID=UserId AND LTYPE=@Type);
     
      while jCount > jCount0
      do 
      
       
        SET @id = (
                          SELECT  USMESS_ID FROM USMESS
                          WHERE (USMESS.USER_ID=UserId) AND (USMESS.LTYPE=@Type) AND USMESS.LEX_ID != TrueTargetLexId
                          ORDER BY USMESS.DELTA DESC
                          LIMIT 1 
                         );  
               
               
          DELETE FROM USMESS
          WHERE USMESS_ID=@id AND LTYPE=@Type;
          
          SET jCount = jCount-1;
      
      end while;
     

     
     /* результаты уходят в USMESS */
 
 /*
    INSERT INTO USMESS (USER_ID, LEX_ID, TXT, LTYPE)
    SELECT UserId, LEX_ID, TXT, 1
    FROM USMESS;
*/
     
   
END sp