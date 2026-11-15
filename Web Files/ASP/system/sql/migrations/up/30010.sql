--
-- Table structure for table `failed_snapshot`
--

CREATE TABLE IF NOT EXISTS `failed_snapshot` (
  `id` INT UNSIGNED AUTO_INCREMENT,                      -- Row ID
  `server_id` SMALLINT UNSIGNED NOT NULL,
  `timestamp` INT UNSIGNED NOT NULL DEFAULT 0,  -- Snapshot award short name, case sensitive
  `filename` VARCHAR(128),             -- Full name of the award, human readable
  `reason`  VARCHAR(128),              -- 0 = ribbon, 1 = Badge, 2 = medal
  PRIMARY KEY(`id`),
  FOREIGN KEY(`server_id`) REFERENCES server(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Always update version table!!!
--
INSERT INTO `_version`(`updateid`, `version`) VALUES (30010, '3.0.1');