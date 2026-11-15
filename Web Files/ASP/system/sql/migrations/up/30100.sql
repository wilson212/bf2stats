--
-- Drop existing table
--
DROP TABLE IF EXISTS `eligible_smoc`;
DROP VIEW IF EXISTS `eligible_smoc_view`;
DROP TABLE IF EXISTS `eligible_general`;
DROP VIEW IF EXISTS `eligible_general_view`;

--
-- Table structure for table `eligible_smoc`
--

CREATE TABLE `eligible_smoc` (
  `player_id` INT UNSIGNED PRIMARY KEY,
  `global_score` INT UNSIGNED NOT NULL,
  `rank_score` INT UNSIGNED NOT NULL,
  `rank_time` INT UNSIGNED NOT NULL,
  `rank_games` SMALLINT UNSIGNED NOT NULL,
  `spm` INT UNSIGNED NOT NULL,
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `eligible_general`
--

CREATE TABLE `eligible_general` (
  `player_id` INT UNSIGNED PRIMARY KEY,
  `global_score` INT UNSIGNED NOT NULL,
  `rank_score` INT UNSIGNED NOT NULL,
  `rank_time` INT UNSIGNED NOT NULL,
  `rank_games` SMALLINT UNSIGNED NOT NULL,
  `spm` INT UNSIGNED NOT NULL,
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Create views
--
CREATE OR REPLACE VIEW `eligible_smoc_view` AS
  SELECT es.player_id AS `player_id`, es.global_score AS `global_score`, es.rank_games AS `rank_games`,
         es.rank_score AS `rank_score`,  es.rank_time AS `rank_time`, es.spm AS `spm`, p.rank_id AS `rank_id`, p.name AS `name`,
         p.lastonline AS `lastonline`, p.permban AS `banned`, p.country AS `country`, r.weeklyscore AS `weekly_score`,
         (CASE WHEN p.password IS NOT NULL AND p.password <> '' THEN 0 ELSE 1 END) AS `is_bot`
  FROM eligible_smoc AS es
    LEFT JOIN player AS p on es.player_id = p.id
    LEFT JOIN risingstar AS r on p.id = r.player_id;

CREATE OR REPLACE VIEW `eligible_general_view` AS
  SELECT es.player_id AS `player_id`, es.global_score AS `global_score`, es.rank_games AS `rank_games`,
    es.rank_score AS `rank_score`,  es.rank_time AS `rank_time`, es.spm AS `spm`, p.rank_id AS `rank_id`, p.name AS `name`,
    p.lastonline AS `lastonline`, p.permban AS `banned`, p.country AS `country`, r.weeklyscore AS `weekly_score`,
    (CASE WHEN p.password IS NOT NULL AND p.password <> '' THEN 0 ELSE 1 END) AS `is_bot`
  FROM eligible_general AS es
    LEFT JOIN player AS p on es.player_id = p.id
    LEFT JOIN risingstar AS r on p.id = r.player_id;

--
-- Always update version table!!!
--
INSERT INTO `_version`(`updateid`, `version`) VALUES (30100, '3.1.0');