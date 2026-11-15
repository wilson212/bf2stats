--
-- Database: `bf2stats`
-- Version: 4.0.0
--

-- --------------------------------------------------------
-- Delete Tables/Views/Triggers First
-- --------------------------------------------------------

DROP TRIGGER IF EXISTS `_version_insert_time`;
DROP TRIGGER IF EXISTS `player_joined`;
DROP TRIGGER IF EXISTS `player_rank_change`;
DROP VIEW IF EXISTS `player_weapon_view`;
DROP VIEW IF EXISTS `round_history_view`;
DROP VIEW IF EXISTS `player_history_view`;
DROP VIEW IF EXISTS `player_awards_view`;
DROP VIEW IF EXISTS `player_map_top_players_view`;
DROP VIEW IF EXISTS `top_player_army_view`;
DROP VIEW IF EXISTS `top_player_kit_view`;
DROP VIEW IF EXISTS `top_player_vehicle_view`;
DROP VIEW IF EXISTS `top_player_weapon_view`;
DROP VIEW IF EXISTS `eligible_smoc_view`;
DROP VIEW IF EXISTS `eligible_general_view`;
DROP PROCEDURE IF EXISTS `generate_rising_star`;
DROP PROCEDURE IF EXISTS `create_player`;
DROP TABLE IF EXISTS `battlespy_message`;
DROP TABLE IF EXISTS `battlespy_report`;
DROP TABLE IF EXISTS `ip2nationcountries`;
DROP TABLE IF EXISTS `ip2nation`;
DROP TABLE IF EXISTS `eligible_smoc`;
DROP TABLE IF EXISTS `eligible_general`;
DROP TABLE IF EXISTS `risingstar`;
DROP TABLE IF EXISTS `player_weapon`;
DROP TABLE IF EXISTS `player_unlock`;
DROP TABLE IF EXISTS `player_vehicle`;
DROP TABLE IF EXISTS `player_rank_history`;
DROP TABLE IF EXISTS `player_history`;
DROP TABLE IF EXISTS `player_map`;
DROP TABLE IF EXISTS `player_kill`;
DROP TABLE IF EXISTS `player_kit`;
DROP TABLE IF EXISTS `player_army`;
DROP TABLE IF EXISTS `player_award`;
DROP TABLE IF EXISTS `player_kit_history`;
DROP TABLE IF EXISTS `player_kill_history`;
DROP TABLE IF EXISTS `player_vehicle_history`;
DROP TABLE IF EXISTS `player_weapon_history`;
DROP TABLE IF EXISTS `player_army_history`;
DROP TABLE IF EXISTS `player_round_history`;
DROP TABLE IF EXISTS `player`;
DROP TABLE IF EXISTS `weapon`;
DROP TABLE IF EXISTS `vehicle`;
DROP TABLE IF EXISTS `unlock_requirement`;
DROP TABLE IF EXISTS `unlock`;
DROP TABLE IF EXISTS `round_history`;
DROP TABLE IF EXISTS `round`;
DROP TABLE IF EXISTS `game_mod`;
DROP TABLE IF EXISTS `game_mode`;
DROP TABLE IF EXISTS `failed_snapshot`;
DROP TABLE IF EXISTS `server_auth_ip`;
DROP TABLE IF EXISTS `server`;
DROP TABLE IF EXISTS `stats_provider_auth_ip`;
DROP TABLE IF EXISTS `stats_provider`;
DROP TABLE IF EXISTS `rank`;
DROP TABLE IF EXISTS `mapinfo`;
DROP TABLE IF EXISTS `map`;
DROP TABLE IF EXISTS `kit`;
DROP TABLE IF EXISTS `award`;
DROP TABLE IF EXISTS `army`;
DROP TABLE IF EXISTS `_version`;

-- --------------------------------------------------------
-- Non-Player Tables First
-- --------------------------------------------------------

--
-- Table structure for table `_version`
--

CREATE TABLE `_version` (
  `updateid` INT UNSIGNED,
  `version` VARCHAR(10) NOT NULL,
  `time` INT UNSIGNED DEFAULT 0,
  PRIMARY KEY(`updateid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TRIGGER `_version_insert_time` BEFORE INSERT ON `_version`
FOR EACH ROW SET new.time = UNIX_TIMESTAMP();

--
-- Table structure for table `army`
--

CREATE TABLE `army` (
  `id` TINYINT UNSIGNED,
  `name` VARCHAR(32) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `award`
--

CREATE TABLE `award` (
  `id` MEDIUMINT UNSIGNED,              -- Award id, as defined in the medal_data.py
  `code` VARCHAR(6) UNIQUE NOT NULL,    -- Snapshot award short name, case sensitive
  `name` VARCHAR(64) NOT NULL,          -- Full name of the award, human readable
  `type` TINYINT NOT NULL,              -- 0 = ribbon, 1 = Badge, 2 = medal
  `backend` TINYINT NOT NULL DEFAULT 0, -- Bool: Awarded in the ASP snapshot processor?
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `game_mod`
--

CREATE TABLE `game_mod` (
  `id` TINYINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(24) UNIQUE NOT NULL,
  `longname` VARCHAR(48) NOT NULL,
  `authorized` TINYINT(1) NOT NULL DEFAULT 1, -- Indicates whether we allow this mod
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `game_mode`
--

CREATE TABLE `game_mode` (
  `id` TINYINT UNSIGNED,
  `name` VARCHAR(48) UNIQUE NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `kit`
--

CREATE TABLE `kit` (
  `id` TINYINT UNSIGNED,
  `name` VARCHAR(32) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `map`
--

CREATE TABLE `map` (
  `id` SMALLINT UNSIGNED,
  `name` VARCHAR(48) UNIQUE NOT NULL,
  `displayname` VARCHAR(48) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `kit`
--

CREATE TABLE `rank` (
  `id` TINYINT UNSIGNED,
  `name` VARCHAR(32) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `stats_provider`
--

CREATE TABLE `stats_provider` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,     -- Public provider ID
  `auth_id` MEDIUMINT UNSIGNED NOT NULL UNIQUE,       -- Private auth id
  `auth_token` VARCHAR(16) NOT NULL,                  -- Private auth token
  `name` VARCHAR(100) DEFAULT NULL,                   -- Provider name
  `authorized` TINYINT(1) NOT NULL DEFAULT 0,         -- Auth Token is allowed to post stats data to the ASP
  `plasma` TINYINT(1) NOT NULL DEFAULT 0,             -- Plasma all of their servers?
  `lastupdate` INT UNSIGNED NOT NULL DEFAULT 0,       -- Timestamp of the last ranked game posted
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `stats_provider_auth_ip`
--

CREATE TABLE `stats_provider_auth_ip` (
  `provider_id` SMALLINT UNSIGNED NOT NULL,           -- Provider ID
  `address` VARCHAR(50) NOT NULL DEFAULT '',          -- Authorized IP Address, length 46 + 4 for CIDR ranges
  PRIMARY KEY(`provider_id`, `address`),
  FOREIGN KEY(`provider_id`) REFERENCES stats_provider(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `server`
--

CREATE TABLE `server` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,     -- Public server ID, row id
  `provider_id` SMALLINT UNSIGNED NOT NULL,           -- Stats Provider ID
  `name` VARCHAR(100) DEFAULT NULL,                   -- Server display name
  `ip` VARCHAR(46) NOT NULL DEFAULT '',               -- IP Address
  `gameport` SMALLINT UNSIGNED DEFAULT 16567,
  `queryport` SMALLINT UNSIGNED NOT NULL DEFAULT 29900,
  `lastupdate` INT UNSIGNED NOT NULL DEFAULT 0,       -- Timestamp of the last ranked game posted
  `online` TINYINT(1) NOT NULL DEFAULT 0,             -- Is currently online, updated by MasterServer
  `lastseen` INT UNSIGNED NOT NULL DEFAULT 0,         -- Timestamp last seen by the MasterServer
  PRIMARY KEY(`id`),
  FOREIGN KEY(`provider_id`) REFERENCES stats_provider(`id`),
  CONSTRAINT `unique_ip_port` UNIQUE (`ip`, `queryport`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `round`
--

CREATE TABLE `round` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `map_id` SMALLINT UNSIGNED NOT NULL,
  `server_id` SMALLINT UNSIGNED NOT NULL,
  `mod_id` TINYINT UNSIGNED NOT NULL,
  `gamemode_id` TINYINT UNSIGNED NOT NULL,
  `team1_army_id` TINYINT UNSIGNED NOT NULL,      -- Team 1 Army ID
  `team2_army_id` TINYINT UNSIGNED NOT NULL,      -- Team 2 Army ID
  `time_start` INT UNSIGNED NOT NULL,
  `time_end` INT UNSIGNED NOT NULL,
  `time_imported` INT UNSIGNED NOT NULL,       -- Timestamp when the game was processed into database
  `winner` TINYINT NOT NULL,              -- Winning team (0 for none)
  `tickets1` SMALLINT UNSIGNED NOT NULL,  -- Remaining tickets on team1
  `tickets2` SMALLINT UNSIGNED NOT NULL,  -- Remaining tickets on team2
  PRIMARY KEY(`id`),
  FOREIGN KEY(`map_id`) REFERENCES map(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY(`server_id`) REFERENCES server(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY(`mod_id`) REFERENCES game_mod(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY(`gamemode_id`) REFERENCES game_mode(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY(`team1_army_id`) REFERENCES army(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY(`team2_army_id`) REFERENCES army(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Create Indexes for Snapshot's checking if a round has been processed
--

CREATE INDEX `idx_round_processed` ON round(`map_id`, `server_id`, `time_end`, `time_start`);

--
-- Table structure for table `unlock`
--

CREATE TABLE `unlock` (
  `id` SMALLINT UNSIGNED,
  `kit_id` TINYINT UNSIGNED NOT NULL,
  `name` VARCHAR(32) NOT NULL,
  `desc` VARCHAR(64) NOT NULL,
  PRIMARY KEY(`id`),
  FOREIGN KEY(`kit_id`) REFERENCES kit(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `id` TINYINT UNSIGNED,
  `name` VARCHAR(32) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `weapon`
--

CREATE TABLE `weapon` (
  `id` TINYINT UNSIGNED,
  `name` VARCHAR(32) NOT NULL,
  `is_explosive` TINYINT(1) NOT NULL DEFAULT 0,
  `is_equipment` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `failed_snapshot`
--

CREATE TABLE `failed_snapshot` (
  `id` INT UNSIGNED AUTO_INCREMENT,                      -- Row ID
  `server_id` SMALLINT UNSIGNED NOT NULL,
  `timestamp` INT UNSIGNED NOT NULL DEFAULT 0,  --
  `filename` VARCHAR(128),             --
  `reason`  VARCHAR(128),              --
  PRIMARY KEY(`id`),
  FOREIGN KEY(`server_id`) REFERENCES server(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- --------------------------------------------------------
-- Stats: Player Tables
-- --------------------------------------------------------
-- On composite key tables, Always place the player ID first!
--
-- Indexes on fields such as (a,b,c); the records are sorted first
-- on a, then b, then c. Most searches are by player ID, therefor
-- the player ID should come first in the index (includes primary
-- keys).
-- --------------------------------------------------------


--
-- Table structure for table `player`
--

CREATE TABLE `player` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) UNIQUE NOT NULL,
  `password` VARCHAR(32) NOT NULL,
  `email` VARCHAR(64) DEFAULT NULL,
  `country` CHAR(2) NOT NULL DEFAULT 'xx',
  `lastip` VARCHAR(15) NOT NULL DEFAULT '0.0.0.0',
  `joined` INT UNSIGNED NOT NULL DEFAULT 0,
  `lastonline` INT UNSIGNED NOT NULL DEFAULT 0,
  `time` INT UNSIGNED NOT NULL DEFAULT 0,
  `rounds` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `rank_id` TINYINT UNSIGNED NOT NULL,
  `score` MEDIUMINT NOT NULL DEFAULT 0,
  `cmdscore` MEDIUMINT NOT NULL DEFAULT 0,
  `skillscore` MEDIUMINT NOT NULL DEFAULT 0,
  `teamscore` MEDIUMINT NOT NULL DEFAULT 0,
  `kills` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `deaths` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `captures` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `neutralizes` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `captureassists` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `neutralizeassists` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `defends` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `damageassists` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `heals` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `revives` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `resupplies` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `repairs` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `targetassists` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `driverspecials` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `driverassists` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,  -- Not actually used!
  `teamkills` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `teamdamage` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `teamvehicledamage` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `suicides` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `killstreak` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `deathstreak` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `banned` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `kicked` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `cmdtime` INT UNSIGNED NOT NULL DEFAULT 0,
  `sqltime` INT UNSIGNED NOT NULL DEFAULT 0,
  `sqmtime` INT UNSIGNED NOT NULL DEFAULT 0,
  `lwtime` INT UNSIGNED NOT NULL DEFAULT 0,
  `timepara` MEDIUMINT NOT NULL DEFAULT 0,	-- Time in parachute
  `wins` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `losses` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `bestscore` SMALLINT UNSIGNED NOT NULL DEFAULT 0,  -- Best Round Score
  `chng` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,  -- Rank changed flag
  `decr` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,  -- Rank decreased flag
  `mode0` SMALLINT UNSIGNED NOT NULL DEFAULT 0,  -- Games played in Conquest
  `mode1` SMALLINT UNSIGNED NOT NULL DEFAULT 0,  -- Games played in Single Player
  `mode2` SMALLINT UNSIGNED NOT NULL DEFAULT 0,  -- Games played in Coop
  `permban` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `bantime` INT UNSIGNED NOT NULL DEFAULT 0,    -- Banned Timestamp
  `online` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY(`id`),
  FOREIGN KEY(`rank_id`) REFERENCES `rank`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2900000 DEFAULT CHARSET=utf8;

--
-- Create Indexes for BFHQ Leaderboard
--
CREATE INDEX `idx_player_score` ON player(`score`);
CREATE INDEX `idx_player_skillscore` ON player(`skillscore`);
CREATE INDEX `idx_player_teamscore` ON player(`teamscore`);
CREATE INDEX `idx_player_cmdscore` ON player(`cmdscore`);

--
-- Create `player` table triggers
--

delimiter $$

# Set player joined timestamp on insert
CREATE TRIGGER `player_joined` BEFORE INSERT ON `player`
  FOR EACH ROW BEGIN
    IF new.joined = 0 THEN
      SET new.joined = UNIX_TIMESTAMP();
    END IF;
  END $$

# Insert row into `player_rank_history` on rank change
CREATE TRIGGER `player_rank_change` AFTER UPDATE ON `player`
  FOR EACH ROW BEGIN
    IF new.rank_id != old.rank_id THEN
      REPLACE INTO player_rank_history VALUES (new.id, new.rank_id, old.rank_id, UNIX_TIMESTAMP());
    END IF;
  END $$

delimiter ;

--
-- Table structure for table `player_army`
--

CREATE TABLE `player_army` (
  `player_id` INT UNSIGNED NOT NULL,
  `army_id` TINYINT UNSIGNED NOT NULL,
  `time` INT UNSIGNED NOT NULL DEFAULT 0,
  `wins` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `losses` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `score` INT NOT NULL DEFAULT 0,
  `best` SMALLINT NOT NULL DEFAULT 0,   -- Best Round Score
  `worst` SMALLINT NOT NULL DEFAULT 0,  -- Worst Round Score
  `brnd` SMALLINT NOT NULL DEFAULT 0,   -- Number of times as Best round Player
  PRIMARY KEY(`player_id`,`army_id`),
  FOREIGN KEY(`army_id`) REFERENCES army(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `player_award`
--

CREATE TABLE `player_award` (
  `player_id` INT UNSIGNED NOT NULL,        -- Player ID
  `award_id` MEDIUMINT UNSIGNED NOT NULL,   -- Award ID
  `round_id` INT UNSIGNED NOT NULL,         -- The round this award was earned in
  `level` TINYINT UNSIGNED NOT NULL DEFAULT 1, -- Badges ONLY, 1 = bronze, 2 = silver, 3 = gold
  PRIMARY KEY(`player_id`, `award_id`, `round_id`, `level`),
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`award_id`) REFERENCES award(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY(`round_id`) REFERENCES round(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `player_kill`
--

CREATE TABLE `player_kill` (
  `attacker` INT UNSIGNED NOT NULL,
  `victim` INT UNSIGNED NOT NULL,
  `count` SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY(`attacker`,`victim`),
  FOREIGN KEY(`attacker`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`victim`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `player_map`
--

CREATE TABLE `player_map` (
  `player_id` INT UNSIGNED NOT NULL,
  `map_id` SMALLINT UNSIGNED NOT NULL,
  `score` INT NOT NULL DEFAULT 0,
  `time` INT UNSIGNED NOT NULL DEFAULT 0,
  `kills` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `deaths` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `games` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `wins` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `losses` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `bestscore` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `worstscore` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`player_id`,`map_id`),
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`map_id`) REFERENCES map(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `player_kit`
--

CREATE TABLE `player_kit` (
  `player_id` INT UNSIGNED NOT NULL,
  `kit_id` TINYINT UNSIGNED NOT NULL,
  `time` INT UNSIGNED NOT NULL DEFAULT 0,
  `score` INT NOT NULL DEFAULT 0,
  `kills` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `deaths` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`player_id`,`kit_id`),
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`kit_id`) REFERENCES kit(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Add index for the GetLeaderboard.aspx
--
CREATE INDEX `idx_player_kit_id_kills_time` ON player_kit(`kit_id`, `kills`, `time`);

--
-- Table structure for table `player_round_history`
--

CREATE TABLE `player_round_history` (
  `player_id` INT UNSIGNED NOT NULL,
  `round_id` INT UNSIGNED NOT NULL,
  `army_id` TINYINT UNSIGNED NOT NULL,
  `time` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `rank_id` TINYINT UNSIGNED NOT NULL, -- Rank at the end of the round, post ASP corrections!
  `score` SMALLINT NOT NULL DEFAULT 0,
  `cmdscore` SMALLINT NOT NULL DEFAULT 0,
  `skillscore` SMALLINT NOT NULL DEFAULT 0,
  `teamscore` SMALLINT NOT NULL DEFAULT 0,
  `kills` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `deaths` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `captures` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `neutralizes` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `captureassists` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `neutralizeassists` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `defends` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `heals` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `revives` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `resupplies` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `repairs` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `damageassists` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `targetassists` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `driverspecials` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  -- `driverassists` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  -- `passengerassists` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `teamkills` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `teamdamage` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `teamvehicledamage` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `suicides` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `killstreak` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `deathstreak` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `cmdtime` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `sqltime` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `sqmtime` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `lwtime` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `timepara` SMALLINT NOT NULL DEFAULT 0,	-- Time in parachute
  `completed` TINYINT(1) NOT NULL DEFAULT 0, -- Completed round?
  `banned` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `kicked` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`player_id`, `round_id`),
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`round_id`) REFERENCES round(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`army_id`) REFERENCES army(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY(`rank_id`) REFERENCES `rank`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- CREATE INDEX `idx_player_history_timestamp_score_pid` ON player_round_history(`timestamp`, `score`, `player_id`);

--
-- Table structure for table `player_rank_history`
--

CREATE TABLE `player_rank_history` (
  `player_id` INT UNSIGNED NOT NULL,
  `to_rank_id` TINYINT UNSIGNED NOT NULL,
  `from_rank_id` TINYINT UNSIGNED NOT NULL,
  `timestamp` INT UNSIGNED NOT NULL,
  PRIMARY KEY(`player_id`,`timestamp`),
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`to_rank_id`) REFERENCES `rank`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY(`from_rank_id`) REFERENCES `rank`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET utf8;

--
-- Table structure for table `player_unlock`
--

CREATE TABLE `player_unlock` (
  `player_id` INT UNSIGNED NOT NULL,
  `unlock_id` SMALLINT UNSIGNED NOT NULL,
  `timestamp` INT UNSIGNED NOT NULL,
  PRIMARY KEY(`player_id`,`unlock_id`),
  FOREIGN KEY(`player_id`) REFERENCES `player`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`unlock_id`) REFERENCES `unlock`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET utf8;

--
-- Table structure for table `player_vehicle`
--

CREATE TABLE `player_vehicle` (
  `player_id` INT UNSIGNED NOT NULL,
  `vehicle_id` TINYINT UNSIGNED NOT NULL,
  `time` INT UNSIGNED NOT NULL DEFAULT 0,
  `score` INT NOT NULL DEFAULT 0,
  `kills` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `deaths` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `roadkills` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`player_id`,`vehicle_id`),
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`vehicle_id`) REFERENCES vehicle(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Add index for the GetLeaderboard.aspx
--
CREATE INDEX `idx_player_vehicle_id_kills_time` ON player_vehicle(`vehicle_id`, `kills`, `time`);

--
-- Table structure for table `weapons`
--

CREATE TABLE `player_weapon` (
  `player_id` INT UNSIGNED NOT NULL,
  `weapon_id` TINYINT UNSIGNED NOT NULL,
  `time` INT UNSIGNED NOT NULL DEFAULT 0,
  `score` INT NOT NULL DEFAULT 0,
  `kills` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `deaths` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `fired` INT UNSIGNED NOT NULL DEFAULT 0,
  `hits` INT UNSIGNED NOT NULL DEFAULT 0,
  `deployed` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`player_id`,`weapon_id`),
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`weapon_id`) REFERENCES weapon(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Add index for the GetLeaderboard.aspx
--
CREATE INDEX `idx_player_weapon_id_kills_time` ON player_weapon(`weapon_id`, `kills`, `time`);

--
-- Table structure for table `player_army_history`
--

CREATE TABLE `player_army_history` (
  `player_id` INT UNSIGNED NOT NULL,
  `round_id` INT UNSIGNED NOT NULL,
  `army_id` TINYINT UNSIGNED NOT NULL,
  `time` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`player_id`,`round_id`,`army_id`),
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`round_id`) REFERENCES round(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`army_id`) REFERENCES army(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `player_kit_history`
--

CREATE TABLE `player_kit_history` (
  `player_id` INT UNSIGNED NOT NULL,
  `round_id` INT UNSIGNED NOT NULL,
  `kit_id` TINYINT UNSIGNED NOT NULL,
  `time` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `score` SMALLINT NOT NULL DEFAULT 0,
  `kills` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `deaths` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`player_id`,`round_id`,`kit_id`),
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`round_id`) REFERENCES round(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`kit_id`) REFERENCES kit(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `player_kit_history`
--

CREATE TABLE `player_kill_history` (
  `round_id` INT UNSIGNED NOT NULL,
  `attacker` INT UNSIGNED NOT NULL,
  `victim` INT UNSIGNED NOT NULL,
  `count` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`round_id`,`attacker`,`victim`),
  FOREIGN KEY(`round_id`) REFERENCES round(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`attacker`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`victim`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `player_vehicle_history`
--

CREATE TABLE `player_vehicle_history` (
  `player_id` INT UNSIGNED NOT NULL,
  `round_id` INT UNSIGNED NOT NULL,
  `vehicle_id` TINYINT UNSIGNED NOT NULL,
  `time` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `score` SMALLINT NOT NULL DEFAULT 0,
  `kills` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `deaths` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `roadkills` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`player_id`,`round_id`,`vehicle_id`),
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`round_id`) REFERENCES round(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`vehicle_id`) REFERENCES vehicle(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `player_weapon_history`
--

CREATE TABLE `player_weapon_history` (
  `player_id` INT UNSIGNED NOT NULL,
  `round_id` INT UNSIGNED NOT NULL,
  `weapon_id` TINYINT UNSIGNED NOT NULL,
  `time` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `score` SMALLINT NOT NULL DEFAULT 0,
  `kills` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `deaths` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `fired` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `hits` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `deployed` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`player_id`,`round_id`,`weapon_id`),
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`round_id`) REFERENCES round(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`weapon_id`) REFERENCES weapon(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `risingstar`
--
CREATE TABLE `risingstar` (
  `pos` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `player_id` INT UNSIGNED NOT NULL,
  `weeklyscore` INT UNSIGNED NOT NULL,
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX `idx_risingstar_pid` ON risingstar(`player_id`);

--
-- Table structure for table `eligible_smoc`
--

CREATE TABLE `eligible_smoc` (
  `player_id` INT UNSIGNED PRIMARY KEY,
  `global_score` INT UNSIGNED NOT NULL,
  `rank_score` INT UNSIGNED NOT NULL,
  `rank_time` INT UNSIGNED NOT NULL,
  `rank_games` SMALLINT UNSIGNED NOT NULL,
  `spm` INT UNSIGNED NOT NULL,
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `eligible_general`
--

CREATE TABLE `eligible_general` (
  `player_id` INT UNSIGNED PRIMARY KEY,
  `global_score` INT UNSIGNED NOT NULL,
  `rank_score` INT UNSIGNED NOT NULL,
  `rank_time` INT UNSIGNED NOT NULL,
  `rank_games` SMALLINT UNSIGNED NOT NULL,
  `spm` INT UNSIGNED NOT NULL,
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `battlespy_report`
--

CREATE TABLE `battlespy_report` (
  `id` INT UNSIGNED AUTO_INCREMENT,
  `server_id` SMALLINT UNSIGNED NOT NULL,
  `round_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY(`id`),
  FOREIGN KEY(`server_id`) REFERENCES server(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`round_id`) REFERENCES round(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `battlespy_message`
--

CREATE TABLE `battlespy_message` (
  `id` INT UNSIGNED AUTO_INCREMENT,
  `report_id` INT UNSIGNED NOT NULL,      -- Report ID
  `player_id` INT UNSIGNED NOT NULL,      -- Player ID
  `flag` MEDIUMINT UNSIGNED NOT NULL,
  `severity` TINYINT UNSIGNED NOT NULL,
  `message` VARCHAR(128),
  PRIMARY KEY(`id`),
  FOREIGN KEY(`report_id`) REFERENCES battlespy_report(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`player_id`) REFERENCES player(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
-- Create Views
-- --------------------------------------------------------

CREATE OR REPLACE VIEW `player_weapon_view` AS
  SELECT `weapon_id`, `player_id`, `time`, `kills`, `deaths`, `fired`, `hits`, COALESCE((`hits` * 1.0) / `fired`, 0) AS `accuracy`
  FROM `player_weapon`;

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

CREATE OR REPLACE VIEW `player_awards_view` AS
  SELECT a.award_id AS `id`, a.player_id AS `pid`, MAX(r.time_end) AS `earned`, MIN(r.time_end) AS `first`, COUNT(`level`) AS `level`
  FROM player_award AS a
    LEFT JOIN round AS r ON a.round_id = r.id
  GROUP BY a.player_id, a.award_id;

CREATE OR REPLACE VIEW `round_history_view` AS
  SELECT h.id AS `id`, mi.displayname AS `map`, h.time_end AS `round_end`, h.team1_army_id AS `team1`,
         h.team2_army_id AS `team2`, h.winner AS `winner`, s.name AS `server_name`, s.id AS `server_id`, s.provider_id,
         GREATEST(h.tickets1, h.tickets2) AS `tickets`,
         (SELECT COUNT(*) FROM player_round_history AS prh WHERE prh.round_id = h.id) AS `players`
  FROM `round` AS h
    LEFT JOIN map AS mi ON h.map_id = mi.id
    LEFT JOIN `server` AS s ON h.server_id = s.id;

CREATE OR REPLACE VIEW `player_history_view` AS
  SELECT ph.*, mi.name AS mapname, mi.displayname AS map_display_name, server.name AS name, rh.time_end
  FROM player_round_history AS ph
    LEFT JOIN round AS rh ON ph.round_id = rh.id
    LEFT JOIN server ON rh.server_id = server.id
    LEFT JOIN map AS mi ON rh.map_id = mi.id;

CREATE OR REPLACE VIEW `player_map_top_players_view` AS
  SELECT m.map_id, m.player_id, m.time, m.score, m.kills, m.deaths, m.games, p.name, p.country, p.rank_id
  FROM player_map AS m
    JOIN player AS p on m.player_id = p.id;

CREATE OR REPLACE VIEW `rising_star_view` AS
  SELECT pos, player_id, weeklyscore, p.name, p.rank_id, p.country, p.joined, p.time
  FROM risingstar AS r
    LEFT JOIN player AS p ON player_id = p.id;

CREATE OR REPLACE VIEW `eligible_smoc_view` AS
  SELECT es.player_id AS `player_id`, es.global_score AS `global_score`, es.rank_games AS `rank_games`,
         es.rank_score AS `rank_score`,  es.rank_time AS `rank_time`, es.spm AS `spm`, p.rank_id AS `rank_id`, p.name AS `name`,
         p.lastonline AS `lastonline`, p.permban AS `banned`, p.country AS `country`, r.weeklyscore AS `weekly_score`,
         (CASE WHEN p.password IS NOT NULL AND p.password <> '' THEN 0 ELSE 1 END) AS `is_bot`
  FROM eligible_smoc AS es
    LEFT JOIN player AS p on es.player_id = p.id
    LEFT JOIN risingstar AS r on p.id = r.player_id;

CREATE OR REPLACE VIEW `eligible_general_view` AS
  SELECT es.player_id AS `player_id`, es.global_score AS `global_score`, es.rank_games AS `rank_games`,
         es.rank_score AS `rank_score`,  es.rank_time AS `rank_time`, es.spm AS `spm`, p.rank_id AS `rank_id`, p.name AS `name`,
         p.lastonline AS `lastonline`, p.permban AS `banned`, p.country AS `country`, r.weeklyscore AS `weekly_score`,
         (CASE WHEN p.password IS NOT NULL AND p.password <> '' THEN 0 ELSE 1 END) AS `is_bot`
  FROM eligible_general AS es
    LEFT JOIN player AS p on es.player_id = p.id
    LEFT JOIN risingstar AS r on p.id = r.player_id;

-- --------------------------------------------------------
-- Create Procedures
-- --------------------------------------------------------
delimiter $$

CREATE PROCEDURE `create_player`(
  IN `playerName` VARCHAR(32),
  IN `playerPassword` VARCHAR(32), -- MD5 Hash
  IN `countryCode` VARCHAR(2),
  IN `ipAddress` VARCHAR(46),
  OUT `pid` INT
)
  BEGIN
    INSERT INTO player(`id`, `name`, `password`, `country`, `lastip`, `rank_id`)
      VALUES(pid, playerName, playerPassword, countryCode, ipAddress, 0);
    SELECT pid;
  END $$

delimiter ;
