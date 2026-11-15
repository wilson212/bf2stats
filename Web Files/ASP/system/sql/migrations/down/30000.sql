--
-- Drop added tables in 3.0.1
--
DROP TABLE IF EXISTS `failed_snapshot`;

--
-- Always delete record from version table!!!
--
DELETE FROM `_version` WHERE updateid = 30010;