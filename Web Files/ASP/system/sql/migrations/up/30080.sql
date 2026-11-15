--
-- Drop existing table
--
DROP TABLE IF EXISTS `unlock_requirement`;

--
-- Table structure for table `unlock_requirement`
--

CREATE TABLE `unlock_requirement` (
  `parent_id` SMALLINT UNSIGNED,
  `child_id` SMALLINT UNSIGNED,
  PRIMARY KEY(`parent_id`, `child_id`),
  FOREIGN KEY(`parent_id`) REFERENCES `unlock`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`child_id`) REFERENCES `unlock`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure adjustment for table `player_unlock`
--
ALTER TABLE `player_unlock` ADD COLUMN `timestamp` INT NOT NULL DEFAULT 0;

--
-- Insert default unlocks data, in case its missing
--
INSERT IGNORE INTO `unlock` VALUES (11, 0, 'Chsht_protecta', 'Protecta shotgun with slugs');
INSERT IGNORE INTO `unlock` VALUES (22, 1, 'Usrif_g3a3', 'H&K G3');
INSERT IGNORE INTO `unlock` VALUES (33, 2, 'USSHT_Jackhammer', 'Jackhammer shotgun');
INSERT IGNORE INTO `unlock` VALUES (44, 3, 'Usrif_sa80', 'SA-80');
INSERT IGNORE INTO `unlock` VALUES (55, 4, 'Usrif_g36c', 'G36C');
INSERT IGNORE INTO `unlock` VALUES (66, 5, 'RULMG_PKM', 'PKM');
INSERT IGNORE INTO `unlock` VALUES (77, 6, 'USSNI_M95_Barret', 'Barret M82A2 (.50 cal rifle)');
INSERT IGNORE INTO `unlock` VALUES (88, 1, 'sasrif_fn2000', 'FN2000');
INSERT IGNORE INTO `unlock` VALUES (99, 2, 'sasrif_mp7', 'MP-7');
INSERT IGNORE INTO `unlock` VALUES (111, 3, 'sasrif_g36e', 'G36E');
INSERT IGNORE INTO `unlock` VALUES (222, 4, 'usrif_fnscarl', 'FN SCAR - L');
INSERT IGNORE INTO `unlock` VALUES (333, 5, 'sasrif_mg36', 'MG36');
INSERT IGNORE INTO `unlock` VALUES (444, 0, 'eurif_fnp90', 'P90');
INSERT IGNORE INTO `unlock` VALUES (555, 6, 'gbrif_l96a1', 'L96A1');

--
-- Insert default
--
INSERT INTO `unlock_requirement`(`parent_id`, `child_id`) VALUES (22, 88);
INSERT INTO `unlock_requirement`(`parent_id`, `child_id`) VALUES (33, 99);
INSERT INTO `unlock_requirement`(`parent_id`, `child_id`) VALUES (44, 111);
INSERT INTO `unlock_requirement`(`parent_id`, `child_id`) VALUES (55, 222);
INSERT INTO `unlock_requirement`(`parent_id`, `child_id`) VALUES (66, 333);
INSERT INTO `unlock_requirement`(`parent_id`, `child_id`) VALUES (11, 444);
INSERT INTO `unlock_requirement`(`parent_id`, `child_id`) VALUES (77, 555);

--
-- Always update version table!!!
--
INSERT INTO `_version`(`updateid`, `version`) VALUES (30080, '3.0.8');