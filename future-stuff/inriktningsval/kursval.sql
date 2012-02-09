-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Värd: localhost
-- Skapad: 09 feb 2012 kl 17:33
-- Serverversion: 5.5.19
-- PHP-version: 5.3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databas: `kursval`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `block_kurser`
--

CREATE TABLE IF NOT EXISTS `block_kurser` (
  `block_kurser_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `inr_pak_ID` varchar(15) COLLATE utf8_swedish_ci NOT NULL,
  `kurskod` varchar(15) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`block_kurser_ID`),
  UNIQUE KEY `kombination` (`inr_pak_ID`,`kurskod`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='Vilka kurser hör ihop med valt paket eller vald inriktning' AUTO_INCREMENT=32 ;

--
-- Dumpning av Data i tabell `block_kurser`
--

INSERT INTO `block_kurser` (`block_kurser_ID`, `inr_pak_ID`, `kurskod`) VALUES
(18, 'ark1', 'arkark0'),
(20, 'ark1', 'bilbil01a2'),
(19, 'ark1', 'cadcad02'),
(25, 'civing', 'fysfys02'),
(24, 'civing', 'matmat04'),
(23, 'des1', 'bilbil01a1'),
(22, 'des1', 'cadcad01'),
(21, 'des1', 'kotkos01'),
(1, 'design', 'bilbil01a1'),
(2, 'design', 'cadcad01'),
(3, 'design', 'desdes01'),
(4, 'design', 'kotkos01'),
(10, 'it', 'daodat01a'),
(9, 'it', 'prrprr01'),
(8, 'it', 'webweu01'),
(16, 'it1', 'grägrä0'),
(17, 'it1', 'webweb01'),
(26, 'it2', 'prrprr02'),
(27, 'it2', 'webweu02'),
(7, 'prdpro01', 'pruprd01s'),
(15, 'prod1', 'mekmek01'),
(14, 'prod1', 'prdpro01'),
(29, 'prod2', 'dardat01'),
(28, 'prod2', 'pruprd02s'),
(5, 'produktion', 'mekmek01'),
(6, 'produktion', 'prdpro01'),
(30, 'sam2', 'hålhåb0'),
(31, 'sam2', 'hålmij0'),
(11, 'samhall', 'arkark0'),
(13, 'samhall', 'hålhåb0'),
(12, 'samhall', 'hålmij0');

-- --------------------------------------------------------

--
-- Tabellstruktur `elever`
--

CREATE TABLE IF NOT EXISTS `elever` (
  `personnummer` varchar(11) COLLATE utf8_swedish_ci NOT NULL,
  `fornamn` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `efternamn` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `kod` varchar(4) COLLATE utf8_swedish_ci NOT NULL,
  `inriktning` varchar(15) COLLATE utf8_swedish_ci DEFAULT NULL,
  `paket1` varchar(15) COLLATE utf8_swedish_ci DEFAULT NULL,
  `paket2` varchar(15) COLLATE utf8_swedish_ci DEFAULT NULL,
  `kommentar` varchar(500) COLLATE utf8_swedish_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_swedish_ci DEFAULT NULL,
  `confirmed` enum('ja') COLLATE utf8_swedish_ci DEFAULT NULL,
  `year` year(4) NOT NULL COMMENT 'Vilket år valet görs',
  PRIMARY KEY (`personnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumpning av Data i tabell `elever`
--

INSERT INTO `elever` (`personnummer`, `fornamn`, `efternamn`, `kod`, `inriktning`, `paket1`, `paket2`, `kommentar`, `email`, `confirmed`, `year`) VALUES
('950101-1234', 'Allan', 'Andersson', 'aaaa', 'it', 'it1', 'civing', '', NULL, NULL, 2012),
('950102-9876', 'Beda', 'Bengtsson', 'bbbb', 'design', 'prod1', 'sam2', 'Jag vill bli polis.', NULL, NULL, 2012);

-- --------------------------------------------------------

--
-- Tabellstruktur `inriktning_paket`
--

CREATE TABLE IF NOT EXISTS `inriktning_paket` (
  `inr_pak_ID` varchar(15) COLLATE utf8_swedish_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `passar4` varchar(15) COLLATE utf8_swedish_ci DEFAULT NULL COMMENT 'Te4 kombination',
  `req` varchar(15) COLLATE utf8_swedish_ci DEFAULT NULL COMMENT 'Annat paket eller inriktning som är förkrav',
  `typ` varchar(15) COLLATE utf8_swedish_ci NOT NULL,
  `json_modul` enum('inriktningar','paket1','paket2') COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`inr_pak_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumpning av Data i tabell `inriktning_paket`
--

INSERT INTO `inriktning_paket` (`inr_pak_ID`, `name`, `passar4`, `req`, `typ`, `json_modul`) VALUES
('ark1', NULL, NULL, 'design', 'design', 'paket1'),
('civing', NULL, NULL, NULL, 'civing', 'paket2'),
('des1', NULL, NULL, NULL, 'design', 'paket1'),
('design', 'Design och produktutveckling', 'prod1', '', 'design', 'inriktningar'),
('it', 'IT och media', 'it1', '', 'it', 'inriktningar'),
('it1', NULL, NULL, 'it', 'it', 'paket1'),
('it2', NULL, NULL, 'it', 'it', 'paket2'),
('prod1', NULL, NULL, NULL, 'produktion', 'paket1'),
('prod2', NULL, NULL, 'produktion', 'produktion', 'paket2'),
('produktion', 'Produktionsteknik', 'des1', '', 'produktion', 'inriktningar'),
('sam2', NULL, NULL, NULL, 'samhall', 'paket2'),
('samhall', 'Samhällsbyggande och miljö', NULL, '', 'samhall', 'inriktningar');

-- --------------------------------------------------------

--
-- Tabellstruktur `kurser`
--

CREATE TABLE IF NOT EXISTS `kurser` (
  `kurskod` varchar(10) COLLATE utf8_swedish_ci NOT NULL,
  `kursnamn` varchar(150) COLLATE utf8_swedish_ci NOT NULL,
  `poang` tinyint(4) NOT NULL,
  PRIMARY KEY (`kurskod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumpning av Data i tabell `kurser`
--

INSERT INTO `kurser` (`kurskod`, `kursnamn`, `poang`) VALUES
('arkark0', 'Arkitektur hus', 100),
('bilbil01a1', 'Bild och form 1a1', 50),
('bilbil01a2', 'Bild och form 1a2', 50),
('cadcad01', 'CAD 1', 50),
('cadcad02', 'CAD 2', 50),
('daodat01a', 'Datorteknik 1a', 100),
('dardat01', 'Datorstyrd produktion 1', 100),
('desdes01', 'Design 1', 100),
('fysfys02', 'Fysik 2', 100),
('grägrä0', 'Gränssnittsdesign', 100),
('hålhåb0', 'Hållbart samhällsbyggande', 100),
('hålmij0', 'Miljö och energikunskap', 100),
('kotkos01', 'Konstruktion 1', 100),
('matmat04', 'Matematik 4', 100),
('mekmek01', 'Mekatronik 1', 100),
('prdpro01', 'Produktionskunskap 1', 100),
('prrprr01', 'Programmering 1', 100),
('prrprr02', 'Programmering 2', 100),
('pruprd01s', 'Produktionsutrustning 1', 100),
('pruprd02s', 'Produktionsutrustning 2', 100),
('webweb01', 'Webbserverprogrammering 1', 100),
('webweu01', 'Webbutveckling 1', 100),
('webweu02', 'Webbutveckling 2', 100);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
