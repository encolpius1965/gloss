CREATE DEFINER=`root`@`%` PROCEDURE `_PutTargetRec`(in UserId int, in LexTargetId int)
BEGIN

 SET @LexTarget = (SELECT TXT FROM LEX WHERE LEX_ID=LexTargetId);

  CALL PutTargetRec( UserId,  @LexTarget);  


    
END