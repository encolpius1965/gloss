CREATE DEFINER=`root`@`%` FUNCTION `GetUserId`(__login varchar(255), __password varchar(255)) RETURNS int(11)
BEGIN
SET @UserId = ifnull(
 (SELECT USER_ID FROM USER WHERE LOGIN=__login AND _PASSWORD=  MD5(__password)),0);
 
RETURN @UserId;
END