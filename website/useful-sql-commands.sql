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

-- create from scratch

CREATE TABLE `books` (
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


CREATE TABLE IF NOT EXISTS `booksections` (
  `booksectionID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bookID` varchar(5) COLLATE utf8_swedish_ci NOT NULL,
  `section` varchar(12) COLLATE utf8_swedish_ci NOT NULL COMMENT 'The section number as in the book n.n.n',
  `title` varchar(100) COLLATE utf8_swedish_ci NOT NULL COMMENT 'The title of the section in the book',
  `sortorder` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`booksectionID`),
  UNIQUE KEY `nodupsections` (`bookID`,`section`),
  KEY `book` (`bookID`),
  KEY `section` (`section`),
  KEY `sortorder` (`sortorder`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='Used to generate TOC and provide relations between links, videos, etc' AUTO_INCREMENT=36 ;

ALTER TABLE `booksections`
  ADD CONSTRAINT `booksections_ibfk_1` FOREIGN KEY (`bookID`) REFERENCES `books` (`bookID`) ON UPDATE CASCADE;

INSERT INTO `booksections` (`booksectionID`, `bookID`, `section`, `title`, `sortorder`) VALUES
(1, 'wu1', '1', 'Vad är webbteknik', 1),
(2, 'wu1', '1.1', 'Några snabba kommentarer om koden', 2),
(3, 'wu1', '1.1.1', 'JavaScript för beteende', 3),
(4, 'wu1', '1.2', 'Standarder', 4),
(5, 'wu1', '1.3', 'Validering', 5),
(6, 'wu1', '1.4', 'Standardiseringsorgan och fler standarder', 6),
(7, 'wu1', '1.4.1', 'DNS och URL:er', 7),
(8, 'wu1', '1.4.2', 'Schema (fördjupning på webben)', 8),
(9, 'wu1', '1.4.3', 'Auktoritet (fördjupning på webben)', 9),
(10, 'wu1', '1.4.4', 'Sökväg och resursnamn', 10),
(11, 'wu1', '1.4.5', 'Query strings och fragment identifiers', 11),
(12, 'wu1', '1.4.6', 'Bra URL-design', 12),
(13, 'wu1', '1.4.7', 'Relativa och absoluta sökvägar', 13),
(14, 'wu1', '1.4.8', 'http och https', 14),
(15, 'wu1', '1.4.9', 'HTTP-huvuden, metoder och statuskoder', 15),
(16, 'wu1', '1.5', 'Vattenfallsdiagram och sidornas fart (fördjupning på webben)', 16),
(17, 'wu1', '1.6', 'Cachning', 17),
(18, 'wu1', '1.7', 'Statiska kontra dynamiska sidor', 18),
(19, 'wu1', '1.8', 'Enkla sidor kontra fullfjädrade applikationer (Ajax)', 19),
(20, 'wu1', '1.9', 'Proprietära tekniker', 20),
(21, 'wu1', '1.9.1', 'Insticksprogram (plug-ins)', 21),
(22, 'wu1', '1.9.2', 'Ljud och videoformat', 22),
(23, 'wu1', '1.9.3', 'Öppen källkod och öppna standarder (fördjupning på webben)', 23),
(24, 'wu1', '1.9.4', 'Webbläsarkrig ', 24),
(25, 'wu1', '1.10', 'Läs mer här', 25),
(26, 'wu1', '1.10.1', 'Webbutveckling.nu', 26),
(27, 'wu1', '1.10.2', 'InterACT with Web Standards', 27),
(28, 'wu1', '1.10.3', 'W3C Web Standards Curriculum (W3C WSC)', 28),
(29, 'wu1', '1.10.4', 'HTML Dog', 29),
(30, 'wu1', '1.10.5', 'Mozilla Developer Network (MDN)', 30),
(31, 'wu1', '1.10.6', 'SitePoint', 31),
(32, 'wu1', '1.10.7', 'Webforum', 32),
(33, 'wu1', '1.10.8', 'StackOverflow', 33),
(34, 'wu1', '1.10.9', 'Max räckvidd med HTML och CSS', 34),
(35, 'wu1', '1.11', 'Men inte här', 35);

CREATE TABLE IF NOT EXISTS `links` (
  `linkID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `linktext` varchar(80) COLLATE utf8_swedish_ci NOT NULL,
  `linkurl` varchar(250) COLLATE utf8_swedish_ci NOT NULL,
  `linktype` enum('book','ref','note','tip','deep') CHARACTER SET latin1 NOT NULL,
  `booksectionID` mediumint(8) unsigned DEFAULT NULL,
  `time_added` datetime NOT NULL,
  `bookID` varchar(10) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`linkID`),
  UNIQUE KEY `noduplinks` (`linkurl`,`bookID`),
  KEY `bookID` (`bookID`),
  KEY `booksectionID` (`booksectionID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='Links for and from the book(s)' AUTO_INCREMENT=222 ;

ALTER TABLE `links`
  ADD CONSTRAINT `links_ibfk_1` FOREIGN KEY (`bookID`) REFERENCES `books` (`bookID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `links_ibfk_2` FOREIGN KEY (`booksectionID`) REFERENCES `booksections` (`booksectionID`) ON UPDATE CASCADE;

INSERT INTO `links` (`linkID`, `linktext`, `linkurl`, `linktype`, `booksectionID`, `time_added`, `bookID`) VALUES
(1, 'HTML Introduction', 'https://developer.mozilla.org/en/HTML/Introduction', 'ref', 1, '2012-08-15 02:33:25', 'wu1'),
(2, 'What''s the difference between JavaScript and Java', 'http://stackoverflow.com/questions/245062/whats-the-difference-between-javascript-and-java', 'note', 3, '2012-08-15 02:33:25', 'wu1'),
(3, 'validator.nu', 'http://validator.nu/', 'book', 5, '2012-08-15 02:33:25', 'wu1'),
(4, 'W3C', 'http://w3.org', 'ref', 6, '2012-08-15 02:33:25', 'wu1'),
(5, 'ECMAScript hos ECMA', 'http://www.ecma-international.org/publications/standards/Stnindex.htm#Software', 'ref', 6, '2012-08-15 02:33:25', 'wu1'),
(6, 'WhatWG', 'http://www.whatwg.org/', 'ref', 6, '2012-08-15 02:33:25', 'wu1'),
(7, 'Khronos Group', 'http://www.khronos.org/webgl/', 'ref', 6, '2012-08-15 02:33:25', 'wu1'),
(8, 'The WebM Project', 'http://www.webmproject.org/', 'ref', 6, '2012-08-15 02:33:25', 'wu1'),
(9, 'WebCL', 'http://www.khronos.org/webcl/wiki/Main_Page', 'note', 6, '2012-08-15 02:33:25', 'wu1'),
(10, 'URI scheme', 'http://en.wikipedia.org/wiki/URI_scheme', 'tip', 16, '2012-08-15 02:33:25', 'wu1'),
(11, 'about:about', 'about:about', 'book', 16, '2012-08-15 02:33:25', 'wu1'),
(12, 'Sökning efter itpastorn', 'http://google.com/search?q=itpastorn&lang=sv', 'book', 11, '2012-08-15 02:33:25', 'wu1'),
(13, 'Et-tecknet', 'http://sv.wikipedia.org/wiki/Et-tecken', 'note', 11, '2012-08-15 02:33:25', 'wu1'),
(14, 'Nummertecknet #', 'http://sv.wikipedia.org/wiki/Nummertecken', 'note', 11, '2012-08-15 02:33:25', 'wu1'),
(15, 'Protocole Relative URL', 'http://paulirish.com/2010/the-protocol-relative-url/', 'note', 13, '2012-08-15 02:33:25', 'wu1'),
(16, 'Ajax: A New Approach to Web Applications', 'http://www.adaptivepath.com/ideas/ajax-new-approach-web-applications', 'note', 19, '2012-08-15 02:33:25', 'wu1'),
(17, 'Mozillas demosida för nya webbtekniker', 'https://developer.mozilla.org/en-US/demos/', 'ref', 19, '2012-08-15 02:33:25', 'wu1'),
(18, 'Emberwind i HTML5 canvas', 'http://my.opera.com/chooseopera/blog/2011/07/07/emberwind-a-html5-masterpiece', 'tip', 19, '2012-08-15 02:33:25', 'wu1'),
(19, 'Boken InterACT with Web Standards', 'http://interactwithwebstandards.com/', 'book', 27, '2012-08-15 02:33:25', 'wu1'),
(20, 'Web Standards Curriculum', 'http://www.w3.org/wiki/Web_Standards_Curriculum', 'book', 28, '2012-08-15 02:33:25', 'wu1'),
(21, 'HTML beginner', 'http://htmldog.com/guides/htmlbeginner/', 'book', 29, '2012-08-15 02:33:25', 'wu1'),
(22, 'Mozilla Developer Network', 'https://developer.mozilla.org/', 'book', 30, '2012-08-15 02:33:25', 'wu1'),
(23, 'MDN Learn', 'https://developer.mozilla.org/en-US/learn', 'book', 30, '2012-08-15 02:33:25', 'wu1'),
(24, 'SitePoint', 'http://www.sitepoint.com/', 'book', 31, '2012-08-15 02:33:25', 'wu1'),
(25, 'Page Structure', 'http://reference.sitepoint.com/html/page-structure', 'book', 31, '2012-08-15 02:33:25', 'wu1'),
(26, 'Webforum', 'http://www.webforum.nu/', 'book', 32, '2012-08-15 02:33:25', 'wu1'),
(27, 'Webforums stora tipstråd', 'http://www.webforum.nu/forumdisplay.php?f=12', 'book', 32, '2012-08-15 02:33:25', 'wu1'),
(28, 'Stackoverflow', 'http://stackoverflow.com/', 'book', 33, '2012-08-15 02:33:25', 'wu1'),
(29, 'Om boken Max Räckvidd med HTML och CSS', 'http://kaxigt.com/2009/11/max-rackvidd-med-html-css-tommy-olsson-berattar-om-sin-bok/', 'book', 34, '2012-08-15 02:33:25', 'wu1'),
(30, 'W3 Fools', 'http://w3fools.com/', 'book', 35, '2012-08-15 02:33:25', 'wu1'),
(31, 'Historical artifacts to avoid', 'https://developer.mozilla.org/en/Web_development/Historical_artifacts_to_avoid', 'ref', 35, '2012-08-15 02:33:25', 'wu1'),
(218, 'CSS validering', 'http://jigsaw.w3.org/css-validator/', 'ref', 5, '2012-08-15 22:13:01', 'wu1'),
(219, 'Firebug', 'https://getfirebug.com/', 'ref', 5, '2012-08-15 22:13:01', 'wu1'),
(220, 'Validator for Firebug', 'https://addons.mozilla.org/en-us/firefox/addon/validator/', 'ref', 5, '2012-08-15 22:13:01', 'wu1'),
(221, 'Mozilla Thimble', 'https://thimble.webmaker.org/', 'ref', 1, '2012-08-15 22:42:22', 'wu1');

CREATE TABLE `videos` (
  `videoname` varchar(20) COLLATE utf8_swedish_ci NOT NULL,
  `title` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `bookID` varchar(5) COLLATE utf8_swedish_ci NOT NULL,
  `booksectionID` mediumint(8) unsigned NOT NULL,
  `tags` varchar(150) COLLATE utf8_swedish_ci NOT NULL,
  `order` mediumint(8) unsigned DEFAULT NULL COMMENT 'Suggested viewing order',
  PRIMARY KEY (`videoname`),
  KEY `booksectionID` (`booksectionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumpning av Data i tabell `videos`
--

INSERT INTO `videos` (`videoname`, `title`, `bookID`, `booksectionID`, `tags`, `order`) VALUES
('kap-1-a-1', 'Ett enkelt HTML-dokument, del 1', 'wu1', 1, 'html', 1),
('kap-1-a-2', 'Ett enkelt HTML-dokument, del 2', 'wu1', 1, 'html,css', 2),
('kap-1-a-3', 'Värdet av doctype samt inspektera element i Firefox', 'wu1', 1, 'html, doctype, verktyg', 3),
('kap-1-a-4', 'Validering', 'wu1', 5, 'verktyg, validering', 5),
('thimble', 'Bonusvideo: Mozilla Thimble', 'wu1', 1, 'verktyg', 4);

CREATE TABLE IF NOT EXISTS `workplaces` (
  `workplaceID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `schoolID` varchar(6) COLLATE utf8_swedish_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_swedish_ci NOT NULL,
  `since` date NOT NULL,
  PRIMARY KEY (`workplaceID`),
  UNIQUE KEY `nodup` (`schoolID`,`email`),
  KEY `email` (`email`),
  KEY `schoolID` (`schoolID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='Where teachers work' AUTO_INCREMENT=1 ;

-- -------------------------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `belonging_groups` (
  `tgID` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) COLLATE utf8_swedish_ci NOT NULL,
  `groupID` varchar(5) COLLATE utf8_swedish_ci NOT NULL,
  `since` date NOT NULL,
  PRIMARY KEY (`tgID`),
  UNIQUE KEY `nodups` (`email`,`groupID`),
  KEY `email` (`email`,`groupID`),
  KEY `groupsID` (`groupID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='What groups does a teacher run' AUTO_INCREMENT=9 ;

--
-- Dumpning av Data i tabell `belonging_groups`
--

INSERT INTO `belonging_groups` (`tgID`, `email`, `groupID`, `since`) VALUES
(3, 'gunther@keryx.se', 'tzdrq', '2012-08-22'),
(4, 'gunther@keryx.se', 'cpsaf', '2012-08-22'),
(8, 'info@keryx.se', 'cpsaf', '2012-08-22');

-- --------------------------------------------------------

--
-- Tabellstruktur `courses`
--

CREATE TABLE IF NOT EXISTS `courses` (
  `courseID` varchar(25) COLLATE utf8_swedish_ci NOT NULL,
  `course_name` varchar(200) COLLATE utf8_swedish_ci NOT NULL,
  `course_url` varchar(250) COLLATE utf8_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`courseID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumpning av Data i tabell `courses`
--

INSERT INTO `courses` (`courseID`, `course_name`, `course_url`) VALUES
('WEBWEB01', 'Webbserverprogrammering 1', 'http://www.skolverket.se/forskola-och-skola/gymnasieutbildning/amnes-och-laroplaner/sok-program-och-amnesplaner/subject.htm?subjectCode=WEB&courseCode=WEBWEB01'),
('WEBWEU01', 'Webbutveckling 1', 'http://www.skolverket.se/forskola-och-skola/gymnasieutbildning/amnes-och-laroplaner/sok-program-och-amnesplaner/subject.htm?subjectCode=WEB&courseCode=WEBWEU01');

-- --------------------------------------------------------

-- --------------------------------------------------------
-- 2012-08-22 added tables
-----------------------------------------------------------

CREATE TABLE IF NOT EXISTS `groups` (
  `groupID` varchar(5) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL COMMENT 'Invitation code to group',
  `schoolID` varchar(6) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `courseID` varchar(10) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL COMMENT 'Code in the national system',
  `group_nickname` varchar(20) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL COMMENT 'Daily usage ',
  `group_max_size` mediumint(8) unsigned NOT NULL,
  `group_start_date` date NOT NULL,
  `group_url` varchar(150) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL COMMENT 'E.g. learning platform link',
  PRIMARY KEY (`groupID`),
  UNIQUE KEY `nodup` (`schoolID`,`group_nickname`,`group_start_date`),
  KEY `schoolID` (`schoolID`),
  KEY `courseID` (`courseID`),
  KEY `date` (`group_start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `courses` (
  `courseID` varchar(25) COLLATE utf8_swedish_ci NOT NULL,
  `course_name` varchar(200) COLLATE utf8_swedish_ci NOT NULL,
  `course_url` varchar(250) COLLATE utf8_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`courseID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

INSERT INTO `courses` (`courseID`, `course_name`, `course_url`) VALUES
('WEBWEB01', 'Webbserverprogrammering 1', 'http://www.skolverket.se/forskola-och-skola/gymnasieutbildning/amnes-och-laroplaner/sok-program-och-amnesplaner/subject.htm?subjectCode=WEB&courseCode=WEBWEB01'),
('WEBWEU01', 'Webbutveckling 1', 'http://www.skolverket.se/forskola-och-skola/gymnasieutbildning/amnes-och-laroplaner/sok-program-och-amnesplaner/subject.htm?subjectCode=WEB&courseCode=WEBWEU01');

CREATE TABLE IF NOT EXISTS `schools` (
  `schoolID` varchar(6) COLLATE utf8_swedish_ci NOT NULL,
  `school_name` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `school_place` varchar(50) COLLATE utf8_swedish_ci NOT NULL COMMENT 'Town, county, web, etc',
  `school_url` varchar(100) COLLATE utf8_swedish_ci NOT NULL COMMENT 'School web page',
  `numtextbooks` mediumint(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Willl limit total size of all groups',
  `numworkbooks` mediumint(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Will limit total size of all groups',
  `numteachguides` smallint(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`schoolID`),
  UNIQUE KEY `nodup` (`school_name`,`school_place`),
  KEY `numtextbooks` (`numtextbooks`,`numworkbooks`,`numteachguides`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE IF NOT EXISTS `belonging_groups` (
  `tgID` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) COLLATE utf8_swedish_ci NOT NULL,
  `groupID` varchar(5) COLLATE utf8_swedish_ci NOT NULL,
  `since` date NOT NULL,
  PRIMARY KEY (`tgID`),
  UNIQUE KEY `nodups` (`email`,`groupID`),
  KEY `email` (`email`,`groupID`),
  KEY `groupsID` (`groupID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='What groups does a teacher run' AUTO_INCREMENT=9 ;

CREATE TABLE IF NOT EXISTS `teaching_groups` (
  `tgID` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) COLLATE utf8_swedish_ci NOT NULL,
  `groupID` varchar(5) COLLATE utf8_swedish_ci NOT NULL,
  `since` date NOT NULL,
  PRIMARY KEY (`tgID`),
  UNIQUE KEY `nodups` (`email`,`groupID`),
  KEY `email` (`email`,`groupID`),
  KEY `groupsID` (`groupID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='What groups does a teacher run' AUTO_INCREMENT=7 ;

CREATE TABLE IF NOT EXISTS `workplaces` (
  `workplaceID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `schoolID` varchar(6) COLLATE utf8_swedish_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_swedish_ci NOT NULL,
  `since` date NOT NULL,
  PRIMARY KEY (`workplaceID`),
  UNIQUE KEY `nodup` (`schoolID`,`email`),
  KEY `email` (`email`),
  KEY `schoolID` (`schoolID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='Where teachers work' AUTO_INCREMENT=42 ;

ALTER TABLE `belonging_groups`
  ADD CONSTRAINT `belonging_groups_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON UPDATE CASCADE,
  ADD CONSTRAINT `belonging_groups_ibfk_2` FOREIGN KEY (`groupID`) REFERENCES `groups` (`groupID`) ON UPDATE CASCADE;

ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`schoolID`) REFERENCES `schools` (`schoolID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `groups_ibfk_2` FOREIGN KEY (`courseID`) REFERENCES `courses` (`courseID`) ON UPDATE CASCADE;

ALTER TABLE `teaching_groups`
  ADD CONSTRAINT `teaching_groups_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON UPDATE CASCADE,
  ADD CONSTRAINT `teaching_groups_ibfk_2` FOREIGN KEY (`groupID`) REFERENCES `groups` (`groupID`) ON UPDATE CASCADE;

ALTER TABLE `workplaces`
  ADD CONSTRAINT `workplaces_ibfk_1` FOREIGN KEY (`schoolID`) REFERENCES `schools` (`schoolID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `workplaces_ibfk_2` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON UPDATE CASCADE;


-- -----------------------------------------------------------------------
-- Changelog
-- -----------------------------------------------------------------------

ALTER TABLE `videos` CHANGE `book` `bookID` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL;
ALTER TABLE `videos` DROP `section`;

ALTER TABLE `flashcards` CHANGE `flashcardsID` `flashcardID` MEDIUMINT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT 

ALTER TABLE `userprogress` ADD INDEX ( `status` );

-- 2012-08-22

ALTER TABLE `webbtek_webbtek`.`joblist` ADD INDEX ( `what_to_do` );
ALTER TABLE `webbtek_webbtek`.`joblist` ADD INDEX ( `where_to_do_it` );
ALTER TABLE `webbtek_webbtek`.`joblist` ADD INDEX ( `joborder` );
ALTER TABLE `webbtek_webbtek`.`joblist` ADD INDEX ( `track` );
ALTER TABLE `webbtek_webbtek`.`joblist` ADD INDEX ( `chapter` );

ALTER TABLE `books` CHANGE `type`
   `type` ENUM( 'textbook', 'workbook', 'workbookanswers', 'teacherguide', 'none' )
   CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL COMMENT 'none for dummy no book at all' ;

-- 2012-08-27
 
ALTER TABLE `books` ADD `courseID` VARCHAR( 25 ) NULL ;

ALTER TABLE `books` ADD INDEX ( `courseID` ) ;

ALTER TABLE `videos` CHANGE `bookID` `bookID` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NULL ,
CHANGE `booksectionID` `booksectionID` MEDIUMINT( 8 ) UNSIGNED NULL; 

 ALTER TABLE `videos` ADD FOREIGN KEY ( `booksectionID` ) REFERENCES `webbtek_webbtek`.`booksections` (
`booksectionID`
) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `webbtek_webbtek`.`videos` ADD INDEX ( `bookID` ) 

ALTER TABLE `books` CHANGE `isbn` `isbn` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NULL;

ALTER TABLE `books` ADD FOREIGN KEY ( `authormail` ) REFERENCES `webbtek_webbtek`.`users` (
`email`
) ON DELETE RESTRICT ON UPDATE CASCADE ;

ALTER TABLE `books` ADD FOREIGN KEY ( `courseID` ) REFERENCES `webbtek_webbtek`.`courses` (
`courseID`
) ON DELETE RESTRICT ON UPDATE CASCADE ;

INSERT IGNORE INTO `books` (`bookID`, `booktitle`, `author`, `authormail`, `isbn`, `type`, `bookurl`, `courseID`) VALUES
('ws1', 'Webbserverprogrammering 1 med PHP', 'Lars Gunther', 'gunther@keryx.se', NULL, 'textbook', '', 'WEBWEB01'),
('wu1', 'Webbutveckling 1', 'Lars Gunther', 'gunther@keryx.se', '978-91-7379-175-5', 'textbook', 'http://www.skolportalen.se/laromedel/produkt/J200%204500/Webbutveckling%201%20-%20L%C3%A4robok/', 'WEBWEU01'),
('wu1tg', 'Lärarhandledning Webbutveckling 1', 'Lars Gunther', 'gunther@keryx.se', '', 'teacherguide', '', 'WEBWEU01');

ALTER TABLE `flashcardsets` CHANGE `section` `section` VARCHAR( 12 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NULL;

ALTER TABLE `joblist`  ADD `bookID` VARCHAR(5) CHARACTER SET utf8 
  COLLATE utf8_swedish_ci NULL AFTER `track`,  ADD INDEX (`bookID`);

INSERT INTO `webbtek_webbtek`.`books` (
  `bookID`,`booktitle`, `author`, `authormail`, `isbn`, `type`, `bookurl`, `courseID`
)
VALUES (
    'site', 'Webbplatsen', 'Lars Gunther', 'gunther@keryx.se', NULL , 'none', '', NULL
);
UPDATE `joblist` SET `bookID`='wu1';

ALTER TABLE `joblist` ADD FOREIGN KEY ( `bookID` ) REFERENCES `webbtek_webbtek`.`books` (
`bookID`
) ON DELETE RESTRICT ON UPDATE CASCADE ;


UPDATE `webbtek_webbtek`.`videos` SET `bookID` = 'site' WHERE `videos`.`videoname` = 'registrera-konto';

ALTER TABLE `links` CHANGE `bookID` `bookID` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NULL; -- Allow null

ALTER TABLE links MODIFY COLUMN `time_added` INT AFTER `bookID`;
ALTER TABLE `links` CHANGE `linktype` `linktype` ENUM( 'book', 'ref', 'note', 'tip', 'deep' )
  CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL; -- UTF-8

ALTER TABLE `links` ADD `videoname` VARCHAR( 20 ) CHARACTER SET utf8
  COLLATE utf8_swedish_ci NULL COMMENT 'Matches what video' AFTER `bookID` ,
  ADD INDEX ( `videoname` );

ALTER TABLE `links` CHANGE `time_added` `time_added` DATETIME NULL DEFAULT NULL; -- oops
UPDATE `links` SET `time_added` = NOW();

ALTER TABLE `links` ADD FOREIGN KEY ( `videoname` ) REFERENCES `webbtek_webbtek`.`videos` (
`videoname`
) ON DELETE RESTRICT ON UPDATE CASCADE ;

UPDATE `links` SET videoname = 'wu-lb-1-10' WHERE `booksectionID` > 26;
UPDATE `webbtek_webbtek`.`links` SET `videoname` = 'kap-1-a-1' WHERE `links`.`linkID` =1;
UPDATE `webbtek_webbtek`.`links` SET `videoname` = 'thimble' WHERE `links`.`linkID` =221;
UPDATE `webbtek_webbtek`.`links` SET `videoname` = 'kap-1-a-3' WHERE `links`.`linkID` =2;
UPDATE `webbtek_webbtek`.`links` SET `videoname` = 'kap-1-a-4' WHERE `links`.`booksectionID` =5;

ALTER TABLE `flashcardsets` CHANGE `section` `booksectionID` MEDIUMINT( 8 ) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `webbtek_webbtek`.`flashcardsets` ADD INDEX ( `booksectionID` );

ALTER TABLE `flashcardsets` ADD FOREIGN KEY ( `booksectionID` ) REFERENCES `webbtek_webbtek`.`booksections` (
`booksectionID`
) ON DELETE RESTRICT ON UPDATE CASCADE ;

ALTER TABLE `webbtek_webbtek`.`flashcards` DROP INDEX `term` , ADD INDEX `term` ( `term` ):

ALTER TABLE `flashcards` ADD INDEX ( `setID` );

ALTER TABLE `flashcards` ADD FOREIGN KEY ( `setID` ) REFERENCES `webbtek_webbtek`.`flashcardsets` (`setID`)
  ON DELETE RESTRICT ON UPDATE CASCADE ;

ALTER TABLE `flashcards` CHANGE `term` `term` VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL ,
  CHANGE `short` `short` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL ,
  CHANGE `long` `long` VARCHAR( 180 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL ,
  CHANGE `setID` `setID` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL ;

ALTER TABLE `flashcardsets` CHANGE `book` `bookID` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NULL DEFAULT NULL;
 
ALTER TABLE `webbtek_webbtek`.`flashcardsets` ADD INDEX ( `bookID` );
ALTER TABLE `flashcardsets` ADD FOREIGN KEY ( `bookID` ) REFERENCES `webbtek_webbtek`.`books` (
`bookID`
) ON DELETE RESTRICT ON UPDATE CASCADE ;

ALTER TABLE `videos` ADD `chapter` TINYINT UNSIGNED NULL AFTER `bookID` ,
ADD INDEX ( `chapter` );

ALTER TABLE `links` ADD `chapter` TINYINT UNSIGNED NULL AFTER `bookID` ,
ADD INDEX ( `chapter` );
UPDATE `links` SET `chapter`=1 WHERE booksectionID < 35 AND `booksectionID` IS NOT NULL;

ALTER TABLE `privilege_questions` ADD `bookID` VARCHAR( 5 ) NOT NULL AFTER `privileges` ,
ADD INDEX ( `bookID` );
UPDATE `webbtek_webbtek`.`privilege_questions` SET `bookID` = 'wu1';
ALTER TABLE `privilege_questions` ADD FOREIGN KEY ( `bookID` ) REFERENCES `webbtek_webbtek`.`books` (
`bookID`
) ON DELETE RESTRICT ON UPDATE CASCADE ;

INSERT INTO `webbtek_webbtek`.`privilege_questions` (`pqID`, `question`, `answer`, `privileges`, `bookID`, `times_used`)
VALUES (null, 'I första stycket i avsnitt 1 i Läroboken Webbserverprogrammering 1 med PHP, vad är det första markerade ordet?', 'körmiljö', '7', 'ws1', '0'),
       (null, 'I första stycket i avsnitt 1.2 i Läroboken Webbserverprogrammering 1 med PHP, vad är det första markerade ordet?', 'XAMPP', '7', 'ws1', '0');

CREATE TABLE IF NOT EXISTS `access_control` (
  `aclID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) COLLATE utf8_swedish_ci NOT NULL,
  `bookID` varchar(5) COLLATE utf8_swedish_ci NOT NULL,
  `since` datetime NOT NULL,
  PRIMARY KEY (`aclID`),
  UNIQUE KEY `nodup` (`email`,`bookID`),
  KEY `bookID` (`bookID`),
  KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=1 ;
ALTER TABLE `access_control`
  ADD CONSTRAINT `access_control_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON UPDATE CASCADE,
  ADD CONSTRAINT `access_control_ibfk_2` FOREIGN KEY (`bookID`) REFERENCES `books` (`bookID`) ON UPDATE CASCADE;

ALTER TABLE `belonging_groups` CHANGE `tgID` `bgID` INT( 11 ) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users` ADD `currentbook` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NULL
COMMENT 'What book user is currently working with',
ADD INDEX ( `currentbook` );

-- Not put to server below

ALTER TABLE `webbtek_webbtek`.`booksections` ADD UNIQUE `nodupsorted` ( `bookID` , `sortorder` );

INSERT INTO `webbtek_webbtek`.`books` (`bookID`, `booktitle`, `author`, `authormail`, `isbn`, `type`, `bookurl`, `courseID`) VALUES ('git', 'Versionshantering med Git och GitHub', 'Lars Gunther', 'gunther@keryx.se', NULL, 'none', '', NULL);

-- Test SQL

-- All users that belong to a group
SELECT users.*, groups.group_nickname, schools.school_name FROM `users` 
INNER JOIN belonging_groups USING (email)
INNER JOIN groups USING (groupID)
INNER JOIN schools USING(schoolID)
ORDER BY schools.schoolID, groups.group_nickname, users.lastname ASC, users.firstname DESC

-- Access Control via group
SELECT bg.*, groups.group_nickname, schools.school_name FROM `belonging_groups` AS bg
INNER JOIN groups ON (bg.groupID = groups.groupID)
INNER JOIN books ON (groups.courseID = books.courseID)
INNER JOIN schools ON (groups.schoolID = schools.schoolID)
WHERE bg.email = :email AND books.bookID = :bookID

