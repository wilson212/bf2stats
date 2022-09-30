# common scoring

import host
import bf2
from bf2.stats.constants import *
from bf2 import g_debug

# Player Scoring

SCORE_KILL = 2
SCORE_TEAMKILL = -4
SCORE_SUICIDE = -2
SCORE_REVIVE = 2
SCORE_TEAMDAMAGE = -2
SCORE_TEAMVEHICLEDAMAGE = -1
SCORE_DESTROYREMOTECONTROLLED = 1
SCORE_DRIVERSPECIAL = 1 # special point given to driver, if someone in vehicle gets an abilitypoint

SCORE_KILLASSIST_DRIVER = 1
SCORE_KILLASSIST_PASSENGER = 0
SCORE_KILLASSIST_TARGETER = 1
SCORE_KILLASSIST_DAMAGE = 1

SCORE_HEAL = 1
SCORE_GIVEAMMO = 1
SCORE_REPAIR = 1

# Bot Scoring

AI_SCORE_KILL = 2
AI_SCORE_TEAMKILL = -4
AI_SCORE_SUICIDE = -2
AI_SCORE_REVIVE = 2
AI_SCORE_TEAMDAMAGE = -2
AI_SCORE_TEAMVEHICLEDAMAGE = -1
AI_SCORE_DESTROYREMOTECONTROLLED = 1
AI_SCORE_DRIVERSPECIAL = 1

AI_SCORE_KILLASSIST_DRIVER = 1
AI_SCORE_KILLASSIST_PASSENGER = 0
AI_SCORE_KILLASSIST_TARGETER = 1
AI_SCORE_KILLASSIST_DAMAGE = 1

AI_SCORE_HEAL = 1
AI_SCORE_GIVEAMMO = 1
AI_SCORE_REPAIR = 1

#Global

REPAIR_POINT_LIMIT = 100
HEAL_POINT_LIMIT = 100
GIVEAMMO_POINT_LIMIT = 100
TEAMDAMAGE_POINT_LIMIT = 50
TEAMVEHICLEDAMAGE_POINT_LIMIT = 50

REPLENISH_POINT_MIN_INTERVAL = 30	# seconds

# sub score
NORMAL = 0
SKILL = 1
RPL = 2
CMND = 3



def init():

	# set limits for how many repair HPs etc are needed to get a callback
	bf2.gameLogic.setHealPointLimit(HEAL_POINT_LIMIT)
	bf2.gameLogic.setRepairPointLimit(REPAIR_POINT_LIMIT)
	bf2.gameLogic.setGiveAmmoPointLimit(GIVEAMMO_POINT_LIMIT)
	bf2.gameLogic.setTeamDamagePointLimit(TEAMDAMAGE_POINT_LIMIT)
	bf2.gameLogic.setTeamVehicleDamagePointLimit(TEAMVEHICLEDAMAGE_POINT_LIMIT)

	host.registerGameStatusHandler(onGameStatusChanged)

	if g_debug: print "scoring common init"



def onGameStatusChanged(status):
	if status == bf2.GameStatus.Playing:
		host.registerHandler('PlayerKilled', onPlayerKilled)
		host.registerHandler('PlayerDeath', onPlayerDeath)
		host.registerHandler('PlayerRevived', onPlayerRevived)
		host.registerHandler('PlayerHealPoint', onPlayerHealPoint)
		host.registerHandler('PlayerRepairPoint', onPlayerRepairPoint)
		host.registerHandler('PlayerGiveAmmoPoint', onPlayerGiveAmmoPoint)
		host.registerHandler('PlayerTeamDamagePoint', onPlayerTeamDamagePoint)
		host.registerHandler('VehicleDestroyed', onVehicleDestroyed)

	elif status == bf2.GameStatus.EndGame:

		giveCommanderEndScore(bf2.playerManager.getCommander(1), bf2.gameLogic.getWinner())
		giveCommanderEndScore(bf2.playerManager.getCommander(2), bf2.gameLogic.getWinner())



# give commander score for every player score
def addScore(player, points, subScore = NORMAL, subPoints = -1):

	# commander doesnt get score for regular actions, only for pure commander tasks. he also gets punishing points.
	if not player.isCommander() or subScore == CMND or points < 0:
		player.score.score += points
		if subPoints == -1:
			subPoints = points

		# sub score
		if subScore == RPL:
			player.score.rplScore += subPoints
		if subScore == SKILL:
			player.score.skillScore += subPoints
		if subScore == CMND:
			player.score.cmdScore += subPoints

	# commander score
	commander = bf2.playerManager.getCommander(player.getTeam())
	if commander != None and commander.isValid() and subScore != CMND and player != commander and points > 0:
		preScore = commander.score.score
		numPlayers = bf2.playerManager.getNumberOfAlivePlayersInTeam(commander.getTeam())
		if numPlayers > 0:
			commander.score.score += float(points) / numPlayers
			scoreGotten = commander.score.score - preScore
			if scoreGotten > 0:
				commander.score.cmdScore += scoreGotten



def giveCommanderEndScore(player, winningTeam):
	if player == None: return
	if player.getTeam() != winningTeam: return

	# double the commander score and add to regular score
	player.score.score = (player.score.score + player.score.fracScore - player.score.cmdScore) + player.score.cmdScore * 2
	player.score.cmdScore = player.score.cmdScore * 2



def onPlayerKilled(victim, attacker, weapon, assists, object):

	killedByEmptyVehicle = False
	countAssists = False

	# killed by unknown, no score
	if attacker == None:

		# check if killed by vehicle in motion
		if weapon == None and object != None:
			if hasattr(object, 'lastDrivingPlayerIndex'):
				attacker = bf2.playerManager.getPlayerByIndex(object.lastDrivingPlayerIndex)
				killedByEmptyVehicle = True


		if attacker == None:
			if g_debug: print "No attacker found"
			pass

	victimVehicle = victim.getVehicle()


	# killed by remote controlled vehicle, no score awarded in this game
	if object and object.isPlayerControlObject and object.getIsRemoteControlled():
		pass

	# no attacker, killed by object
	elif attacker == None:
		pass

	# killed by self
	elif attacker == victim:

		# no suicides from own wreck
		if killedByEmptyVehicle and object.getIsWreck():
			return

		attacker.score.suicides += 1
		if attacker.isAIPlayer():
			addScore(attacker, AI_SCORE_SUICIDE, RPL)
		else:
			addScore(attacker, SCORE_SUICIDE, RPL)

	# killed by own team
	elif attacker.getTeam() == victim.getTeam():

		# no teamkills from wrecks
		if object != None and object.getIsWreck():
			return

		# no teamkills from artillery
		if weapon:
			attackerVehicle = bf2.objectManager.getRootParent(weapon)
			if attackerVehicle.isPlayerControlObject and attackerVehicle.getIsRemoteControlled():
				return

#HF Start
		if weapon == None and object != None:
			victimVehicle = victim.getVehicle()
			victimRootVehicle = bf2.objectManager.getRootParent(victimVehicle)
			victimVehicleType = getVehicleType(victimRootVehicle.templateName)
			attackerVehicle = attacker.getVehicle()
			attackerRootVehicle = bf2.objectManager.getRootParent(attackerVehicle)
			attackerVehicleType = getVehicleType(attackerRootVehicle.templateName)
			#No tk on roadkill from airplane and helo
			if attackerVehicleType == VEHICLE_TYPE_AVIATOR and victimVehicleType == VEHICLE_TYPE_SOLDIER:
				return
#HF Stop

		attacker.score.TKs += 1
		if attacker.isAIPlayer():
			addScore(attacker, AI_SCORE_TEAMKILL, RPL)
		else:
			addScore(attacker, SCORE_TEAMKILL, RPL)

		countAssists = True

	# killed by enemy
	else:
		attacker.score.kills += 1
		if attacker.isAIPlayer():
			addScore(attacker, AI_SCORE_KILL, SKILL)
		else:
			addScore(attacker, SCORE_KILL, SKILL)

		countAssists = True


	# kill assist
	if countAssists and victim:

		for a in assists:
			assister = a[0]
			assistType = a[1]

			if assister.getTeam() != victim.getTeam():

				# passenger
				if assistType == 0:
					assister.score.passengerAssists += 1
					if attacker.isAIPlayer():
						addScore(assister, AI_SCORE_KILLASSIST_PASSENGER, RPL)
					else:
						addScore(assister, SCORE_KILLASSIST_PASSENGER, RPL)
				# targeter
				elif assistType == 1:
					assister.score.targetAssists += 1
					if attacker.isAIPlayer():
						addScore(assister, AI_SCORE_KILLASSIST_TARGETER, RPL)
					else:
						addScore(assister, SCORE_KILLASSIST_TARGETER, RPL)
				# damage
				elif assistType == 2:
					assister.score.damageAssists += 1
					if attacker.isAIPlayer():
						addScore(assister, AI_SCORE_KILLASSIST_DAMAGE, RPL)
					else:
						addScore(assister, SCORE_KILLASSIST_DAMAGE, RPL)
				# driver passenger
				elif assistType == 3:
					assister.score.driverAssists += 1
					if attacker.isAIPlayer():
						addScore(assister, AI_SCORE_KILLASSIST_DRIVER, RPL)
					else:
						addScore(assister, SCORE_KILLASSIST_DRIVER, RPL)
				else:
					# unknown kill type
					pass



def onPlayerDeath(victim, vehicle):
	victim.score.deaths += 1



def onPlayerRevived(victim, attacker):
	if attacker == None or victim == None or attacker.getTeam() != victim.getTeam():
		return

	attacker.score.revives += 1
	if attacker.isAIPlayer():
		addScore(attacker, AI_SCORE_REVIVE, RPL)
	else:
		addScore(attacker, SCORE_REVIVE, RPL)

	bf2.gameLogic.sendGameEvent(attacker, 10, 4) #10 = Replenish, 4 = Revive



# prevent point-exploiting by replenishing same player again
def checkGrindBlock(player, object):

	if object.isPlayerControlObject:
		defPlayers = object.getOccupyingPlayers()
		if len(defPlayers) > 0:
			defPlayer = defPlayers[0]

			if not hasattr(player, 'lastReplenishPointMap'):
				player.lastReplenishPointMap = {}
			else:
				if defPlayer.index in player.lastReplenishPointMap:
					if player.lastReplenishPointMap[defPlayer.index] + REPLENISH_POINT_MIN_INTERVAL > host.timer_getWallTime():
						return True

			player.lastReplenishPointMap[defPlayer.index] = host.timer_getWallTime()

	return False


def onPlayerHealPoint(player, object):
	if checkGrindBlock(player, object):
		return

	player.score.heals += 1
	if attacker.isAIPlayer():
		addScore(player, AI_SCORE_HEAL, RPL)
	else:
		addScore(player, SCORE_HEAL, RPL)
	bf2.gameLogic.sendGameEvent(player, 10, 0) 	# 10 = Replenish, 0 = Heal

	giveDriverSpecialPoint(player)



def onPlayerRepairPoint(player, object):
	if checkGrindBlock(player, object):
		return

	player.score.repairs += 1
	if attacker.isAIPlayer():
		addScore(player, AI_SCORE_REPAIR, RPL)
	else:
		addScore(player, SCORE_REPAIR, RPL)
	bf2.gameLogic.sendGameEvent(player, 10, 1) 	# 10 = Replenish, 1 = Repair

	giveDriverSpecialPoint(player)



def onPlayerGiveAmmoPoint(player, object):
	if checkGrindBlock(player, object):
		return

	player.score.ammos += 1
	if attacker.isAIPlayer():
		addScore(player, AI_SCORE_GIVEAMMO, RPL)
	else:
		addScore(player, SCORE_GIVEAMMO, RPL)
	bf2.gameLogic.sendGameEvent(player, 10, 2) 	# 10 = Replenish, 2 = Ammo

	giveDriverSpecialPoint(player)



def giveDriverSpecialPoint(player):
	# special point given to driver, if someone in vehicle gets an abilitypoint
	vehicle = player.getVehicle()
	if vehicle:
		rootVehicle = bf2.objectManager.getRootParent(vehicle)
		plrs = rootVehicle.getOccupyingPlayers()
		# Protect against empty tuple, which seems to occur.
		if len(plrs) == 0: return
		driver = plrs[0]
		if driver != None and driver != player and driver.getVehicle() == rootVehicle:
			driver.score.driverSpecials += 1
			if attacker.isAIPlayer():
				addScore(driver, AI_SCORE_DRIVERSPECIAL, RPL)
			else:
				addScore(driver, SCORE_DRIVERSPECIAL, RPL)
			bf2.gameLogic.sendGameEvent(driver, 10, 3) #10 = Replenish, 3 = DriverAbility



def onPlayerTeamDamagePoint(player, object):
	vehicleType = getVehicleType(object.templateName)

	if not player.isCommander():
		if vehicleType == VEHICLE_TYPE_SOLDIER:
			player.score.teamDamages += 1
			if attacker.isAIPlayer():
				addScore(player, AI_SCORE_TEAMDAMAGE, RPL)
			else:
				addScore(player, SCORE_TEAMDAMAGE, RPL)
		else:
			player.score.teamVehicleDamages += 1
			if attacker.isAIPlayer():
				addScore(player, AI_SCORE_TEAMVEHICLEDAMAGE, RPL)
			else:
				addScore(player, SCORE_TEAMVEHICLEDAMAGE, RPL)



# prevent point-exploiting by replenishing same player again
def checkGrindBlockRemote(player, object):

	if not hasattr(player, 'lastDestroyedRemote'):
		player.lastDestroyedRemote = {}
	else:
		if object in player.lastDestroyedRemote:
			if player.lastDestroyedRemote[object] + REPLENISH_POINT_MIN_INTERVAL > host.timer_getWallTime():
				return True

	player.lastDestroyedRemote[object] = host.timer_getWallTime()
	return False

def onVehicleDestroyed(vehicle, attacker):
	if attacker != None and vehicle.getTeam() != 0 and vehicle.getTeam() != attacker.getTeam() and vehicle.getIsRemoteControlled():
		if not checkGrindBlockRemote(attacker, vehicle):
			if attacker.isAIPlayer():
				addScore(attacker, AI_SCORE_DESTROYREMOTECONTROLLED, RPL)
			else:
				addScore(attacker, SCORE_DESTROYREMOTECONTROLLED, RPL)
			
			bf2.gameLogic.sendGameEvent(attacker, 10, 5) #10 = Replenish, 5 = DestroyStrategic



