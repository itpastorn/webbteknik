-- phpMyAdmin SQL Dump
-- version 3.5.2
-- http://www.phpmyadmin.net
--
-- Värd: localhost
-- Skapad: 13 aug 2012 kl 19:48
-- Serverversion: 5.5.25a
-- PHP-version: 5.4.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databas: `webbtek_webbtek`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `flashcards`
--

CREATE TABLE IF NOT EXISTS `flashcards` (
  `flashcardsID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `term` varchar(15) NOT NULL,
  `short` varchar(50) NOT NULL,
  `long` varchar(160) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `setID` varchar(50) NOT NULL,
  PRIMARY KEY (`flashcardsID`),
  KEY `term` (`term`,`setID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

--
-- Dumpning av Data i tabell `flashcards`
--

INSERT INTO `flashcards` (`flashcardsID`, `term`, `short`, `long`, `setID`) VALUES
(1, 'GPU', 'Graphics Processing Unit', 'Grafikprocessor som gör att datorn kan rita komplexa 2D och 3D mönster, fonter (text) och genomskinliga effekter mycket snabbt.', 'test'),
(2, 'RAM', 'Random Access Memory', 'Datorns arbetsminne/ primärminne. Mycket snabb. Flyktigt dvs när strömmen bryts tappas informationen. 1-16 GB på moderna datorer.', 'test'),
(3, 'CPU', 'Central Processing Unit', 'Processorn. Utför beräkningar och kontrollerar dataflöden. Datorns <q>motor.</q>', 'test'),
(4, 'USB', 'Universal Serial Bus', 'GrÃ¤nssnitt (kontakt) för att ansluta kringutrustning som mus, tangentbord, lagringsenheter, skrivare och mobiltelefoner.', 'test'),
(5, 'SSD', 'Solid State Drive', 'Flashminnesbaserad hårddisk, som har lägre latens (väntetid) än magnetiska diskar och drar mindre ström än vad de gör.', 'test'),
(6, 'GUI', 'Graphical User Interface', 'Användarmiljö som bygger på muspekare, ikoner, fönster och menyer.', 'test'),
(7, 'CLI', 'Command Line Interface', 'Användarmiljö som bygger på att kommandon skrivs i en texbaserad konsoll/terminal.', 'test'),
(8, 'HDMI', 'High-Definition Multimedia Interface', 'Digital anslutning för bild och ljud. Vanlig på TV, DVD och Blue Ray. Liknar DisplayPort.', 'test'),
(9, 'DisplayPort', 'Digital anslutning för bild och ljud', 'Liknar HDMI, men är aningen bättre ur teknisk synvinkel. Vanlig på något dyrare PC.', 'test'),
(10, 'Binära tal', 'Tal som bara består av bara nollor och ettor', 'Talsystem som bygger på basen 2. Platsvärdet dubblas för varje steg åt vänster. 01100101', 'dao1'),
(11, 'bit', 'Precis en etta eller en nolla', 'Ett binärt tal. Den minsta dataenheten. Skrivs med litet b.', 'dao1'),
(12, 'Byte', '8 bits (nollor/ettor)', 'Användsom mått på lagrings&shy;kapacitet och RAM. Skrivs med stort B.', 'dao1'),
(13, 'Hårdvara', 'Datorer och andra enheter.', 'Elektriska enheter som lagrar, behandlar och transporterar data.', 'dao1'),
(14, 'Mjukvara', 'BIOS, operativsystem och program', 'Det som skapas med kod, när man programmerar, är mjukvara.', 'dao1'),
(15, 'Operativsystem', 'Exempel GNU/Linux, Windows, Mac OS X', 'Den mjukvara som styr hårdvaran och tillämpningsprogrammen.', 'dao1'),
(16, 'Komponent', 'En del av en dator', 'Exempel: CPU, arbetsminne, moderkort, hårddisk, nätaggregat och chassi.', 'dao1'),
(17, 'Arbetsminne', 'Snabbt minne som används vid exekvering av program', 'RAM. Flyktigt d v s tappar info när strömmen bryts. 1-16 GB på moderna datorer.', 'dao1'),
(18, 'ESD', 'ElectroStatic Discharge', 'Urladdning av statisk elekticitet, som kan skada komponenter.', 'dao2'),
(19, 'ESD-armband', 'Armband som hindrar ESD', 'Avleder statisk elektricitet från händerna. Ska förbindas med elektrisk jord.', 'dao2'),
(20, 'ESD-matta', 'ESD säker yta att lägga komponenter på', 'Placera RAM, CPU, etc på en sådan när du behöver lägga dem på ett bord.', 'dao2'),
(21, 'ESD-påse', 'Elektriskt avskärmad plastpåse som skyddar mot ESD', 'Används att förvara komponenter inuti. Att lägga dem ovanpå förvärrar risken för ESD.', 'dao2'),
(22, 'Klockfrekvens', 'Mäts i Hz (Hertz) dvs kHz, MHz, GHz.', 'En elektrisk puls som styr farten på komponenter och bussar.', 'dao3'),
(23, 'CPU cache', 'Ultrasnabbt minne för det processorn använder mest', 'Görs med SRAM-teknik. Oftast inbyggt i processorn. Cache gör att CPU:n kan slippa vänta på RAM', 'dao3'),
(24, 'Multi-core', 'Processorer med mer än en kärna', 'Som om man hade flera separata processorer, fast de byggts ihop på samma chip.', 'dao3'),
(25, 'Multi-thread', 'CPU-kärna som kan göra mer än en sak samtidigt', 'Effektiviserad behandling av parallell körning av mjukvarutrådar.', 'dao3'),
(26, '32 och 64 bit', 'Hur många bits som kan bearbetas parallellt', 'Påverkar främst hur mycket RAM som kan användas. 32 bit klarar max 4GB.', 'dao3'),
(27, 'Intel', 'Världens största tillverkare av mikrochip', 'Företaget som bl a gör Pentium (x86) och core i3, i5, i7.', 'dao3'),
(28, 'AMD', 'Advanced Micro Devices', 'Gör CPU:er som liknar Intels, bl a Athlon och Phenom. Äger ATI som gör GPU:er.', 'dao3'),
(29, 'ARM', 'CPU arkitektur för telefoner och surfplattor', 'Används av nästan alla Android-enheter, iPhone, iPad, m.fl.', 'dao3'),
(30, 'Primärminne', 'Arbetsminne, RAM', 'Gjort med DRAM-teknik. Tappar data när strömmen bryts.', 'dao6'),
(31, 'Gibibyte', '1024 x 1024 x 1024 Byte, GB', 'Det som i dagligt tal kallas "Gigabyte" för RAM.', 'dao6'),
(32, 'DIMM', 'Dual Inline Memory Module', 'Formfaktor för RAM på stationära datorer. 64-bit bussbredd.', 'dao6'),
(33, 'SO-DIMM', 'Small Outline DIMM', 'Formfaktor för RAM på bärbara datorer. Smalare än DIMM. 64-bit bussbredd.', 'dao6'),
(34, 'Dual Channel', 'Parallell åtkomst av DIMM-moduler', 'Ökar bussbredden till 128 bit. Kräver dubbelmontage och hårdvarustöd.', 'dao6'),
(35, 'CAS latency', 'Hur många klockcykler det dröjer innan RAM svarar', 'När CPU:n ber RAM om data, så kan det sällan svara utan ett par klockcyklers fördröjning.', 'dao6');

-- --------------------------------------------------------

--
-- Tabellstruktur `flashcardsets`
--

CREATE TABLE IF NOT EXISTS `flashcardsets` (
  `setID` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `setname` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `book` varchar(5) COLLATE utf8_swedish_ci DEFAULT NULL,
  `section` varchar(12) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`setID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='Flashcards sets';

--
-- Dumpning av Data i tabell `flashcardsets`
--

INSERT INTO `flashcardsets` (`setID`, `setname`, `book`, `section`) VALUES
('wu1-1', 'Flashcards till kapitel 1', 'wu1', '1');

-- --------------------------------------------------------

--
-- Tabellstruktur `joblist`
--

CREATE TABLE IF NOT EXISTS `joblist` (
  `joblistID` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `what_to_do` varchar(200) COLLATE utf8_swedish_ci NOT NULL,
  `where_to_do_it` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `fast_track_order` smallint(5) unsigned DEFAULT NULL,
  `slow_track_order` smallint(6) NOT NULL,
  `bonusjob` enum('no','yes') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'no',
  `chapter` int(11) NOT NULL COMMENT 'What book chapter it relates to',
  PRIMARY KEY (`joblistID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='Suggested working order' AUTO_INCREMENT=32 ;

--
-- Dumpning av Data i tabell `joblist`
--

INSERT INTO `joblist` (`joblistID`, `what_to_do`, `where_to_do_it`, `fast_track_order`, `slow_track_order`, `bonusjob`, `chapter`) VALUES
(1, 'Uppgift 1A', 'Arbetsboken Webbutveckling 1', 4, 4, 'no', 1),
(2, 'Avsnitt 1.1', 'Arbetsboken Webbutveckling 1', 5, 5, 'no', 1),
(3, 'Läs till och med avsnitt 1.1.1', 'Läroboken Webbutveckling 1', 6, 6, 'no', 1),
(4, 'Uppgift 1B', 'Arbetsboken Webbutveckling 1', 7, 7, 'no', 1),
(5, 'Avsnitt 1.2', 'Arbetsboken Webbutveckling 1', 8, 8, 'no', 1),
(6, 'Läs avsnitt 1.2 och 1.3', 'Läroboken Webbutveckling 1', 10, 10, 'no', 1),
(7, 'Uppgift 1C', 'Arbetsboken Webbutveckling 1', 11, 11, 'no', 1),
(8, 'video', 'kap-1-a-1', 1, 1, 'no', 1),
(9, 'video', 'kap-1-a-2', 2, 2, 'no', 1),
(10, 'video', 'kap-1-a-3', 3, 3, 'no', 1),
(11, 'video', 'kap-1-a-4', 9, 9, 'no', 1),
(12, 'video', 'thimble', NULL, 12, 'no', 1),
(13, 'Läs avsnitt 1.4', 'Läroboken Webbutveckling 1', NULL, 13, 'no', 1),
(14, 'Uppgift 1D', 'Arbetsboken Webbutveckling 1', NULL, 14, 'no', 1),
(15, 'Avsnitt 1.3', 'Arbetsboken Webbutveckling 1', NULL, 15, 'no', 1),
(16, 'Uppgift 1E', 'Arbetsboken Webbutveckling 1', NULL, 16, 'yes', 1),
(17, 'Avsnitt 1.4', 'Arbetsboken Webbutveckling 1', NULL, 17, 'no', 1),
(18, 'Läs avsnitt 1.5', 'online', NULL, 21, 'yes', 1),
(19, 'video', 'kap-1-149', NULL, 20, 'no', 1),
(20, 'video', 'kap-1-150', NULL, 22, 'yes', 1),
(21, 'Läs avsnitt 1.6', 'Läroboken Webbutveckling 1', NULL, 23, 'no', 1),
(22, '1.4.2', 'online', NULL, 18, 'yes', 1),
(23, '1.4.3', 'online', NULL, 19, 'yes', 1),
(24, 'Läs avsnitt 1.7 och 1.8', 'Läroboken Webbutveckling 1', 12, 24, 'no', 1),
(25, 'Avsnitt 1.5', 'Arbetsboken Webbutveckling 1', 13, 25, 'no', 1),
(26, 'Avsnitt 1.9', 'Läroboken Webbutveckling 1', 14, 26, 'no', 1),
(27, '1.9.3', 'online', NULL, 27, 'yes', 1),
(28, 'Läs avsnitt 1.10', 'Arbetsboken Webbutveckling 1', 15, 26, 'no', 1),
(29, 'Uppgift 1F', 'Arbetsboken Webbutveckling 1', 16, 27, 'no', 1),
(30, 'video', 'kap-1-10', 17, 29, 'no', 1),
(31, 'Avsnitt 1.6', 'Arbetsboken Webbutveckling 1', 18, 30, 'no', 1);

-- --------------------------------------------------------

--
-- Tabellstruktur `privilege_questions`
--

CREATE TABLE IF NOT EXISTS `privilege_questions` (
  `pqID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(150) COLLATE utf8_swedish_ci NOT NULL,
  `answer` varchar(15) COLLATE utf8_swedish_ci NOT NULL,
  `privileges` tinyint(3) unsigned NOT NULL COMMENT 'Vilket privilegium som frågan kollar',
  `times_used` int(11) NOT NULL COMMENT 'Antal gånger en fråga använts',
  PRIMARY KEY (`pqID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=3 ;

--
-- Dumpning av Data i tabell `privilege_questions`
--

INSERT INTO `privilege_questions` (`pqID`, `question`, `answer`, `privileges`, `times_used`) VALUES
(1, 'I första stycket i avsnitt 1 i läroboken, vad är det första markerade ordet?', 'textredigerare', 7, 0),
(2, 'I första stycket i avsnitt 1.2 i läroboken, vad är det första markerade ordet?', 'operativsystem', 7, 0);

-- --------------------------------------------------------

--
-- Tabellstruktur `userprogress`
--

CREATE TABLE IF NOT EXISTS `userprogress` (
  `upID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) COLLATE utf8_swedish_ci NOT NULL COMMENT 'user id',
  `joblistID` mediumint(9) NOT NULL COMMENT 'foreign key',
  `tablename` varchar(20) COLLATE utf8_swedish_ci NOT NULL COMMENT 'What table has info about the resource',
  `resourceID` varchar(20) COLLATE utf8_swedish_ci NOT NULL,
  `progressdata` varchar(300) COLLATE utf8_swedish_ci NOT NULL COMMENT 'JSON object describing progress',
  `percentage_complete` tinyint(3) unsigned DEFAULT NULL,
  `status` enum('begun','skipped','finished') COLLATE utf8_swedish_ci DEFAULT 'begun',
  `approved` enum('no','yes') COLLATE utf8_swedish_ci DEFAULT NULL COMMENT 'Set by teacher',
  PRIMARY KEY (`upID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='Keep track of what the user has done' AUTO_INCREMENT=7 ;

--
-- Dumpning av Data i tabell `userprogress`
--

INSERT INTO `userprogress` (`upID`, `email`, `joblistID`, `tablename`, `resourceID`, `progressdata`, `percentage_complete`, `status`, `approved`) VALUES
(2, 'gunther@keryx.se', 8, 'videos', 'kap-1-a-1', '{"firstStop":39.8,"viewTotal":39.8,"stops":[{"start":0,"end":39.8}]}', NULL, 'skipped', NULL),
(3, 'gunther@keryx.se', 9, 'videos', 'kap-1-a-2', '{"firstStop":16.733015,"viewTotal":16.733015,"stops":[{"start":0,"end":16.733015}]}', 2, 'finished', NULL),
(5, 'gunther@keryx.se', 11, 'videos', 'kap-1-a-4', '{"firstStop":19.2,"viewTotal":19.2,"stops":[{"start":0,"end":19.2}]}', 3, 'begun', NULL),
(6, 'gunther@keryx.se', 10, 'videos', 'kap-1-a-3', '{"firstStop":38.533129,"viewTotal":38.533129,"stops":[{"start":0,"end":38.533129}]}', 4, 'begun', NULL);

-- --------------------------------------------------------

--
-- Tabellstruktur `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `email` varchar(150) COLLATE utf8_swedish_ci NOT NULL,
  `firstname` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `lastname` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `privileges` tinyint(4) DEFAULT '1',
  `user_since` datetime DEFAULT NULL COMMENT 'När användaren registrerades',
  `privlevel_since` datetime DEFAULT NULL COMMENT 'När behörighetsnivån bekräftades',
  `suspended_until` date DEFAULT NULL COMMENT 'Kontot avstängt till och med detta datum',
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumpning av Data i tabell `users`
--

INSERT INTO `users` (`email`, `firstname`, `lastname`, `privileges`, `user_since`, `privlevel_since`, `suspended_until`) VALUES
('gunther@keryx.se', 'Lars', 'Gunther', 127, '2012-06-17 11:53:45', '2012-06-17 14:38:05', NULL),
('info@keryx.se', 'Lars info-test', 'Gunther', 7, '2012-06-17 16:48:01', '2012-06-17 20:06:18', NULL),
('itpastorn@gmail.com', NULL, NULL, 7, '2012-07-09 22:03:54', '2012-07-09 22:14:51', NULL);

-- --------------------------------------------------------

--
-- Tabellstruktur `videos`
--

CREATE TABLE IF NOT EXISTS `videos` (
  `videoname` varchar(20) COLLATE utf8_swedish_ci NOT NULL,
  `title` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `book` varchar(5) COLLATE utf8_swedish_ci NOT NULL,
  `section` varchar(12) COLLATE utf8_swedish_ci NOT NULL,
  `tags` varchar(150) COLLATE utf8_swedish_ci NOT NULL,
  `order` mediumint(8) unsigned DEFAULT NULL COMMENT 'Suggested viewing order',
  PRIMARY KEY (`videoname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumpning av Data i tabell `videos`
--

INSERT INTO `videos` (`videoname`, `title`, `book`, `section`, `tags`, `order`) VALUES
('kap-1-a-1', 'Avsnitt 1.0 och 1.1: Ett enkelt HTML-dokument, del 1', 'wu1', '1.0', 'html', 1),
('kap-1-a-2', 'Avsnitt 1.0 och 1.1: Ett enkelt HTML-dokument, del 2', 'wu1', '1.0', 'html,css', 2),
('kap-1-a-3', 'Avsnitt 1.0 och 1.1: Värdet av doctype samt inspektera element i Firefox', 'wu1', '1.0', 'html, doctype, verktyg', 3),
('kap-1-a-4', 'Avsnitt 1.3: Validering', 'wu1', '1.3', 'verktyg, validering', 5),
('thimble', 'Bonusvideo: Mozilla Thimble', 'wu1', '', 'verktyg', 4);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
