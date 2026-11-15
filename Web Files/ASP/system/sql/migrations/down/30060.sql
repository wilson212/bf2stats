-- --------------------------------------------------------
-- Drop Added View
-- --------------------------------------------------------
DROP VIEW IF EXISTS `rising_star_view`;

-- --------------------------------------------------------
-- Re-Create Procedure
-- --------------------------------------------------------
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

delimiter ;

--
-- Always delete record from version table!!!
--
DELETE FROM `_version` WHERE updateid = 30070;
