CREATE DEFINER=`root`@`%` PROCEDURE `TmpPasswordApply`(  
__password varchar(255), __email varchar(255), lMsg tinyint
)
BEGIN

SET @UserId = (SELECT USER_ID FROM USER WHERE EMAIL=__email);
SET @login = (SELECT LOGIN FROM USER WHERE EMAIL=__email);
if ( @UserId>0)
  then 
    CALL UpdateUserInstall(@UserId,   @login, __password, __email);  
    if ( lMsg>0)
      then INSERT INTO USMESS (USER_ID, LTYPE, TXT)
                  VALUES (@UserId, -1,
                       'To '+ __email + ': Логин '+ @login+' Новый пароль '+ __password); 
                       end if;
   end if;   


END