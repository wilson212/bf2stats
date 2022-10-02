#################################################
#
# History:
#   11/24/05 v0.0.1 - ALPHA build
#   11/28/05 v0.0.2 - Removed killedByPlayer
#                   - Added kills
#   12/08/05 v0.0.3 - Added deaths
#   12/09/05 v0.0.4 - Removed tnv/tgm
#   12/10/05 v0.0.5 - Added prefix
#   12/14/05 v0.0.6 - Removed useless GS call
#                   - Removed tactical/zip/grappling kills
#                   - Added grappling deaths
#   12/25/05 v0.0.7 - Added v
#   01/03/06 v0.1 - BETA release
#   01/05/06 v0.1.1 - Added master db
#                   - Added socket timeout/error handling
#   01/25/06 v0.1.2 - Updated CDB IP
#   02/15/06 v0.1.3 - Updated CDB URL
#   06/17/06 v0.1.4 - Added EF army
#   02/01/06 v1.0 - Public Release
#
#   06/10/10 - Removed Combat scores, they now calculate in bf2statistics.php
#   06/10/10 - Added return
#   06/10/10 - Corrected Teamwork keys
#  
#################################################

#################################################
#     DO NOT EDIT ANYTHING BELOW THIS LINE!
#################################################

# ------------------------------------------------------------------------------
# omero 2006-03-31
# ------------------------------------------------------------------------------
# Settings now imported from bf2.BF2StatisticsConfig module, see below.

#HOST = '192.168.13.141'						# webserver listening ip address
#PORT = 80					                # webserver listening http port
#PATH = '/ASP/bf2statistics.php'		# webserver path to script, relative to rootdir
#PREFIX = ''				                # log file prefix

import host
import bf2.PlayerManager
import fpformat
from constants import *
from bf2 import g_debug
from bf2.stats.stats import getStatsMap, setStatsMap, getPlayerConnectionOrderIterator, setPlayerConnectionOrderIterator, roundArmies
from bf2.stats.medals import getMedalMap, setMedalMap

# ------------------------------------------------------------------------------
# omero 2006-03-31
# ------------------------------------------------------------------------------
from bf2.BF2StatisticsConfig import snapshot_logging, snapshot_log_path_sent, snapshot_log_path_unsent, http_backend_addr, http_backend_port, http_backend_asp, http_central_enable, http_central_addr, http_central_port, http_central_asp, snapshot_prefix
from bf2.stats.miniclient import miniclient, http_postSnapshot

# Added by Chump - for bf2statistics stats
from time import time, localtime, strftime

# omero, 2006-03-31
# the following is no longer necessary
#import socket

map_start = 0

def init():
	print "Snapshot module initialized"

# Added by Chump - for bf2statistics stats
	host.registerGameStatusHandler(onChangeGameStatus)
	
	
# Added by Chump - for bf2statistics stats
def onChangeGameStatus(status):
	global map_start
	if status == bf2.GameStatus.Playing:
		map_start = time()

def invoke():

# Added by Chump - for bf2statistics stats
	#host.pers_gamespyStatsNewGame()
	
	snapshot_start = host.timer_getWallTime()
	
	if g_debug: print "Gathering SNAPSHOT Data"
	snapShot = getSnapShot()

	# Send snapshot to Backend Server
	print "Sending SNAPSHOT to backend: %s" % str(http_backend_addr)
	SNAP_SEND = 0

	
	# -------------------------------------------------------------------
	# Attempt to send snapshot
	# -------------------------------------------------------------------
	try:
		backend_response = http_postSnapshot( http_backend_addr, http_backend_port, http_backend_asp, snapShot )
		if backend_response and backend_response[0] == 'O':
			print "SNAPSHOT Received: OK"
			SNAP_SEND = 1
			
		else:
			print "SNAPSHOT Received: ERROR"
			if backend_response and backend_response[0] == 'E':
				datalines = backend_response.splitlines()
				print "Backend Response: %s" % str(datalines[2])
				
			SNAP_SEND = 0
		
	except Exception, e:
		SNAP_SEND = 0
		print "An error occurred while sending SNAPSHOT to backend: %s" % str(e)
		
	
	# -------------------------------------------------------------------
	# If SNAPSHOT logging is enabled, or the snapshot failed to send, 
	# then log the snapshot
	# -------------------------------------------------------------------	
	if SNAP_SEND == 0:
		log_time = str(strftime("%Y%m%d_%H%M", localtime()))
		snaplog_title = snapshot_log_path_unsent + "/" + snapshot_prefix + "-" + bf2.gameLogic.getMapName() + "_" + log_time + ".txt"
		print "Logging snapshot for manual processing..."
		print "SNAPSHOT log file: %s" % snaplog_title
		
		try:
			snap_log = file(snaplog_title, 'a')
			snap_log.write(snapShot)
			snap_log.close()
		
		except Exception, e:
			print "Cannot write to SNAPSHOT log file! Reason: %s" % str(e)
			print "Printing Snapshot as last resort manual processing: ", snapShot
			
	elif SNAP_SEND == 1 and snapshot_logging == 1:
		log_time = str(strftime("%Y%m%d_%H%M", localtime()))
		snaplog_title = snapshot_log_path_sent + "/" + snapshot_prefix + "-" + bf2.gameLogic.getMapName() + "_" + log_time + ".txt"
		print "SNAPSHOT log file: %s" % snaplog_title
		
		try:
			snap_log = file(snaplog_title, 'a')
			snap_log.write(snapShot)
			snap_log.close()
		
		except Exception, e:
			print "Cannot write to SNAPSHOT log file! Reason: %s" % str(e)
			print "Printing Snapshot as last resort manual processing: ", snapShot
			

	# Send Snapshot to Central Backend Server
	if http_central_enable == 1 or http_central_enable == 2:
		print "Sending SNAPSHOT to Central Backend: %s" % str(http_central_addr)
		
		#Append CDB Setting so backened knows what to do with this
		snapShotCDB = snapShot + '\\cdb_update\\' + http_central_enable
		
		try:
			backend_response = http_postSnapshot( http_central_addr, http_central_port, http_central_asp, snapShotCDB )
			if backend_response and backend_response[0] == 'O':
				print "SNAPSHOT Received: OK"
				
			else:
				print "SNAPSHOT Received: ERROR"
				if backend_response and backend_response[0] == 'E':
					datalines = backend_response.splitlines()
					print "Backend Response: %s" % str(datalines[2])
			
		except Exception, e:
			print "An error occurred while sending SNAPSHOT to Central Backend: %s" % str(e)
		
	
	print "SNAPSHOT Processing Time: %d" % (host.timer_getWallTime() - snapshot_start)


# ------------------------------------------------------------------------------
# omero 2006-03-31
# ------------------------------------------------------------------------------
# always do the following at the end...
	repackStatsVectors()


def repackStatsVectors():

	# remove disconnected players
	cleanoutStatsVector()
	cleanoutMedalsVector()
	
	# repack stats and medal vector so there are no holes. gamespy doesnt like holes.
	medalMap = getMedalMap()
	statsMap = getStatsMap()
	playerOrderIt = getPlayerConnectionOrderIterator()

	newOrderIterator = 0
	newStatsMap = {}
	newMedalMap = {}

	highestId = 0
	for id, statsItem in statsMap.iteritems():

		newStatsMap[newOrderIterator] = statsItem
		if id in medalMap:
			newMedalMap[newOrderIterator] = medalMap[id]

		statsItem.connectionOrderNr = newOrderIterator
		newOrderIterator += 1
		
	print "Repacked stats map. Stats map size=%d. OrderIt changed from %d to %d" % (len(statsMap), playerOrderIt, newOrderIterator)

	setPlayerConnectionOrderIterator(newOrderIterator)
	setStatsMap(newStatsMap)
	setMedalMap(newMedalMap)
		
		

def cleanoutStatsVector():
	print "Cleaning out unconnected players from stats map"
	statsMap = getStatsMap()
	
	# remove disconnected players after snapshot was sent
	removeList = []
	for pid in statsMap:
		foundPlayer = False
		for p in bf2.playerManager.getPlayers():
			if p.stats == statsMap[pid]:
				foundPlayer = True
				break

		if not foundPlayer:
			removeList += [pid]

	for pid in removeList:
		print "Removed player %d from stats." % pid
		del statsMap[pid]		



def cleanoutMedalsVector():
	print "Cleaning out unconnected players from medal map"
	medalMap = getMedalMap()
	
	# remove disconnected players after snapshot was sent
	removeList = []
	for pid in medalMap:
		foundPlayer = False
		for p in bf2.playerManager.getPlayers():
			if p.medals == medalMap[pid]:
				foundPlayer = True
				break

		if not foundPlayer:
			removeList += [pid]

	for pid in removeList:
		if g_debug: print "Removed player %d from medals." % pid
		del medalMap[pid]

	
	
def getSnapShot():
	print "Assembling snapshot"
	
	global map_start
	snapShot = snapshot_prefix + '\\' + str(bf2.serverSettings.getServerConfig('sv.serverName')) + '\\'
	snapShot += 'gameport\\' + str(bf2.serverSettings.getServerConfig('sv.serverPort')) + '\\'
	snapShot += 'queryport\\' + str(bf2.serverSettings.getServerConfig('sv.gameSpyPort')) + '\\'
	snapShot += 'mapname\\' + str(bf2.gameLogic.getMapName()) + '\\'
	snapShot += 'mapid\\' + str(getMapId(bf2.serverSettings.getMapName())) + '\\'
	snapShot += 'mapstart\\' + str(map_start) + '\\mapend\\' + str(time()) + '\\'
	snapShot += 'win\\' + str(bf2.gameLogic.getWinner()) + '\\'
	
	if g_debug: print 'Finished Pre-Compile SNAPSHOT'

	statsMap = getStatsMap()
	
	# ----------------------------------------------------------------------------
	# omero 2006-04-10
	# ----------------------------------------------------------------------------
	# this will be used for detecting which mod is running and
	# set standardKeys['v'] accordingly
	# defaults to 'bf2'
	#
	running_mod = str(host.sgl_getModDirectory())
	if ( running_mod.lower() == 'mods/bf2' ):
		v_value = 'bf2'
	elif ( running_mod.lower() == 'mods/bf2sp64' ):
		v_value = 'bf2sp64'
	elif ( running_mod.lower() == 'mods/xpack' ):
		v_value = 'xpack'
	elif ( running_mod.lower() == 'mods/poe2' ):
		v_value = 'poe2'
	elif ( running_mod.lower() == 'mods/aix2' ):
		v_value = 'aix2'
	else:
		v_value = 'bf2'
	
	if g_debug: print 'Running MOD: %s' % (str(v_value))
	
	standardKeys = [
		("gm",		getGameModeId(bf2.serverSettings.getGameMode())),
		("m",		getMapId(bf2.serverSettings.getMapName())),
		("v",		str(v_value)),
		("pc",		len(statsMap)),
	]

	# only send rwa key if there was a winner
	winner = bf2.gameLogic.getWinner()
	if winner != 0: 
		standardKeys += [("rwa", roundArmies[winner])]
	
	# get final ticket score
	if g_debug: print "Army 1 (%s) Score: %s" % (str(roundArmies[1]), str(bf2.gameLogic.getTickets(1)))
	if g_debug: print "Army 2 (%s) Score: %s" % (str(roundArmies[2]), str(bf2.gameLogic.getTickets(2)))
	standardKeys += [
		("ra1", str(roundArmies[1])),
		("rs1", str(bf2.gameLogic.getTickets(1))),
		("ra2", str(roundArmies[2])),
		("rs2", str(bf2.gameLogic.getTickets(2))),
    ]
	
	standardKeys += [("rst2", str(bf2.gameLogic.getTickets(2)))]
	
	stdKeyVals = []
	for k in standardKeys:
		stdKeyVals.append ("\\".join((k[0], str(k[1]))))

	snapShot += "\\".join(stdKeyVals)

	if g_debug: print 'Snapshot Pre-processing complete: %s' % (str(snapShot))
	
	playerSnapShots = ""
	if g_debug: print 'Num clients to base snap on: %d' % (len(statsMap))
	for sp in statsMap.itervalues():
		if g_debug: print 'Processing PID: %s' % (str(sp.profileId))
		playerSnapShots += getPlayerSnapshot(sp)

	print "Doing Player SNAPSHOTS"
	snapShot += playerSnapShots
	
	# Add EOF marker for validation
	snapShot += "\\EOF\\1"
	
	return snapShot



def getPlayerSnapshot(playerStat):

	# The player didn't spawn in... 
	if playerStat.timePlayed == 0:
		return ""
		
	playerKeys = 	[

		# main keys 
		("pID", 	playerStat.profileId),
		("name",	playerStat.name),
		("t",		playerStat.team),
		("a",		playerStat.army),
		("ctime",	int(playerStat.timePlayed)),
		("c",		playerStat.complete),
		("ip",		playerStat.ipaddr),
		("ai",		playerStat.isAIPlayer),
		
		# score keys
		("rs",		playerStat.score),
		("cs",		playerStat.cmdScore),
		("ss", 		playerStat.skillScore),
		("ts",		playerStat.teamScore),
		("kills",	playerStat.kills),
		("deaths",	playerStat.deaths),
		("cpc",		playerStat.localScore.cpCaptures + playerStat.localScore.cpNeutralizes),
		("cpa",		playerStat.localScore.cpAssists + playerStat.localScore.cpNeutralizeAssists),
		#("cpc",	playerStat.localScore.cpCaptures),			// Processed in backend
		#("cpn",	playerStat.localScore.cpNeutralizes),		// Processed in backend
		#("cpa",	playerStat.localScore.cpAssists),			// Processed in backend
		#("cpna",	playerStat.localScore.cpNeutralizeAssists),	// Processed in backend
		("cpd",		playerStat.localScore.cpDefends),
		("ka",		playerStat.localScore.damageAssists),
		("he",		playerStat.localScore.heals),
		("rev",		playerStat.localScore.revives),
		("rsp",		playerStat.localScore.ammos),
		("rep",		playerStat.localScore.repairs),
		("tre",		playerStat.localScore.targetAssists),
		("drs",		playerStat.localScore.driverSpecials + playerStat.localScore.driverAssists),
		#("drs",	playerStat.localScore.driverSpecials),		// Processed in backend
		#("dra",	playerStat.localScore.driverAssists),		// Processed in backend
		#("pa",		playerStat.localScore.passengerAssists),	// Processed in backend
		
		# Additional player stats
		("tmkl",	playerStat.teamkills),
		("tmdg",	playerStat.localScore.teamDamages),
		("tmvd",	playerStat.localScore.teamVehicleDamages),
		("su",		playerStat.localScore.suicides),
		("ks",		playerStat.longestKillStreak),
		("ds",		playerStat.longestDeathStreak),
		("rank",	playerStat.rank),
		("ban",		playerStat.timesBanned),
		("kck",		playerStat.timesKicked),		
		
		# time keys
		("tco",		int(playerStat.timeAsCmd)),
		("tsl",		int(playerStat.timeAsSql)),
		("tsm",		int(playerStat.timeInSquad - playerStat.timeAsSql)),
		("tlw",		int(playerStat.timePlayed - playerStat.timeAsCmd - playerStat.timeInSquad)),
		
		# Base Game Stuff
		("ta0",		int(playerStat.timeAsArmy[ARMY_USA])),
		("ta1",		int(playerStat.timeAsArmy[ARMY_MEC])),
		("ta2", 	int(playerStat.timeAsArmy[ARMY_CHINESE])),
		#XPack1 Stuff
		("ta3", 	int(playerStat.timeAsArmy[ARMY_SEALS])),
		("ta4", 	int(playerStat.timeAsArmy[ARMY_SAS])),
		("ta5", 	int(playerStat.timeAsArmy[ARMY_SPETZNAS])),
		("ta6", 	int(playerStat.timeAsArmy[ARMY_MECSF])),
		("ta7", 	int(playerStat.timeAsArmy[ARMY_REBELS])),
		("ta8", 	int(playerStat.timeAsArmy[ARMY_INSURGENTS])),
		#EF Booster Pack Stuff
		("ta9", 	int(playerStat.timeAsArmy[ARMY_EURO])),
		#POE2 Stuff
		("ta10", 	int(playerStat.timeAsArmy[ARMY_GER])),
		("ta11", 	int(playerStat.timeAsArmy[ARMY_UKR])),
		#AIX
		("ta12",     int(playerStat.timeAsArmy[ARMY_UN])),
		#CANADIAN FORCES
		("ta13",     int(playerStat.timeAsArmy[ARMY_CANADIAN])),

	]
	
	# victims / victimizers
	statsMap = getStatsMap()

	for p in playerStat.killedPlayer:
		if not p in statsMap:
			if g_debug: print "killedplayer_id victim connorder: ", playerStat.killedPlayer[p], " wasnt in statsmap!"
		else:
			playerKeys.append(("mvns", str(statsMap[p].profileId)))
			playerKeys.append(("mvks", str(playerStat.killedPlayer[p])))

# Added by Chump - for bf2statistics stats
	#for p in playerStat.killedByPlayer:
	#	if not p in statsMap:
	#		if g_debug: print "killedBYplayer_id victim connorder: ", playerStat.killedByPlayer[p], " wasnt in statsmap!"
	#	else:
	#		playerKeys.append(("vmns", str(statsMap[p].profileId)))
	#		playerKeys.append(("vmks", str(playerStat.killedByPlayer[p])))

	keyvals = []
	for k in playerKeys:
		keyvals.append ("\\".join((k[0], str(k[1]))))

	playerSnapShot = "\\".join(keyvals)
	
	# medals
	medalsSnapShot = ""
	if playerStat.medals:
		if g_debug: print "Medals Found (%s), Processing Medals Snapshot" % (playerStat.profileId)
		medalsSnapShot = playerStat.medals.getSnapShot()
	
	# vehicles
	vehicleKeys = 	[
		("tv0",		int(playerStat.vehicles[VEHICLE_TYPE_ARMOR].timeInObject)),
		("tv1",		int(playerStat.vehicles[VEHICLE_TYPE_AVIATOR].timeInObject)),
		("tv2",		int(playerStat.vehicles[VEHICLE_TYPE_AIRDEFENSE].timeInObject)),
		("tv3",		int(playerStat.vehicles[VEHICLE_TYPE_HELICOPTER].timeInObject)),
		("tv4",		int(playerStat.vehicles[VEHICLE_TYPE_TRANSPORT].timeInObject)),
		("tv5",		int(playerStat.vehicles[VEHICLE_TYPE_ARTILLERY].timeInObject)),
		("tv6",		int(playerStat.vehicles[VEHICLE_TYPE_GRNDDEFENSE].timeInObject)),
		("tvp",		int(playerStat.vehicles[VEHICLE_TYPE_PARACHUTE].timeInObject)),

		# Added by Chump - these do not register with onEnterVehicle()
		# XPack1 Stuff
		#("tnv",		int(playerStat.vehicles[VEHICLE_TYPE_NIGHTVISION].timeInObject)),
		#("tgm",		int(playerStat.vehicles[VEHICLE_TYPE_GASMASK].timeInObject)),
		
		("kv0",		playerStat.vehicles[VEHICLE_TYPE_ARMOR].kills),
		("kv1",		playerStat.vehicles[VEHICLE_TYPE_AVIATOR].kills),
		("kv2",		playerStat.vehicles[VEHICLE_TYPE_AIRDEFENSE].kills),
		("kv3",		playerStat.vehicles[VEHICLE_TYPE_HELICOPTER].kills),
		("kv4",		playerStat.vehicles[VEHICLE_TYPE_TRANSPORT].kills),
		("kv5",		playerStat.vehicles[VEHICLE_TYPE_ARTILLERY].kills),
		("kv6",		playerStat.vehicles[VEHICLE_TYPE_GRNDDEFENSE].kills),
		
		("bv0",		playerStat.vehicles[VEHICLE_TYPE_ARMOR].deaths),
		("bv1",		playerStat.vehicles[VEHICLE_TYPE_AVIATOR].deaths),
		("bv2",		playerStat.vehicles[VEHICLE_TYPE_AIRDEFENSE].deaths),
		("bv3",		playerStat.vehicles[VEHICLE_TYPE_HELICOPTER].deaths),
		("bv4",		playerStat.vehicles[VEHICLE_TYPE_TRANSPORT].deaths),
		("bv5",		playerStat.vehicles[VEHICLE_TYPE_ARTILLERY].deaths),
		("bv6",		playerStat.vehicles[VEHICLE_TYPE_GRNDDEFENSE].deaths),

		("kvr0",	playerStat.vehicles[VEHICLE_TYPE_ARMOR].roadKills),
		("kvr1",	playerStat.vehicles[VEHICLE_TYPE_AVIATOR].roadKills),
		("kvr2",	playerStat.vehicles[VEHICLE_TYPE_AIRDEFENSE].roadKills),
		("kvr3",	playerStat.vehicles[VEHICLE_TYPE_HELICOPTER].roadKills),
		("kvr4",	playerStat.vehicles[VEHICLE_TYPE_TRANSPORT].roadKills),
		("kvr5",	playerStat.vehicles[VEHICLE_TYPE_ARTILLERY].roadKills),
		("kvr6",	playerStat.vehicles[VEHICLE_TYPE_GRNDDEFENSE].roadKills),

	]

	vehkeyvals = []
	for k in vehicleKeys:
		#if k[1] == 0: continue
		vehkeyvals.append ("\\".join((k[0], str(k[1]))))

	vehicleSnapShot = "\\".join(vehkeyvals)
	
	# kits
	kitKeys = 	[
		("tk0",		int(playerStat.kits[KIT_TYPE_AT].timeInObject)),
		("tk1",		int(playerStat.kits[KIT_TYPE_ASSAULT].timeInObject)),
		("tk2",		int(playerStat.kits[KIT_TYPE_ENGINEER].timeInObject)),
		("tk3",		int(playerStat.kits[KIT_TYPE_MEDIC].timeInObject)),
		("tk4",		int(playerStat.kits[KIT_TYPE_SPECOPS].timeInObject)),
		("tk5",		int(playerStat.kits[KIT_TYPE_SUPPORT].timeInObject)),
		("tk6",		int(playerStat.kits[KIT_TYPE_SNIPER].timeInObject)),
		
		("kk0",		playerStat.kits[KIT_TYPE_AT].kills),
		("kk1",		playerStat.kits[KIT_TYPE_ASSAULT].kills),
		("kk2",		playerStat.kits[KIT_TYPE_ENGINEER].kills),
		("kk3",		playerStat.kits[KIT_TYPE_MEDIC].kills),
		("kk4",		playerStat.kits[KIT_TYPE_SPECOPS].kills),
		("kk5",		playerStat.kits[KIT_TYPE_SUPPORT].kills),
		("kk6",		playerStat.kits[KIT_TYPE_SNIPER].kills),
		
		("dk0",		playerStat.kits[KIT_TYPE_AT].deaths),
		("dk1",		playerStat.kits[KIT_TYPE_ASSAULT].deaths),
		("dk2",		playerStat.kits[KIT_TYPE_ENGINEER].deaths),
		("dk3",		playerStat.kits[KIT_TYPE_MEDIC].deaths),
		("dk4",		playerStat.kits[KIT_TYPE_SPECOPS].deaths),
		("dk5",		playerStat.kits[KIT_TYPE_SUPPORT].deaths),
		("dk6",		playerStat.kits[KIT_TYPE_SNIPER].deaths),
	]

	kitkeyvals = []
	for k in kitKeys:
		kitkeyvals.append ("\\".join((k[0], str(k[1]))))

	kitSnapShot = "\\".join(kitkeyvals)
		
	# weapons
	weaponKeys = 	[
		("tw0",		int(playerStat.weapons[WEAPON_TYPE_ASSAULT].timeInObject)),
		("tw1",		int(playerStat.weapons[WEAPON_TYPE_ASSAULTGRN].timeInObject)),
		("tw2",		int(playerStat.weapons[WEAPON_TYPE_CARBINE].timeInObject)),
		("tw3",		int(playerStat.weapons[WEAPON_TYPE_LMG].timeInObject)),
		("tw4",		int(playerStat.weapons[WEAPON_TYPE_SNIPER].timeInObject)),
		("tw5",		int(playerStat.weapons[WEAPON_TYPE_PISTOL].timeInObject)),
		("tw6",		int(playerStat.weapons[WEAPON_TYPE_ATAA].timeInObject)),
		("tw7",		int(playerStat.weapons[WEAPON_TYPE_SMG].timeInObject)),
		("tw8",		int(playerStat.weapons[WEAPON_TYPE_SHOTGUN].timeInObject)),
		("te0",		int(playerStat.weapons[WEAPON_TYPE_KNIFE].timeInObject)),
		("te1",		int(playerStat.weapons[WEAPON_TYPE_C4].timeInObject)),
		("te3",		int(playerStat.weapons[WEAPON_TYPE_HANDGRENADE].timeInObject)),
		("te2",		int(playerStat.weapons[WEAPON_TYPE_CLAYMORE].timeInObject)),
		("te4",		int(playerStat.weapons[WEAPON_TYPE_SHOCKPAD].timeInObject)),
		("te5",		int(playerStat.weapons[WEAPON_TYPE_ATMINE].timeInObject)),
		# XPack1 Stuff
		("te6",		int(playerStat.weapons[WEAPON_TYPE_TACTICAL].timeInObject)),
		("te7",		int(playerStat.weapons[WEAPON_TYPE_GRAPPLINGHOOK].timeInObject)),
		("te8",		int(playerStat.weapons[WEAPON_TYPE_ZIPLINE].timeInObject)),
		
		("kw0",		playerStat.weapons[WEAPON_TYPE_ASSAULT].kills),
		("kw1",		playerStat.weapons[WEAPON_TYPE_ASSAULTGRN].kills),
		("kw2",		playerStat.weapons[WEAPON_TYPE_CARBINE].kills),
		("kw3",		playerStat.weapons[WEAPON_TYPE_LMG].kills),
		("kw4",		playerStat.weapons[WEAPON_TYPE_SNIPER].kills),
		("kw5",		playerStat.weapons[WEAPON_TYPE_PISTOL].kills),
		("kw6",		playerStat.weapons[WEAPON_TYPE_ATAA].kills),
		("kw7",		playerStat.weapons[WEAPON_TYPE_SMG].kills),
		("kw8",		playerStat.weapons[WEAPON_TYPE_SHOTGUN].kills),
		("ke0",		playerStat.weapons[WEAPON_TYPE_KNIFE].kills),
		("ke1",		playerStat.weapons[WEAPON_TYPE_C4].kills),
		("ke3",		playerStat.weapons[WEAPON_TYPE_HANDGRENADE].kills),
		("ke2",		playerStat.weapons[WEAPON_TYPE_CLAYMORE].kills),
		("ke4",		playerStat.weapons[WEAPON_TYPE_SHOCKPAD].kills),
		("ke5",		playerStat.weapons[WEAPON_TYPE_ATMINE].kills),

		("bw0",		playerStat.weapons[WEAPON_TYPE_ASSAULT].deaths),
		("bw1",		playerStat.weapons[WEAPON_TYPE_ASSAULTGRN].deaths),
		("bw2",		playerStat.weapons[WEAPON_TYPE_CARBINE].deaths),
		("bw3",		playerStat.weapons[WEAPON_TYPE_LMG].deaths),
		("bw4",		playerStat.weapons[WEAPON_TYPE_SNIPER].deaths),
		("bw5",		playerStat.weapons[WEAPON_TYPE_PISTOL].deaths),
		("bw6",		playerStat.weapons[WEAPON_TYPE_ATAA].deaths),
		("bw7",		playerStat.weapons[WEAPON_TYPE_SMG].deaths),
		("bw8",		playerStat.weapons[WEAPON_TYPE_SHOTGUN].deaths),
		("be0",		playerStat.weapons[WEAPON_TYPE_KNIFE].deaths),
		("be1",		playerStat.weapons[WEAPON_TYPE_C4].deaths),
		("be3",		playerStat.weapons[WEAPON_TYPE_HANDGRENADE].deaths),
		("be2",		playerStat.weapons[WEAPON_TYPE_CLAYMORE].deaths),
		("be4",		playerStat.weapons[WEAPON_TYPE_SHOCKPAD].deaths),
		("be5",		playerStat.weapons[WEAPON_TYPE_ATMINE].deaths),
		# XPack1 Stuff
		("be8",		playerStat.weapons[WEAPON_TYPE_ZIPLINE].deaths),
		("be9",		playerStat.weapons[WEAPON_TYPE_GRAPPLINGHOOK].deaths),

		# XPack1 Stuff
		("de6",		playerStat.weapons[WEAPON_TYPE_TACTICAL].deployed),
		("de7",		playerStat.weapons[WEAPON_TYPE_GRAPPLINGHOOK].deployed),
		("de8",		playerStat.weapons[WEAPON_TYPE_ZIPLINE].deployed),
		
		("sw0",		playerStat.weapons[WEAPON_TYPE_ASSAULT].bulletsFired),
		("sw1",		playerStat.weapons[WEAPON_TYPE_ASSAULTGRN].bulletsFired),
		("sw2",		playerStat.weapons[WEAPON_TYPE_CARBINE].bulletsFired),
		("sw3",		playerStat.weapons[WEAPON_TYPE_LMG].bulletsFired),
		("sw4",		playerStat.weapons[WEAPON_TYPE_SNIPER].bulletsFired),
		("sw5",		playerStat.weapons[WEAPON_TYPE_PISTOL].bulletsFired),
		("sw6",		playerStat.weapons[WEAPON_TYPE_ATAA].bulletsFired),
		("sw7",		playerStat.weapons[WEAPON_TYPE_SMG].bulletsFired),
		("sw8",		playerStat.weapons[WEAPON_TYPE_SHOTGUN].bulletsFired),
		
		("se0",		playerStat.weapons[WEAPON_TYPE_KNIFE].bulletsFired),
		("se1",		playerStat.weapons[WEAPON_TYPE_C4].bulletsFired),
		("se2",		playerStat.weapons[WEAPON_TYPE_CLAYMORE].bulletsFired),
		("se3",		playerStat.weapons[WEAPON_TYPE_HANDGRENADE].bulletsFired),
		("se4",		playerStat.weapons[WEAPON_TYPE_SHOCKPAD].bulletsFired),
		("se5",		playerStat.weapons[WEAPON_TYPE_ATMINE].bulletsFired),

		("hw0",		playerStat.weapons[WEAPON_TYPE_ASSAULT].bulletsHit),
		("hw1",		playerStat.weapons[WEAPON_TYPE_ASSAULTGRN].bulletsHit),
		("hw2",		playerStat.weapons[WEAPON_TYPE_CARBINE].bulletsHit),
		("hw3",		playerStat.weapons[WEAPON_TYPE_LMG].bulletsHit),
		("hw4",		playerStat.weapons[WEAPON_TYPE_SNIPER].bulletsHit),
		("hw5",		playerStat.weapons[WEAPON_TYPE_PISTOL].bulletsHit),
		("hw6",		playerStat.weapons[WEAPON_TYPE_ATAA].bulletsHit),
		("hw7",		playerStat.weapons[WEAPON_TYPE_SMG].bulletsHit),
		("hw8",		playerStat.weapons[WEAPON_TYPE_SHOTGUN].bulletsHit),
		
		("he0",		playerStat.weapons[WEAPON_TYPE_KNIFE].bulletsHit),
		("he1",		playerStat.weapons[WEAPON_TYPE_C4].bulletsHit),
		("he2",		playerStat.weapons[WEAPON_TYPE_CLAYMORE].bulletsHit),
		("he3",		playerStat.weapons[WEAPON_TYPE_HANDGRENADE].bulletsHit),
		("he4",		playerStat.weapons[WEAPON_TYPE_SHOCKPAD].bulletsHit),
		("he5",		playerStat.weapons[WEAPON_TYPE_ATMINE].bulletsHit),
	]

	weapkeyvals = []
	for k in weaponKeys:
		weapkeyvals.append ("\\".join((k[0], str(k[1]))))

	weaponSnapShot = "\\".join(weapkeyvals)
	
	allSnapShots = []
	if len(playerSnapShot) > 0: allSnapShots = allSnapShots + [playerSnapShot]
	if len(medalsSnapShot) > 0: allSnapShots = allSnapShots + [medalsSnapShot]
	if len(vehicleSnapShot) > 0: allSnapShots = allSnapShots + [vehicleSnapShot]
	if len(kitSnapShot) > 0: allSnapShots = allSnapShots + [kitSnapShot]
	if len(weaponSnapShot) > 0: allSnapShots = allSnapShots + [weaponSnapShot]
	
	playerSnapShot = "\\".join(allSnapShots)
	
	# add pid to all keys (gamespy likes this)
	transformedSnapShot = ""
	i = 0
	idString = "_" + str(playerStat.connectionOrderNr)
	
	while i < len(playerSnapShot):
		key = ""
		while playerSnapShot[i] != "\\":
			key += playerSnapShot[i]
			i += 1
		i += 1
		value = ""
		while i < len(playerSnapShot) and playerSnapShot[i] != "\\":
			value += playerSnapShot[i]
			i += 1

		transformedKeyVal = key + idString + "\\" + value
		if i != len(playerSnapShot):
			transformedKeyVal += "\\"
			
		transformedSnapShot += transformedKeyVal
		i += 1
		
	return "\\" + transformedSnapShot

