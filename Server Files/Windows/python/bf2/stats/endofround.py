import host
import bf2.PlayerManager
import bf2.GameLogic
from constants import *
from bf2 import g_debug
from bf2.stats.stats import getStatsMap



def init():
	host.registerHandler('ConsoleSendCommand', onSendCommand)
	if g_debug: print "End of round module initialized."


def onSendCommand(command, args):
	#if g_debug: print "command:", command, " args:", args
	if string.lower(command) == "eor":
		for p in bf2.playerManager.getPlayers():
			bf2.stats.stats.finalizePlayer(p)

		invoke()

def invoke():
	if g_debug: print "Invoked end-of-round data-send"

	# collect needed stats
	e = {}
	
	statsMap = getStatsMap()
	
	# find top player in different categories
	for sp in statsMap.itervalues():
		for k in range(0, NUM_KIT_TYPES + 1):
			if k in sp.kits and sp.kits[k].timeInObject > 0:
				findTop(e, "sk" + str(k), "skn" + str(k), sp.kits[k].score, sp.name)

		for v in range(0, NUM_VEHICLE_TYPES + 1):
			if v in sp.vehicles and sp.vehicles[v].timeInObject > 0:
				findTop(e, "sv" + str(v), "svn" + str(v), sp.vehicles[v].score, sp.name)

		findTop(e, "ts", "tsn", sp.teamScore, sp.name)
		findTop(e, "ss", "ssn", sp.skillScore, sp.name)
		findTop(e, "cpc", "cpcn", sp.localScore.cpCaptures, sp.name)
		findTop(e, "cpa", "cpan", sp.localScore.cpAssists, sp.name)
		findTop(e, "cpd", "cpdn", sp.localScore.cpDefends, sp.name)
		findTop(e, "ka", "kan", sp.localScore.damageAssists + sp.localScore.targetAssists + sp.localScore.passengerAssists, sp.name)
		findTop(e, "he", "hen", sp.localScore.heals, sp.name)
		findTop(e, "rev", "revn", sp.localScore.revives, sp.name)
		findTop(e, "rsp", "rspn", sp.localScore.ammos, sp.name)
		findTop(e, "rep", "repn", sp.localScore.repairs, sp.name)
		findTop(e, "drs", "drsn", sp.localScore.driverSpecials + sp.localScore.driverAssists, sp.name)
		
	
	# find top-3
	if len(statsMap) > 0:
		sortedPlayers = []
		for sp in statsMap.itervalues():
			sortedPlayers += [((sp.score, sp.skillScore, -sp.deaths), sp)]
	
		sortedPlayers.sort()
		sortedPlayers.reverse()
		
		# stats for top-3 scoring players
		for i in range(3):
			if len(sortedPlayers) <= i:
				break
	
			sp = sortedPlayers[i][1]
			e["np" + str(i)] = sp.name
			e["tsp" + str(i)] = sp.teamScore
			e["ssp" + str(i)] = sp.skillScore
			e["csp" + str(i)] = sp.cmdScore
			e["bfp" + str(i)] = sp.bulletsFired
			e["bhp" + str(i)] = sp.bulletsHit
			for k in range(0, NUM_KIT_TYPES + 1):
				if sp.kits[k].timeInObject > 0:
					e["tk" + str(k) + "p" + str(i)] = int(sp.kits[k].timeInObject)
					
			for v in range(0, NUM_VEHICLE_TYPES + 1):
				if sp.vehicles[v].timeInObject > 0:
					e["tv" + str(v) + "p" + str(i)] = int(sp.vehicles[v].timeInObject)
		
	keyvals = []
	for k in e:
		keyvals.append ("\\".join((k, str(e[k]))))

	dataString = "\\" + "\\".join(keyvals)
	
	if g_debug: print dataString
	host.gl_sendEndOfRoundData(dataString)

	
		
def findTop(e, vkey, nkey, value, name):
	if not vkey in e or value > e[vkey]:
		e[vkey] = value
		e[nkey] = name
	
		
