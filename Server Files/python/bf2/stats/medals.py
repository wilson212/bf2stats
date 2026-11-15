# medals upgrades

# Changelog
# ------------------------------------------------------------------------------
# omero, 2006-06-02
# + corrected wrong indentation bug in onPlayerConnect() event callback function.
#
# omero, 2006-03-31
# + finalized import of configuration settings from bf2.BF2StatisticsConfig
#
# omero, 2006-03-15
#	+minor cosmethic changes to debug statements
#
# omero, 2006-03-10
#  +import http_backend_addr, http_backend_port from bf2.stats.BF2StatisticsConfig
#  +import http_get from bf2.stats.miniclient (improved code, reverted to HTTP/1.1)
#
# omero, 2006-03-03
#  +import http_backend_addr, http_backend_port from __init__.py
#
# omero, 2006-02-27
#  +added substitutes for onPlayerStatsResponse(), onPlayerAwardsResponse()
#  
# omero, 2006-02-20
#  +miniclient functions (HTTP/1.0) working.
#  +stats and award processing hardcoded in onPlayerConnect()
#
# omero, 2006-02-12
#  +experimental calls to miniclient functions (HTTP/1.1) working but with
#  +long time required to process each player (not closing socket?).
#
# Wilson212, 2012-02-02
#  + Improved custom medals data loading
#
# Wilson212, 2015-02-17
#  + Fixed a typo
#  + Fixed a bug loading xpack medal data from the Control Center
#  + createGlobalKeyString() removed, globalKeyString is now static (as it should be), since
#      the ASP stats server requires the keystring to be static
#  + Reverted to original Xpack medal checking as it was in the official python files
#	   Mods that will use SF medals are defined in the Bf2StatisticsConfig.py as "medals_xpack_mods"
# Krauzi, 2025-08-15
#  + removed http calls (using gamespy emulator services)
#  + removed rank setting (this is already happens in rank.py)
#  + removed xpack medal check - mods must define their medals in medal_data_<mod>.py
# ------------------------------------------------------------------------------

import host
import bf2.PlayerManager
import bf2.Timer
import os

from bf2 import g_debug

from constants import *

# Import mod specific medals data
modDirectory = host.sgl_getModDirectory()
if modDirectory == "mods/bf2":
	from medal_data import *
elif modDirectory == "mods/xpack":
	from medal_data_xpack import *
else:
	try:
		exec "from modal_data_" + modDirectory[len("mods/"):] + " import *"
	except:
		print "failed to load medal data for %s" % (modDirectory)
		from medal_data import *
	else:
		print "loaded medal data for %s" % (modDirectory)

# Get Player Stats Data
from stats import getStatsMap, getPlayerConnectionOrderIterator, setPlayerConnectionOrderIterator


sessionPlayerMedalMap = {}
def getMedalMap():
	global sessionPlayerMedalMap
	return sessionPlayerMedalMap

def setMedalMap(map):
	global sessionPlayerMedalMap
	sessionPlayerMedalMap = map

# The global key string used by servers to fetch stats from the ASP Backend
globalKeyString = "&info=rank,ktm-,dfcp,rpar,vtm-,bksk,scor,wdsk,wkl-,heal,dsab,cdsc,tsql,tsqm,wins,vkl-,twsc,time,kill,rsup,tcdr,de-,vac-"
g_lastPlayerChecked = 0

def init():
	# If stats are disabled, quit
	if host.ss_getParam("ranked"):
		host.registerHandler('PlayerConnect', onPlayerConnect, 1)
		host.registerHandler('PlayerDisconnect', onPlayerDisconnect, 1)
		host.registerHandler('PlayerStatsResponse', onStatsResponse, 1)
		host.registerHandler('PlayerAwardsResponse', onAwardsResponse, 1)
		host.registerGameStatusHandler(onGameStatusChanged)

		print "Medal awarding module initialized"


updateTimer = None

def onGameStatusChanged(status):
	global updateTimer
	if status == bf2.GameStatus.Playing:
		host.registerHandler('PlayerKilled', onPlayerKilled3)
		host.registerHandler('ExitVehicle', onExitVehicle)
		host.registerHandler('PlayerScore', onPlayerScore)
		
		if updateTimer:
			updateTimer.destroy()
	
		updateTimer = bf2.Timer(onUpdate, 1, 0)
		updateTimer.setRecurring(1)
	
		# connect already connected players if reinitializing
		for p in bf2.playerManager.getPlayers():
			onPlayerConnect(p)
			
		global g_lastPlayerChecked
		g_lastPlayerChecked = 0
	
	elif status == bf2.GameStatus.EndGame:
		print " ---------------- END OF GAME ----------------"
		givePositionalMedals(True, bf2.gameLogic.getWinner())
		
		# produce final snapshot
		bf2.stats.snapshot.invoke(True)
		
		if updateTimer:
			updateTimer.destroy()
			updateTimer = None



def onPlayerConnect(player):
	global updateTimer
	
	id = player.stats.connectionOrderNr
	
	# Check if player already in MedalMap, if so reconnect them
	reconnect = id in sessionPlayerMedalMap

	if id in sessionPlayerMedalMap:
		if g_debug: print "Player id=%d found in sessionPlayerMedalMap" % int(id)
	
	if not reconnect:
		newMedalSet = MedalSet()
		sessionPlayerMedalMap[id] = newMedalSet

	player.medals = sessionPlayerMedalMap[id]
	player.medals.connect(reconnect)

	if not reconnect:	
		#rank
		player.score.rank = 0

		# Added by Chump - for bf2statistics stats
		if g_debug: print "Added player %d, %s (%s) to medal/rank checking" % ( player.index, player.getName(), str(player.getProfileId()) )

	else:
		player.score.rank = player.stats.rank

		# Added by Chump - for bf2statistics stats
		if g_debug: print "Readded player %d to medal/rank checking" % player.index
		
		# This code is used to reduce the "request storm" generated at the start of a round. 
		#	Check if this player's STATS were updated in the last 30 seconds
		#	We only need to do this at the start of a round, just ignore otherwise
		if player.getGlobalUpdateTime() > 30 and player.stats.wasHereAtStart == 1:
			# STATS are a bit stale, force gamespy request (this should only occur ONCE per round)
			# Final check just to make sure player isn't reconnecting mid-round due to a CTD
			if player.stats.timeOnLine < 30:
				reconnect = False

	if not player.isAIPlayer():
		print "Getting STATS for player %s (%s)..."  % ( player.getName(), str(player.getProfileId()) )
		# get persistant stats from gamespy

		# Added by Chump - for bf2statistics stats (plus de-indenting)
		player.score.rank = player.stats.rank
		
		if host.pers_plrRequestStats(player.index, 1, globalKeyString):
			if g_debug: print "Successfully requested STATS via GameSpy"
		else:
			print "Failed to request STATS via GameSpy"
		
		if host.pers_plrRequestAwards(player.index, 1, ""):
			if g_debug: print "Successfully requested AWARDS via GameSpy"
		else:
			print "Failed to request AWARDS via GameSpy"
			
		# Record STATS update time
		player.setGlobalUpdateTime()


def onPlayerDisconnect(player):
	bf2.stats.snapshot.invoke()
	return

class MedalSet:
	def __init__(self):
		self.gsiMedals = {}
		self.roundMedals = {}
		self.globalKeys = {}
	
		
	def connect(self, reconnect):

		if not reconnect:
			# omero, 2006-03-14
			if g_debug: print "Will retrieve medals from GSI..."
			
			# init position medals
			self.placeMedals = [0, 0, 0]
		
		else:
			# already connected, just clear round-only medals
			if g_debug: print "Resetting unkept round-only medals..."
			for medal in medal_data:
				id = medal[0]
				keep = medal[2] != 0
				if not keep and id in self.roundMedals:
					del self.roundMedals[id]
	
		self.placeMedalThisRound = 0

		if g_debug: print "roundMedals: ", self.roundMedals
		
	
	def getSnapShot(self):
		medalKeys = {}
		prevKeys = {}
				
		# sum up medals with same key into one record (badges), for backend state and for current game state
		for medal in medal_data:
			id = medal[0]
			key = medal[1]
			
			if g_debug: print "Found Medal (%s:%s)" % (id, key)
			
			if '_' in medal[0]:
				# do special level calculation on badges, as they are sent as one key, but received as several
				if id in self.roundMedals:
					if not key in medalKeys:
						# can only have one
						medalKeys[key] = 1
					else:
						# increase medal level
						medalKeys[key] = medalKeys[key] + 1
	
				if id in self.gsiMedals:
					if not key in prevKeys:
						# can only have one
						prevKeys[key] = 1
					else:
						# increase medal level
						prevKeys[key] = prevKeys[key] + 1
		
		
			else:
				# regular medals
				if id in self.roundMedals:
					medalKeys[key] = self.roundMedals[id]
				
				if id in self.gsiMedals:
					prevKeys[key] = self.gsiMedals[id]


		# only send medal stats when we have increased level
		removeList = []
		for key in medalKeys:
			if key in prevKeys:
				if prevKeys[key] >= medalKeys[key]:
					# already had this medal, no need to send in snapshot
					removeList += [key]
		
		for key in removeList:
			del medalKeys[key]
		
		if self.placeMedalThisRound == 1:
			medalKeys['erg'] = 1
		elif self.placeMedalThisRound == 2:
			medalKeys['ers'] = 1
		elif self.placeMedalThisRound == 3:
			medalKeys['erb'] = 1	
		
		keyvals = []
		for k in medalKeys:
			keyvals.append ('{"id":"' + str(k) + '","level":' + str(medalKeys[k]) + '}')
				
		return ",".join(keyvals)



def givePositionalMedals(endOfRound, winningTeam):
	if endOfRound:
		# give medals for position
		sortedPlayers = []
		
		statsMap = getStatsMap()
		for sp in statsMap.itervalues():
			sortedPlayers += [((sp.score, sp.skillScore, -sp.deaths), sp.connectionOrderNr)]

		sortedPlayers.sort()
		sortedPlayers.reverse()
		
		global sessionPlayerMedalMap
		if len(sortedPlayers) > 0 and sortedPlayers[0][1] in sessionPlayerMedalMap:
			sessionPlayerMedalMap[sortedPlayers[0][1]].placeMedals[0] += 1
			sessionPlayerMedalMap[sortedPlayers[0][1]].placeMedalThisRound = 1
		if len(sortedPlayers) > 1 and sortedPlayers[1][1] in sessionPlayerMedalMap:
			sessionPlayerMedalMap[sortedPlayers[1][1]].placeMedals[1] += 1
			sessionPlayerMedalMap[sortedPlayers[1][1]].placeMedalThisRound = 2
		if len(sortedPlayers) > 2 and sortedPlayers[2][1] in sessionPlayerMedalMap:
			sessionPlayerMedalMap[sortedPlayers[2][1]].placeMedals[2] += 1
			sessionPlayerMedalMap[sortedPlayers[2][1]].placeMedalThisRound = 3
			
				

def onUpdate(data):
	global g_lastPlayerChecked
	
	# check one player
	for i in range (0, 2):
		p = bf2.playerManager.getNextPlayer(g_lastPlayerChecked)
		if not p: break

# Added by Chump - for bf2statistics stats
		#if p.isAlive() and not p.isAIPlayer():
		if p.isAlive():
			checkMedals(p)

		g_lastPlayerChecked = p.index
		

	
def onPlayerKilled3(victim, attacker, weapon, assists, object):
	if attacker != None:
		checkMedals(attacker)
	
	
	
def onExitVehicle(player, vehicle):
	checkMedals(player)



def onPlayerScore(player, difference):
	if player != None and difference > 0:
		checkRank(player)



def checkMedals(player):
	if not player.isAlive():
		return

	for medal in medal_data:
		
		# check that player does not already have this medal this round
		id = medal[0]
		
		# 2025/08/25 krauzi: removed xpack check as the imported medals are mod specific already
		#if its an xpack award, skip it if we are not playing SF
		#if not allow_xpack_medals:
		#	if id[1:3] == "26":
		#		continue
		
		# determine if player already has award
		player_has_award = id in player.medals.roundMedals
		
		# If medal does not have multiple-times criteria, and player has it already, skip
		if player_has_award and medal[2] != 2:
			continue
			
		# check if criteria was met
		checkCriteria = medal[3]
		if not checkCriteria(player):
			continue
		
		idStr = medal[0]
		newLevel = 1
		if '_' in medal[0]:
			# strip underscore
			newLevel = int(idStr[idStr.find('_') + 1:])
			idStr = idStr[:idStr.find('_')]

			awardMedal(player, int(idStr), newLevel)
		else:		
			if player_has_award:
				newLevel = player.medals.roundMedals[id] + 1	

			awardMedal(player, int(idStr), 0)
		
		player.medals.roundMedals[id] = newLevel
		


def checkRank(player):

	oldRank = player.score.rank

	rankCriteria = None
	highestRank = player.score.rank
	for rankItem in rank_data:
		rankCriteria = rankItem[2]
		if rankItem[0] > highestRank and rankCriteria(player):
			highestRank = rankItem[0]
	
	if oldRank < highestRank:
		player.score.rank = highestRank
		awardRank(player, player.score.rank)
		if g_debug: print "Player %s got promoted to rank: %d" % (player.getName(),player.score.rank)
	
		

def awardMedal(player, id, level):
	if g_debug: print "Player %s earned AWARD %d at level %d" % (player.getName(), id, level)
	bf2.gameLogic.sendMedalEvent(player, id, level)



def awardRank(player, rank):
	if g_debug: print "Player %s promoted from RANK %d to %d" % (player.getName(), player.score.rank, rank)
	bf2.gameLogic.sendRankEvent(player, rank, player.score.score)


	
def onStatsResponse(succeeded, player, stats):
	if not succeeded:
		if player == None:
			playerIndex = "unknown"
		else:
			playerIndex = player.index
		print "onStatsResponse -> Stats request FAILED for player ", playerIndex, ": ", stats
		return

	if not player: 
		if g_debug: print "onStatsResponse -> No player for stats response."
		return

	if "<html>" in stats:
		print "onStatsResponse -> The stats response seems wrong:"
		print stats
		print "<end-of-stats>"
		return
		
	if stats and stats[0] == 'O':
		print "onStatsResponse -> Stats response received for player %d size %d." % (player.index, len(stats))
	
		data = {}
		datalines = stats.splitlines()
		keys = datalines[3].split('\t')
		vals = datalines[4].split('\t')
		
		if (len(keys) == len(vals)):
			#if g_debug:	
			print "Assembling STATS dictionary with %d keys" % int(len(keys))
			
			for idx in range(1,len(keys)):
				data[keys[idx]] = vals[idx]
			
		# eventually reattach persistent stats records to this player
		attachPlayerStats(player, data)
		
	else:
		print "onStatsResponse -> Stats response received for player %d is NOT VALID! size %d" % (int(player.index), int(len(stats)))
	
	

# omero, 2006-03-15
# todo:
# is still of any use?
def getMedalEntry(key):
	for item in medal_data:
		if medalKey == item[0]:
			return item
	return None


	
# create faster medal-data lookup map
medalDataKeyLookup = {}
for item in medal_data:
	medalDataKeyLookup[item[0]] = item



def getMedalEntry(key):
	if key in medalDataKeyLookup:
		return medalDataKeyLookup[key]
	return None
	
	
	
def onAwardsResponse(succeeded, player, awards):
	if not succeeded:
		if player == None:
			playerIndex = "unknown"
		else:
			playerIndex = player.index
		
		print "onAwardsResponse -> Medal request failed for player ", playerIndex, ": ", stats
		return
		
	if g_debug: print "onAwardsResponse -> Awards response received: ", awards
	
	if not player: 
		print "onAwardsResponse -> No player for medal response."
		return
		
	for a in awards:
		medalKey = str(a[0])
		# distinguish between 4 types of medal-entry:
		# 1. Regular medal with key like '123456', with only one level and can only be gotten once
		# 2. Medal with simple key like '123456', can be gotten multiple times, where criteria is changed depending on previous level
		# 3. Medal with simple key like '123456', can be gotten multiple times regardless of previous level, but only once per round
		# 4. Badge with key like '123456_1', that has individual medal entries per level
		item = getMedalEntry(medalKey)
		if item:
		
			# some medals are not kept, and can be gotten regardless of previous level. ex: purple heart
			keep = item[2] != 0
			if keep:
				# case 1, 2
				player.medals.gsiMedals[medalKey] = int(a[1])
				player.medals.roundMedals[medalKey] = int(a[1])
			else:
				# case 3
				pass
			
		else:
			# case 4: badge with individual per-level criterias
			if a[1] > 0:
				medalKey += '_' + str(a[1])
				
			item = getMedalEntry(medalKey)	
			
			if item:
				player.medals.gsiMedals[medalKey] = 1
				player.medals.roundMedals[medalKey] = 1
			else:
				print "onAwardsResponse -> Medal", medalKey,"not found in medal data."
	
	print "onAwardsResponse -> Player medals:", player.medals.gsiMedals
