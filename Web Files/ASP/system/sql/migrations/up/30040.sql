--
-- Add new columns for table `stats_provider`
--
ALTER TABLE `stats_provider` ADD COLUMN `lastupdate` INT UNSIGNED NOT NULL DEFAULT 0;

--
-- Add new columns for table `server`
--
ALTER TABLE `server` CHANGE `lastupdate` `lastseen` INT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `server` ADD COLUMN `lastupdate` INT UNSIGNED NOT NULL DEFAULT 0;

--
-- Always update version table!!!
--
INSERT INTO `_version`(`updateid`, `version`) VALUES (30040, '3.0.4');