CREATE DEFINER=`root`@`%` PROCEDURE `AddNewConcept`(userId int, newName varchar(255))
BEGIN
SET @nCount = (SELECT COUNT(*) FROM CONCEPT WHERE NAME=newName AND USER_ID=userId);

if (@nCount=0)
 then 
   INSERT INTO CONCEPT (USER_ID, NAME) VALUE(userId, newName);
   SET @ConceptId = (SELECT CONCEPT_ID FROM CONCEPT WHERE USER_ID=userId AND NAME=newName);
   
   UPDATE USER 
   SET CONCEPT_ID=@ConceptId
   WHERE USER_ID=userId;
   
end if;
END