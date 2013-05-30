SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `Comments` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `Comments` ;

-- -----------------------------------------------------
-- Table `Comments`.`Sessions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `Comments`.`Sessions` (
  `idSessions` INT NOT NULL AUTO_INCREMENT ,
  `lastActivity` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ,
  `captcha` VARCHAR(10) NULL ,
  PRIMARY KEY (`idSessions`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Comments`.`Comments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `Comments`.`Comments` (
  `idComments` INT NOT NULL AUTO_INCREMENT ,
  `post` BLOB(256) NOT NULL ,
  `comment` TEXT NOT NULL ,
  `author` TEXT(128) NULL ,
  `time` TIMESTAMP NOT NULL DEFAULT NOW() ,
  PRIMARY KEY (`idComments`) )
ENGINE = InnoDB;

USE `Comments` ;

GRANT ALL ON `Comments`.* TO 'comments';
GRANT SELECT ON TABLE `Comments`.* TO 'comments';
GRANT SELECT, INSERT, TRIGGER ON TABLE `Comments`.* TO 'comments';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
