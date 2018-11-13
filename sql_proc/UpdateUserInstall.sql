CREATE DEFINER=`root`@`%` PROCEDURE `UpdateUserInstall`( UserId int,
__login varchar(255), __password varchar(255), __email varchar(255)
)
BEGIN
if ( UserId>0)
  then 
    UPDATE USER
    SET 
        LOGIN=__login,
        _PASSWORD=MD5(__password),
        EMAIL=__email
     WHERE USER_ID=UserId;
   else
    CALL NewUserInstall(__login,__password, __email); 
   end if;   


END