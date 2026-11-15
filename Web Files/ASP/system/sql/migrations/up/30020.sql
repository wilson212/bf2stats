--
-- Add new columns for table `player_map`
--
ALTER TABLE `player_map` ADD COLUMN `score` INT NOT NULL DEFAULT 0 AFTER `map_id`;
ALTER TABLE `player_map` ADD COLUMN `kills` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER `time`;
ALTER TABLE `player_map` ADD COLUMN `deaths` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER `kills`;
ALTER TABLE `player_map` ADD COLUMN `games` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER `deaths`;

--
-- Add new columns for table `player_map`
--
CREATE OR REPLACE VIEW `player_map_top_players_view` AS
  SELECT m.map_id, m.player_id, m.time, m.score, m.kills, m.deaths, m.games, p.name, p.country, p.rank_id
  FROM player_map AS m
    JOIN player AS p on m.player_id = p.id;

--
-- Update Procedure
--
DROP PROCEDURE IF EXISTS `generate_rising_star`;

delimiter $$

CREATE PROCEDURE `generate_rising_star`()
  BEGIN
    -- Get timestamp from a week ago
    DECLARE lastweek INT;
    SET lastweek = UNIX_TIMESTAMP() - (86400 * 7);

    -- Delete all rising star rows
    DELETE FROM `risingstar`;
    ALTER TABLE `risingstar` AUTO_INCREMENT = 1;

    -- Fill Rising Star Table
    INSERT INTO `risingstar`(player_id, weeklyscore)
      SELECT player_id, sum(h.score) AS weeklyscore
      FROM player_round_history AS h
        JOIN round r ON h.round_id = r.id
      WHERE r.time_end >= lastweek AND score > 0
      GROUP BY player_id
      ORDER BY weeklyscore DESC;
  END $$

--
-- Create Procedure to update the new added columns
--
CREATE PROCEDURE build_player_map_table()
  BEGIN
    -- declare variables
    DECLARE finished INTEGER DEFAULT 0;
    DECLARE mapId SMALLINT UNSIGNED DEFAULT 0;
    DECLARE playerId INTEGER UNSIGNED DEFAULT 0;
    DECLARE playerScore INTEGER DEFAULT 0;
    DECLARE playerKills MEDIUMINT UNSIGNED DEFAULT 0;
    DECLARE playerDeaths MEDIUMINT UNSIGNED DEFAULT 0;
    DECLARE playerGames SMALLINT UNSIGNED DEFAULT 0;

    -- declare cursor for player_map table
    DEClARE player_map_cursor CURSOR FOR
      SELECT
        m.map_id,
        p.id,
        SUM(h.score) AS `score`,
        SUM(h.kills) AS `kills`,
        SUM(h.deaths) AS `deaths`,
        COUNT(h.player_id) AS `games`
      FROM player_map AS m
        JOIN player AS p on m.player_id = p.id
        JOIN round AS r ON r.map_id = m.map_id
        JOIN player_round_history AS h on r.id = h.round_id AND h.player_id = m.player_id
      GROUP BY h.player_id, m.map_id;

    -- declare NOT FOUND handler
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished = 1;

    OPEN player_map_cursor;

    get_row: LOOP

      FETCH player_map_cursor INTO mapId, playerId, playerScore, playerKills, playerDeaths, playerGames;

      -- Check for finish
      IF finished = 1 THEN
        LEAVE get_row;
      END IF;

      -- Update row
      UPDATE player_map
        SET score = playerScore, kills = playerKills, deaths = playerDeaths, games = playerGames
      WHERE player_id = playerId AND map_id = mapId;

    END LOOP get_row;

    CLOSE player_map_cursor;

  END $$

delimiter ;

CALL build_player_map_table();
DROP PROCEDURE build_player_map_table;

--
-- Always update version table!!!
--
INSERT INTO `_version`(`updateid`, `version`) VALUES (30020, '3.0.2');