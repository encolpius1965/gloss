CREATE DEFINER=`root`@`%` FUNCTION `SetLex`(LexTxt varchar(255)) RETURNS int(11)
BEGIN

SET @LexId =  
 (SELECT LEX_ID FROM LEX  WHERE TXT=LexTxt); 

if (@LexId is null)
  then
          insert into LEX (TXT) values(LexTxt); 
          SET @LexId =  (SELECT LEX_ID FROM LEX  WHERE TXT=LexTxt);

end if;

RETURN @LexId;
END