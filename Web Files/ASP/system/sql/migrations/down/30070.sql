--
-- Delete table added
--
DROP TABLE IF EXISTS `unlock_requirement`;

--
-- Table structure adjustment for table `player_unlock`
--
ALTER TABLE `player_unlock` DROP COLUMN `timestamp`;

--
-- Always delete record from version table!!!
--
DELETE FROM `_version` WHERE updateid = 30080;