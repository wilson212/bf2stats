import host
import bf2.PlayerManager
from bf2.stats.constants import *
from bf2 import g_debug

# ------------------------------------------------------------------------------
# omero 2006-02-27
# ------------------------------------------------------------------------------
from bf2.BF2StatisticsConfig import http_backend_addr, http_backend_port
from bf2.stats.miniclient import miniclient, http_get



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
	if g_debug: print "Initializing unlock module..."
	# Events
	host.registerHandler('PlayerConnect', onPlayerConnect, 1)

# Added by Chump - for bf2statistics stats (plus de-indenting)
	#if bf2.serverSettings.getUseGlobalUnlocks():
	host.registerHandler('PlayerUnlocksResponse', onUnlocksResponse, 1)

	# Connect already connected players if reinitializing
	for p in bf2.playerManager.getPlayers():
		onPlayerConnect(p)

	if g_debug: print "Unlock module initialized"



class UnlockSet: pass



def onPlayerConnect(player):

	defaultUnlocks = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
	host.pers_plrSetUnlocks(player.index, defaultUnlocks, defaultUnlocks)

	if g_debug: print "Unlock module: onPlayerConnect"
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
		
# Added by Chump - for bf2statistics stats (plus de-indenting)
		#if bf2.serverSettings.getUseGlobalUnlocks():
		if player.getProfileId() > 2000:
			# Added by ArmEagle needed to prevent servercrash on linux on offline account connect
			#    but host.pmgr_p_set logically raises an exception when called on an online account
			if host.pmgr_p_get("profileid", player.index) == 0:
				host.pmgr_p_set("profileid", player.index, player.getProfileId())
			
			success = host.pers_plrRequestUnlocks(player.index, 1)
			if not success:
				if g_debug: print "Requesting unlocks: Failed"
			else:
				if g_debug: print "Requesting unlocks: Success"
				
		else:
			if g_debug: print "Player %d had no profile id, can't request unlocks" % player.index
				
		if g_debug: print "Added player %d to unlock checking" % (player.index)
		


def onUnlocksResponse(succeeded, player, unlocks):
	if not succeeded:
		print "Unlocks request failed for player %d (%s): %s" % (player.index, player.getName(), unlocks)
		return
	
	if g_debug: print "Unlocks received for player %d (%s): %s" % (player.index, player.getName(), unlocks)
	
	# translate gamespy item vector into a kit-based unlock vector handled by game
	kitUnlocks = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
	for item in unlocks:
		if item in unlockItemMap:
			kitUnlocks[unlockItemMap[item]] = 1
		
	if g_debug: print "Kit unlocks: ", kitUnlocks
	#We do not yet support giving different unlocks to different teams
	host.pers_plrSetUnlocks(player.index, kitUnlocks, kitUnlocks)

