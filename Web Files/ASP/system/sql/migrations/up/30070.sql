--
-- Replace Views, since MySQL cache's these
--
DROP PROCEDURE IF EXISTS `generate_rising_star`;

--
-- Replace Views, since MySQL cache's these
--
CREATE OR REPLACE VIEW `rising_star_view` AS
  SELECT pos, player_id, weeklyscore, p.name, p.rank_id, p.country, p.joined, p.time
  FROM risingstar AS r
    LEFT JOIN player AS p ON player_id = p.id;

--
-- Always update version table!!!
--
INSERT INTO `_version`(`updateid`, `version`) VALUES (30070, '3.0.7');