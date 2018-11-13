CREATE DEFINER=`root`@`%` PROCEDURE `NewGlossInstall`(ConceptRusId int, ConceptEngId int)
BEGIN
DECLARE done INT DEFAULT 0;
DECLARE rusTxt  varchar(45);
DECLARE engTxt varchar(45);

DECLARE cur CURSOR FOR SELECT RUS, ENG  FROM basegloss WHERE SERIE=1;

DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

-- SELECT 'before open';
-- SELECT COUNT(*) FROM basegloss WHERE SERIE=1;
OPEN cur;
REPEAT 
  FETCH  cur INTO rusTxt, engTxt;
  IF NOT DONE THEN
             begin
 --            SELECT rusTxt, ConceptRusId, engTxt,ConceptEngId;
     CALL SetDegree (rusTxt, ConceptRusId, engTxt,ConceptEngId);  
             end;
   end if;
UNTIL done END repeat;

CLOSE cur;

--  SELECT 'after close';
END