CREATE DEFINER=`root`@`%` PROCEDURE `DeleteConcept`( UserId int, ConceptId int)
BEGIN
Declare Ret int;
SET Ret=0;

/* 
Три варианта - либо такая запись не обнаружена, либо имеются записи, имеющие отношение, либо всё ok и мы каскадно вычищаем всё, что имеет отношение   
  */
case 
  when (SELECT COUNT(*) FROM CONCEPT WHERE CONCEPT_ID=ConceptId)=0
             then SET Ret=-1;
             
  when 
       ( SELECT COUNT(*) FROM CONCEPT 
         JOIN MSHIP ON MSHIP.CONCEPT_ID=CONCEPT.CONCEPT_ID
         JOIN LEX ON LEX.LEX_ID=MSHIP.MSHIP_ID
         WHERE CONCEPT.CONCEPT_ID=ConceptId
         )>0
               then SET Ret=-2;      
   else
       begin
        /* вымарываем всё */
        
        /* COMPAR */
        
        DELETE FROM COMPAR 
        WHERE COMPAR.MSHIP_ID_1 IN 
        (
         SELECT MSHIP_ID FROM MSHIP
         WHERE MSHIP.CONCEPT_ID=ConceptID
        );
 
 
        DELETE FROM COMPAR 
        WHERE COMPAR.MSHIP_ID_2 IN 
        (
         SELECT MSHIP_ID FROM MSHIP
         WHERE MSHIP.CONCEPT_ID=ConceptID
        );
 
 
        
        /* CONCEPT */
        DELETE FROM CONCEPT
        WHERE CONCEPT_ID=ConceptId;
        
        /* DEGREE */
        DELETE FROM DEGREE
        WHERE CONCEPT_TARGET_ID=ConceptId;
        
        DELETE FROM DEGREE 
        WHERE MSHIP_SOURCE_ID IN
        (SELECT MSHIP_ID FROM MSHIP WHERE MSHIP.CONCEPT_ID=ConceptId);
        
        /* MSHIP */
        DELETE FROM MSHIP
        WHERE MSHIP.CONCEPT_ID=ConceptId;
        
        SET Ret = 1;
 
       end;  
end case;

UPDATE USER 
SET
 DELRES=Ret
 WHERE USER_ID=UserId;

END