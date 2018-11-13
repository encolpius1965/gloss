CREATE DEFINER=`root`@`%` FUNCTION `UnSetCompar`(LexSource varchar(255), ConceptSourceId int,  LexTarget varchar(255), ConceptTargetId int) RETURNS int(11)
BEGIN
SET @MShipSourceId = SetMShip(LexSource, ConceptSourceId);
SET @MShipIdTargetId = SetMShip(LexTarget, ConceptTargetId);


Set @Ret= (SELECT COUNT(*) FROM COMPAR 
                         WHERE 
                         (MSHIP_ID_1=@MShipSourceId AND   MSHIP_ID_2=@MShipTargetId)
                         OR
                         (MSHIP_ID_2=@MShipSourceId AND   MSHIP_ID_1=@MShipTargetId)
                    );


DELETE FROM COMPAR
                         WHERE 
                         (MSHIP_ID_1=@MShipSourceId AND   MSHIP_ID_2=@MShipTargetId)
                         OR
                         (MSHIP_ID_2=@MShipSourceId AND   MSHIP_ID_1=@MShipTargetId)
                    ;


RETURN @Ret;
END