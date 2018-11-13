CREATE DEFINER=`root`@`%` PROCEDURE `GetSourceRec`(in UserId int)
BEGIN
SET  @ScoreMax= (SELECT SCOREMAX FROM USER WHERE USER_ID=UserId);
SET @ConceptSourceId=(SELECT CONC_SOURCE_ID FROM USER WHERE USER_ID=UserId);
SET @ConceptTargetId=(SELECT CONC_TARGET_ID FROM USER WHERE USER_ID=UserId);
SET @Txt="";

drop temporary  table if exists tmpSource;
drop temporary  table if exists tmpMultiSource;

create temporary table  tmpSource
(
     MinId int,
     MaxId int,
     Count int,
     MinScore int,
     Average int, 
     DegreeId int,
     Score int,
     Txt varchar(255)
  );
  

create temporary table  tmpMultiSource
(
     DegreeId int,
     Score int,
     Txt varchar(255)
  );

   
  
INSERT INTO tmpSource (MinId, MaxId, Count, MinScore, Average)
SELECT     MIN(DEGREE.DEGREE_ID) AS MinID, MAX(DEGREE.DEGREE_ID) AS MaxId,  COUNT(*) As Count,  MIN(DEGREE.Score) AS MinScore,   AVG(DEGREE.SCORE) AS Average
FROM         DEGREE INNER JOIN
                      MSHIP ON MSHIP.MSHIP_ID = DEGREE.MSHIP_SOURCE_ID
WHERE     (DEGREE.USER_ID = UserId) AND (MSHIP.CONCEPT_ID = @ConceptSourceId) AND (DEGREE.CONCEPT_TARGET_ID = @ConceptTargetId) AND 
                      (DEGREE.SCORE < @ScoreMax);

SET @Average=(SELECT Average FROM TmpSource);
SET @Count=(SELECT Count FROM TmpSource);
SET @MinScore=(SELECT MinScore FROM TmpSource);
SET @MinId = (SELECT MinId FROM TmpSource);
SET @MaxId = (SELECT MaxId FROM TmpSource);

case
   when (@nCount=0)   /* нет ничего. */
      then 
		begin
        end;
      
   when (@MinScore<0)
        then 
          begin
          /*
                 SET @Txt=
							(
                              SELECT  LEX.TXT                 
									FROM         DEGREE
									INNER JOIN MSHIP ON MSHIP.MSHIP_ID = DEGREE.MSHIP_SOURCE_ID
									INNER JOIN LEX ON LEX.LEX_ID=MSHIP.LEX_ID
                                    WHERE     (DEGREE.USER_ID = UserId) AND (MSHIP.CONCEPT_ID = @ConceptSourceId) AND (DEGREE.CONCEPT_TARGET_ID = @ConceptTargetId) AND 
                                    DEGREE.SCORE=@MinScore
                                    LIMIT 1
                                    );
            */
            
            
 
            
            
                          UPDATE TmpSource,   (
									SELECT  LEX.TXT, DEGREE.DEGREE_ID, DEGREE.SCORE
									FROM         DEGREE
									INNER JOIN MSHIP ON MSHIP.MSHIP_ID = DEGREE.MSHIP_SOURCE_ID
									INNER JOIN LEX ON LEX.LEX_ID=MSHIP.LEX_ID
                                    WHERE     (DEGREE.USER_ID = UserId) AND (MSHIP.CONCEPT_ID = @ConceptSourceId) AND (DEGREE.CONCEPT_TARGET_ID = @ConceptTargetId) AND 
                                    DEGREE.SCORE=@MinScore
                                    LIMIT 1
											 )
                                         Tmp1
							SET 
								TmpSource.Txt = Tmp1.Txt,
                                TmpSource.DegreeId = Tmp1.DEGREE_ID,
                                TmpSource.Score=Tmp1.SCORE
                                ;
 
          end;
		  else 
			begin
              set @id = @MinId + Floor ( rand()* (@MaxId - @MinId));


             /* вынужденное средство чтобы гасить разреженность */      
               INSERT INTO  tmpMultiSource (Txt, DegreeId, Score)
                               SELECT  LEX.TXT, DEGREE.DEGREE_ID, DEGREE.SCORE 
								FROM         DEGREE  JOIN
													MSHIP ON MSHIP.MSHIP_ID = DEGREE.MSHIP_SOURCE_ID
											JOIN LEX ON LEX.LEX_ID=mship.LEX_ID         
								WHERE     (DEGREE.USER_ID = UserId) AND (MSHIP.CONCEPT_ID = @ConceptSourceId) AND (DEGREE.CONCEPT_TARGET_ID = @ConceptTargetId) AND 
											(DEGREE.SCORE < @ScoreMax)
											AND DEGREE.DEGREE_ID>=@id 
											ORDER BY DEGREE.DEGREE_ID    
											limit 11;
			
            SET @MinId = (SELECT MIN(DegreeId) FROM tmpMultiSource); 
            SET @MaxId = (SELECT MAX(DegreeId) FROM tmpMultiSource); 
            set @id = @MinId + Floor ( rand()* (@MaxId - @MinId));

            /*  теперь мы вроде как защишены от разреженности. в пределах 11 слов                  */
              
                          UPDATE TmpSource,   (
                                                                        SELECT Txt, DegreeId, Score 
								FROM  TmpMultiSource
								 	        WHERE DegreeId>=@id 
											ORDER BY DegreeId    
											limit 1
                                            )
                                         Tmp1
							SET 
								TmpSource.Txt = Tmp1.Txt,
                                TmpSource.DegreeId = Tmp1.DegreeId,
                                 TmpSource.Score=Tmp1.Score
                                ;
  					
                   

/*   старый бесхистростный вариант              
                          UPDATE TmpSource,   (
                                                                        SELECT  LEX.TXT, DEGREE.DEGREE_ID, DEGREE.SCORE 
								FROM         DEGREE  JOIN
													MSHIP ON MSHIP.MSHIP_ID = DEGREE.MSHIP_SOURCE_ID
											JOIN LEX ON LEX.LEX_ID=mship.LEX_ID         
								WHERE     (DEGREE.USER_ID = UserId) AND (MSHIP.CONCEPT_ID = @ConceptSourceId) AND (DEGREE.CONCEPT_TARGET_ID = @ConceptTargetId) AND 
											(DEGREE.SCORE < @ScoreMax)
											AND DEGREE.DEGREE_ID>=@id 
											ORDER BY DEGREE.DEGREE_ID    
											limit 1
                                            )
                                         Tmp1
							SET 
								TmpSource.Txt = Tmp1.Txt,
                                TmpSource.DegreeId = Tmp1.DEGREE_ID,
                                 TmpSource.Score=Tmp1.SCORE
                                ;
  					
*/

                
                
            
            end;
end case;   

         UPDATE  USER, TmpSource
         SET  USER.LEX_SOURCE=TmpSource.Txt,
              USER.DEGREE_ID= TmpSource.DegreeId,
              USER.SCORE=TmpSource.Score,
              USER.AVERAGE=@Average,
              USER.COUNT=@Count,
              USER.CONCEPT_ID=@id
         WHERE USER_ID=UserId;
        
        /*
         UPDATE USER,   (SELECT * FROM TmpSource) Tmp1
           SET 
              USER.AVERAGE=Tmp1.Average
           WHERE USER_ID=UserId;
           */
/*
(
     MinId int,
     MaxId int,
     Count int,
     MinScore int,
     Average int
  );

*/         
END