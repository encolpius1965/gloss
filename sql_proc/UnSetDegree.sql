CREATE DEFINER=`root`@`%` PROCEDURE `UnSetDegree`(LexSource varchar(255), ConceptSourceId int,
                                                LexTarget varchar(255), ConceptTargetId int )
BEGIN
SET @ComparId=SetCompar(LexSource, ConceptSourceId, LexTarget, ConceptTargetId);



SET @UserId = (SELECT USER_ID FROM CONCEPT WHERE CONCEPT_ID=ConceptSourceId);

SET @LexSourceId = SetLex(LexSource);

SET @MshipSourceId = SetMship(LexSource, ConceptSourceId);

 -- удапяем тольк в случае если нет других 
 


  
   CALL LexTarget (@LexSourceId, ConceptSourceId, ConceptTargetId);
   SET @nCount = (SELECT COUNT(*) FROM tmpLexTarget WHERE tmpLexTarget.TXT != LexTarget); 
 

    if ( @nCount = 0)
    then
                  DELETE  FROM DEGREE 
                     WHERE USER_ID=@UserId AND MSHIP_SOURCE_ID=@MshipSourceId AND CONCEPT_TARGET_ID=ConceptTargetId;
           
    end if; 
  SET @nCompar=UnSetCompar(LexSource, ConceptSourceId, LexTarget, ConceptTargetId);
END