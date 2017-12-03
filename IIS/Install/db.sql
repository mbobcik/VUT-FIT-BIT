-- phpMyAdmin SQL Dump
-- version 4.4.4
-- http://www.phpmyadmin.net
--
-- Počítač: innodb.endora.cz:3306
-- Vytvořeno: Ned 03. pro 2017, 18:53
-- Verze serveru: 5.6.28-76.1
-- Verze PHP: 5.4.45

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `iis2017`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `ADRESA`
--

CREATE TABLE IF NOT EXISTS `ADRESA` (
  `ID` int(10) unsigned NOT NULL,
  `ULICE` varchar(256) DEFAULT NULL,
  `MESTO` varchar(256) NOT NULL,
  `PSC` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `HISTORIE`
--

CREATE TABLE IF NOT EXISTS `HISTORIE` (
  `ID` int(10) unsigned NOT NULL,
  `UZIVATELID` int(10) unsigned NOT NULL,
  `CINNOST` text NOT NULL,
  `DATUMACAS` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `PRESTUPEK`
--

CREATE TABLE IF NOT EXISTS `PRESTUPEK` (
  `ID` int(10) unsigned NOT NULL,
  `DATUMACAS` datetime NOT NULL,
  `MISTO` text NOT NULL,
  `EVIDENCNICISLO` int(11) NOT NULL,
  `PRESTUPEKTYPID` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `PRESTUPEKTYP`
--

CREATE TABLE IF NOT EXISTS `PRESTUPEKTYP` (
  `ID` int(10) unsigned NOT NULL,
  `NAZEV` varchar(256) NOT NULL,
  `POPIS` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `PRESTUPEKTYP`
--

INSERT INTO `PRESTUPEKTYP` (`ID`, `NAZEV`, `POPIS`) VALUES
(1, 'Překročena rychlost v obci I', 'Překročení maximální povolené rychlosti v obci o více než 5 km/h.'),
(2, 'Překročena rychlost v obci II', 'Překročení maximální povolené rychlosti v obci o více než 10 km/h.');

-- --------------------------------------------------------

--
-- Struktura tabulky `PRUKAZ`
--

CREATE TABLE IF NOT EXISTS `PRUKAZ` (
  `ID` int(10) unsigned NOT NULL,
  `SERIOVECISLO` int(10) NOT NULL,
  `PLATNOSTOD` date NOT NULL,
  `PLATNOSTDO` date DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `PRUKAZSKUPINY`
--

CREATE TABLE IF NOT EXISTS `PRUKAZSKUPINY` (
  `ID` int(10) unsigned NOT NULL,
  `PRUKAZID` int(10) unsigned NOT NULL,
  `SKUPINAID` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `RIDIC`
--

CREATE TABLE IF NOT EXISTS `RIDIC` (
  `ID` int(10) unsigned NOT NULL,
  `PRIJMENI` varchar(128) NOT NULL,
  `JMENO` varchar(128) NOT NULL,
  `RODNECISLO` bigint(11) NOT NULL,
  `DATUMNAROZENI` date NOT NULL,
  `ADRESAID` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `RIDICPRUKAZ`
--

CREATE TABLE IF NOT EXISTS `RIDICPRUKAZ` (
  `ID` int(10) unsigned NOT NULL,
  `RIDICID` int(10) unsigned NOT NULL,
  `PRUKAZID` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `RIDICVOZIDLO`
--

CREATE TABLE IF NOT EXISTS `RIDICVOZIDLO` (
  `ID` int(10) unsigned NOT NULL,
  `RIDICID` int(10) unsigned NOT NULL,
  `VOZIDLOID` int(10) unsigned NOT NULL,
  `DATUMPREPSANI` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `RIDICVOZIDLOPRESTUPEK`
--

CREATE TABLE IF NOT EXISTS `RIDICVOZIDLOPRESTUPEK` (
  `ID` int(10) unsigned NOT NULL,
  `RIDICID` int(10) unsigned NOT NULL,
  `VOZIDLOID` int(10) unsigned NOT NULL,
  `PRESTUPEKID` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `ROLE`
--

CREATE TABLE IF NOT EXISTS `ROLE` (
  `ID` int(10) unsigned NOT NULL,
  `NAZEVROLE` varchar(128) NOT NULL,
  `ZOBRAZENIZAZNAMU` tinyint(1) NOT NULL,
  `UPRAVAZAZNAMU` tinyint(1) NOT NULL,
  `PRIDANIZAZNAMU` tinyint(1) NOT NULL,
  `SMAZANIZAZNAMU` tinyint(1) NOT NULL,
  `PRIDANIUZIVATELE` tinyint(1) NOT NULL,
  `ODEBRANIUZIVATELE` tinyint(1) NOT NULL,
  `ZMENAPRAVUZIVATELE` tinyint(1) NOT NULL,
  `ZMENAHESLAUZIVATELE` tinyint(1) NOT NULL,
  `ZMENAVLASTNIHOHESLA` tinyint(1) NOT NULL,
  `ZOBRAZENIHISTORIE` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `ROLE`
--

INSERT INTO `ROLE` (`ID`, `NAZEVROLE`, `ZOBRAZENIZAZNAMU`, `UPRAVAZAZNAMU`, `PRIDANIZAZNAMU`, `SMAZANIZAZNAMU`, `PRIDANIUZIVATELE`, `ODEBRANIUZIVATELE`, `ZMENAPRAVUZIVATELE`, `ZMENAHESLAUZIVATELE`, `ZMENAVLASTNIHOHESLA`, `ZOBRAZENIHISTORIE`) VALUES
(1, 'user', 1, 0, 0, 0, 0, 0, 0, 0, 1, 0),
(2, 'editor', 1, 1, 1, 0, 0, 0, 0, 0, 1, 0),
(3, 'admin', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `SKUPINA`
--

CREATE TABLE IF NOT EXISTS `SKUPINA` (
  `ID` int(10) unsigned NOT NULL,
  `OZNACENI` varchar(128) NOT NULL,
  `NAZEV` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `SKUPINA`
--

INSERT INTO `SKUPINA` (`ID`, `OZNACENI`, `NAZEV`) VALUES
(1, 'AM', 'AM'),
(2, 'A1', 'A1'),
(3, 'A2', 'A2'),
(4, 'A', 'A'),
(5, 'B1', 'B1'),
(6, 'B', 'B'),
(7, 'C1', 'C1'),
(8, 'C', 'C'),
(9, 'D1', 'D1'),
(10, 'D', 'D'),
(11, 'BE', 'BE'),
(12, 'C1E', 'C1E'),
(13, 'CE', 'CE'),
(14, 'D1E', 'D1E'),
(15, 'DE', 'DE'),
(16, 'T', 'T');

-- --------------------------------------------------------

--
-- Struktura tabulky `TECHNICKA`
--

CREATE TABLE IF NOT EXISTS `TECHNICKA` (
  `ID` int(10) unsigned NOT NULL,
  `EVIDENCNICISLO` int(10) unsigned NOT NULL,
  `PLATNOSTOD` date NOT NULL,
  `PLATNOSTDO` date DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `UZIVATEL`
--

CREATE TABLE IF NOT EXISTS `UZIVATEL` (
  `ID` int(10) unsigned NOT NULL,
  `JMENO` varchar(128) NOT NULL,
  `HESLO` varchar(128) NOT NULL,
  `ROLEID` int(10) unsigned NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `UZIVATEL`
--

INSERT INTO `UZIVATEL` (`ID`, `JMENO`, `HESLO`, `ROLEID`) VALUES
(1, 'admin', 'f6fdffe48c908deb0f4c3bd36c032e72', 3),
(3, 'user', '5cc32e366c87c4cb49e4309b75f57d64', 1),
(5, 'editor', '7ddb9545d033542c9b21b7b280e3a4d1', 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `VOZIDLO`
--

CREATE TABLE IF NOT EXISTS `VOZIDLO` (
  `ID` int(10) unsigned NOT NULL,
  `SPZ` varchar(128) NOT NULL,
  `BARVA` varchar(128) NOT NULL,
  `ZNACKA` varchar(128) NOT NULL,
  `MODEL` varchar(128) NOT NULL,
  `ROKVYROBY` int(11) NOT NULL,
  `SKUPINAID` int(10) unsigned NOT NULL,
  `UKRADENO` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `VOZIDLOTECHNICKA`
--

CREATE TABLE IF NOT EXISTS `VOZIDLOTECHNICKA` (
  `ID` int(10) unsigned NOT NULL,
  `VOZIDLOID` int(10) unsigned NOT NULL,
  `TECHNICKAID` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `ADRESA`
--
ALTER TABLE `ADRESA`
  ADD PRIMARY KEY (`ID`);

--
-- Klíče pro tabulku `HISTORIE`
--
ALTER TABLE `HISTORIE`
  ADD PRIMARY KEY (`ID`);

--
-- Klíče pro tabulku `PRESTUPEK`
--
ALTER TABLE `PRESTUPEK`
  ADD PRIMARY KEY (`ID`);

--
-- Klíče pro tabulku `PRESTUPEKTYP`
--
ALTER TABLE `PRESTUPEKTYP`
  ADD PRIMARY KEY (`ID`);

--
-- Klíče pro tabulku `PRUKAZ`
--
ALTER TABLE `PRUKAZ`
  ADD PRIMARY KEY (`ID`);

--
-- Klíče pro tabulku `PRUKAZSKUPINY`
--
ALTER TABLE `PRUKAZSKUPINY`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `PRUKAZID` (`PRUKAZID`),
  ADD KEY `SKUPINAID` (`SKUPINAID`);

--
-- Klíče pro tabulku `RIDIC`
--
ALTER TABLE `RIDIC`
  ADD PRIMARY KEY (`ID`);

--
-- Klíče pro tabulku `RIDICPRUKAZ`
--
ALTER TABLE `RIDICPRUKAZ`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `RIDICID` (`RIDICID`),
  ADD KEY `PRUKAZID` (`PRUKAZID`);

--
-- Klíče pro tabulku `RIDICVOZIDLO`
--
ALTER TABLE `RIDICVOZIDLO`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `VOZIDLOID` (`VOZIDLOID`);

--
-- Klíče pro tabulku `RIDICVOZIDLOPRESTUPEK`
--
ALTER TABLE `RIDICVOZIDLOPRESTUPEK`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `RIDICID` (`RIDICID`),
  ADD KEY `VOZIDLOID` (`VOZIDLOID`),
  ADD KEY `PRESTUPEKID` (`PRESTUPEKID`);

--
-- Klíče pro tabulku `ROLE`
--
ALTER TABLE `ROLE`
  ADD PRIMARY KEY (`ID`);

--
-- Klíče pro tabulku `SKUPINA`
--
ALTER TABLE `SKUPINA`
  ADD PRIMARY KEY (`ID`);

--
-- Klíče pro tabulku `TECHNICKA`
--
ALTER TABLE `TECHNICKA`
  ADD PRIMARY KEY (`ID`);

--
-- Klíče pro tabulku `UZIVATEL`
--
ALTER TABLE `UZIVATEL`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ROLEID` (`ROLEID`);

--
-- Klíče pro tabulku `VOZIDLO`
--
ALTER TABLE `VOZIDLO`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `SKUPINAID` (`SKUPINAID`);

--
-- Klíče pro tabulku `VOZIDLOTECHNICKA`
--
ALTER TABLE `VOZIDLOTECHNICKA`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `VOZIDLOID` (`VOZIDLOID`),
  ADD KEY `TECHNICKAID` (`TECHNICKAID`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `ADRESA`
--
ALTER TABLE `ADRESA`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT pro tabulku `HISTORIE`
--
ALTER TABLE `HISTORIE`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT pro tabulku `PRESTUPEK`
--
ALTER TABLE `PRESTUPEK`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pro tabulku `PRESTUPEKTYP`
--
ALTER TABLE `PRESTUPEKTYP`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pro tabulku `PRUKAZ`
--
ALTER TABLE `PRUKAZ`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT pro tabulku `PRUKAZSKUPINY`
--
ALTER TABLE `PRUKAZSKUPINY`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=88;
--
-- AUTO_INCREMENT pro tabulku `RIDIC`
--
ALTER TABLE `RIDIC`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT pro tabulku `RIDICPRUKAZ`
--
ALTER TABLE `RIDICPRUKAZ`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT pro tabulku `RIDICVOZIDLO`
--
ALTER TABLE `RIDICVOZIDLO`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pro tabulku `RIDICVOZIDLOPRESTUPEK`
--
ALTER TABLE `RIDICVOZIDLOPRESTUPEK`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT pro tabulku `ROLE`
--
ALTER TABLE `ROLE`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pro tabulku `SKUPINA`
--
ALTER TABLE `SKUPINA`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT pro tabulku `TECHNICKA`
--
ALTER TABLE `TECHNICKA`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT pro tabulku `UZIVATEL`
--
ALTER TABLE `UZIVATEL`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT pro tabulku `VOZIDLO`
--
ALTER TABLE `VOZIDLO`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT pro tabulku `VOZIDLOTECHNICKA`
--
ALTER TABLE `VOZIDLOTECHNICKA`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `PRUKAZSKUPINY`
--
ALTER TABLE `PRUKAZSKUPINY`
  ADD CONSTRAINT `PRUKAZSKUPINY_ibfk_1` FOREIGN KEY (`PRUKAZID`) REFERENCES `PRUKAZ` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `PRUKAZSKUPINY_ibfk_2` FOREIGN KEY (`SKUPINAID`) REFERENCES `SKUPINA` (`ID`);

--
-- Omezení pro tabulku `RIDICPRUKAZ`
--
ALTER TABLE `RIDICPRUKAZ`
  ADD CONSTRAINT `RIDICPRUKAZ_ibfk_1` FOREIGN KEY (`RIDICID`) REFERENCES `RIDIC` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `RIDICPRUKAZ_ibfk_2` FOREIGN KEY (`PRUKAZID`) REFERENCES `PRUKAZ` (`ID`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `RIDICVOZIDLO`
--
ALTER TABLE `RIDICVOZIDLO`
  ADD CONSTRAINT `RIDICVOZIDLO_ibfk_1` FOREIGN KEY (`VOZIDLOID`) REFERENCES `VOZIDLO` (`ID`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `RIDICVOZIDLOPRESTUPEK`
--
ALTER TABLE `RIDICVOZIDLOPRESTUPEK`
  ADD CONSTRAINT `RIDICVOZIDLOPRESTUPEK_ibfk_1` FOREIGN KEY (`RIDICID`) REFERENCES `RIDIC` (`ID`),
  ADD CONSTRAINT `RIDICVOZIDLOPRESTUPEK_ibfk_2` FOREIGN KEY (`VOZIDLOID`) REFERENCES `VOZIDLO` (`ID`),
  ADD CONSTRAINT `RIDICVOZIDLOPRESTUPEK_ibfk_3` FOREIGN KEY (`PRESTUPEKID`) REFERENCES `PRESTUPEK` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `UZIVATEL`
--
ALTER TABLE `UZIVATEL`
  ADD CONSTRAINT `UZIVATEL_ibfk_1` FOREIGN KEY (`ROLEID`) REFERENCES `ROLE` (`ID`);

--
-- Omezení pro tabulku `VOZIDLO`
--
ALTER TABLE `VOZIDLO`
  ADD CONSTRAINT `VOZIDLO_ibfk_1` FOREIGN KEY (`SKUPINAID`) REFERENCES `SKUPINA` (`ID`);

--
-- Omezení pro tabulku `VOZIDLOTECHNICKA`
--
ALTER TABLE `VOZIDLOTECHNICKA`
  ADD CONSTRAINT `VOZIDLOTECHNICKA_ibfk_1` FOREIGN KEY (`VOZIDLOID`) REFERENCES `VOZIDLO` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `VOZIDLOTECHNICKA_ibfk_2` FOREIGN KEY (`TECHNICKAID`) REFERENCES `TECHNICKA` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

