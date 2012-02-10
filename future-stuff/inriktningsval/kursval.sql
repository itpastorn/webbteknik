-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Värd: localhost
-- Skapad: 10 feb 2012 kl 01:01
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
  `klass` varchar(5) COLLATE utf8_swedish_ci NOT NULL,
  `kod` varchar(4) COLLATE utf8_swedish_ci NOT NULL,
  `inriktning` varchar(15) COLLATE utf8_swedish_ci DEFAULT NULL,
  `paket1` varchar(15) COLLATE utf8_swedish_ci DEFAULT NULL,
  `paket2` varchar(15) COLLATE utf8_swedish_ci DEFAULT NULL,
  `kommentar` varchar(500) COLLATE utf8_swedish_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8_swedish_ci DEFAULT NULL,
  `confirmed` enum('ja') COLLATE utf8_swedish_ci DEFAULT NULL,
  `year` year(4) NOT NULL COMMENT 'Vilket år valet görs',
  PRIMARY KEY (`personnummer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumpning av Data i tabell `elever`
--

INSERT INTO `elever` (`personnummer`, `fornamn`, `efternamn`, `klass`, `kod`, `inriktning`, `paket1`, `paket2`, `kommentar`, `email`, `confirmed`, `year`) VALUES
('930303-6454', ' Vandio', 'Villanueva Quizon', 'Te1A', 'apae', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('940724-3451', ' Sahin', 'Duman', 'Te1B', 'agqs', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('941130-0552', ' Oskar', 'Erkmar', 'Te1A', 'axzv', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950101-1234', 'Allan', 'Andersson', 'Te0F', 'aaaa', 'it', 'it1', 'civing', '', NULL, NULL, 2012),
('950102-9876', 'Beda', 'Bengtsson', 'Te0F', 'bbbb', 'design', 'prod1', 'sam2', 'Jag vill bli polis.', NULL, NULL, 2012),
('950104-1637', ' Oscar', 'Korshavn', 'Te1B', 'acgc', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950106-7426', ' Madeleine', 'Bolk', 'Te1A', 'anxr', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950116-0536', ' Joseph', 'Haddad', 'Te1B', 'atzk', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950121-3012', ' Igor', 'Velemir', 'Te1B', 'ajbj', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950121-8581', ' Emelie', 'Johansson', 'Te1B', 'agmd', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950123-3895', ' Markus', 'Olsson', 'Te1B', 'aiyz', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950125-0584', ' Matilda', 'Klang', 'Te1A', 'akqa', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950131-2400', ' Fanny', 'Liesén Gullmander', 'Te1B', 'aaip', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950206-1535', ' Alexander', 'Kelman', 'Te1A', 'azaq', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950206-4810', ' Jonas', 'Linell', 'Te1A', 'arrg', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950216-4818', ' Piotr', 'Ostrowski', 'Te1A', 'aehq', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950220-5058', ' Toni', 'Karam', 'Te1A', 'aqcd', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950222-1758', ' Casper', 'Johansson', 'Te1A', 'aqan', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950222-5395', ' Erik', 'Lexberg', 'Te1B', 'ambb', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950223-0338', ' Alemayehu', 'Wakjira', 'Te1B', 'asnf', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950301-6058', ' Saif', 'Abdalhe', 'Te1B', 'afhd', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950304-3953', ' Jesper', 'Isbrand', 'Te1B', 'akft', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950318-1779', ' Marcus', 'Rydell', 'Te1A', 'axjg', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950318-5234', ' Mikael', 'Pochwat', 'Te1A', 'axxv', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950320-7178', ' Isak', 'Johansson', 'Te1A', 'azjt', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950323-6854', ' Marcus', 'Hagström', 'Te1A', 'aype', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950410-8672', ' Anton', 'Vinnerholt', 'Te1A', 'argv', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950411-0199', ' Filip', 'Kalmertun', 'Te1B', 'azye', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950502-2930', ' Daniel', 'Hallberg', 'Te1A', 'acff', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950515-6613', ' Jimmy', 'Hamdoun', 'Te1B', 'agnm', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950517-6827', ' Linnea', 'Stjärnborg', 'Te1A', 'aarc', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950519-1966', ' Andrea', 'Andersson', 'Te1A', 'azqi', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950521-3679', ' Anton', 'Lundqvist', 'Te1B', 'ahci', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950530-4569', ' Linnea', 'Olsson', 'Te1A', 'abhq', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950530-5376', ' Jim', 'Lien', 'Te1A', 'ancb', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950608-2255', ' Simon', 'Svederberg', 'Te1B', 'aqbd', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950616-2974', ' Lucas', 'Lyxell', 'Te1B', 'akps', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950625-3203', ' Agnes', 'Karlsson', 'Te1B', 'ahga', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950702-5477', ' Ludvig', 'Ånnhagen', 'Te1B', 'ahin', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950703-6151', ' Patrik', 'Johansson', 'Te1B', 'akir', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950704-1722', ' Sara', 'Berglund', 'Te1B', 'aasr', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950712-5566', ' Johanna', 'Olsson', 'Te1A', 'afqm', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950714-3056', ' Christoffer', 'Johansson', 'Te1A', 'ahtg', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950715-8914', ' Anthony', 'Khalil', 'Te1B', 'aipj', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950721-6993', ' Daniel', 'Sano', 'Te1A', 'ayai', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950722-7032', ' Sabrija', 'Jasarevic', 'Te1A', 'avmc', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950725-4994', ' Shkölqim', 'Fejzi', 'Te1B', 'arqj', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950726-4175', ' Filip', 'Olsson', 'Te1B', 'aenr', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950727-0693', ' Eric', 'Kjellberg', 'Te1A', 'abtk', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950803-3892', ' Adam', 'Bohlin', 'Te1A', 'ahnq', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950813-9343', ' Hanna', 'Marke', 'Te1A', 'azrc', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950821-6091', ' Alexander', 'Karlsson', 'Te1B', 'aqgt', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950824-7013', ' Samir', 'Ljajic', 'Te1B', 'amvx', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950826-3036', ' Tim', 'Skogsberg', 'Te1B', 'aeyx', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950905-5845', ' Emma', 'Jernstig', 'Te1A', 'ajac', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950909-1133', 'Tage', 'Testare', 'Te0F', 'test', NULL, NULL, NULL, '', NULL, NULL, 2012),
('950912-9038', ' Robert Rafael', 'Romi', 'Te1A', 'aiyp', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950913-TF22', ' Rola', 'Seeman', 'Te1B', 'ajng', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950922-7154', ' Antonio', 'Kendes', 'Te1A', 'andk', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('950930-6214', ' Rasmus', 'Abrahamsson', 'Te1B', 'amki', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('951004-0018', ' Simon', 'Sörensen', 'Te1A', 'agbk', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('951005-4282', ' Beatrice', 'Asplund', 'Te1B', 'aivg', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('951007-6186', ' Cecilia', 'Lundbladh', 'Te1A', 'aiig', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('951015-7952', ' Björn', 'Isaksson', 'Te1A', 'ahym', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('951101-5423', ' Agnes', 'Johansson', 'Te1A', 'aper', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('951201-9036', ' Christoffer', 'Colliander', 'Te1A', 'agny', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('951214-7357', ' Niklas', 'Vallebrant', 'Te1B', 'atnp', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('951215-6416', ' Fredrick', 'Frendin', 'Te1A', 'arbb', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('951218-0648', ' Johanna', 'Andersson', 'Te1A', 'asiz', NULL, NULL, NULL, NULL, NULL, NULL, 2012),
('961119-3179', ' Ali', 'Abdallah', 'Te1B', 'axnk', NULL, NULL, NULL, NULL, NULL, NULL, 2012);

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
