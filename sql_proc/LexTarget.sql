CREATE DEFINER=`root`@`%` PROCEDURE `LexTarget`(LexSourceId  int, ConceptSourceId  int, ConceptTargetId int)
BEGIN


drop temporary  table if exists tmpLexTarget;

create temporary table  tmpLexTarget
(
        LEX_ID int,
        TXT nvarchar(255)
  );


        INSERT INTO tmpLexTarget(
                                LEX_ID,
                                TXT
                )

        

SELECT  LEX_2.LEX_ID,  LEX_2.TXT
FROM COMPAR
LEFT JOIN MSHIP MSHIP_1  ON MSHIP_1.MSHIP_ID=COMPAR.MSHIP_ID_1
LEFT JOIN MSHIP MSHIP_2  ON MSHIP_2.MSHIP_ID=COMPAR.MSHIP_ID_2
LEFT JOIN CONCEPT CONCEPT_1 ON CONCEPT_1.CONCEPT_ID=MSHIP_1.CONCEPT_ID
LEFT JOIN CONCEPT CONCEPT_2 ON CONCEPT_2.CONCEPT_ID=MSHIP_2.CONCEPT_ID
LEFT JOIN LEX LEX_1 ON LEX_1.LEX_ID=MSHIP_1.LEX_ID
LEFT JOIN LEX LEX_2 ON LEX_2.LEX_ID=MSHIP_2.LEX_ID
WHERE
LEX_1.LEX_ID=LexSourceId AND CONCEPT_1.CONCEPT_ID=ConceptSourceId AND  CONCEPT_2.CONCEPT_ID=ConceptTargetId

UNION ALL

SELECT  LEX_1.LEX_ID,  LEX_1.TXT
FROM COMPAR
LEFT JOIN MSHIP MSHIP_1  ON MSHIP_1.MSHIP_ID=COMPAR.MSHIP_ID_1
LEFT JOIN MSHIP MSHIP_2  ON MSHIP_2.MSHIP_ID=COMPAR.MSHIP_ID_2
LEFT JOIN CONCEPT CONCEPT_1 ON CONCEPT_1.CONCEPT_ID=MSHIP_1.CONCEPT_ID
LEFT JOIN CONCEPT CONCEPT_2 ON CONCEPT_2.CONCEPT_ID=MSHIP_2.CONCEPT_ID
LEFT JOIN LEX LEX_1 ON LEX_1.LEX_ID=MSHIP_1.LEX_ID
LEFT JOIN LEX LEX_2 ON LEX_2.LEX_ID=MSHIP_2.LEX_ID
WHERE
LEX_2.LEX_ID=LexSourceId AND CONCEPT_2.CONCEPT_ID=ConceptSourceId AND  CONCEPT_1.CONCEPT_ID=ConceptTargetId;


END