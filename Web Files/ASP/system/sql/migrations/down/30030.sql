--
-- Drop added columns in 3.0.4
--
ALTER TABLE `stats_provider` DROP COLUMN `lastupdate`;
ALTER TABLE `server` DROP COLUMN  `lastseen`;

-- Always delete record from version table!!!
--
DELETE FROM `_version` WHERE updateid = 30040;