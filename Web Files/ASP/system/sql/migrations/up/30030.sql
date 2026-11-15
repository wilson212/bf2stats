--
-- Add new views
--

CREATE OR REPLACE VIEW `top_player_army_view` AS
  SELECT pk.*, p.name, p.country, p.rank_id
  FROM `player_army` AS pk
    JOIN player AS p on pk.player_id = p.id;

CREATE OR REPLACE VIEW `top_player_weapon_view` AS
  SELECT pk.*, p.name, p.country, p.rank_id, COALESCE((`hits` * 1.0) / GREATEST(`fired`, 1), 0) AS `accuracy`,
    COALESCE((pk.kills * 1.0) / GREATEST(pk.deaths, 1), 0) AS `ratio`
  FROM `player_weapon` AS pk
    JOIN player AS p on pk.player_id = p.id;

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
INSERT INTO `_version`(`updateid`, `version`) VALUES (30030, '3.0.3');