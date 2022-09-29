# rank upgrades

import host
import bf2.PlayerManager
import bf2.Timer
from bf2.stats.constants import *
from bf2 import g_debug



def init():
	# Events
	host.registerHandler('ConsoleSendCommand', onSendCommand)

	if bf2.serverSettings.getUseGlobalRank():
		host.registerHandler('PlayerConnect', onPlayerConnect, 1)
		host.registerHandler('PlayerStatsResponse', onStatsResponse, 1)

	host.registerGameStatusHandler(onGameStatusChanged)
	
	# Connect already connected players if reinitializing
	for p in bf2.playerManager.getPlayers():
		onPlayerConnect(p)

	if g_debug: print "Rank module initialized"

		
def onSendCommand(command, args):
	if command == "giverank":
		pId = int(args[0])
		rank = int(args[1])
		
		player = bf2.playerManager.getPlayerByIndex(pId)
		awardRank(player, rank)
	
	#jlo: temp for laedre! remove!
	elif command == "setname":
		pId = int(args[0])
		name = args[1]
		player = bf2.playerManager.getPlayerByIndex(pId)
		player.setName(name)

def onGameStatusChanged(status):
	if status == bf2.GameStatus.Playing:
		#updateTimer = bf2.Timer(onUpdate, 20)
		#updateTimer.setRecurring(20)
		pass
	else:
		if g_debug: print "Destroyed timer"
		#updateTimer.destroy()


def onUpdate(data):
	#if g_debug: print "update rank"
	for p in bf2.playerManager.getPlayers():
		if p.isAlive():
			checkRank(p)



### Event hooks

def onPlayerConnect(player):
	id = player.index
	if player.score.rank == -1:
		player.score.rank = 0
	
	# request rank
	if bf2.serverSettings.getUseGlobalRank():
		if player.getProfileId() > 2000:		
			success = host.pers_plrRequestStats(player.index, 1, "&info=rank")
		else:
			if g_debug: print "Player %d had no profile id, can't request rank" % player.index
		
	
	if g_debug: print "Added player %d to rank checking" % (player.index)
	
	
	
def onStatsResponse(succeeded, player, stats):
	if player == None:
		playerIndex = "unknown"
	else:
		playerIndex = player.index

	if not "rank" in stats:
		if g_debug: print "rank not found, aborting"
		return

	if g_debug: print "Rank received for player ", playerIndex, ":", host.pers_getStatsKeyVal("rank")
	if not player: return
	
	
	if not player: return
	
	if not "rank" in stats:
		if g_debug: print "Rank not found in stats response"
	else:
		value = host.pers_getStatsKeyVal("rank")
		if g_debug: print "Player %s Rank:%s" % (player.index, int(value))
		player.score.rank = int(value)
		player.stats.rank = int(value)
		
		
		
		






