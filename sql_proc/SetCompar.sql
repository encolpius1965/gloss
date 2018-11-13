CREATE DEFINER=`root`@`%` FUNCTION `SetCompar`(LexSource varchar(255), ConceptSourceId int,  LexTarget varchar(255), ConceptTargetId int) RETURNS int(11)
BEGIN
SET @MShipSourceId = SetMShip(LexSource, ConceptSourceId);
SET @MShipTargetId = SetMShip(LexTarget, ConceptTargetId);

SET @ComparId =  (SELECT COMPAR_ID FROM COMPAR WHERE MSHIP_ID_1=@MShipSourceId AND   MSHIP_ID_2=@MShipTargetId);


if (@ComparId is null)
  then
     SET @ComparId =  (SELECT COMPAR_ID FROM COMPAR WHERE MSHIP_ID_2=@MShipSourceId AND   MSHIP_ID_1=@MShipTargetId);
   end if;


if (@ComparId is null)
  then
          insert into COMPAR (MSHIP_ID_1, MSHIP_ID_2 ) values(@MShipSourceId,  @MShipTargetId   ); 
          SET @ComparId =  (SELECT COMPAR_ID FROM COMPAR WHERE MSHIP_ID_1=@MShipSourceId AND   MSHIP_ID_2=@MShipTargetId);
          
   end if;



RETURN @ComparId;
END