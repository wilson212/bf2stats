--
-- Adjust View
--
CREATE OR REPLACE VIEW `round_history_view` AS
  SELECT h.id AS `id`, mi.displayname AS `map`, h.time_end AS `round_end`, h.team1_army_id AS `team1`,
         h.team2_army_id AS `team2`, h.winner AS `winner`, s.name AS `server_name`, s.id AS `server_id`,
         GREATEST(h.tickets1, h.tickets2) AS `tickets`,
         (SELECT COUNT(*) FROM player_round_history AS prh WHERE prh.round_id = h.id) AS `players`
  FROM `round` AS h
    LEFT JOIN map AS mi ON h.map_id = mi.id
    LEFT JOIN `server` AS s ON h.server_id = s.id;


-- Always delete record from version table!!!
--
DELETE FROM `_version` WHERE updateid = 30050;