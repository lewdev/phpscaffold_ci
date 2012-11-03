CREATE TABLE `Employee` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `fname` VARCHAR(45) NOT NULL ,
  `lname` VARCHAR(45) NOT NULL ,
  `mi` VARCHAR(45) NULL ,
  `address` VARCHAR(127) NULL ,
  `city` VARCHAR(127) NULL ,
  `state` VARCHAR(45) NULL ,
  `zip` VARCHAR(6) NULL ,
  `phone` VARCHAR(45) NULL ,
  `email` VARCHAR(127) NOT NULL ,
  `status` VARCHAR(45) NULL ,
  `hiredate` DATETIME NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;