--
-- Drop added columns in 3.0.2
--
ALTER TABLE `player_map` DROP COLUMN `score`;
ALTER TABLE `player_map` DROP COLUMN `kills`;
ALTER TABLE `player_map` DROP COLUMN `deaths`;
ALTER TABLE `player_map` DROP COLUMN `games`;

--
-- Drop added view in 3.0.2
--
DROP VIEW IF EXISTS `player_map_top_players_view`;

-- Always delete record from version table!!!
--
DELETE FROM `_version` WHERE updateid = 30020;