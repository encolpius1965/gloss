CREATE DEFINER=`root`@`%` PROCEDURE `ArrangeLexSource`(LexSource varchar(255),  ConceptSourceId int, ConceptTargetId int   )
BEGIN

SET @MShip=SetMShip(LexSource, ConceptSourceId);
 CALL GetLexTarget(LexSource,  ConceptSourceId , ConceptTargetId ); 

    
    
END