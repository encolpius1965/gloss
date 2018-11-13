CREATE DEFINER=`root`@`%` PROCEDURE `SetDegree`(LexSource varchar(255), ConceptSourceId int,
                                                LexTarget varchar(255), ConceptTargetId int )
BEGIN
SET @ComparId=SetCompar(LexSource, ConceptSourceId, LexTarget, ConceptTargetId);



SET @UserId = (SELECT USER_ID FROM CONCEPT WHERE CONCEPT_ID=ConceptSourceId);

SET @LexSourceId = SetLex(LexSource);

SET @MshipSourceId = SetMship(LexSource, ConceptSourceId);

SET @DegreeId = 
           (
              SELECT DEGREE_ID FROM DEGREE 
                     WHERE USER_ID=@UserId AND MSHIP_SOURCE_ID=@MshipSourceId AND CONCEPT_TARGET_ID=ConceptTargetId
            );


if (@DegreeId is null)
then
          insert into DEGREE ( USER_ID, MSHIP_SOURCE_ID, CONCEPT_TARGET_ID  )
                 values(@UserId,  @MshipSourceId, ConceptTargetId   ); 
          

   end if;
END