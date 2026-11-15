--
-- Add new columns for table `player_kit` and `player_kit_history`
--
ALTER TABLE `player_kit` ADD COLUMN `score` INT NOT NULL DEFAULT 0 AFTER `time`;
ALTER TABLE `player_kit_history` ADD COLUMN `score` SMALLINT NOT NULL DEFAULT 0 AFTER `time`;

--
-- Add new columns for table `player_vehicle` and `player_vehicle_history`
--
ALTER TABLE `player_vehicle` ADD COLUMN `score` INT NOT NULL DEFAULT 0 AFTER `time`;
ALTER TABLE `player_vehicle_history` ADD COLUMN `score` SMALLINT NOT NULL DEFAULT 0 AFTER `time`;

--
-- Add new columns for table `player_weapon` and `player_weapon_history`
--
ALTER TABLE `player_weapon` ADD COLUMN `score` INT NOT NULL DEFAULT 0 AFTER `time`;
ALTER TABLE `player_weapon_history` ADD COLUMN `score` SMALLINT NOT NULL DEFAULT 0 AFTER `time`;

--
-- Replace Views, since MySQL cache's these
--
CREATE OR REPLACE VIEW `top_player_kit_view` AS
  SELECT pk.*, p.name, p.country, p.rank_id, COALESCE((pk.kills * 1.0) / GREATEST(pk.deaths, 1), 0) AS `ratio`
  FROM `player_kit` AS pk
    JOIN player AS p on pk.player_id = p.id;

CREATE OR REPLACE VIEW `top_player_vehicle_view` AS
  SELECT pk.*, p.name, p.country, p.rank_id, COALESCE((pk.kills * 1.0) / GREATEST(pk.deaths, 1), 0) AS `ratio`
  FROM `player_vehicle` AS pk
    JOIN player AS p on pk.player_id = p.id;

--
-- Always update version table!!!
--
INSERT INTO `_version`(`updateid`, `version`) VALUES (30060, '3.0.6');