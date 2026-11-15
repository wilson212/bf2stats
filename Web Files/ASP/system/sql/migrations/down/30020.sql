--
-- Remove Views
--

DROP VIEW IF EXISTS `top_player_army_view`;
DROP VIEW IF EXISTS `top_player_kit_view`;
DROP VIEW IF EXISTS `top_player_vehicle_view`;
DROP VIEW IF EXISTS `top_player_weapon_view`;

-- Always delete record from version table!!!
--
DELETE FROM `_version` WHERE updateid = 30030;