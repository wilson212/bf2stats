--
-- Drop existing tables
--
DROP TABLE IF EXISTS `eligible_smoc`;
DROP VIEW IF EXISTS `eligible_smoc_view`;
DROP TABLE IF EXISTS `eligible_general`;
DROP VIEW IF EXISTS `eligible_general_view`;

--
-- Always delete record from version table!!!
--
DELETE FROM `_version` WHERE updateid = 30100;