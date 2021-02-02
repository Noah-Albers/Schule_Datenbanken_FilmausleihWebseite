-- Kunden Tabelle --
CREATE TABLE `schule_videoondemand2`.`kunde` ( `ID` INT NOT NULL AUTO_INCREMENT , `SessionID` VARCHAR(20) NOT NULL , `Vorname` VARCHAR(20) NOT NULL , `Nachname` VARCHAR(20) NOT NULL , `Email` VARCHAR(30) NOT NULL , `Pass_hash` VARCHAR(32) NOT NULL , `Pass_salt` VARCHAR(10) NOT NULL , `Geburtstag` DATE NOT NULL , PRIMARY KEY (`ID`), UNIQUE (`Email`)) ENGINE = InnoDB;

-- Film Tabelle --
CREATE TABLE `schule_videoondemand2`.`film` ( `id` INT NOT NULL AUTO_INCREMENT , `name` TEXT NOT NULL , `Erscheinungsdatum` DATE NOT NULL , `FSK` INT NOT NULL , `Ausleihzeit` TIMESTAMP NOT NULL , PRIMARY KEY (`id`), UNIQUE (`name`)) ENGINE = InnoDB;

-- Ausleih Tabelle --
CREATE TABLE `schule_videoondemand2`.`ausleih` ( `ID` INT NOT NULL AUTO_INCREMENT , `KundenID` INT NOT NULL , `FilmID` INT NOT NULL , `Ausleihdatum` DATETIME NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;

-- CSRF Tokens --
CREATE TABLE `schule_videoondemand2`.`csrftokens` ( `id` INT NOT NULL , `KundenID` INT NOT NULL , `Loeschdatum` DATETIME NOT NULL , `Erstelldatum` DATETIME NOT NULL ) ENGINE = InnoDB;
