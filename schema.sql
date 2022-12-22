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
,  UNIQUE (`entry_date`,`entry_amount`,`entry_balance`)
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
	ON (entry_text LIKE CONCAT('%', rule_pattern, '%'))
	NATURAL JOIN views
	WHERE entry_id = NEW.entry_id;
END;
