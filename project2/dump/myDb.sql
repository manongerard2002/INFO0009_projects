SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;
-- --------------------------------------------------------
--
-- Structure du tableau `CD`
--
CREATE TABLE IF NOT EXISTS `CD` (
  `CD_NUMBER` INT NOT NULL,
  `TITLE` VARCHAR(30) NOT NULL,
  `PRODUCER` VARCHAR(50) NOT NULL,
  `YEAR` YEAR NOT NULL,
  `COPIES` SMALLINT NOT NULL,
  CONSTRAINT CHECK_CD_NUMBER CHECK (`CD_NUMBER` > 0),
  CONSTRAINT CHECK_YEAR CHECK (`YEAR` >= 1982),
  CONSTRAINT CHECK_COPIES CHECK (`COPIES` >= 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `CLIENT`
--
CREATE TABLE IF NOT EXISTS `CLIENT` (
  `CLIENT_NUMBER` INT NOT NULL,
  `FIRST_NAME` VARCHAR(50) NOT NULL,
  `LAST_NAME` VARCHAR(50) NOT NULL,
  `EMAIL_ADDRESS` VARCHAR(319) DEFAULT NULL,
  `PHONE_NUMBER` VARCHAR(20) NOT NULL,
  CONSTRAINT CHECK_CLIENT_NUMBER CHECK (`CLIENT_NUMBER` > 0),
  CONSTRAINT CHECK_EMAIL CHECK (
    (
      `EMAIL_ADDRESS` IS NULL
    )
    OR (
      CHAR_LENGTH(`EMAIL_ADDRESS`) - CHAR_LENGTH(REPLACE(`EMAIL_ADDRESS`,'@','')) >= 1
      AND
      CHAR_LENGTH(SUBSTRING_INDEX(`EMAIL_ADDRESS`, '@', 1)) <= 64
      AND
      CHAR_LENGTH(SUBSTRING_INDEX(`EMAIL_ADDRESS`, '@', -1)) <= 255
    )
  )
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `EMPLOYEE`
--
CREATE TABLE IF NOT EXISTS `EMPLOYEE` (
  `ID` INT NOT NULL,
  `FIRSTNAME` VARCHAR(15) NOT NULL,
  `LASTNAME` VARCHAR(15) NOT NULL,
  CONSTRAINT CHECK_ID_EMPLOYEE CHECK (`ID` > 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `DJ`
--
CREATE TABLE IF NOT EXISTS `DJ` (
  `ID` INT NOT NULL,
  CONSTRAINT CHECK_ID_DJ CHECK (`ID` > 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `EVENTPLANNER`
--
CREATE TABLE IF NOT EXISTS `EVENTPLANNER` (
  `ID` INT NOT NULL,
  CONSTRAINT CHECK_ID_EVENTPLANNER CHECK (`ID` > 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `GENRE`
--
CREATE TABLE IF NOT EXISTS `GENRE` (
  `NAME` VARCHAR(50) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `LOCATION`
--
CREATE TABLE IF NOT EXISTS `LOCATION` (
  `ID` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `STREET` VARCHAR(50) NOT NULL,
  `CITY` VARCHAR(50) NOT NULL,
  `POSTAL_CODE` VARCHAR(10) NOT NULL,
  `COUNTRY` VARCHAR(50) NOT NULL,
  `COMMENT` VARCHAR(30) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `MANAGER`
--
CREATE TABLE IF NOT EXISTS `MANAGER` (
  `ID` INT NOT NULL,
  CONSTRAINT CHECK_ID_MANAGER CHECK (`ID` > 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `PLAYLIST`
--
CREATE TABLE IF NOT EXISTS `PLAYLIST` (
  `NAME` VARCHAR(50) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `SONG`
--
CREATE TABLE IF NOT EXISTS `SONG` (
  `CD_NUMBER` INT NOT NULL,
  `TRACK_NUMBER` INT NOT NULL,
  `TITLE` VARCHAR(100) NOT NULL,
  `ARTIST` VARCHAR(50) NOT NULL,
  `DURATION` TIME NOT NULL,
  `GENRE` VARCHAR(50) NOT NULL,
  CONSTRAINT CHECK_CD_NUMBER_SONG CHECK (`CD_NUMBER` > 0),
  CONSTRAINT CHECK_TRACK_NUMBER CHECK (`TRACK_NUMBER` > 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `CONTAINS`
--
CREATE TABLE IF NOT EXISTS `CONTAINS` (
  `PLAYLIST` VARCHAR(50) NOT NULL,
  `TRACK_NUMBER` INT NOT NULL,
  `CD_NUMBER` INT NOT NULL,
  CONSTRAINT CHECK_TRACK_CONTAINS CHECK (`TRACK_NUMBER` > 0),
  CONSTRAINT CHECK_CD_NUMBER_CONTAINS CHECK (`CD_NUMBER` > 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `SPECIALIZATION`
--
CREATE TABLE IF NOT EXISTS `SPECIALIZATION` (
  `DJ` INT NOT NULL,
  `GENRE` VARCHAR(50) NOT NULL,
  CONSTRAINT CHECK_DJ_SPECIALIZATION CHECK (`DJ` > 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `SPECIALIZES`
--
CREATE TABLE IF NOT EXISTS `SPECIALIZES` (
  `SUBGENRE` VARCHAR(50) NOT NULL,
  `GENRE` VARCHAR(50) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `SUPERVISION`
--
CREATE TABLE IF NOT EXISTS `SUPERVISION` (
  `SUPERVISOR_ID` INT NOT NULL,
  `EMPLOYEE_ID` INT NOT NULL,
  CONSTRAINT CHECK_SUPERVISOR_ID CHECK (`SUPERVISOR_ID` > 0),
  CONSTRAINT CHECK_EMPLOYEE_ID CHECK (`EMPLOYEE_ID` > 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `THEME`
--
CREATE TABLE IF NOT EXISTS `THEME` (
  `NAME` VARCHAR(50) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `SUITABLEFOR`
--
CREATE TABLE IF NOT EXISTS `SUITABLEFOR` (
  `THEME` VARCHAR(50) NOT NULL,
  `PLAYLIST` VARCHAR(50) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `EVENT`
--
CREATE TABLE IF NOT EXISTS `EVENT` (
  `ID` INT NOT NULL,
  `NAME` VARCHAR(50) NOT NULL,
  `DATE` DATE NOT NULL,
  `DESCRIPTION` VARCHAR(200) DEFAULT NULL,
  `CLIENT` INT NOT NULL,
  `MANAGER` INT NOT NULL,
  `EVENT_PLANNER` INT NOT NULL,
  `DJ` INT NOT NULL,
  `THEME` VARCHAR(30) NOT NULL,
  `TYPE` VARCHAR(30) NOT NULL,
  `LOCATION` INT DEFAULT NULL,
  `RENTAL_FEE` INT DEFAULT NULL,
  `PLAYLIST` VARCHAR(50) DEFAULT NULL,
  CONSTRAINT CHECK_ID_EVENT CHECK (`ID` > 0),
  CONSTRAINT CHECK_CLIENT CHECK (`CLIENT` > 0),
  CONSTRAINT CHECK_MANAGER CHECK (`MANAGER` > 0),
  CONSTRAINT CHECK_EVENT_PLANNER CHECK (`EVENT_PLANNER` > 0),
  CONSTRAINT CHECK_DJ CHECK (`DJ` > 0),
  CONSTRAINT CHECK_RENTAL_FEE CHECK (`RENTAL_FEE` >= 0),
  CONSTRAINT CHECK_UN_DJ_PAR_JOUR UNIQUE (`DATE`, `DJ`),
  CONSTRAINT CHECK_UN_EVENT_PLANNER_PAR_JOUR  UNIQUE (`DATE`, `EVENT_PLANNER`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
--
-- Structure du tableau `USERS`
--
CREATE TABLE IF NOT EXISTS `USERS` (
  `Login` VARCHAR(20) NOT NULL,
  `Pass` VARCHAR(20) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
--
-- Remplissage manuel de la table `USERS`
--
INSERT INTO `USERS` (`Login`, `Pass`)
VALUES ('group05', 'secret');
-- --------------------------------------------------------
--
-- Index des tables eportées
--
--
-- Index de la table `CD`
--
ALTER TABLE `CD`
ADD PRIMARY KEY (`CD_NUMBER`, `TITLE`);
--
-- Index de la table `CLIENT`
--
ALTER TABLE `CLIENT`
ADD PRIMARY KEY (`CLIENT_NUMBER`);
ALTER TABLE `CLIENT`
ADD UNIQUE KEY `EMAIL_ADDRESS` (`EMAIL_ADDRESS`);
--
-- Index de la table `EMPLOYEE`
--
ALTER TABLE `EMPLOYEE`
ADD PRIMARY KEY (`ID`);
--
-- Index de la table `DJ`
--
ALTER TABLE `DJ`
ADD PRIMARY KEY (`ID`);
--
-- Index de la table `EVENTPLANNER`
--
ALTER TABLE `EVENTPLANNER`
ADD PRIMARY KEY (`ID`);
--
-- Index de la table `GENRE`
--
ALTER TABLE `GENRE`
ADD PRIMARY KEY (`NAME`);
--
-- Index de la table `MANAGER`
--
ALTER TABLE `MANAGER`
ADD PRIMARY KEY (`ID`);
--
-- Index de la table `PLAYLIST`
--
ALTER TABLE `PLAYLIST`
ADD PRIMARY KEY (`NAME`);
--
-- Index de la table `SONG`
--
ALTER TABLE `SONG`
ADD PRIMARY KEY (`CD_NUMBER`, `TRACK_NUMBER`);
--
-- Index de la table `CONTAINS`
--
ALTER TABLE `CONTAINS`
ADD PRIMARY KEY (`PLAYLIST`, `TRACK_NUMBER`, `CD_NUMBER`);
--
-- Index de la table `SPECIALIZATION`
--
ALTER TABLE `SPECIALIZATION`
ADD PRIMARY KEY (`DJ`, `GENRE`);
--
-- Index de la table `SPECIALIZES`
--
ALTER TABLE `SPECIALIZES`
ADD PRIMARY KEY (`SUBGENRE`, `GENRE`);
--
-- Index de la table `SUPERVISION`
--
ALTER TABLE `SUPERVISION`
ADD PRIMARY KEY (`EMPLOYEE_ID`);
--
-- Index de la table `THEME`
--
ALTER TABLE `THEME`
ADD PRIMARY KEY (`NAME`);
--
-- Index de la table `SUITABLEFOR`
--
ALTER TABLE `SUITABLEFOR`
ADD PRIMARY KEY (`THEME`, `PLAYLIST`);
--
-- Index de la table `EVENT`
--
ALTER TABLE `EVENT`
ADD PRIMARY KEY (`ID`);
-- --------------------------------------------------------
--
-- Limitations sur les tables exportées
--
--
-- Contraintes de la table `DJ`
--
ALTER TABLE `DJ`
ADD CONSTRAINT `DJ_fk_1` FOREIGN KEY (`ID`) REFERENCES `EMPLOYEE` (`ID`);
--
-- Contraintes de la table `EVENTPLANNER`
--
ALTER TABLE `EVENTPLANNER`
ADD CONSTRAINT `EVENTPLANNER_fk_1` FOREIGN KEY (`ID`) REFERENCES `EMPLOYEE` (`ID`);
--
-- Contraintes de la table `MANAGER`
--
ALTER TABLE `MANAGER`
ADD CONSTRAINT `MANAGER_fk_1` FOREIGN KEY (`ID`) REFERENCES `EMPLOYEE` (`ID`);
--
-- Contraintes de la table `SONG`
--
ALTER TABLE `SONG`
ADD CONSTRAINT `SONG_fk_1` FOREIGN KEY (`CD_NUMBER`) REFERENCES `CD` (`CD_NUMBER`);
ALTER TABLE `SONG`
ADD CONSTRAINT `SONG_fk_2` FOREIGN KEY (`GENRE`) REFERENCES `GENRE` (`NAME`);
--
-- Contraintes de la table `CONTAINS`
--
ALTER TABLE `CONTAINS`
ADD CONSTRAINT `CONTAINS_fk_1` FOREIGN KEY (`PLAYLIST`) REFERENCES `PLAYLIST` (`NAME`);
/*
ALTER TABLE `CONTAINS`
ADD CONSTRAINT `CONTAINS_fk_2` FOREIGN KEY (`CD_NUMBER`, `TRACK_NUMBER`) REFERENCES `SONG` (`CD_NUMBER`, `TRACK_NUMBER`);
*/
--
-- Contraintes de la table `SPECIALIZATION`
--
ALTER TABLE `SPECIALIZATION`
ADD CONSTRAINT `SPECIALIZATION_fk_1` FOREIGN KEY (`DJ`) REFERENCES `DJ` (`ID`);
ALTER TABLE `SPECIALIZATION`
ADD CONSTRAINT `SPECIALIZATION_fk_2` FOREIGN KEY (`GENRE`) REFERENCES `GENRE` (`NAME`);
--
-- Contraintes de la table `SPECIALIZES`
--
ALTER TABLE `SPECIALIZES`
ADD CONSTRAINT `SPECIALIZES_fk_1` FOREIGN KEY (`SUBGENRE`) REFERENCES `GENRE` (`NAME`);
ALTER TABLE `SPECIALIZES`
ADD CONSTRAINT `SPECIALIZES_fk_2` FOREIGN KEY (`GENRE`) REFERENCES `GENRE` (`NAME`);
--
-- Contraintes de la table `SUPERVISION`
--
-- employee ou juste manager
-- pas possible de mettre eventplanner ou dj donc laisser en général employee
ALTER TABLE `SUPERVISION`
ADD CONSTRAINT `SUPERVISION_fk_1` FOREIGN KEY (`SUPERVISOR_ID`) REFERENCES `MANAGER` (`ID`);
ALTER TABLE `SUPERVISION`
ADD CONSTRAINT `SUPERVISION_fk_2` FOREIGN KEY (`EMPLOYEE_ID`) REFERENCES `EMPLOYEE` (`ID`);
--
-- Contraintes de la table `SUITABLEFOR`
--
ALTER TABLE `SUITABLEFOR`
ADD CONSTRAINT `SUITABLEFOR_fk_1` FOREIGN KEY (`THEME`) REFERENCES `THEME` (`NAME`);
ALTER TABLE `SUITABLEFOR`
ADD CONSTRAINT `SUITABLEFOR_fk_2` FOREIGN KEY (`PLAYLIST`) REFERENCES `PLAYLIST` (`NAME`);
--
-- Contraintes de la table `EVENT`
--
ALTER TABLE `EVENT`
ADD CONSTRAINT `EVENT_fk_1` FOREIGN KEY (`CLIENT`) REFERENCES `CLIENT` (`CLIENT_NUMBER`);
ALTER TABLE `EVENT`
ADD CONSTRAINT `EVENT_fk_2` FOREIGN KEY (`MANAGER`) REFERENCES `MANAGER` (`ID`);
ALTER TABLE `EVENT`
ADD CONSTRAINT `EVENT_fk_3` FOREIGN KEY (`EVENT_PLANNER`) REFERENCES `EVENTPLANNER` (`ID`);
ALTER TABLE `EVENT`
ADD CONSTRAINT `EVENT_fk_4` FOREIGN KEY (`DJ`) REFERENCES `DJ` (`ID`);
ALTER TABLE `EVENT`
ADD CONSTRAINT `EVENT_fk_5` FOREIGN KEY (`THEME`) REFERENCES `THEME` (`NAME`);
ALTER TABLE `EVENT`
ADD CONSTRAINT `EVENT_fk_6` FOREIGN KEY (`LOCATION`) REFERENCES `LOCATION` (`ID`)
  ON DELETE RESTRICT
  ON UPDATE CASCADE;
ALTER TABLE `EVENT`
ADD CONSTRAINT `EVENT_fk_7` FOREIGN KEY (`PLAYLIST`) REFERENCES `PLAYLIST` (`NAME`);
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;
-- --------------------------------------------------------
-- Remplissage des tables en téléchargeant les fichiers
LOAD DATA INFILE '/docker-entrypoint-initdb.d/CD.csv' INTO TABLE `CD` FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/Client.csv' INTO TABLE `CLIENT` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
(CLIENT_NUMBER, FIRST_NAME, LAST_NAME, @email, PHONE_NUMBER)
SET
  `EMAIL_ADDRESS` = NULLIF(@email,'');
LOAD DATA INFILE '/docker-entrypoint-initdb.d/Employee.csv' INTO TABLE `EMPLOYEE` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/DJ.csv' INTO TABLE `DJ` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/EventPlanner.csv' INTO TABLE `EVENTPLANNER` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/Genre.csv' INTO TABLE `GENRE` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/Location.csv' INTO TABLE `LOCATION` FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/Manager.csv' INTO TABLE `MANAGER` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/Playlist.csv' INTO TABLE `PLAYLIST` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/Song.csv' INTO TABLE `SONG` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
(CD_NUMBER, TRACK_NUMBER, TITLE, ARTIST, @duration, GENRE)
SET `DURATION` = 
  STR_TO_DATE(
    @duration,
    IF(LENGTH(@duration) <= 2, '%s',
    IF(LENGTH(@duration) <= 5, '%i:%s',
    '%H:%i:%s')
    )
);
LOAD DATA INFILE '/docker-entrypoint-initdb.d/Contains.csv' INTO TABLE `CONTAINS` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/Specialization.csv' INTO TABLE `SPECIALIZATION` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/Specializes.csv' INTO TABLE `SPECIALIZES` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/Supervision.csv' INTO TABLE `SUPERVISION` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/Theme.csv' INTO TABLE `THEME` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/SuitableFor.csv' INTO TABLE `SUITABLEFOR` FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS;
LOAD DATA INFILE '/docker-entrypoint-initdb.d/Event.csv' INTO TABLE `EVENT` FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
(ID, NAME, DATE, @description, CLIENT, MANAGER, EVENT_PLANNER, DJ, THEME, TYPE, @location, @rental_fee, @playlist)
SET
  `DESCRIPTION` = NULLIF(@description,''),
  `LOCATION` = NULLIF(@location,''),
  `RENTAL_FEE` = NULLIF(@rental_fee,''),
  `PLAYLIST` = NULLIF(@playlist,'');