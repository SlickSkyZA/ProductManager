-- ---
-- Globals
-- ---

-- SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
-- SET FOREIGN_KEY_CHECKS=0;

-- ---
-- Table 'Customer'
--
-- ---

DROP TABLE IF EXISTS `Customer`;

CREATE TABLE `Customer` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(256) NOT NULL DEFAULT 'NULL',
  `PriorityID` INTEGER NOT NULL DEFAULT 0,
  `RegionID` INTEGER NOT NULL DEFAULT 0,
  `AddedDate` DATETIME NOT NULL,
  `UpdatedDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Notes` MEDIUMTEXT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'Competitor'
--
-- ---

DROP TABLE IF EXISTS `Competitor`;

CREATE TABLE `Competitor` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(256) NOT NULL DEFAULT 'NULL',
  `ShortName` VARCHAR(256) NOT NULL DEFAULT 'NULL',
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'Product'
--
-- ---

-- DROP TABLE IF EXISTS `Product`;
-- 
-- CREATE TABLE `Product` (
--   `id` INTEGER NOT NULL AUTO_INCREMENT,
--   `Name` VARCHAR(256) NOT NULL DEFAULT 'NULL',
--   `GroupID` INTEGER NOT NULL,
--   `Version` VARCHAR(256) DEFAULT 'NULL',
--   `PriorityID` INTEGER NOT NULL,
--   `AddedDate` DATETIME NOT NULL,
--   `UpdatedDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   `Notes` MEDIUMTEXT NULL,
--   PRIMARY KEY (`id`)
-- );
-- 
-- -- ---
-- -- Table 'Product Group'
-- --
-- -- ---
-- 
-- DROP TABLE IF EXISTS `Product_Group`;
-- 
-- CREATE TABLE `Product_Group` (
--   `id` INTEGER NOT NULL AUTO_INCREMENT,
--   `Name` VARCHAR(256) NOT NULL DEFAULT 'NULL',
--   `AddedDate` DATETIME NOT NULL,
--   `UpdatedDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   `Notes` MEDIUMTEXT NULL,
--   PRIMARY KEY (`id`)
-- );
-- 
-- -- ---
-- -- Table 'Product Group'
-- --
-- -- ---
-- 
-- DROP TABLE IF EXISTS `Priority`;
-- 
-- CREATE TABLE `Priority` (
--   `id` INTEGER NOT NULL AUTO_INCREMENT,
--   `Value` INTEGER NOT NULL,
--   `Name` VARCHAR(256) NOT NULL DEFAULT 'NULL',
--   `AddedDate` DATETIME NOT NULL,
--   `UpdatedDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   `Notes` MEDIUMTEXT NULL,
--   PRIMARY KEY (`id`)
-- );

-- ---
-- Table 'Product Status'
--
-- ---

DROP TABLE IF EXISTS `Product_Status`;

CREATE TABLE `Product_Status` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `StatusName` VARCHAR(256) NOT NULL DEFAULT 'NULL',
  PRIMARY KEY (`id`)
);

-- -- ---
-- -- Table 'Product Platform'
-- --
-- -- ---
-- 
-- DROP TABLE IF EXISTS `Product_Platform`;
-- 
-- CREATE TABLE `Product_Platform` (
--   `id` INTEGER NOT NULL AUTO_INCREMENT,
--   `Name` VARCHAR(256) NOT NULL DEFAULT 'NULL',
--   `AddedDate` DATETIME NOT NULL,
--   `UpdatedDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   `Notes` MEDIUMTEXT NULL,
--   PRIMARY KEY (`id`)
-- );
-- 
-- ---
-- Table 'XSCP'
--
-- ---

DROP TABLE IF EXISTS `XSCP`;

CREATE TABLE `XSCP` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `ProductID` INTEGER NOT NULL,
  `CustomerID` INTEGER NOT NULL,
  `CompetitorID` INTEGER NOT NULL,
  `CustomerStatusID` INTEGER NOT NULL,
  `StatusID` INTEGER NOT NULL,
  `PlatformID` INTEGER NOT NULL,
  `AddedDate` DATETIME NOT NULL,
  `UpdatedDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Notes` MEDIUMTEXT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'Customer Status'
--
-- ---

DROP TABLE IF EXISTS `Customer_Status`;

CREATE TABLE `Customer_Status` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(256) NOT NULL DEFAULT 'NULL',
  PRIMARY KEY (`id`)
);

-- -- ---
-- -- Table 'Customer Region'
-- --
-- -- ---
-- 
-- DROP TABLE IF EXISTS `Customer_Region`;
-- 
-- CREATE TABLE `Customer_Region` (
--   `id` INTEGER NOT NULL AUTO_INCREMENT,
--   `Name` VARCHAR(256) NOT NULL DEFAULT 'NULL',
--   `ShortName` VARCHAR(256) NOT NULL DEFAULT 'NULL',
--   `AddedDate` DATETIME NOT NULL,
--   `UpdatedDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`)
-- );

-- ---
-- Foreign Keys
-- ---

ALTER TABLE `Customer` ADD FOREIGN KEY (RegionID) REFERENCES `Customer_Region` (`id`);
ALTER TABLE `Customer` ADD FOREIGN KEY (PriorityID) REFERENCES `Priority` (`id`);
ALTER TABLE `Product` ADD FOREIGN KEY (GroupID) REFERENCES `Product_Group` (`id`);
ALTER TABLE `Product` ADD FOREIGN KEY (PriorityID) REFERENCES `Priority` (`id`);
ALTER TABLE `XSCP` ADD FOREIGN KEY (StatusID) REFERENCES `Product_Status` (`id`);
ALTER TABLE `XSCP` ADD FOREIGN KEY (PlatformID) REFERENCES `Product_Platform` (`id`);
ALTER TABLE `XSCP` ADD FOREIGN KEY (ProductID) REFERENCES `Product` (`id`);
ALTER TABLE `XSCP` ADD FOREIGN KEY (CustomerID) REFERENCES `Customer` (`id`);
ALTER TABLE `XSCP` ADD FOREIGN KEY (CompetitorID) REFERENCES `Competitor` (`id`);
ALTER TABLE `XSCP` ADD FOREIGN KEY (CustomerStatusID) REFERENCES `Customer_Status` (`id`);

-- ---
-- Table Properties
-- ---

-- ALTER TABLE `Customer` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
-- ALTER TABLE `Competitor` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
-- ALTER TABLE `Product` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
-- ALTER TABLE `Product Group` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
-- ALTER TABLE `Product Status` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
-- ALTER TABLE `Product Platform` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
-- ALTER TABLE `XSCP` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
-- ALTER TABLE `Customer Status` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
-- ALTER TABLE `Customer Region` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ---
-- Test Data
-- ---

-- INSERT INTO `Customer` (`id`,`Name`,`Priority`,`RegionID`) VALUES
-- ('','','','');
-- INSERT INTO `Competitor` (`id`,`Name`,`ShortName`) VALUES
-- ('','','');
-- INSERT INTO `Product` (`id`,`Name`,`GroupID`,`StatusID`,`PlatformID`,`AddTime`,`UpdateTime`,`Notes`) VALUES
-- ('','','','','','','','');
-- INSERT INTO `Product Group` (`id`,`Name`) VALUES
-- ('','');
-- INSERT INTO `Product Status` (`id`,`StatusName`) VALUES
-- ('','');
-- INSERT INTO `Product Platform` (`id`,`Name`) VALUES
-- ('','');
-- INSERT INTO `XSCP` (`id`,`ProductID`,`CustomerID`,`CompetitorID`,`CustomerStatusID`,`AddTime`,`UpdateTime`,`Notes`) VALUES
-- ('','','','','','','','');
-- INSERT INTO `Customer Status` (`id`,`Name`) VALUES
-- ('','');
-- INSERT INTO `Customer Region` (`id`,`Name`,`ShortName`) VALUES
-- ('','','');
