<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-sign-post">Welcome Page</span>
	</div>
	<div class="mws-panel-body">
		<div class="mws-panel-content">
<pre>
====================================================================================
BF2Statistics Official <?php echo CODE_VER; ?> Release - Private Statistics System for Battlefield 2
====================================================================================

Released by:  		Wilson212 (based on the work of TheShadow, MrNiceGuy, Chump, nylOn, Wolverine, and others)
Release date: 		<?php echo CODE_VER_DATE; ?>&nbsp;
Release version:	<?php echo CODE_VER; ?>&nbsp;
License:		GNU General Public License

Support URL:		http://www.bf2statistics.com/

Release Author:		Wilson212
Author Email:		wilson.steven10@yahoo.com
Author URL:		http://wilson212.net


Legal Bit:
==========
Copyright &copy; 2006 - 2013  BF2Statistics

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
		TheShadow
		Wilson212 (Since v1.4.3)
		
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
		Shark-kun
		Everyone else @ BF2statistics.com ;)


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
WILL cause CTD's on start-up.  To resolve this use the "BF2StatisticsClientLauncher.exe" included in the 
"Client Files" folder of this archive (Only works on Windows systems). This executable helps player's 
to update their HOSTS file easily (see: Chapter 3.1 in the Readme for more info).  In addition, the GameSpy code 
has been validated against other third-party BF2 Stats generators.


Compatibility:
==============
The developer of this release CANNOT guarantee compatibility with all systems. Any bugs reported will
be address on a "best-effort" basis. This release has been developed and tested against the following
systems:
	Game Server:
		Windows Server 2003 w/ SP1
		Battlefield 2 "Unranked" Server version 1.2+ (Windows)
		PIII 1.1GHz, 512MB RAM

	Web Server:
		Windows Server 2003 w/ SP1
		Apache 2.2+ or IIS6 w/PHP 5.3.2 or newer ISAPI
		PHP 5.3.2 or newer
		MySQL 5
		PII 550Mhz, 512MB RAM
		
Note 1:	The scripts should be universal; however Linux users may have to change any path references to
	match Linux file conventions (eg, ___init__.py).
Note 2:	Tested to be compatible with both Windows and Linux platforms! :)
Note 3:	Tested against various MODs (BF2sp64, Project Reality). Seems to function correctly...

Ah, before anyone comments, YES the hardware specs of the servers I use are quite low.  But this is
actually good as most people with have MUCH better hardware than me.  I'll code it work on my low-end
systems so your high-end ones should work really well! :)  I've tested this with 32 player Co-Op maps
and 30 'bots! On some maps there is a little bit of lag, but certainly MORE than playable. <GRIN>


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
 - WAMP/XAMPP/Apache/IIS5+
 - PHP version 5.3.2 or newer
 - PHP PDO extension with pdo_mysql.
 - MySQL 5.x

Note: IIS requires you to add/edit the file type ".aspx" to use PHP instead of the standard	ASP.NET. 
	This should be configured the same way as ".php" file types.


Helpful Resources:
==================
As can be seen above this system reliese on technologies from around the Internet. Here's a brief list
web sites that you may find helpful in setting you this system:

	PHP:	http://www.php.net/
	MySQL:	http://www.mysql.com/
	Apache:	http://www.apache.org/
	XAMPP:	http://www.apachefriends.org/en/xampp.html
	WAMP:	http://www.wampserver.com/en/



Troubleshooting:
================
OK, so something is not quite working as it should. Before you start trawling the BF2Statistics.com site
for answers, follow this simple troubleshooting guide. It won't necessarily solve your problem, but it
should help you isolate the cause:
	1.	Re-check you configuration on both the game server and web server
	2.	Review the log files generated in the following locations:
		 - "<BF2 Server Path>/python/bf2/logs/"
		 - "<Web Server>/ASP/system/logs/"
		 - "<PHP Root>/logs/"
	3.	Check the operation of the "BF2web.gamespy.com" redirections:
		 a. Start BF2 (Server or Client) using one of the HOSTS file "work-arounds"
		 b. Open a command prompt and type:
				ping bf2web.gamespy.com
		 c. If the response is from a HOST other then the redirect IP, then the redirection is not working :(
	4.	Verify you are not using and modified script files
	5.	Disable/Check any firewalls (ZoneAlarms has a nasty habit of blocking everything!)
	6.	Ok, now you can call for help! ;)


Known Issues:
=============
 - Unlocks do NOT work for offline accounts and bots.  This limitation is hard-coded into the BF2
	server executable.  Thanks nyl0n for this info. ;)
 - Medals do not seem to 100%. There have been reports of players incorrectly recieving medals. This
	does not seem consistient or easily reproducable. Further investigation is require to solve.


ToDo:
=====
There's always a ToDo list isn't there! ;)  Anyway, here a short list of what I want/need to do:
 - Move Game Server configuration to central Admin Web GUI. Add option to update config at start of
	each round. Config would be done on a per-server basis.
 - Enhance support for Central Database server. The current Central Server option is intended for
	LAN and/or Tournament systems. A Community Based Central Server would require additional coding
	(ie, valididation of SNAPSHOT data) to ensure that hackers and/or cheaters don't exploit the system.
 - Update BF2sClone to use PDO instead of the depreciated native mysql query methods.


Enjoy,
The Shadow, Wilson212
shadow42@iinet.net.au, wilson.steven10@yahoo.com

-EOF-
</pre>
		</div>
	</div>
</div>