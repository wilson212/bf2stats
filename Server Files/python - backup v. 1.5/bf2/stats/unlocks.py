import host
import bf2.PlayerManager
from bf2.stats.constants import *
from bf2 import g_debug



# map gamespy item ids to kits
unlockItemMap = {
			11 : 0,
			22 : 1,
			33 : 2,
			44 : 3,
			55 : 4,
			66 : 5,
			77 : 6,
			88 : 1,
			99 : 2,
		       111 : 3,
		       222 : 4,
		       333 : 5,
		       444 : 0,
		       555 : 6,
		}

sessionPlayerUnlockMap = {}



def init():
	# Events
	host.registerHandler('PlayerConnect', onPlayerConnect, 1)
	
	if bf2.serverSettings.getUseGlobalUnlocks():
		host.registerHandler('PlayerUnlocksResponse', onUnlocksResponse, 1)

	# Connect already connected players if reinitializing
	for p in bf2.playerManager.getPlayers():
		onPlayerConnect(p)

	if g_debug: print "Unlock module initialized"



class UnlockSet: pass



def onPlayerConnect(player):

	defaultUnlocks = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
	host.pers_plrSetUnlocks(player.index, defaultUnlocks, defaultUnlocks)

	if not player.isAIPlayer():
		id = player.index
		reconnect = id in sessionPlayerUnlockMap
		
		# always get new unlocks on reconnect/map restart/map change etc
		if reconnect:
			del sessionPlayerUnlockMap[id]
			
		newUnlockSet = UnlockSet()

		newUnlockSet.unlockLevel = {}
		for i in range(0, NUM_KIT_TYPES):
			newUnlockSet.unlockLevel[i] = 0

		sessionPlayerUnlockMap[id] = newUnlockSet
		
		player.unlocks = sessionPlayerUnlockMap[id]

		if bf2.serverSettings.getUseGlobalUnlocks():
			if player.getProfileId() > 2000:		
				success = host.pers_plrRequestUnlocks(player.index, 1)
				if not success:
					if g_debug: print "Failed requesting unlocks"
			else:
				if g_debug: print "Player %d had no profile id, can't request unlocks" % player.index
				
		if g_debug: print "Added player %d to unlock checking" % (player.index)
		
		

def onUnlocksResponse(succeeded, player, unlocks):
	if not succeeded:
		print "Unlocks request failed for player %d %d: %s" % (player.index, player.getName(), unlocks)
		return
	
	# print "Unlocks received for player ", player.getName(), "(",player.index, ") : ", unlocks
	
	# translate gamespy item vector into a kit-based unlock vector handled by game
	kitUnlocks = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
	for item in unlocks:
		if item in unlockItemMap:
			kitUnlocks[unlockItemMap[item]] = 1
		
	if g_debug: print "Kit unlocks: ", kitUnlocks
	#We do not yet support giving different unlocks to different teams
	host.pers_plrSetUnlocks(player.index, kitUnlocks, kitUnlocks)

