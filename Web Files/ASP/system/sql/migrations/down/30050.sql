--
-- Drop new columns for table `player_kit` and `player_kit_history`
--
ALTER TABLE `player_kit` DROP COLUMN `score`;
ALTER TABLE `player_kit_history` DROP COLUMN `score`;

--
-- Drop new columns for table `player_vehicle` and `player_vehicle_history`
--
ALTER TABLE `player_vehicle` DROP COLUMN `score`;
ALTER TABLE `player_vehicle_history` DROP COLUMN `score`;

--
-- Drop new columns for table `player_weapon` and `player_weapon_history`
--
ALTER TABLE `player_weapon` DROP COLUMN `score`;
ALTER TABLE `player_weapon_history` DROP COLUMN `score`;

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
-- Always delete record from version table!!!
--
DELETE FROM `_version` WHERE updateid = 30060;