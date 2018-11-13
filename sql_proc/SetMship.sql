CREATE DEFINER=`root`@`%` FUNCTION `SetMship`( LexTxt varchar(255),  ConceptId int) RETURNS int(11)
BEGIN
/* проводим привязку MSHIP. UnSet не имеет большого значения, заметим  */
SET @LexID = SetLex(LexTxt);

SET @MshipId =  (SELECT MSHIP_ID FROM MSHIP WHERE CONCEPT_ID=ConceptId AND  LEX_ID=@LexId );

if (@MShipId is null)
  then
          insert into MSHIP (CONCEPT_ID, LEX_ID) values(ConceptId, @LexId); 
          SET @MshipId =  (SELECT MSHIP_ID FROM MSHIP WHERE CONCEPT_ID=ConceptId AND  LEX_ID=@LexId);

   end if;



RETURN @MshipId;
END