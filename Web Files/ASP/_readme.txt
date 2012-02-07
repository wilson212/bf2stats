====================================================================================
BF2Statistics Unofficial 1.4.7 Release - Private Statistics System for Battlefield 2
====================================================================================

Released by:  		Wilson212 (based on the work of The Shadow, MrNiceGuy, Chump, nylOn, Wolverine, and others)
Release date: 		2012-02-07
Release version:	1.4.7
License:		GNU General Public License

Support URL:		http://www.bf2statistics.com/

Original Author:	The Shadow
Release Author:		Wilson212
Author Email:		wilson.steven10@yahoo.com
Author URL:		http://wilson212.net


Legal Bit:
==========
Copyright (C) 2006 - 2012  BF2Statistics

This program is free software; you can redistribute it and/or modify it under the terms of the GNU
General Public License as published by the Free Software Foundation; either version 2 of the License,
or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General
Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not,
write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


Credits:
========
Many long hours have been poured into the development of this system. However, none of it would have
been possible without the help of everyone involved in the community at BF2statistics.com. I myself am 
just one of the many people who have contributed to this modification to the game we love: Battlefield 2.
Thank you DICE/EA for producing such a fun and enjoyable game (even if the ride hasn't always been
smooth ;) ).

BF2Statistics is the product of the contributions of many (apologies if I miss anyone! :( ):
	Hosting:
		MrNiceGuy - http://www.bf2statistics.com
		
	Coding:
		Chump
		omero
		nylOn
		Wolverine
		ArmEagle
		THE_WUQKED
		Hand (for solving the 'sendall' bug!)
		Thunder (POE2 stats data)
		Wilson212 (Fixes and added mod support)
		
	Testers:
		TheFlyingCorpse
		sysy (Sylvain)
		PowerPanda
		MacNeill_USA
		thomaskunze
		SAGA
		Dogstar
		XMog
		mdjdoniz
		TK
		CSUNO
		Kinsman
		Everyone else @ BF2statistics.com ;)
		
	Special Thanks:
		My Family (for putting up with me over the last few months)
		Everyone who uses this system. It's all for you anyway! :)


Purpose:
========
This system is designed to enable a server admin to run their own Private Statistics system for EA's
Battlefield 2 game. It aims to emulate the functionality of the official statistics system included in 
the game.  However, as it is controlled by the server admin, it can easily be customised to suit a 
particular need/purpose (ie, LAN Sessions, Private Clans, etc...).  This release includes some 
SIGNIFICANT changes to the BF2 private stats system, with a simpified web based admin tool. The entire
system has been validated against actual GameSpy data responses and information from http://ubar.bf2s.com
to ensure the highest levels of compatibility with third-party web based stats viewers AND BF2 itself.


Description:
============
These scripts have been extensively debugged to ensure proper operation.  They have been verified
against BF2 1.2, 1.3 & 1.4.  However, due to changes within the BF2 1.3 code HOSTS entry redirections
WILL cause CTD’s on start-up.  To resolve this use the "BF2PrivateStats.vbs" script included in the 
"Utils" folder of this archive (Only works on Windows systems).

There is also a small VBScript (BF2PrivateStats.vbs) file included that helps player’s to update
their HOSTS file easily (see: BF2PrivateStats for more info.  In addition, the GameSpy code has been
validated against other third-party BF2 Stats generators.

Note 1:	This has been tested against BF2:SF (ie, xpack) and seems to work well. Thanks MajArcher.
Note 2:	This release has been tested on Linux systems and appears to work well. Thanks PrePOD & others.


Compatibility:
==============
The developer of this release CANNOT guarantee compatibility with all systems. Any bugs reported will
be address on a “best-effort” basis. This release has been developed and tested against the following
systems:
	Game Server:
		Windows Server 2003 w/ SP1
		Battlefield 2 “Unranked” Server version 1.4 (Windows)
		PIII 1.1GHz, 512MB RAM

	Web Server:
		Windows Server 2003 w/ SP1
		IIS6 w/PHP 5.1.4 ISAPI
		MySQL5 (v4 Compatibility Mode)
		PII 550Mhz, 512MB RAM
		
Note 1:	The scripts should be universal; however Linux users may have to change any path references to
	match Linux file conventions (eg, ___init__.py).
Note 2:	Tested to be compatible with both Windows and Linux platforms! :)
Note 3:	Tested against various MODs (BF2sp64, Project Reality). Seems to function correctly...
		
Ah, before anyone comments, YES the hardware specs of the servers I use are quite low.  But this is
actually good as most people with have MUCH better hardware than me.  I’ll code it work on my low-end
systems so your high-end ones should work really well! :)  I’ve tested this with 32 player Co-Op maps
and 30 ‘bots! On some maps there is a little bit of lag, but certainly MORE than playable. <GRIN>


Server Operation Tips:
======================
Over the time of operating my own BF2 Server I have found a few tid-bits that help with keeping your 
server up and running smoothly.
 1) If you run your server on lower spec hardware, try setting the bf2 server process to a higher
	priority (how you do this depends upon your platform).
 2) Make the server "roll over" debug errors: add "+ignoreAsserts 1" to the command-line you use to
	start-up your server. This makes the server ignore non-critical errors and just keep going.  
	One side-effect (yet to be confirmed) is that it stops client systems from CTD due to missing
	award data. :)
 3) Update .con file setting WITHOUT stopping your server: add "+fileMonitor 1" to the command-line
	you use to	start-up your server. This makes the server re-check the source files everytime
	the map changes. Quite handy for testing different settings. ;)
 4) Other command-line options:
	"+ranked 1"		This option actually has NO effect on bf2statistics and should NOT be used!
	"+dedicated 1"	If you are running a server, then you should already know about this one! ;)
 5) Shutdown ALL non-essential processes on your server! How you do this is dependant upon you server.
	My Windows Server 2003 system runs VERY lean (the OS uses about 100MB RAM) with only 12 active
	processes! If you run Linux, you could probably do even better than this! ;)


Requirements:
=============
 - Battlefield 2 Server (patch 1.2+)
 - XAMPP/Apache/IIS5+
 - PHP 4.4.x or PHP 5.1.x
 - MySQL 4.x or MySQL 5.x

Note: IIS requires you to add/edit the file type “.aspx” to use PHP instead of the standard	ASP.NET. 
	This should be configured the same way as “.php” file types.


Helpful Resources:
==================
As can be seen above this system reliese on technologies from around the Internet. Here's a brief list
web sites that you may find helpful in setting you this system:

	PHP:	http://www.php.net/
	MySQL:	http://www.mysql.com/
	Apache:	http://www.apache.org/
	XAMPP:	http://www.apachefriends.org/en/xampp.html


New Installations:
==================
If you are new to BF2Statistics, then this what you need to know to get your own private statistics
system operational. Before proceeding, please ensure you have your web server operational with PHP &
MySQL installed and tested!
For those unsure how to do this, try XAMPP from Apache Friends (http://www.apachefriends.org/en/xampp.html).


Database Server (MySQL):
------------------------
	1.	Install MySQL 4 or MySQL 5
	2.	Create New Database (ie, bf2stats)
	3.	Create New DB User Account (ie, bf2statslogger)
	4.	Grant DBO (Database Owner) rights to new user account
	5.	Grant Global Right 'FILE ACCESS' (for database backups within Web Admin: OPTIONAL)


Web Server (PHP):
-----------------
	1.	Install Web Server (ie, Apache, IIS, other...) with PHP support.
	2.	Configure PHP to support the following extensions:
			- MySQL
			- CURL (or set "allow_url_fopen = 1")
	3.	Create a "/ASP/" directory in the root of your web server. Default locations:
			IIS 	==> "C:/InetPub/wwwroot"
			Apache	==> "C:/Program Files/Apache Group/Apache2/htdocs" ==or== /usr/local/httpd/htdocs
			XAMPP	==> "C:/Program Files/XAMPP/htdocs" ==or== /opt/lampp/htdocs/
	4.	Copy contents of "/ASP/" in archive to the location above (including ALL sub-directories)
	5.	Ensure the following files/directories har read/write access by PHP (CHMOD 777):
			/ASP/_config.php
			/ASP/_config.php.bak	(for config backups)
			/ASP/_backup
			/ASP/logs
			/ASP/logs/_processed
	6.	Config your web server to process .aspx files as PHP files. For Apache based systems this should
			be automatic (via the .htaccess file). For IIS (and others?) you will have extra work to do.
			For IIS6 users (IIS5.x systems should be similar...):
				a. Start "Internet Information Services" Manager
				b. Navigate to your web site (ie, "Default Web Site"), right-click t and choose	properties
				c. Select the "Home Directory" tab
				d. Click "Configuration..."
				e. In the "Applications Extensions" list edit .aspx (if it doesn't exist, simply add it)
				f. Change the "Executable" to be the same as what your .php files use (ie, C:\PHP\php.exe,
					C:\PHP\php4isapi.dll, or C:\PHP\php5isapi.dll)
				g. Set "Verbs, Limit to:" to GET,POST,HEAD
				h. OK all windows. Done!
	7.	With a Web Browser, browse to: http://localhost/ASP/
			Note: If you are browsing from a remote machine, please chnage the value of $admin_hosts
			in /ASP/_config.php to include your IP address.
	8.	Login to the Web Admin (Defaults: admin / password)
	9.	Select the "Install DB" link
	10.	Enter the database details defined above in the "Database Server" section
	11.	Select "Confirm"
	12.	Click "Process" (this may take a few minutes depending on the speed of your systems)
	13.	Review the response for any errors
	14.	If ALL is good, click the BF2 Logo and you should now have a FULL menu!
	15.	Select the "Edit Config" link. Update your configuration as desired (Make sure you "Update" it!)
			Note: Set the "Error Level" to "Detailed (4)"
	16.	Select the "Test Config" link. Confirm that you want to proceed. The script will now perform
			some basic tests on the Web Server components.
	17.	Review the test results. With luck everything should pass (warnings are OK, it usually just means a
			log file or something hasn't been created yet).


Game Server (Battlefield 2):
----------------------------
	1.	Make a backup of the following folder:
			"<Battlefield 2 Server Path>/python/bf2"
	2.	Copy the contents of "/python/bf2" to "<Battlefield 2 Server Path>/python/bf2" (including sub-
			directories), overwrite existing files. This release supports BOTH BF2 and BF2:SF; the scripts
			will detect which MOD is running.
	3.	Using a text editor, open "python/bf2/BF2StatisticsConfig.py"
	4.	Change the configuration options to suit your needs.  Specifically, change the "Backend Web Server"
			setting to match your configuration.
			WARNING: Even though you can change the "port" and "ASP" settings, this not recommended as BF2
				itself will *NOT* support this! You've been warned!
	5.	In the configuration file, set "debug_enable = 1"
	6.	Edit your server configuration files (ie, "<BF2 Server Path>/mods/bf2/settings/ServerSettings.con"
			& "<BF2 Server Path>/mods/bf2/settings/maplist.con") as desired.
	7.	Redirect "BF2web.gamespy.com" to resolve to your web server's IP address:
		 - Windows Servers, use the "/Utils/BF2PrivateStats.vbs" script file contained within this archive.
			a. Copy "/Utils/BF2PrivateStats.vbs" & "/Utils/SetACL.exe" to "<Battlefield 2 Server Path>" 
			b. Using a text editor, edit the "strLookupAddr" value to match your web server. This can be
				set to a valid DNS host	name or and IP Address. Also, change "strBF2exe" to match the file
				used to launch your	server (ie, bf2_w32ded.exe)
			c. Use this script to start your BF2 server. All command-line paramters are passed directly
				to BF2.
		- For Linux servers, you will have use a DNS redirect spoof (all Linux admins know how to do 
			this right?)
	8.	Start your Battlefield 2 Server (it should start without any errors).
	9.	Check the contents on the log file generated (default location "/python/bf2/logs/"). Look for any
			obvious erros.


Game Client (Battlefield 2):
----------------------------
	1.	Redirect "BF2web.gamespy.com" to resolve to your web server's IP address:
		Note: This *ONLY* works on systems using the NTFS file system!!!
		a. Copy "/Utils/BF2PrivateStats.vbs" & "/Utils/SetACL.exe" to "<Battlefield 2 Path>" 
		b. Using a text editor, edit the "strLookupAddr" value to match your web server. This can be set 
			to a valid DNS host	name or and IP Address.  Also, change "strBF2exe" to match the file used
			to launch your game (ie, bf2.exe)
		c. Use this script to start your BF2 game. All command-line paramters are passed directly to BF2.
	2.	Play and have fun! :)
	3.	After you have completed one round of play (a player voted map change will speed this up). Check
			BFHQ within the game. If all is working as it should, you will find stats data from your game.
			If not, then go through the troubleshooting section later in this guide.
	4.	If all is good, set the "debug" options on both servers back to defaults!



Upgrade Existing Install:
=========================
If you already have BF2Statistics operational, then you've already done all the hard work getting this
system operational. The following guide should allow you to successfully upgrade to this version:

WARNING: Before proceeding Backup your exisitng system!!!  If something goings wrong, you can always go
	back and try again later! This IS *CRITICAL*!!!!

Database Server (MySQL):
------------------------
	1.	Backup your existing BF2Statistics database! Can't say this enough!!!
	2.	Verify the account used to access this database has DBO (Database Owner) rights!
	3.	Grant Global Right 'FILE ACCESS' (for database backups within Web Admin: OPTIONAL)


Web Server (PHP):
-----------------
	1.	Backup your existing "/ASP/" directory! Default locations:
			IIS 	==> "C:/InetPub/wwwroot"
			Apache	==> "C:/Program Files/Apache Group/Apache2/htdocs" --or-- /usr/local/httpd/htdocs
			XAMPP	==> "C:/Program Files/XAMPP/htdocs" --or-- /opt/lampp/htdocs/
	2.	Esure PHP supports the following extensions:
			- MySQL (it was already working, this should already be set)
			- CURL (or set "allow_url_fopen = 1")
	3.	Remove the current contents of "/ASP/" (Note: the config file is different and it's easier just
			to re-create it)
	4.	Copy contents of "/ASP/" in archive to the location above (including ALL sub-directories)
	5.	Ensure the following files/directories har read/write access by PHP (CHMOD 777):
			/ASP/_config.php
			/ASP/_config.php.bak	(for config backups)
			/ASP/_backup
			/ASP/logs
			/ASP/logs/_processed
	6.	Ensure your web server processes .aspx files as PHP files (should already be done, but can't hurt
			right?). For Apache based systems this should be automatic (via the .htaccess file). For IIS
			(and others?) you will have extra work to do.
			For IIS6 users (IIS5.x systems should be similar...):
				a. Start "Internet Information Services" Manager
				b. Navigate to your web site (ie, "Default Web Site"), right-click and choose properties
				c. Select the "Home Directory" tab
				d. Click "Configuration..."
				e. In the "Applications Extensions" list edit .aspx (if it doesn't exist, simply add it)
				f. Change the "Executable" to be the same as what your .php files use (ie, C:\PHP\php.exe,
					C:\PHP\php4isapi.dll, or C:\PHP\php5isapi.dll)
				g. Set "Verbs, Limit to:" to GET,POST,HEAD
				h. OK all windows. Done!
	7.	With a Web Browser, browse to: http://localhost/ASP/
			Note: If you are browsing from a remote machine, please chnage the value of $admin_hosts
			in /ASP/_config.php to include your IP address.
	8.	Login to the Web Admin (Defaults: admin / password)
	9.	As the config file is unlikely to know your database details yet, it will highlight the "Install DB"
			link. Simply ignore this and select "Upgrade DB"
	10.	Enter the database details defined above in the "Database Server" section
	11.	Select "Confirm"
	12.	Click "Process" (this may take a few minutes depending on the speed of your systems)
	13.	Review the response for any errors.
			Note: if your previous system was running a 1.3 release, then it is most likely you will see some
				errors. This is normal and expected.
	14.	If ALL is good, click the BF2 Logo and you should now have a FULL menu!
	15.	Select the "Edit Config" link. Update your configuration as desired (Make sure you "Update" it!)
	16.	Select the "Test Config" link. Confirm that you want to proceed. The script will now perform
			some basic tests on the Web Server components.
	17.	Review the test results. With luck everything should pass (warnings are OK, it usually just means a
			log file or something hasn't been created yet).


Game Server (Battlefield 2):
----------------------------
	1.	Make a backup of the following folder:
			"<Battlefield 2 Server Path>/python/bf2"
	2.	Copy the contents of "/python/bf2" to "<Battlefield 2 Server Path>/python/bf2" (including sub-
			directories), overwrite existing files. This release supports BOTH BF2 and BF2:SF; the scripts
			will detect which MOD is running.
	3.	Using a text editor, open "python/bf2/BF2StatisticsConfig.py"
	4.	Change the configuration options to suit your needs.  Specifically, change the "Backend Web Server"
			setting to match your configuration.
			WARNING: Even though you can change the "port" and "ASP" settings, this not recommended as BF2
				itself will *NOT* support this! You've been warned!
	5.	In the configuration file, set "debug_enable = 1"
	6.	Edit your server configuration files (ie, "<BF2 Server Path>/mods/bf2/settings/ServerSettings.con"
			& "<BF2 Server Path>/mods/bf2/settings/maplist.con") as desired.
	7.	Redirect "BF2web.gamespy.com" to resolve to your web server's IP address:
		 - Windows Servers, use the "/Utils/BF2PrivateStats.vbs" script file contained within this archive.
			a. Copy "/Utils/BF2PrivateStats.vbs" & "/Utils/SetACL.exe" to "<Battlefield 2 Server Path>" 
			b. Using a text editor, edit the "strLookupAddr" value to match your web server. This can be
				set to a valid DNS host	name or and IP Address. Also, change "strBF2exe" to match the file
				used to launch your	server (ie, bf2_w32ded.exe)
			c. Use this script to start your BF2 server. All command-line paramters are passed directly
				to BF2.
		 - For Linux servers, you will have use a DNS redirect spoof (all Linux admins know how to do 
			this right?)
	8.	Start your Battlefield 2 Server (it should start without any errors).
	9.	Check the contents on the log file generated (default location "<BF2 Server Path>/python/bf2/logs/").
			Look for any obvious erros.


Game Client (Battlefield 2):
----------------------------
	1.	Redirect "BF2web.gamespy.com" to resolve to your web server's IP address:
		Note: This *ONLY* works on systems using the NTFS file system!!!
		a. Copy "/Utils/BF2PrivateStats.vbs" & "/Utils/SetACL.exe" to "<Battlefield 2 Path>" 
		b. Using a text editor, edit the "strLookupAddr" value to match your web server. This can be set 
			to a valid DNS host	name or and IP Address.  Also, change "strBF2exe" to match the file used
			to launch your game (ie, bf2.exe)
		c. Use this script to start your BF2 game. All command-line paramters are passed directly	to BF2
	2.	Play and have fun! :)
	3.	After you have completed one round of play (a player voted map change will speed this up). Check
			BFHQ within the game. If all is working as it should, you will find stats data from your game.
			If not, then go through the troubleshooting section later in this guide.
	4.	If all is good, set the "debug" options on both servers back to defaults!


Troubleshooting:
================
OK, so something is not quite working as it should. Before you start trawling the BF2Statistics.com site
for answers, follow this simple troubleshooting guide. It won't necessarily solve your problem, but it
should help you isolate the cause:
	1.	Re-check you configuration on both the game server and web server
	2.	Review the log files generated in the following locations:
		 - "<BF2 Server Path>/python/bf2/logs/"
		 - "<Web Server>/ASP/logs/"
		 - "<PHP Root>/logs/"
	3.	Check the operation of the "BF2web.gamespy.com" redirections:
		 a. Start BF2 (Server or Client) using one of the HOSTS file "work-arounds"
		 b. Open a command prompt and type:
				ping bf2web.gamespy.com
		 c. If the response is from a HOST with IP: 207.38.10.110, then the redirection is not working :(
	4.	Verify you are not using and modified script files
	5.	Disable/Check any firewalls (ZoneAlarms has a nasty habit of blocking everything!)
	6.	Ok, now you can call for help! ;)
	

Changelog:
==========
Sorry all, I’m not really a programmer (more of a code hacker really), so this list may not include ALL
changes.  Also, I have been a bit lazy with my comments in code. :/

Changes (2006-10-14):
 - Fixed Custom Map responses in getplayerinfo.aspx. Data was being mis-aligned.
 - Fixed maplist responses from getmapinfo.aspx. Query parameters were being ignored
 
Changes (2006-10-12):
 - Improved 'Unknown' army checking in bf2statistics.php
 - Added Min. Game Time (per Player) check to bf2statistics.php includes associated config item
 - Added check for predefined (ie, set in constants.py) custom maps

Changes (2006-10-11):
 - Added support for POE2 (thanks Thunder)
 - Fixed issues with cmap- responses in getplayerinfo.aspx to support static custom maps (ie, POE2).
 - Added mods- info string part for requesting mods data from getplayerinfo.apsx.
 - Extended Amry Table to support new army data (POE2 support)
 - Fixed issue with custom maps and getplayerinfo.aspx
 - Further improve performance of processing kills data in bf2statistics.php
 - Added full playerid list to getplayerid.aspx for use in synchronsing web stats leadboards lists
 - Fixed 'unknown' army handling in bf2statisitics.php
 - Fixed issue with favourite opponent/victim code in getplayerinfo.aspx.

Changes (2006-10-09):
 - Corrected numerous typo's in Web Admin (thanks CSUNO)
 - Added DNS Cache server control to "override" script
 
Changes (2006-10-08):
 - Fixed incorrect medal (Sharpshooter) awarding bug!
 - Fixed divided by zero error in Web Admin
 - Fixed Test config error (short tags)
 - Fixed "invisible" server issue with "override" script
 
Changes (2006-10-06):
 - Fixed PHP4 compatibility. This required a re-writte of the Config Class and updating ALL code references
	to function with the new class (300+ entries).
 - Fixed test validation checks to be comatible with Apache
 - Fixed some issues where DB connection was sometimes lost due to another script closing the connection
 - Added Total Vehicle Road kills data to getplayerinfo.aspx
 - Fixed issue with BF2logo on linux systems
 - Altered the method for updating Kills data. should be MUCH faster on large databases 

Changes (2006-09-28):
 - Fixed compatibility issues with Linux systems
 - Fixed typos in Web Admin
 - Added extra validation to Web Admin "Edit Config"
 - Fixed potential issues with file paths missing trailing '/'! :(

Changes (2006-09-26):
 - Fixed a compatibility issue with getpalyerinfo.aspx response.

Changes (2006-09-25):
 - Improved test scripts to ensure more reliable results

Changes (2006-09-24):
 - Added option to support custom medal_data files. Crrently ONLY for Game Server files.
 - Added detection routines for database install/upgrades
 - Fixed a number of web admin bugs

Changes (2006-09-23):
 - Added pagination to Player Selection Lists. Page size configurable.
 - Added option to ignore AI players in lists.
 - Added check for Remote URL options in Config Test
 - Fixed an issue with SMoC & Gen Rank Calculations.

Changes (2006-09-23):
 - Added MapInfo List to Web Admin. This allows for easy visibility of custom map ID's.
 - Added Server List to Web Admin. This is just a simple list for now. Visibility of active servers is 
	planned.
 - Added Game port data to SNAPSHOT for inclusion with Server List
 - Fixed futher issue with Config Tester that did not completely remove all test data.
 - Added 'custom' field to mapinfo to tag map as custom or not. This is in prepartion to being able to
	change custom map values in future.

Changes (2006-09-22):
 - Added End-of-Round Ticket score to SNAPSHOT. BF2Stats doesn't process it yet, but...
 - Cleaned up BF2StatisticsConfig.py config file to be more consitent and readable.
 - Created simple "outsmart.vbs" to work around EA's HOSTS file checks. Uses SetACL.EXE. Works very well
	and should be compatible with non-English systems. Only supports client BF2.EXE, but a simple
	file edit can change this.
 - Fixed issue with custom maps not obtaining a correct mapid.
 - Added database version check to bf2statistics.php. Saves SNAPSHOT, then fails on db mis-match.
 - Fixed issue with Config Tester that did not completely remove all test data.

Changes (2006-09-21):
 - Added Config Test Script to Web Admin. Checks various settings to ensure BF2Stats will work.
 - Added menu items for Server Info & Map Info to Web Admin.
 - Added basic validation to Edit Config in Web Admin.
 - Fixed issue with SNAPSHOT auto-archive.

Changes (2006-09-20):
 - Added Database & Codebase version checking to Web Admin to ensure db and code remain in-sync.
 - Changed ClearDB function to use TRUNCATE TABLE instead of delete.
 - Improved Unlock performance and configuration. Now includes option for Expert badges and Rank determines
	when bonus unlocks become available.
 - Added Delete Player option to Web Admin.
 - Added Merge Player option to Web Admin.
 - Added confirmation requirement to every page in Web Admin.

Changes (2006-09-19):
 - Changed Config File Management to suit new Admin Web GUI. Required reworking ALL areas where config data
	is used. Renamed config file to _config.php.
 - Added option to Auto- Archive processed SNAPSHOT logs. This allows for simplified import of missed SNAPSHOTs.
 - Merged ALL config items on game server to a single config file (including Debug!)
 - Added general LAN Override IP Address for correct country lookup for local players. Manual per-player
	override enhanced.

Changes (2006-09-17):
 - Added display of Readme file within Admin Web GUI.
 - Added per-player selection for ALL player based Admin Web GUI functions.
 
Changes (2006-09-16):
 - Added Backup & Restore capability to Admin Web GUI. Includes Date seriaised backup versions.
 - Added capability to Permantly Ban players! BlackList MUST be set on in game server config.
 - Added secure logon to Admin Web GUI. Includes 30min inactivity auto-logout.
  
Changes (2006-09-14):
 - Added *NEW* Web GUI for managing BF2STatistics backend database and configuration.
 - Cleaned up /ASP/ folder. All game server GET requests use .aspx files.
 
Changes (2006-09-11):
 - Re-worked ClanManager to make it easier to use and provide better control/options: Blacklist, Whitelist,
	Greylist (requirements based). Blacklist is manged via banned filed in player table. A value of 999
	indicates a player is ALWAYS on the blacklist! No Web GUI for controlling this has been created at
	this time... 
 - Remove RCon commands from ClanManger.  I hope to re-enable these for the whole system, no promises...
 - Fixed an issue with detecting MOD version running
 - Fixed an issue with the ClanTag value not registering with the ClanManger
 
Changes (2006-09-04):
 - Added Option to Disable Unlocks
 - Added RCon commands to ClanManager (currentMode, changeMode, isOnList, numFromList)
 - Added OPTIONAL Blacklist to ClanManger.

Changes (2006-09-03):
 - Added Clan Manger Code to control access to servers. Criteria configurable on a per server basis.

Changes (2006-09-01):
 - Fixed issue with some Linux servers not sending full snapshot data.
 
Changes (2006-08-25):
 - Added End-of-File (EOF) marker to SNAPSHOT to allow for validation of complete data being collected. In
	instances, the POST file is limited to 50k. :(
 - Fixed issue with "RisingStar" results in getleaderboard.aspx

Changes (2006-08-24):
 - Fixed an issue with backend awards. Awadrs with mulitple criteria's would return a false-positive result.
		
Changes (2006-08-22):
 - Fixed issues with getleaderboard.aspx always returning position 1 when comparing two (2) players.
 - Added "RisingStar" code to getleaderboard.aspx. This also requried a seperate data table.
 - Added 4 nex indexes to player table to improve leaderboard performance.

Changes (2006-08-21):
 - Fixed issue with Custom Maps. If no custom maps exist, then the MapID was reset to 1.

Changes (2006-08-20):
 - Fixed issue with custom maps. Only MapInfo table was being updated. Moved Custom Map ID detection code
	to top of script before player processing.

Changes (2006-08-17):
 - Removed OPTIONAL AsyncSnapshot setting. This is now the default config. Game server no longer displays
	"Problem with your connection" error message at the end of a round. :)
 - Added response code header checking to MiniClient. Errors are printed to the log file. This should help
	troubleshoot Webserver connectivity errors. This includes basic error checking.
 - Added IP Auth checking to bf2statistics.php & bf2admin.php.  This checks the clients IP address against
	a list of authorised IP HOSTS and/or Subnets.
 - Added support for a Central STATS server. This provides two modes for updates: Full (All STATS data)
	and Minimal (All, except Rank & Awards). The minimal option is used for LANs or Tournaments where
	the local db starts out blank. A Central Community-based STATS server would require furture work
	to ensure SNAPSHOT data is validated (to stop potential cheaters and hackers! :( ).
 - Fixed onPlayerConnect (medals.py) reconnection checking code. Gamespy "Request Storm" is no gone and
	players only request Gamespy data ONCE per round (even if they reconnect due to a CTD!). :)

Changes (2006-08-16):
 - Fixed onPlayerConnect (medals.py) reconnection checking code. This should also resolve issues with 
	players losing medals if the reconnect mid-round (ie, due to CTD).
 - Tweaked snapshot miniclient POST code to further reduce "Problem with your connection" errors at the
	end of each round.

Changes (2006-08-15):
 - Added check to onPlayerConnect (medals.py) to force GameSpy request ONLY once at start of a new round.
	This should stop the "Request Storm" that is generated at the start of a new round.
 - Moved Awards data checking to common awards include file. Also, changed code to utilise simpler Array
	based data structure. This simplifies custom award handling and allow for migration of award data
	to backend database...
 - Improved bfstatistics.php processing to ensure it continues processing in the event of Game Server
	disconnect (part of the Async SNAPSHOT feature).

Changes (2006-08-14):
 - Added CheckBackend Awards to bf2admin.php. Used for testing backend award data.
 - Moved Map Time based awards to backend.  These are not supported in-game anyway and can cause CTD's.
	See medals_data.py for details. To revert to OLD method, rename the files tagged as "_old" in
	the relevant paths (ie, /ASP/ & /python/bf2/).
 - Relocated Awards processing in bfstatistics.php to support Backend Awards processing.  On my hardware,
	this adds about 0.04 seconds time per player (~2.56sec for 64 players) for the processing of the
	SNAPSHOT by bf2statistics.php.  Backend Awards data is stored in a seperate bf2awardsdata.php
	file. This should allow for easy additions of custom awards.
 - Add SNAPSHOT processing time data to log file.
 - Fixed an issue with setting "$allunlocks = 1" in config.php.  This setting now works as intended.
	Thanks for spotting this [C54]Memus. :)
 - Added MOD check to backend award checking.  For standard BF2, this reduces the processing time to 
	0.015 seconds per player.  BF2:SF is still 0.04 seconds per player.

Changes (2006-08-11):
 - Corrected a minor bug with getunlocksinfo.aspx where players with NO unlocks would receive a response
	about 9 unlocks instead of the expected 7.  This doesn’t actually affect anything, just want to
	be consentient. ;)
 - Added asynchronous mode for SNAPSHOT update.  The Web Server will respond as soon as the SNAPSHOT
	data is written to the backup log file.  The Game Server will continue as soon as it receives
	this message.  Normally, the Game Server waits until the web server has completed its work.
	Note: This may cause issues (ie, Global stats in-game out of sync) with slower web servers and
	map rotations with multiple rounds per map.  Try increasing the delay between rounds if this
	does occur, otherwise set asyncSnapShot to 0.
	
Changes (2006-08-10):
 - Fixed an issue with the Far East Service ribbon. Map #104 (Hingan Hills) does not exist. Removed from
	requirements.
 - Fixed an issue with the Purple Heart Medal.  Criteria now correct (IAR: 5kills, 20deaths, minimum
	dkRatio of 4).
 - Corrected “abr-“ & “mbs-“ getplayerinfo.aspx responses.  Not consistently returning “Best Round
	Score”.
 - Added “abrnd-“ (# Best in round for winning team by army) to getplayerinfo.aspx OPTIONAL response.
 - Added confirmation prompts to bf2admin.php utility.

Changes (2006-08-09):
 - Added support for Best Round Score (mbs-) & Worst Round Score (mws-) for Map Response (ie, mtm-,mwn-,
	mls-) to getplayerinfo.aspx.

Changes (2006-08-08):
 - Fixed web server based PID lookup. Default ‘bot names required escaping to work correctly.
 - Added extra debug code to medals.py to allow capturing of medals snapshot and awarding responses.
	Award information is written to the log files regardless of debug setting.  This should help
	determine where the failure with some medals occurs. At the very least, it’ll allow server
	admins to “posthumously” (sic.) apply missed awards. ;)

Changes (2006-08-07):
 - Moved GameSpy_Port setting to BF2StatisticsConfig.py (Couldn’t detect it from Server Settings).
	This will/can be used in the web stats front-end for live server status info (This has not yet
	been implemented).
 - Fixed MergePlayer function in bf2admin.php (good for those times your players want to change Nicks
	and/or forget their password!).  This utility does NOT have any warning prompts, so be
	EXTREMELY careful!!  Yes, I’ll add some confirmation prompts in the near future. ;)
 - Added OPTIONAL support for Web Server based PID generation for Offline/’bot accounts with
	fail-over. You still need a PID.TXT file; however this is used only in the event the Game
	Server can not contact the Web Backend. Uses miniclient.py. Config setting added to
	BF2StatisticsConfig.py. Original concept by ArmEagle.
 - Added extra data validation code to getplayerinfo.aspx & getunlocksinfo.aspx to handle new player
	record created via Web Server based PID generation which only creates a MINIMAL player record.
		
Changes (2006-08-04):
 - Fixed a bug with the detection of Game Server Query string.  If it is out of order, it would fail. 
	Thanks to Wolverine for the idea.
 - Added function bf2admin.php to merge two player ids, and remove the old one.  Be VERY careful with
	this.  The order for the PIDs is <NewID>;<OldID>!  Not fully tested, so don’t blame me if it
	corrupts your database (I know it did mine whilst I was trying to get it to work).

Changes (2006-08-03):
 - Changed debug log and pid.txt file paths to be relative. This should improve “out-of-the-box” and
	Linux compatibility (I’d be interested to hear from any Linux server admins you have tested
	this!)
 - Changed SMOC/GEN ranks to be awarded (in BF2statistics.php) for a minimum tenure (this is
	configurable via a variable in config.php).
 - Added ranknotification.aspx script to reset Rank Notification data. (Note: I’m not really sure what
	this script should do, but the response from BF2web.gamespy.com seems to indicate that this is
	what it does.) Thanks thomaskunze for the heads-up on this one...
 - Added preliminary support of BF2 patch 1.4.  GameSpy has info of a new map (#12), this has been
	added to the GameSpy response data in preparation of the 1.4 patch.
 - Added MapID 12 to constants.py (Road to Jalalabad) in preparation of Patch 1.4

Changes (2006-08-02):
 - Fixed issue with Service Ribbons Criteria
 - Corrected query string match for getplayerinfo.aspx and revised medals_data
 - Fixed issues with Rank Data criteria
 - Add logging of server data to bf2statistics.php (borrowed from release by ArmEagle)
 - Add sending of queryport to snapshot.py for server data logging (idea borrowed from release by
	ArmEagle)
 - Added a check, as per 1.3 scripts, to ensure snapshot is only sent once!
 - Performance: Bypassed some redundant code in stats.py when not in debug mode
 - Updated log file path in ___init__.py to try and make it more Linux friendly
 - Added code to medals.py to detect if player is a ‘bot BEFORE requesting stats/awards data.  This
	should help reduce the chance of a server with a large AI count from hitting the 256 HTTP
	connections “wall” causing a server crash! :(  Hopefully this will allow large offline/’bot
	counts.  Successfully tested 32 ‘bots in Co-Op mode! :)

Changes (2006-08-01):
 - Validated ALL Awards data against ubar.bf2s.com (BF2 & BF2:SF).  Thanks lev for identifying these
	issues. :)
 - Added support to awards based on individual map time (*WARNING*: This requires the corresponding
	getplayerinfo.aspx ASP file included!).  I’m not sure if these actually can be done in the
	front-end...  If not will change the code to process this in the backend, but this would then
	make these silent awards. :(  It’s always nice to be get an award in-game. :)
 - Added individual map time code to getplayerdata.aspx to support new medals.py query-string

Changes (2006-07-31):
 - Fixed Best Round Score for Army in getplayerinfo.aspx
 - Reduced “noise” when python scripts debug set to ‘0’
 - Changed mod detection behaviour to default to BF2 mode when MOD unknown
 - Added support for recording/reporting Game Mode data (requires schema update!)
 - Added error check for Time Info query in get playerinfo.aspx
 - Added support for BF2SP64 MOD (Supports larger 32/64 player maps in Co-Op mode.  See:
	http://battlefieldsingleplayer.planetbattlefield.gamespy.com/ for details). However, there is
	a limitation of no more than 32 bots as the server WILL crash with a “Too many HTTP
	Connections (256)” error! :(
 - Added support for bonus unlocks.  These are controlled via /ASP/config.php and are linked to Kit
	related badges. Requirements are based on either Basic or Veteran badges and can be set to
	require a minimum number of normal rank unlocks before allowing.  This OPTIONAL system is
	designed for those people who do not have BF2:SF, but want access to extra unlocks! ;)

Changes (2006-07-26):
 - Corrected bug when a new player (ie, one without stats) joins a game and kills the snapshot 
	generation process.  Side affect is that the game server will request data from GameSpy every
	time the onPlayerConnect event is trigged (ie, reconnect forced to FALSE)
 - Snapshot.py module is now about 99.9% reliable (it’s probably 100%, but I don’t like giving people
	false expectations).  It seems that the “new player” bug was the cause.
 - Added support for &transpose in getplayerinfo.apsx (this is useful for validating data responses)
 - Fixed rank reset code (it didn’t work as planned, does now :) )
 - Updated medals_data.py & medals_data_xpack.py data based on info from ubar.bf2s.com
 - Corrected map data output order in getplayerinfo.apsx
 - Corrected available unlock info code in getunlocksinfo.aspx to support tiers.  Previously ALL
	unlocks were available to be selected even if the required tier 1 unlock was not already
	selected.
 - Fixed processing of data from Import routine to help better handle multiple simultaneous processing
	threads.
 - Fixed rank reset code in bf2admin.php
 - Added code to allow bf2statistics.php to handle “holes” in the snapshot data (not sure if this
	actually occurred in my logs due to other issues in the python code, but it doesn’t hurt
	leaving this in)
 - Further validated GameSpy return data (did some cross checks with REAL data ;) ).  Should be almost
	100% now.
 - Enhanced SMOC & GEN check code in bf2statistics.php to notify BF2 of rank change.
 - Added an unlock reset function to bf2admin.php.  You can reset ALL unlocks, or just an single PID.

Changes (2006-07-24):
 - Integrated nylon’s Bot/Offline code changes
 - Added Wolverine’s EF/AF patch
 - Added gpm_coop gametype to constants.py
 - Added gametype detection to load correct medals_data.py file (xpack/SF version WILL cause CTD’s
	with “non-special forces” clients!)
 - Fixed gamespy data lookup for online accounts
 - Corrected Rank reset to ‘0’ (ie, Private issue)
 - Validated gamespy return data
 - Added debug capability to bf2statistics.php code (previously no indication of failures for SNAPSHOT
	uploads was available)
 - Added Fragalyzer enable option to python config file
 - Improved error handling with all database lookups in bf2statistics.php (this helps with the Debug
	feature as most failures a due to missing data in the SNAPSHOT)
 - Fixed Global Score update issue between rounds when reconnect code enabled (ie, because the server
	does not query GameSpy every time, the Global Score value is NOT updated!)


Known Issues:
=============
 - Unlocks do NOT work for offline accounts and ‘bot.  This limitation is hard-coded into the BF2
	server executable.  Thanks nyl0n for this info. ;)
 - Medals do not seem to 100%. There have been reports of players incorrectly recieving medals. This
	does not seem consistient or easily reproducable. Further investigation is require to solve.


ToDo:
=====
There’s always a ToDo list isn’t there! ;)  Anyway, here a short list of what I want/need to do:
 - Move Game Server configuration to central Admin Web GUI. Add option to update config at start of
	each round. Config would be done on a per-server basis.
 - Improve performance and stability (ie, make sure it can run for weeks/months, not days!). Though I
	suspect this is more of an issue with my budget hardware! ;)
 - Enhance support for Central Database server. The current Central Server option is intended for
	LAN and/or Tournament systems. A Community Based Central Server would require additional coding
	(ie, valididation of SNAPSHOT data) to ensure that hackers and/or cheaters don't exploit the
	system. Obviously, someone would have to host such a system or just use ABR instead... ;)


Enjoy,
The Shadow
shadow42@iinet.net.au

-EOF-
