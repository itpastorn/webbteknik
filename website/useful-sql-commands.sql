ALTER TABLE `joblist` CHANGE `bonusjob` `track` ENUM('no','yes','fast','slow','bonus') 
    CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL DEFAULT 'slow';
UPDATE `joblist` SET `track` = 'fast' WHERE `fast_track_order` IS NOT NULL;
UPDATE `joblist` SET `track` = 'bonus' WHERE `track` = 'yes';
UPDATE `joblist` SET `track` = 'slow' WHERE `track` = 'no';
ALTER TABLE `joblist` CHANGE `slow_track_order` `joborder` MEDIUMINT( 6 ) NOT NULL;
ALTER TABLE `joblist` DROP `fast_track_order`;
ALTER TABLE `joblist` CHANGE `track` `track` ENUM('fast','slow','bonus')
    CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL DEFAULT 'slow';
-- Order fast - slow - bonus is important

ALTER TABLE `userprogress` ADD `lastupdate` DATETIME NOT NULL;
ALTER TABLE `userprogress` CHANGE `approved` `approved` DATETIME NULL DEFAULT NULL COMMENT 'Set by teacher';
UPDATE `userprogress` SET `lastupdate`=NOW();
ALTER TABLE `userprogress` MODIFY COLUMN `approved` DATETIME AFTER `lastupdate`;

CREATE TABLE IF NOT EXISTS `links` (
  `linkID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `linktext` varchar(80) NOT NULL,
  `linkurl` varchar(250) NOT NULL,
  `linktype` enum('book','ref','note','tip','deep') NOT NULL,
  `booksection` varchar(10) NOT NULL,
  `time_added` datetime NOT NULL,
  PRIMARY KEY (`linkID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links for and from the book(s)' AUTO_INCREMENT=1 ;

ALTER TABLE `links` CHANGE `booksection` `section` VARCHAR(10) CHARACTER SET latin1
    COLLATE latin1_swedish_ci NOT NULL COMMENT 'The section of the book in n.n.n format';

-- Not put to server below

CREATE TABLE IF NOT EXISTS `booksections` (
  `booksectionID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `book` varchar(5) NOT NULL,
  `section` varchar(12) NOT NULL,
  `title` VARCHAR( 100 ) NOT NULL COMMENT 'The title of the section in the book',
  PRIMARY KEY (`booksectionID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 
  COMMENT='Used to generate TOC and provide relations between links, videos, etc' 
  AUTO_INCREMENT=1 ;

ALTER TABLE `links` 
CHANGE `linktext` `linktext` VARCHAR(80)  CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
CHANGE `linkurl`  `linkurl`  VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
CHANGE `bookID`   `bookID`   VARCHAR(10)  CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL;

ALTER TABLE `links` DEFAULT CHARACTER SET utf8 COLLATE utf8_swedish_ci;
ALTER TABLE `webbtek_webbtek`.`links` ADD INDEX `bookID` ( `bookID` );

ALTER TABLE `booksections` DEFAULT CHARACTER SET utf8 COLLATE utf8_swedish_ci;
ALTER TABLE `booksections`
  CHANGE `book`    `book`    VARCHAR( 5 )  CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  CHANGE `section` `section` VARCHAR( 12 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL;

ALTER TABLE `book` ADD INDEX `section` ( `book` );

ALTER TABLE `booksections` ADD INDEX `section` ( `section` );
ALTER TABLE `booksections` ADD UNIQUE `nodupsections` ( `book` , `section` );
ALTER TABLE `booksections` ADD `sortorder` SMALLINT UNSIGNED NOT NULL, ADD INDEX ( `sortorder` );
ALTER TABLE `booksections` CHANGE `book` `bookID` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL;
ALTER TABLE `booksections` CHANGE `section` `section` VARCHAR( 12 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL COMMENT 'The section number as in the book n.n.n';

ALTER TABLE `videos` CHANGE `book` `bookID` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL;

CREATE TABLE IF NOT EXISTS `books` (
  `bookID` varchar(5) COLLATE utf8_swedish_ci NOT NULL,
  `booktitle` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `author` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `authormail` varchar(150) COLLATE utf8_swedish_ci NOT NULL,
  `isbn` varchar(20) COLLATE utf8_swedish_ci NOT NULL,
  `type` enum('textbook','workbook','workbookanswers','teacherguide') COLLATE utf8_swedish_ci NOT NULL,
  `bookurl` varchar(150) COLLATE utf8_swedish_ci NOT NULL COMMENT 'Where to buy the book',
  PRIMARY KEY (`bookID`),
  UNIQUE KEY `isbn` (`isbn`),
  KEY `authormail` (`authormail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

INSERT INTO `books` (`bookID`, `booktitle`, `author`, `authormail`, `isbn`, `type`, `bookurl`) VALUES
  ('wu1', 'Webbutveckling 1', 'Lars Gunther', 'gunther@keryx.se', '978-91-7379-175-5', 'textbook', 'http://www.skolportalen.se/laromedel/produkt/J200%204500/Webbutveckling%201%20-%20L%C3%A4robok/');

ALTER TABLE `booksections` ADD FOREIGN KEY ( `bookID` ) REFERENCES `webbtek_webbtek`.`books` (
`bookID`
) ON DELETE RESTRICT ON UPDATE CASCADE ;

ALTER TABLE `links` ADD FOREIGN KEY ( `bookID` ) REFERENCES `webbtek_webbtek`.`books` (
`bookID`
) ON DELETE RESTRICT ON UPDATE CASCADE;


-- Fixing links with "import-links-from-markdown.php"

ALTER TABLE `links` ADD FOREIGN KEY ( `booksectionID` ) REFERENCES `webbtek_webbtek`.`booksections` (
`booksectionID`
) ON DELETE RESTRICT ON UPDATE CASCADE ;

