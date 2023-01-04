-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2022. Okt 14. 17:07
-- Kiszolgáló verziója: 10.4.22-MariaDB
-- PHP verzió: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `cdnews`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `cdn_bejegyzesek`
--

CREATE TABLE if not exists `cdn_bejegyzesek` (
  `id` int(11) NOT NULL,
  `user_fk` varchar(60) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `datum` date NOT NULL DEFAULT current_timestamp(),
  `cim` varchar(60) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `slug` varchar(60) COLLATE utf8_hungarian_ci NOT NULL,
  `tartalom` text COLLATE utf8_hungarian_ci DEFAULT NULL,
  `kateg_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `cdn_bejegyzesek`
--

INSERT INTO `cdn_bejegyzesek` (`id`, `user_fk`, `datum`, `cim`, `slug`, `tartalom`, `kateg_id`) VALUES
(1, 'admin', '2022-09-13', 'Pár szó', 'par_szo', '&lt;h2&gt;&lt;img alt=&quot;&quot; src=&quot;http://localhost/images/thumb/like.jpg&quot; style=&quot;float:left;height:246px;width:217px;&quot; /&gt;Az első bejegyzés&lt;/h2&gt;\n\n&lt;p&gt;Az első bekezdés itt olvasható...&lt;/p&gt;\n\n&lt;p&gt;A második máris érkezik rengeteg más dologgal együtt. A kép igazítható bal&lt;br /&gt;\nvagy jobb oldalra is, most bal oldalon van.&lt;/p&gt;\n', 1),
(5, 'teszt', '2022-09-16', 'Ádám és Éva (módosított)', 'adam_es_eva_modositott', '&lt;p&gt;Már az ókori görögök is megmondták... folytassuk egy kis összefoglalóval:&lt;img alt=&quot;humor&quot; src=&quot;http://localhost/images/thumb/129228834_3665479073504431_8553724687828339209_n.jpg&quot; style=&quot;float:right;height:300px;width:233px;&quot; /&gt;&lt;/p&gt;\r\n\r\n&lt;ol&gt;\r\n	&lt;li&gt;rövidített változat (kivonat) elkészült az első 8 szó levágásával&lt;/li&gt;\r\n	&lt;li&gt;kártyák formájában meg tudjuk jeleníteni a bejegyzések előnézetét&lt;/li&gt;\r\n	&lt;li&gt;a kártyán kattintásra előjön a teljes bejegyzés, amire hivatkozik&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p&gt; A kategóriák szerkesztésére lehet esetleg még szükség, mint külön funkció, illetve a bejegyzések szerkesztése, törlése van hátra, mint megoldandó feladat.&lt;/p&gt;\r\n\r\n&lt;p&gt;A végére egy kis stilizálás, aztán &lt;strong&gt;nagyjából kész vagyunk&lt;/strong&gt;. :)&lt;/p&gt;\r\n', 1),
(8, 'admin', '2022-09-21', 'elmélkedjünk', 'elmelkedjunk', '&lt;p&gt;&lt;img alt=&quot;&quot; src=&quot;http://localhost/images/thumb/bicikli.jpg&quot; style=&quot;float:left;height:185px;width:200px;&quot; /&gt;Teszt bejegyzés a biciklihez.&lt;/p&gt;\r\n', 1),
(9, 'admin', '2022-10-03', 'Próba arra az esetre, ha nincs kép', 'proba_arra_az_esetre_ha_nincs_kep', '&lt;p&gt;Eddig minden szépen &lt;strong&gt;működött&lt;/strong&gt;. De! Mi van, ha &lt;em&gt;nincs kép&lt;/em&gt;?&lt;/p&gt;\r\n', 2);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `cdn_felhasznalok`
--

CREATE TABLE if not exists `cdn_felhasznalok` (
  `user` varchar(60) COLLATE utf8_hungarian_ci NOT NULL,
  `pass` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `cdn_felhasznalok`
--

INSERT INTO `cdn_felhasznalok` (`user`, `pass`) VALUES
('admin', '$2y$10$TPNBzfh2Rl7AqHa85qwQo.NkwQqIsDJZ1TmsFpH9TiYNM36DrbDXu'),
('teszt', '$2y$10$aBDKaf9MHuNRLtHx7.y2puFEeZmfcDvnPgfZu0GklODyLZKcf4zmm');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `cdn_kategoriak`
--

CREATE TABLE if not exists `cdn_kategoriak` (
  `id` int(11) NOT NULL,
  `megnevezes` varchar(60) COLLATE utf8_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `cdn_kategoriak`
--

INSERT INTO `cdn_kategoriak` (`id`, `megnevezes`) VALUES
(1, 'edzés'),
(2, 'filozófia'),
(3, 'beszámolók');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `cdn_bejegyzesek`
--
ALTER TABLE `cdn_bejegyzesek`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `egyedi_slug` (`slug`),
  ADD KEY `user_fk` (`user_fk`),
  ADD KEY `kateg_id` (`kateg_id`);

--
-- A tábla indexei `cdn_felhasznalok`
--
ALTER TABLE `cdn_felhasznalok`
  ADD PRIMARY KEY (`user`);

--
-- A tábla indexei `cdn_kategoriak`
--
ALTER TABLE `cdn_kategoriak`
  ADD PRIMARY KEY (`id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `cdn_bejegyzesek`
--
ALTER TABLE `cdn_bejegyzesek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT a táblához `cdn_kategoriak`
--
ALTER TABLE `cdn_kategoriak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `cdn_bejegyzesek`
--
ALTER TABLE `cdn_bejegyzesek`
  ADD CONSTRAINT `cdn_bejegyzesek_ibfk_1` FOREIGN KEY (`user_fk`) REFERENCES `cdn_felhasznalok` (`user`),
  ADD CONSTRAINT `cdn_bejegyzesek_ibfk_2` FOREIGN KEY (`kateg_id`) REFERENCES `cdn_kategoriak` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
