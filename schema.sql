PRAGMA journal_mode = delete;
PRAGMA page_size = 65536;
VACUUM;

PRAGMA auto_vacuum = INCREMENTAL;
PRAGMA case_sensitive_like = ON;
PRAGMA foreign_keys = ON;
PRAGMA journal_mode = MEMORY;
PRAGMA locking_mode = EXCLUSIVE;
PRAGMA synchronous = OFF;
PRAGMA threads = 4;
PRAGMA trusted_schema = OFF;
PRAGMA ignore_check_constraints = OFF;
VACUUM;

CREATE TABLE `account_entries` (
  `acc_id` integer  NOT NULL
,  `entry_id` integer  NOT NULL
,  PRIMARY KEY (`acc_id`,`entry_id`)
,  CONSTRAINT `account_entries_ibfk_1` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`entry_id`) ON DELETE CASCADE ON UPDATE CASCADE
,  CONSTRAINT `account_entries_ibfk_2` FOREIGN KEY (`acc_id`) REFERENCES `accounts` (`acc_id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `accounts` (
  `acc_id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `acc_name` tinytext NOT NULL
,  `acc_balance` decimal(20,5) NOT NULL
,  `acc_initial` decimal(20,5) NOT NULL
);
CREATE TABLE sqlite_sequence(name,seq);
CREATE TABLE `document_data` (
  `doc_id` integer  NOT NULL
,  `doc_data` longblob NOT NULL
,  PRIMARY KEY (`doc_id`)
,  CONSTRAINT `document_data_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `document_entries` (
  `doc_id` integer  NOT NULL
,  `entry_id` integer  NOT NULL
,  PRIMARY KEY (`doc_id`,`entry_id`)
,  CONSTRAINT `document_entries_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE ON UPDATE CASCADE
,  CONSTRAINT `document_entries_ibfk_2` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`entry_id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `documents` (
  `doc_id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `doc_name` tinytext NOT NULL
,  `doc_type` varchar(4) NOT NULL
,  `doc_date` date NOT NULL
,  `doc_hash` char(27) NOT NULL DEFAULT ''
,  UNIQUE (`doc_hash`)
);
CREATE TABLE `entries` (
  `entry_id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `entry_date` date NOT NULL
,  `entry_text` tinytext NOT NULL
,  `entry_amount` decimal(20,5) NOT NULL
,  `entry_balance` decimal(20,5) NOT NULL
,  `entry_date_interest` date NOT NULL
,  `entry_class` integer NOT NULL DEFAULT 0
,  UNIQUE (`entry_date`,`entry_amount`,`entry_balance`)
);
CREATE TABLE `entry_classes` (
	`c_id` INTEGER NOT NULL
,	`c_name` TEXT NOT NULL
,	PRIMARY KEY(c_id)
);
CREATE TABLE `view_entries` (
  `view_id` integer  NOT NULL
,  `entry_id` integer  NOT NULL
,  `ve_comment` tinytext DEFAULT NULL
,  `ve_amount` decimal(20,5) NOT NULL DEFAULT 0.00000
,  `ve_vat` decimal(20,5) NOT NULL DEFAULT 0.00000
,  PRIMARY KEY (`view_id`,`entry_id`)
,  CONSTRAINT `view_entries_ibfk_1` FOREIGN KEY (`view_id`) REFERENCES `views` (`view_id`) ON DELETE CASCADE ON UPDATE CASCADE
,  CONSTRAINT `view_entries_ibfk_2` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`entry_id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `view_rules` (
  `view_id` integer  NOT NULL
,  `rule_pattern` tinytext NOT NULL
,  `rule_vat` integer  DEFAULT NULL
,  PRIMARY KEY (`view_id`,`rule_pattern`)
,  CONSTRAINT `view_rules_ibfk_1` FOREIGN KEY (`view_id`) REFERENCES `views` (`view_id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `views` (
  `view_id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `view_name` tinytext NOT NULL
,  `view_vat` integer  NOT NULL DEFAULT 0
);
CREATE INDEX "idx_documents_doc_date" ON "documents" (`doc_date`);
CREATE INDEX "idx_view_entries_entry_id" ON "view_entries" (`entry_id`);
CREATE INDEX "idx_document_entries_entry_id" ON "document_entries" (`entry_id`);
CREATE INDEX "idx_account_entries_account_entries_ibfk_1" ON "account_entries" (`entry_id`);

CREATE TRIGGER AFTER INSERT ON entries
BEGIN
	INSERT INTO view_entries
	SELECT DISTINCT view_id, entry_id, null, entry_amount * (100-COALESCE(rule_vat, view_vat))/100 as amount, entry_amount * COALESCE(rule_vat, view_vat)/100 as vat
	FROM entries
	INNER JOIN view_rules
	ON (entry_text LIKE ('%' || rule_pattern || '%'))
	NATURAL JOIN views
	WHERE entry_id = NEW.entry_id;
END;

BEGIN;
DELETE FROM entry_classes;
INSERT INTO entry_classes (c_id,c_name) VALUES
(0, "(null)"),
(1000, "Indkomst"),
(1050, "A-indkomst"),
(1100, "B-indkomst (CVR 32104312)"),
(1150, "Ferietillæg"),
(1200, "Børne- og ungeydelse"),
(1250, "Fælles husstand"),
(2000, "Bolig"),
(2050, "Husleje / termin"),
(2100, "El"),
(2150, "Vand/Varme"),
(3000, "Cykler (Babboe eCurve)"),
(4000, "Forsikring"),
(4050, "Familie-/indboforsikring"),
(4100, "Livsforsikring"),
(4150, "Sygesikringen Danmark"),
(5000, "Hverdag"),
(5050, "Dagligvarer"),
(5100, "Restaurantbesøg / takeaway"),
(5150, "Læge, tandlæge, medicin"),
(5200, "Briller, kontaktlinser"),
(5250, "Personlig pleje (kosmetik, frisør, etc.)"),
(5300, "TV / streaming"),
(5350, "Mobiltelefon"),
-- (5400, "Internet"),
(5450, "Fritidsinteresser"),
(5500, "Transport"),
(5550, "Bøger"),
(5600, "Tøj / sko"),
(5650, "Boligudstyr"),
(5700, "Kæledyr (foder, pleje, etc.)"),
(5750, "Diverse (gaver, legetøg, etc.)"),
(6000, "Ferie"),
(6050, "Årskort / entré"),
(7000, "Andet"),
-- (7050, "Fagforening / A-kasse")
;
COMMIT;
