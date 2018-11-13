CREATE DEFINER=`root`@`%` PROCEDURE `NewUserInstall`(__login varchar(255), __password varchar(255), __email varchar(255)  )
BEGIN

SET  @id=GetUserId (__login, __password);
if (@id=0)
  then
    INSERT INTO USER (LOGIN, _PASSWORD, EMAIL) VALUE (__login, MD5(__password), __email);
   SET @Userid=GetUserId (__login, __password);
    -- два концепта 
    SET @name = 'Русские слова';
    INSERT INTO CONCEPT ( NAME, USER_ID) VALUE ( @name,@Userid);
    SET @rusConceptId=(SELECT CONCEPT_ID FROM CONCEPT WHERE NAME=@name AND USER_ID=@Userid);
    
    SET @name = 'Английские слова';
    INSERT INTO CONCEPT ( NAME, USER_ID) VALUE ( @name,@Userid);
    SET @engConceptId=(SELECT CONCEPT_ID FROM CONCEPT WHERE NAME=@name AND USER_ID=@Userid);
    
    -- настроим пользовательскую запись по умолчанию 
    UPDATE USER 
    SET CONC_SOURCE_ID=@rusConceptId,
		CONC_TARGET_ID=@engConceptId
    WHERE USER.USER_ID=@Userid;
    
    -- здесь будет вызоы процедуры NewGlossInstall    
    -- пропишем базовый словарик
    /*
    PROCEDURE `SetDegree`(LexSource varchar(255), ConceptSourceId int,
                                                LexTarget varchar(255), ConceptTargetId int )
    */
    
      CALL NewGlossInstall(@rusConceptId, @engConceptId);
  end if;  

END