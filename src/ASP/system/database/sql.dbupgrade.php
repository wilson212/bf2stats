<?php

$sqlupgrade = array();

// <Text String>, <DB Version Introduced>, <Query String>

$sqlupgrade[] = array('Clear Version Table', CODE_VER,
	"DELETE FROM `_version`;");
	
$sqlupgrade[] = array('Alter Army Table', '1.3.0',
	"ALTER TABLE  `army`
		ADD COLUMN `time9` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `win9` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `loss9` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `score9` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `best9` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `worst9` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `brnd9` int(10) unsigned NOT NULL default '0';");		
		
$sqlupgrade[] = array('Create Servers Table', '1.3.2',	
	"CREATE TABLE `servers` (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`ip` varchar(15) NOT NULL default '',
		`prefix` varchar(30) NOT NULL default '',
		`name` varchar(100) default NULL,
		`port` int(6) unsigned default '0',
		`queryport` int(6) unsigned NOT NULL default '0',
		`lastupdate` datetime NOT NULL default '0000-00-00 00:00:00',
		PRIMARY KEY  (`id`),
		UNIQUE KEY `ip-prefix-unq` (`ip`,`prefix`)
	) TYPE=MyISAM;");

$sqlupgrade[] = array('Alter Player Table (Add Mode Data)', '1.3.2',
	"ALTER TABLE  `player`
		ADD COLUMN `mode0` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `mode1` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `mode2` int(10) unsigned NOT NULL default '0';");

$sqlupgrade[] = array('Alter Player Table (Add Indexes)', '1.3.4',	
	"ALTER TABLE  `player`
		ADD KEY `ScoreIdx` (`score`),
		ADD KEY `CmdScoreIdx` (`cmdscore`),
		ADD KEY `TeamScoreIdx` (`teamscore`),
		ADD KEY `KillsIdx` (`kills`);");

$sqlupgrade[] = array('Create Player History Table', '1.3.4',
	"CREATE TABLE  `player_history` (
		`id` int(8) unsigned NOT NULL default '0',
		`timestamp` int(10) unsigned NOT NULL default '0',
		`time` int(10) unsigned NOT NULL default '0',
		`score` int(10) unsigned NOT NULL default '0',
		`cmdscore` int(10) unsigned NOT NULL default '0',
		`skillscore` int(10) unsigned NOT NULL default '0',
		`teamscore` int(10) unsigned NOT NULL default '0',
		`kills` int(10) unsigned NOT NULL default '0',
		`deaths` int(10) unsigned NOT NULL default '0',
		`rank` tinyint(2) unsigned NOT NULL default '0',
	  PRIMARY KEY  (`id`, `timestamp`),
	  KEY (`score`)
	) TYPE=MyISAM;");
	
$sqlupgrade[] = array('Alter Map Info Table', '1.4.0',
	"ALTER TABLE  `mapinfo`
		ADD COLUMN `custom` tinyint(2) unsigned NOT NULL default '0',
		ADD INDEX `idxMapName`(`name`);");
	
$sqlupgrade[] = array('Alter Player Table (Clan Manager Support)', '1.4.0',	
	"ALTER TABLE  `player`
		ADD COLUMN `clantag` varchar(20) NOT NULL default '',
		ADD COLUMN `permban` tinyint(1) unsigned NOT NULL default '0';");
	
$sqlupgrade[] = array('Create Round History Table', '1.4.0',
	"CREATE TABLE `round_history` (
		`id` SERIAL,
		`timestamp` int(10) unsigned NOT NULL default '0',
		`mapid` int(8) unsigned NOT NULL default '0',
		`time` int(10) unsigned NOT NULL default '0',
		`team1` tinyint(2) unsigned NOT NULL default '0',
		`team2` tinyint(2) unsigned NOT NULL default '0',
		`tickets1` int(10) unsigned NOT NULL default '0',
		`tickets2` int(10) unsigned NOT NULL default '0',
		`pids1` int(10) unsigned NOT NULL default '0',
		`pids1_end` int(10) unsigned NOT NULL default '0',
		`pids2` int(10) unsigned NOT NULL default '0',
		`pids2_end` int(10) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`),
		KEY `timestamp` (`timestamp`),
		KEY `mapid` (`mapid`)
	) TYPE=MyISAM;");
	
$sqlupgrade[] = array('Alter Army Table (POE2 Support)', '1.4.2',
	"ALTER TABLE  `army`
		ADD COLUMN `time10` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `win10` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `loss10` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `score10` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `best10` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `worst10` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `brnd10` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `time11` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `win11` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `loss11` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `score11` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `best11` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `worst11` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `brnd11` int(10) unsigned NOT NULL default '0';");

$sqlupgrade[] = array('Alter Army Table (AIX2 & bf2 Canadian Forces Support)', '1.4.5',
	"ALTER TABLE  `army`
		ADD COLUMN `time12` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `win12` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `loss12` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `score12` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `best12` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `worst12` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `brnd12` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `time13` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `win13` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `loss13` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `score13` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `best13` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `worst13` int(10) unsigned NOT NULL default '0',
		ADD COLUMN `brnd13` int(10) unsigned NOT NULL default '0';");
		
$sqlupgrade[] = array('Alter Servers Table (Add Rcon Port / Password)', '1.5.0',
	"ALTER TABLE `servers`
		ADD COLUMN `rcon_port` int(6) unsigned default '4711' AFTER `queryport`,
		ADD COLUMN `rcon_password` varchar(50) default NULL AFTER `rcon_port`;");
        
$sqlupgrade[] = array('Alter Player Table (Add `isbot` column)', '1.5.0',
	"ALTER TABLE `player`
		ADD COLUMN `isbot` tinyint(1) unsigned NOT NULL default '0';");
		
/* Not Used!
	$sqlupgrade[] = array('Alter Mapinfo Table (Alter `id` to length of 4)', '2.1.0',
		"ALTER TABLE `mapinfo` 
			CHANGE `id` `id` SMALLINT(4) UNSIGNED NOT NULL DEFAULT  '0'"); 
*/
	
$sqlupgrade[] = array('Update Version Table', CODE_VER,
	"INSERT INTO `_version` VALUES ('". CODE_VER ."', ".time().");");

?>
