DELIMITER $$
CREATE TRIGGER bi_gallery
BEFORE INSERT
ON `gallery` FOR EACH ROW
BEGIN
  IF NEW.`type`=2 AND  NEW.`active`=1 THEN
     UPDATE gallery SET `active`=0;
  END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER bi_prayerfocusmemoryverse
BEFORE INSERT
ON `prayerfocusmemoryverse` FOR EACH ROW
BEGIN
  IF NEW.`type`=2 AND  NEW.`active`=1 THEN
     UPDATE prayerfocusmemoryverse SET `active`=0 WHERE type=2 ;
  END IF;
  IF NEW.`type`=1 AND  NEW.`active`=1 THEN
     UPDATE prayerfocusmemoryverse SET `active`=0 WHERE type=1 ;
  END IF;
END$$
DELIMITER ;

