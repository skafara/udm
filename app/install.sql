START TRANSACTION;

-- DATABASE

CREATE DATABASE IF NOT EXISTS `udm` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci;
USE `udm`;

-- TABLES

CREATE TABLE `Department` (
  `id` int(11) NOT NULL,
  `short_name` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `full_name` varchar(256) COLLATE utf8_czech_ci NOT NULL,
  `Faculty_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE `Faculty` (
  `id` int(11) NOT NULL,
  `short_name` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `full_name` varchar(256) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE `LessonType` (
  `id` int(11) NOT NULL,
  `full_name` varchar(256) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE `Material` (
  `id` int(11) NOT NULL,
  `upload_filename` varchar(256) COLLATE utf8_czech_ci NOT NULL,
  `disk_filename` varchar(80) COLLATE utf8_czech_ci NOT NULL,
  `full_name` varchar(64) COLLATE utf8_czech_ci DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `passed` tinyint(4) NOT NULL,
  `MaterialGroup_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE `MaterialGroup` (
  `id` int(11) NOT NULL,
  `description` varchar(64) COLLATE utf8_czech_ci DEFAULT NULL,
  `User_id` int(11) NOT NULL,
  `Subject_id` int(11) NOT NULL,
  `LessonType_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE `Subject` (
  `id` int(11) NOT NULL,
  `short_name` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `full_name` varchar(256) COLLATE utf8_czech_ci NOT NULL,
  `content` varchar(2048) COLLATE utf8_czech_ci DEFAULT NULL,
  `Department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE `Teaches` (
  `id` int(11) NOT NULL,
  `Subject_id` int(11) NOT NULL,
  `LessonType_id` int(11) NOT NULL,
  `User_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE `User` (
  `id` int(11) NOT NULL,
  `first_name` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `last_name` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `login` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(256) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(80) COLLATE utf8_czech_ci NOT NULL,
  `UserType_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE `UserType` (
  `id` int(11) NOT NULL,
  `full_name` varchar(256) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- KEYS

ALTER TABLE `Department`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Department_Faculty1_idx` (`Faculty_id`);

ALTER TABLE `Faculty`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `LessonType`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `Material`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `disk_filename_UNIQUE` (`disk_filename`),
  ADD KEY `fk_Material_MaterialGroup1_idx` (`MaterialGroup_id`);

ALTER TABLE `MaterialGroup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_MaterialGroup_User1_idx` (`User_id`),
  ADD KEY `fk_MaterialGroup_Subject1_idx` (`Subject_id`),
  ADD KEY `fk_MaterialGroup_LessonType1_idx` (`LessonType_id`);

ALTER TABLE `Subject`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Subject_Department_idx` (`Department_id`);

ALTER TABLE `Teaches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Teaches_Subject1_idx` (`Subject_id`),
  ADD KEY `fk_Teaches_LessonType1_idx` (`LessonType_id`),
  ADD KEY `fk_Teaches_User1_idx` (`User_id`);

ALTER TABLE `User`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login_UNIQUE` (`login`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD UNIQUE KEY `password_UNIQUE` (`password`),
  ADD KEY `fk_User_UserType1_idx` (`UserType_id`);

ALTER TABLE `UserType`
  ADD PRIMARY KEY (`id`);

-- AUTOINCREMENT

ALTER TABLE `Department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `LessonType`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Material`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `MaterialGroup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Teaches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `User`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `UserType`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- CONSTRAINTS

ALTER TABLE `Department`
  ADD CONSTRAINT `fk_Department_Faculty1` FOREIGN KEY (`Faculty_id`) REFERENCES `Faculty` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `Material`
  ADD CONSTRAINT `fk_Material_MaterialGroup1` FOREIGN KEY (`MaterialGroup_id`) REFERENCES `MaterialGroup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `MaterialGroup`
  ADD CONSTRAINT `fk_MaterialGroup_LessonType1` FOREIGN KEY (`LessonType_id`) REFERENCES `LessonType` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_MaterialGroup_Subject1` FOREIGN KEY (`Subject_id`) REFERENCES `Subject` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_MaterialGroup_User1` FOREIGN KEY (`User_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `Subject`
  ADD CONSTRAINT `fk_Subject_Department` FOREIGN KEY (`Department_id`) REFERENCES `Department` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `Teaches`
  ADD CONSTRAINT `fk_Teaches_LessonType1` FOREIGN KEY (`LessonType_id`) REFERENCES `LessonType` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Teaches_Subject1` FOREIGN KEY (`Subject_id`) REFERENCES `Subject` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Teaches_User1` FOREIGN KEY (`User_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `User`
  ADD CONSTRAINT `fk_User_UserType1` FOREIGN KEY (`UserType_id`) REFERENCES `UserType` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

START TRANSACTION;

-- VALUES

INSERT INTO `Faculty` (`id`, `short_name`, `full_name`) VALUES(1, 'FAV', 'Fakulta aplikovaných věd');
INSERT INTO `Faculty` (`id`, `short_name`, `full_name`) VALUES(2, 'FPR', 'Fakulta právní');

INSERT INTO `Department` (`id`, `short_name`, `full_name`, `Faculty_id`) VALUES(1, 'KIV', 'Katedra informatiky a výpočetní techniky', 1);
INSERT INTO `Department` (`id`, `short_name`, `full_name`, `Faculty_id`) VALUES(2, 'KMA', 'Katedra matematiky', 1);
INSERT INTO `Department` (`id`, `short_name`, `full_name`, `Faculty_id`) VALUES(3, 'KPD', 'Katedra právních dějin', 2);
INSERT INTO `Department` (`id`, `short_name`, `full_name`, `Faculty_id`) VALUES(4, 'KTP', 'Katedra teorie práva', 2);

INSERT INTO `UserType` (`id`, `full_name`) VALUES(1, 'Student');
INSERT INTO `UserType` (`id`, `full_name`) VALUES(2, 'Vyučující');
INSERT INTO `UserType` (`id`, `full_name`) VALUES(3, 'Administrátor');

INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(1, 'Jméno', 'Příjmení', 'admin', 'admin@xxx.xxx', '$2y$10$5ZxyzIJiOQRG6DFb2zff5.m5AU1Yedf0pNDnrZXLdsriNLpxujvH.', 3);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(2, 'Stanislav', 'Kafara', 'skafara', 'skafara@xxx.xxx', '$2y$10$QisxhTIrI3qB4tB47FbQPeAO.9hD904zNmMj7ylPJ..HQpwN/1Zey', 1);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(3, 'Kazimír', 'Pašák', 'kazimir', 'kazimir@xxx.xxx', '$2y$10$Oe1XSAkko4wRHkX1hwLXbOeDgHtQMA2c.niWL3rjONEaNN4JWnMIq', 1);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(4, 'Pomocný', 'Admin', 'padmin', 'padmin@xxx.xxx', '$2y$10$cOiA.U.OJvzdBjWVJqkej.p4JtzQY0CvuryhJLAJwFRkH2MO2QXum', 3);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(5, 'Kamil', 'Ekštein', 'kekstein', 'kekstein@xxx.xxx', '$2y$10$S7eT9k14Fv5Utib3QByK3OrFMsFdXUz6AdNL83KJbxaPgiiwSpF8O', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(6, 'Michal', 'Nykl', 'nyklm', 'nyklm@xxx.xxx', '$2y$10$SipurnpnwJS/lYUz.4Qo9.7Sb1WI5ShSi8tiDGzdIneJRGLF5kRlO', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(7, 'František', 'Pártl', 'fpartl', 'fpartl@xxx.xxx', '$2y$10$XltNL4M1OMpIGRmX/wFqROm6bL4T/EqwyxchI.83z4m81BkQRXnqa', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(8, 'Martin', 'Dostal', 'madostal', 'madostal@xxx.xxx', '$2y$10$zZ03NC.FpjDckCM1ILwa5eZD2Tm4KLUxJ.WfLklyOzZG/KBRQ.jRe', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(9, 'Miroslav', 'Černý', 'cerny', 'cerny@xxx.xxx', '$2y$10$oSevU8EU1nFf1hhT6KpjW.VREm0YEsLb1B/9T/ymjib.b.ZE0wFr6', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(10, 'Petr', 'Dostalík', 'dostalik', 'dostalik@xxx.xxx', '$2y$10$Q8E.X2wj88KfPbDBgxr0v.I94d6jxkI3.z1DOBMUUSYx6tgI2mti.', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(11, 'František', 'Cvrček', 'cvrcek', 'cvrcek@xxx.xxx', '$2y$10$1SQdqLqQgeoCUX4Ft8aEreJSf/xS2NhI1sddpbOUpeZ9/.0nR/nhu', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(12, 'Lenka', 'Bezoušková', 'lbezousk', 'lbezousk@xxx.xxx', '$2y$10$5mDLmV5S7s0Shb6aI9h3negPFNrjnHTHP1quQeUxc8D2K.RQxKvAK', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(13, 'Přemysl', 'Holub', 'holubpre', 'holubpre@xxx.xxx', '$2y$10$oyKs7da9daHsbkcu4wXnVuUrKhHop8XKj2tt0EKegVG3RBgc3I63K', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(14, 'Jan', 'Ekstein', 'ekstein', 'ekstein@xxx.xxx', '$2y$10$Q77kOODofUMm0807pWysE.UnO379SPo3ALzXMoyd2izWZ/yOhZ/2m', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(15, 'Roman', 'Čada', 'cadar', 'cadar@xxx.xxx', '$2y$10$rQ3RKam5UejRyPd6TcZ9dOjZfHT3ygxXVMJZ9bsZZ4ocj1ELviWvW', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(16, 'Adam', 'Kabela', 'kabela', 'kabela@xxx.xxx', '$2y$10$Z0vIAh6H0befcTStT9P4cuGOZbrJAysa9bkYDsF7RR5tbosJC/LCq', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(17, 'Peter', 'Brezina', 'brezinap', 'brezinap@xxx.xxx', '$2y$10$zRZbP17Ul.G2xLT9XztDM.C/ltPfBwHcGWS3wYlOp0aKvEYxGw3i2', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(18, 'Antonín', 'Hrdina', 'ahrdina', 'ahrdina@xxx.xxx', '$2y$10$r1ZIP7T6egEufoyAM0rjtuC1MOJPriwdNDeAHJetiihbWYvSc04by', 2);
INSERT INTO `User` (`id`, `first_name`, `last_name`, `login`, `email`, `password`, `UserType_id`) VALUES(19, 'Richard', 'Lipka', 'lipka', 'lipka@xxx.xxx', '$2y$10$BRIToU88/2BUj0f7oEZ9i.5MmgekcdEWHhnCbNJamDmFTQiaR4oma', 2);

INSERT INTO `Subject` (`id`, `short_name`, `full_name`, `content`, `Department_id`) VALUES(1, 'PC', 'Programování v jazyce C', NULL, 1);
INSERT INTO `Subject` (`id`, `short_name`, `full_name`, `content`, `Department_id`) VALUES(2, 'WEB', 'Webové aplikace', '1 Úvod do předmětu, \r\norganizační informace, historie webu. \n2 HTML - vývoj, verze, základní \r\nelementy, formuláře. \n3-4 CSS - základy, layout \n5 HTTP, Přehled \r\nserverových technologií - CGI, Servlety a JSP, skripty PHP a Python \n6-7 \r\nPHP - základy, syntax, funkce, soubory, formuláře \n8-10 PHP - databáze, \r\nrelace, třídy a objekty, šablony \n11-12 JavaScript a AJAX \n13. \r\nKonfigurace Apache a PHP, bezpečnost webu', 1);
INSERT INTO `Subject` (`id`, `short_name`, `full_name`, `content`, `Department_id`) VALUES(3, 'ŘP1', 'Římské právo 1', '1. Význam studia ŘP \r\npro současnost, základní seznámení s průběhem výuky předmětu, doporučená \r\nliteratura \n2. Gaiovy instituce, jejich znovuobjevení a obsah \n3. \r\nZákladní právní terminologie podle romanistického pohledu: ius ve svých \r\nrůzných významech, právní norma, právní vztah, právní činy, právní jednání \r\n\n4. Ius civile, ius gentium a ius naturale \n5. Status libertatis, status \r\ncivitatis, status familiae \n6. Negotium iuridicum a jeho různé typy \n7. \r\nElementy obsažené v negotium iuridicum : essentialia, naturalia, \r\naccidentalia \n8. Condicio, dies, modus \n9. Vůle, její možné projevy a \r\nvady: error, dolus, vis \n10. Římský privátní proces: legis actiones, \r\nformulae, cognitio extra ordinem \n11. Věci v ŘP \n12. Věcná práva, \r\nomezení vlastnických práv', 3);
INSERT INTO `Subject` (`id`, `short_name`, `full_name`, `content`, `Department_id`) VALUES(4, 'TPH1', 'Teorie práva 1', NULL, 4);
INSERT INTO `Subject` (`id`, `short_name`, `full_name`, `content`, `Department_id`) VALUES(5, 'LAA', 'Lineární algebra', '1. Komplexní čísla, \r\ntělesa. Polynomy, okruhy. Hornerovo schéma, rozklad polynomu na kořenové \r\nčinitele. \n2. Lineární prostor, lineární závislost a nezávislost, báze a \r\ndimenze prostoru, souřadnice prvku v dané bázi. \n3. Matice, determinant \r\nmatice a jeho základní vlastnosti, rozvoj determinantu. \n4. Gaussova \r\neliminační metoda. Rychlý výpočet determinantu. Lineární prostory \r\npřiřazené k matici. Hodnost matice, určení hodnosti pomocí determinantů. \r\n\n5. Inverzní matice, Jordanova eliminační metoda, konstrukce inverzní \r\nmatice pomocí determinantů. \n6. Lineární zobrazení, jádro a obraz a \r\njejich dimenze, matice lineárního zobrazení a její vlastnosti. Základní \r\nvěta lineární algebry. \n7. Inverzní zobrazení, složené zobrazení a jejich \r\nmatice, izomorfismus lineárních prostorů, změna báze a matice přechodu. \r\n\n8. Soustavy lineárních rovnic, homogenní a nehomogenní soustavy rovnic, \r\nsoustavy rovnic s regulární maticí, Cramerovo pravidlo. \n9. Vlastní čísla \r\na vlastní vektory matice, zobecněné vlastní vektory. Podobnost matic. \r\nJordanův kanonický tvar matice. Maticové funkce. Cayleyho-Hamiltonova \r\nvěta. \n10. Metrika, norma, skalární součin a jejich vlastnosti. \r\nEuklidovské a unitární prostory. Ortogonální a ortonormální báze prostoru. \r\n\n11. Gramův-Schmidtův ortogonalizační proces, ortogonální průmět do \r\npodprostoru. QR rozklad matice. \n12. Lineární metoda nejmenších čtverců. \r\nLineární formy. Multilineární formy. Kvadratické formy a reálné symetrické \r\nmatice. Definitnost matice. \n13. Inercie kvadratické formy, zákon \r\nsetrvačnosti kvadratických forem. Kvadratické formy a optimalizace.', 2);
INSERT INTO `Subject` (`id`, `short_name`, `full_name`, `content`, `Department_id`) VALUES(6, 'DMA', 'Diskrétní matematika', '1. týden \r\n\nZákladní pojmy teorie množin, binární relace \n\n2. týden \nZobrazení, \r\nzákladní algebraické struktury \n\n3. týden \nRelace tolerance a \r\nekvivalence, kongruence modulo p \n\n4. týden \nRelace uspořádání, \r\nčástečné a úplné uspořádání, Hasseův diagram \n\n5. týden \nSupremum a \r\ninfimum, svaz, distributivní svaz \n\n6. týden \nKomplementární svaz, \r\nBooleova algebra, booleovský kalkulus, Stoneova věta o reprezentaci. \r\n\n\n7. týden \nDirektní součet booleovských algeber, booleovské funkce, \r\nbooleovské polynomy, disjunktivní a konjunktivní normální forma \n\n8. \r\ntýden \nPojem grafu, orientované a neorientované grafy, homomorfismy v \r\ngrafech a pojmy související, cesty v grafech, stupeň vrcholu, Eulerovské \r\ngrafy, stromy \n\n9. týden \nOrientované grafy, slabá a silná souvislost, \r\nacyklické grafy, kondenzace grafu \n\n10. týden \nIncidenční matice \r\norientovaného grafu, Laplaceova matice sousednosti, počet koster grafu, \r\nincidenční matice neorientovaného grafu \n\n11. týden \nMatice \r\nsousednosti, počty sledů. Ohodnocené grafy, vzdálenost v grafech, \r\nw-distanční matice \n\n12. týden \nAplikační úlohy: vzdálenost a minimální \r\ncesta - Dijkstrův algoritmus, minimální kostra, kritická cesta \n\n13. \r\ntýden \nRezerva, typové příklady, příprava zkoušky', 2);
INSERT INTO `Subject` (`id`, `short_name`, `full_name`, `content`, `Department_id`) VALUES(7, 'TP3R', 'Teorie práva 3 - dějiny právního \r\nmyšlení', NULL, 4);
INSERT INTO `Subject` (`id`, `short_name`, `full_name`, `content`, `Department_id`) VALUES(8, 'KA1', 'Kanonické právo 1', NULL, 3);
INSERT INTO `Subject` (`id`, `short_name`, `full_name`, `content`, `Department_id`) VALUES(9, 'UUR', 'Úvod do uživatelských rozhraní', NULL, 1);

INSERT INTO `LessonType` (`id`, `full_name`) VALUES(1, 'Přednáška');
INSERT INTO `LessonType` (`id`, `full_name`) VALUES(2, 'Cvičení');
INSERT INTO `LessonType` (`id`, `full_name`) VALUES(3, 'Seminář');

INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(1, 1, 1, 5);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(2, 1, 2, 5);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(3, 1, 2, 7);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(4, 2, 1, 8);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(5, 2, 2, 6);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(6, 3, 1, 9);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(7, 3, 1, 10);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(8, 3, 1, 18);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(9, 3, 3, 9);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(10, 3, 3, 10);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(11, 4, 1, 12);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(12, 5, 1, 13);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(13, 5, 2, 13);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(14, 5, 2, 14);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(15, 5, 2, 16);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(16, 6, 1, 15);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(17, 6, 1, 13);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(18, 6, 2, 14);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(19, 6, 2, 16);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(20, 6, 2, 13);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(21, 7, 1, 17);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(22, 7, 1, 11);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(23, 7, 3, 17);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(24, 7, 3, 11);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(25, 8, 1, 18);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(26, 8, 1, 9);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(27, 9, 1, 19);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(28, 9, 2, 19);
INSERT INTO `Teaches` (`id`, `Subject_id`, `LessonType_id`, `User_id`) VALUES(29, 9, 2, 6);

INSERT INTO `MaterialGroup` (`id`, `description`, `User_id`, `Subject_id`, `LessonType_id`) VALUES(1, NULL, 5, 1, 1);
INSERT INTO `MaterialGroup` (`id`, `description`, `User_id`, `Subject_id`, `LessonType_id`) VALUES(2, 'Nevyplněné', 13, 6, 1);
INSERT INTO `MaterialGroup` (`id`, `description`, `User_id`, `Subject_id`, `LessonType_id`) VALUES(3, NULL, 8, 2, 1);
INSERT INTO `MaterialGroup` (`id`, `description`, `User_id`, `Subject_id`, `LessonType_id`) VALUES(4, 'Zápisky z přednášek', 2, 5, 1);
INSERT INTO `MaterialGroup` (`id`, `description`, `User_id`, `Subject_id`, `LessonType_id`) VALUES(5, 'Zápisky ze cvičení', 2, 6, 2);
INSERT INTO `MaterialGroup` (`id`, `description`, `User_id`, `Subject_id`, `LessonType_id`) VALUES(6, 'Vyplněné', 13, 6, 1);
INSERT INTO `MaterialGroup` (`id`, `description`, `User_id`, `Subject_id`, `LessonType_id`) VALUES(7, 'Zápisky z přednášek', 2, 6, 1);
INSERT INTO `MaterialGroup` (`id`, `description`, `User_id`, `Subject_id`, `LessonType_id`) VALUES(8, 'Zadání příkladů', 16, 6, 2);

INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(1, 'PC2016-01_Intro.pdf', '638f2846a0291', '1. přednáška', NULL, 1, 1);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(2, 'PC2016-02_Konstrukce.pdf', '638f28f318254', '2. přednáška', NULL, 1, 1);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(3, 'PC2016-03_Deklarace-a-definice.pdf', '638f2920137c9', '3. přednáška', NULL, 1, 1);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(4, '1.+prednaska+-+relace.pdf', '638f2b082ed32', '1. přednáška - Binární relace', NULL, 1, 2);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(5, '2.+prednaska+-+tridy+ekvivalence,+grupy+a+telesa+2021.pdf', '638f2b0e769ea', '2. přednáška - Ekvivalence, tělesa zbytkových tříd', NULL, 1, 2);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(6, '3.+prednaska+-+usporadani+a+svazy+2021.pdf', '638f2b6ca9ee2', '3. přednáška - Uspořádání, svazy', NULL, 1, 2);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(7, '1.+Uvod+a+podminky,+HTML.pdf', '638f2be007c1d', '1. Uvod a podminky, HTML.pdf', NULL, 1, 3);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(8, '2.+CSS+1+++git.pdf', '638f2c16596a7', '2. CSS 1 + git.pdf', NULL, 1, 3);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(9, '1.+prednaska.pdf', '638f2c28eaef8', '1. přednáška - Binární relace', NULL, 1, 6);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(10, '2.+prednaska.pdf', '638f2c91c7d7a', '2. přednáška - Ekvivalence, tělesa zbytkových tříd', NULL, 1, 6);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(20, 'LAA_01P.pdf', '638f2cd741197', 'LAA_01P', 9, 1, 4);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(21, 'LAA_02P.pdf', '638f2d64b7983', 'LAA_02P', 8, 1, 4);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(22, 'LAA_03P.pdf', '638f2d912ae31', 'LAA_03P', NULL, 0, 4);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(26, 'DMA_01C.pdf', '638f2de9cecf7', 'DMA_01C', 10, 1, 5);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(27, 'DMA_02C.pdf', '638f2ea7878b4', 'DMA_02C', NULL, 1, 5);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(28, 'DMA_03C.pdf', '638f2ef1bf13b', 'DMA_03C', NULL, 0, 5);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(29, 'DMA_01P.pdf', '6390666d0defd', 'DMA_01P', NULL, 1, 7);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(30, 'DMA_02P.pdf', '6390667497305', 'DMA_02P', NULL, 0, 7);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(31, 'priklady1.pdf', '639066a377276', 'Příklady 1', NULL, 1, 8);
INSERT INTO `Material` (`id`, `upload_filename`, `disk_filename`, `full_name`, `points`, `passed`, `MaterialGroup_id`) VALUES(32, 'priklady2.pdf', '63908d883059d', 'Příklady 2', NULL, 1, 8);
COMMIT;
