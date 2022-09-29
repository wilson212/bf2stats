# co-op

TAKEOVERTYPE_CAPTURE = 1
TAKEOVERTYPE_NEUTRALIZE = 2

# Player Scoring
SCORE_CAPTURE = 2
SCORE_NEUTRALIZE = 2
SCORE_CAPTUREASSIST = 1
SCORE_NEUTRALIZEASSIST = 1
SCORE_DEFEND = 1

# Bot Scoring
AI_SCORE_CAPTURE = 2
AI_SCORE_NEUTRALIZE = 2
AI_SCORE_CAPTUREASSIST = 1
AI_SCORE_NEUTRALIZEASSIST = 1
AI_SCORE_DEFEND = 1

Top = 0
Middle = 1
Bottom = 2

import host
import bf2
import math
from game.scoringCommon import addScore, RPL
from bf2 import g_debug

g_controlPoints = [] # cache, as this map won't change



def init():
	# events hook
	host.registerGameStatusHandler(onGameStatusChanged)
	if host.sgl_getIsAIGame() == 1:
		host.sh_setEnableCommander(1)
	else:
		host.sh_setEnableCommander(1)
		
	host.registerHandler('TimeLimitReached', onTimeLimitReached, 1)	

	if g_debug: print "gpm_coop.py initialized"
		
		
		
def deinit():
	bf2.triggerManager.destroyAllTriggers()
	global g_controlPoints
	g_controlPoints = []
	host.unregisterGameStatusHandler(onGameStatusChanged)
	if g_debug: print "gpm_coop.py uninitialized"



def onGameStatusChanged(status):

	global g_controlPoints
	if status == bf2.GameStatus.Playing: 

		# add control point triggers
		g_controlPoints = bf2.objectManager.getObjectsOfType('dice.hfe.world.ObjectTemplate.ControlPoint')
		for obj in g_controlPoints:
			radius = float(obj.getTemplateProperty('radius'))
			isHemi = int(obj.cp_getParam('isHemisphere'))
			if isHemi != 0:
				id = bf2.triggerManager.createHemiSphericalTrigger(obj, onCPTrigger, '<<PCO>>', radius, (1, 2, 3))
			else:
				id = bf2.triggerManager.createRadiusTrigger(obj, onCPTrigger, '<<PCO>>', radius, (1, 2, 3))			
			obj.triggerId = id
			obj.lastAttackingTeam = 0
			if obj.cp_getParam('team') > 0:
				obj.flagPosition = Top
			else:
				obj.flagPosition = Bottom

		host.registerHandler('ControlPointChangedOwner', onCPStatusChange)

		# setup ticket system
		ticketsTeam1 = calcStartTickets(bf2.gameLogic.getDefaultTickets(1))
		ticketsTeam2 = calcStartTickets(bf2.gameLogic.getDefaultTickets(2))
		
		bf2.gameLogic.setTickets(1, ticketsTeam1)
		bf2.gameLogic.setTickets(2, ticketsTeam2)

		bf2.gameLogic.setTicketState(1, 0)
		bf2.gameLogic.setTicketState(2, 0)

		bf2.gameLogic.setTicketLimit(1, 1, 0)
		bf2.gameLogic.setTicketLimit(2, 1, 0)
		bf2.gameLogic.setTicketLimit(1, 2, 10)
		bf2.gameLogic.setTicketLimit(2, 2, 10)
		bf2.gameLogic.setTicketLimit(1, 3, int(ticketsTeam1*0.1))
		bf2.gameLogic.setTicketLimit(2, 3, int(ticketsTeam2*0.1))
		bf2.gameLogic.setTicketLimit(1, 4, int(ticketsTeam1*0.2))
		bf2.gameLogic.setTicketLimit(2, 4, int(ticketsTeam1*0.2))
		
		host.registerHandler('TicketLimitReached', onTicketLimitReached)
		updateTicketLoss()

		# player events
		host.registerHandler('PlayerDeath', onPlayerDeathCQ)
		host.registerHandler('PlayerKilled', onPlayerKilledCQ)
		host.registerHandler('PlayerRevived', onPlayerRevived)
		host.registerHandler('PlayerSpawn', onPlayerSpawn)
		host.registerHandler('EnterVehicle', onEnterVehicle)
		host.registerHandler('ExitVehicle', onExitVehicle)

		if g_debug: print "co-op gamemode initialized."
	else:
		bf2.triggerManager.destroyAllTriggers()
		g_controlPoints = []



def calcStartTickets(mapDefaultTickets):
	return int(mapDefaultTickets * (bf2.serverSettings.getTicketRatio() / 100.0))
	
	
	
def onTimeLimitReached(value):
	team1tickets = bf2.gameLogic.getTickets(1)
	team2tickets = bf2.gameLogic.getTickets(2)
	
	winner = 0
	victoryType = 0
	if team1tickets > team2tickets:
		winner = 1
		victoryType = 3
	elif team2tickets > team1tickets:
		winner = 2
		victoryType = 3
	

	host.sgl_endGame(winner, victoryType)



# update ticket system
def updateTicketLoss():
	areaValueTeam1 = 0
	areaValueTeam2 = 0
	totalAreaValue = 0
	numCpsTeam0 = 0
	numCpsTeam1 = 0
	numCpsTeam2 = 0
	
	# calculate control point area value for each team
	for obj in g_controlPoints:
		team = obj.cp_getParam('team')
		if team == 1:
			areaValueTeam1 += obj.cp_getParam('areaValue', team)
			totalAreaValue += areaValueTeam1
			numCpsTeam1 += 1
		elif team == 2:
			areaValueTeam2 += obj.cp_getParam('areaValue', team)
			totalAreaValue += areaValueTeam2
			numCpsTeam2 += 1
		else:
			numCpsTeam0 += 1
			totalAreaValue += 0
		
	# check if a team has no control points
	if numCpsTeam1 == 0 or numCpsTeam2 == 0:
		if numCpsTeam1 == 0:
			losingTeam = 1
			winningTeam = 2
		else:
			losingTeam = 2
			winningTeam = 1
		
		# check if there is anyone alive
		foundLivingPlayer = False
		for p in bf2.playerManager.getPlayers():
			if p.getTeam() == losingTeam and p.isAlive():
				foundLivingPlayer = True
				break
				
		if not foundLivingPlayer:

			# drop tickets
			ticketLossPerSecond = bf2.gameLogic.getDefaultTicketLossAtEndPerMin()
			bf2.gameLogic.setTicketChangePerSecond(losingTeam, -ticketLossPerSecond)
			bf2.gameLogic.setTicketChangePerSecond(winningTeam, 0)
			
			return

	
	# update ticket loss
	team1AreaOverweight = areaValueTeam1 - areaValueTeam2
	percentualOverweight = 1.0
	if totalAreaValue != 0:
		percentualOverweight = abs(team1AreaOverweight / totalAreaValue)
	
	ticketLossPerSecTeam1 = calcTicketLossForTeam(1, areaValueTeam2, -team1AreaOverweight)
	ticketLossPerSecTeam2 = calcTicketLossForTeam(2, areaValueTeam1, team1AreaOverweight)
	bf2.gameLogic.setTicketChangePerSecond(1, -ticketLossPerSecTeam1)
	bf2.gameLogic.setTicketChangePerSecond(2, -ticketLossPerSecTeam2)


	
# actual ticket loss calculation function
def calcTicketLossForTeam(team, otherTeamAreaValue, otherTeamAreaOverweight):
	if otherTeamAreaValue >= 100 and otherTeamAreaOverweight > 0:
		ticketLossPerSecond = (bf2.gameLogic.getDefaultTicketLossPerMin(team) / 60.0) * (otherTeamAreaOverweight / 100.0)
		return ticketLossPerSecond
	else:
		return 0


	
DOWNWARDS = -1
UPWARDS = 1	

# called when tickets reach a predetermined limit (negativ value means that the tickets have become less than the limit)
def onTicketLimitReached(team, limitId):
	if (limitId == -1):
		if (team == 1):
			winner = 2
		
		elif (team == 2):
			winner = 1
		
		bf2.gameLogic.setTicketState(1, 0)
		bf2.gameLogic.setTicketState(2, 0)
	
		host.sgl_endGame(winner, 3)		

	# update ticket state
	else:
		updateTicketWarning(team, limitId)

	

# called when the ticket state should be updated (for triggering messages and sounds based on tickets left)
def updateTicketWarning(team, limitId):

	oldTicketState = bf2.gameLogic.getTicketState(team)
	newTicketState = 0
	
	if (oldTicketState >= 10):
		newTicketState = 10		

	if (limitId == -2):
		newTicketState = 10
	
	elif (limitId == 2):
		newTicketState = 0		

	elif (limitId == -3):
		newTicketState += 2

	elif (limitId == -4):
		newTicketState += 1

	if (oldTicketState != newTicketState):
		bf2.gameLogic.setTicketState(team, newTicketState)
		
	
	
# called when someone enters or exits cp radius
def onCPTrigger(triggerId, cp, vehicle, enter, userData):
	if not cp.isValid(): return
	
	if vehicle and vehicle.getParent(): return
	
	# can this cp be captured at all?
	if cp.cp_getParam('unableToChangeTeam') != 0:
		return					
		
	playersInVehicle = None	
	if vehicle:
		playersInVehicle = vehicle.getOccupyingPlayers()
	
	if enter:
		for p in playersInVehicle:
			cp = getOccupyingCP(p)
			if cp != None:
				if not p.getIsInsideCP():
					if g_debug: print "Resetting enterPctAt for player ", p.getName()
					p.enterCpAt = host.timer_getWallTime()
	
	if vehicle:	
		for p in playersInVehicle:
			# only count first player in a vehicle
			if p == playersInVehicle[0]:  
				if g_debug: print p.index, " is in radius. v=", vehicle.templateName
				p.setIsInsideCP(enter)	
			else:
				p.setIsInsideCP(0)
				if enter == 1:
					bf2.gameLogic.sendHudEvent(p, 66, 49) #66 = HEEnableHelpMessage, 49 = VHMExitToCaptureFlag;			

	# count people in radius
	team1Occupants = 0
	team2Occupants = 0

	pcos = bf2.triggerManager.getObjects(cp.triggerId)
	for o in pcos:
		if not o: continue # you can get None in the result tuple when the host can't figure out what object left the trigger
		if o.getParent(): continue # getOccupyingPlayers returns all players downwards in the hierarchy, so dont count them twice
		occupyingPlayers = o.getOccupyingPlayers()
		for p in occupyingPlayers:
			
			# only count first player in a vehicle
			if p != occupyingPlayers[0]: 
				continue
				
			if p.isAlive() and not p.isManDown():
				
				if not p.killed:
					if p.getTeam() == 1:
						team1Occupants += 1
					elif p.getTeam() == 2:
						team2Occupants += 1


	# determine who is taking control
	team1OverWeight = team1Occupants - team2Occupants
	attackOverWeight = 0

	if team1OverWeight > 0:
		attackingTeam = 1
	elif team1OverWeight < 0:
		attackingTeam = 2
	else:
		attackingTeam = 0

	
	if team1Occupants == 0 and team2Occupants == 0:

		# nobody here, slowly go back to owning team
		if cp.cp_getParam('team') == 0:
			attackOverWeight = -0.5
		else:
			attackOverWeight = 0.5
			
		timeToChangeControl = cp.cp_getParam('timeToLoseControl')

	else:

		# raise flag if already ours, or at bottom and neutral. Otherwise lower first.
		if cp.cp_getParam('flag') == attackingTeam or (cp.flagPosition == Bottom and cp.cp_getParam('team') == 0):

			# our flag, raise
			attackOverWeight = abs(team1OverWeight)
			timeToChangeControl = cp.cp_getParam('timeToGetControl')
		else:

			# other team raised flag, lower first
			attackOverWeight = - abs(team1OverWeight)
			timeToChangeControl = cp.cp_getParam('timeToLoseControl')


	if cp.cp_getParam('onlyTakeableByTeam') != 0 and cp.cp_getParam('onlyTakeableByTeam') != attackingTeam:
		return


	# flag can only be changed when at bottom
	if cp.flagPosition == Bottom:
		cp.cp_setParam('flag', attackingTeam)


	# calculate flag raising/lowering speed
	if timeToChangeControl > 0:
		takeOverChangePerSecond = 1.0 * attackOverWeight / timeToChangeControl
	else:
		takeOverChangePerSecond = 0.0

	if (cp.flagPosition == Top and takeOverChangePerSecond > 0) or (cp.flagPosition == Bottom and takeOverChangePerSecond < 0):
		takeOverChangePerSecond = 0.0

	if abs(takeOverChangePerSecond) > 0:
		cp.flagPosition = Middle
				
	cp.cp_setParam('takeOverChangePerSecond', takeOverChangePerSecond)

			
	
# called when a control point flag reached top or bottom
def onCPStatusChange(cp, top):

	playerId = -1
	takeoverType = -1
	newTeam = -1
	scoringTeam = -1
	
	if top:	cp.flagPosition = Top
	else:   cp.flagPosition = Bottom
	
	# determine capture / neutralize / defend
	if cp.cp_getParam('team') != 0:

		if top:
			# regained flag, do nothing
			pass
			
		else:
			# neutralize
			newTeam = 0
			if cp.cp_getParam('team') == 1:
				scoringTeam = 2
			else:
				scoringTeam = 1
				
			takeoverType = TAKEOVERTYPE_NEUTRALIZE

	else:

		if top:
			# capture
			newTeam = cp.cp_getParam('flag')
			scoringTeam = newTeam
			takeoverType = TAKEOVERTYPE_CAPTURE

		else:
			# hit bottom, but still neutral
			pass

	
	# scoring
	if takeoverType > 0:
		pcos = bf2.triggerManager.getObjects(cp.triggerId)
	
		# count number of players
		scoringPlayers = []
		firstPlayers = []
		for o in pcos:
			if o.getParent(): continue

			occupyingPlayers = o.getOccupyingPlayers()
			for p in occupyingPlayers:
			
				# only count first player in a vehicle
				if p != occupyingPlayers[0]: 
					continue
					
				if p.isAlive() and not p.isManDown() and p.getTeam() == scoringTeam:
					if len(firstPlayers) == 0 or p.enterCpAt < firstPlayers[0].enterCpAt:
						firstPlayers = [p]
					elif p.enterCpAt == firstPlayers[0].enterCpAt:
						firstPlayers += [p]
					
					if not p in scoringPlayers:
						scoringPlayers += [p]
	
		# deal score
		for p in scoringPlayers:
			oldScore = p.score.score;
			if takeoverType == TAKEOVERTYPE_CAPTURE:
				if p in firstPlayers:
					p.score.cpCaptures += 1
					if p.isAIPlayer():
						addScore(p, AI_SCORE_CAPTURE, RPL)
					else:
						addScore(p, SCORE_CAPTURE, RPL)
					
					bf2.gameLogic.sendGameEvent(p, 12, 0) #12 = Conquest, 0 = Capture
					playerId = p.index
				else:
					p.score.cpAssists += 1
					if p.isAIPlayer():
						addScore(p, AI_SCORE_CAPTUREASSIST, RPL)
					else:
						addScore(p, SCORE_CAPTUREASSIST, RPL)
						
					bf2.gameLogic.sendGameEvent(p, 12, 2) #12 = Conquest, 2 = Assist


			elif takeoverType == TAKEOVERTYPE_NEUTRALIZE:
				if p in firstPlayers:
					p.score.cpNeutralizes += 1
					if p.isAIPlayer():
						addScore(p, AI_SCORE_NEUTRALIZE, RPL)
					else:
						addScore(p, SCORE_NEUTRALIZE, RPL)
					
					bf2.gameLogic.sendGameEvent(p, 12, 3) #12 = Conquest, 3 = Neutralize
				else:
					p.score.cpNeutralizeAssists += 1
					if p.isAIPlayer():
						addScore(p, AI_SCORE_NEUTRALIZEASSIST, RPL)
					else:
						addScore(p, SCORE_NEUTRALIZEASSIST, RPL)
					
					bf2.gameLogic.sendGameEvent(p, 12, 4) #12 = Conquest, 4 = Neutralize assist
					
				

	# immediate ticket loss for opposite team
	enemyTicketLossInstant = cp.cp_getParam('enemyTicketLossWhenCaptured')
	if enemyTicketLossInstant > 0 and newTeam > 0:
		
		if newTeam == 1:
			punishedTeam = 2
		elif newTeam == 2:
			punishedTeam = 1
		
		tickets = bf2.gameLogic.getTickets(punishedTeam)
		tickets -= enemyTicketLossInstant
		bf2.gameLogic.setTickets(punishedTeam, tickets)
	
	
	# update control point	
	cp.cp_setParam('playerId', playerId) #always set player first
	if newTeam != -1 and cp.cp_getParam('team') != newTeam:
		cp.cp_setParam('team', newTeam)
	onCPTrigger(cp.triggerId, cp, 0, 0, 0)
	updateTicketLoss()

				
				
def onPlayerDeathCQ(victim, vehicle):		

	# punish team with one ticket
	if victim != None:
		team = victim.getTeam()
		teamTickets = bf2.gameLogic.getTickets(team)
		teamTickets -= 1
		bf2.gameLogic.setTickets(team, teamTickets)

	# check if it was the last player
	foundLivingPlayer = False
	for p in bf2.playerManager.getPlayers():
		if p != victim and p.getTeam() == victim.getTeam() and p.isAlive():
			foundLivingPlayer = True
	
	if not foundLivingPlayer:
		updateTicketLoss()
	


def onPlayerKilledCQ(victim, attacker, weapon, assists, object):
	if not victim: 
		return
	
	victim.killed = True
	
	# update flag takeover status if victim was in a CP radius
	cp = getOccupyingCP(victim)
	if cp != None:
		onCPTrigger(-1, cp, victim.getVehicle(), False, None)

		# give defend score if killing enemy within cp radius
		if attacker != None and attacker.getTeam() != victim.getTeam()\
		   and cp.cp_getParam('unableToChangeTeam') == 0 and cp.cp_getParam('onlyTakeableByTeam') == 0:
		
			if cp != None and cp.cp_getParam('team') == attacker.getTeam():
				attacker.score.cpDefends += 1
				if attacker.isAIPlayer():
					addScore(attacker, AI_SCORE_DEFEND, RPL)
				else:
					addScore(attacker, SCORE_DEFEND, RPL)
				
				bf2.gameLogic.sendGameEvent(attacker, 12, 1) #12 = Conquest, 1 = Defend
			
			
			
def onPlayerRevived(victim, attacker):

	# update flag takeover status if victim was in a CP radius
	victim.killed = False
	
	cp = getOccupyingCP(victim)
	if cp != None:
		onCPTrigger(-1, cp, victim.getVehicle(), True, None)
	
	
	
def onPlayerSpawn(player, soldier):
	player.killed = False
	
	
	
def onEnterVehicle(player, vehicle, freeSoldier = False):
	player.setIsInsideCP(False)
	cp = getOccupyingCP(player)
	if cp != None:
		onCPTrigger(-1, cp, vehicle, False, None)
		updateCaptureStatus(vehicle) 


def onExitVehicle(player, vehicle):

	# update flag takeover status if player in a CP radius
	
	if g_debug: print "Player exiting ", player.getName()
	cp = getOccupyingCP(player)

	# can this cp be captured at all?
	player.setIsInsideCP(cp != None and cp.cp_getParam('unableToChangeTeam') == 0)

	updateCaptureStatus(vehicle)
		

#Update cp capture status on players in vehicle
def updateCaptureStatus(vehicle):	

	rootVehicle = bf2.objectManager.getRootParent(vehicle)
	playersInVehicle = rootVehicle.getOccupyingPlayers()
	
	# set the player in the topmost pco as inside - others outside
	for p in playersInVehicle:
		if g_debug: print "Players in vehicle ", p.getName()
		cp = getOccupyingCP(p)
		p.setIsInsideCP(cp != None and cp.cp_getParam('unableToChangeTeam') == 0 and p == playersInVehicle[0])
		
		
# find cp that player is occupying, if any		
def getOccupyingCP(player):
	vehicle = player.getVehicle()
	playerPos = vehicle.getPosition()
	
	# find closest CP
	closestCP = None
	if len(g_controlPoints) == 0: return None
	for obj in g_controlPoints:
		distanceTo = getVectorDistance(playerPos, obj.getPosition())
		if closestCP == None or distanceTo < closestCPdist:
			closestCP = obj
			closestCPdist = distanceTo
	
	# is the player in radius?
	pcos = bf2.triggerManager.getObjects(closestCP.triggerId)
	for o in pcos:
		if o == player.getDefaultVehicle():
			# Player is DEFAULT vehicle - this is needed when called from onEnterVehicle
			return closestCP
		else:
			for p in o.getOccupyingPlayers():
				if p == player:
					return closestCP
	
	return None



# get distance between two positions
def getVectorDistance(pos1, pos2):
	diffVec = [0.0, 0.0, 0.0]
	diffVec[0] = math.fabs(pos1[0] - pos2[0])
	diffVec[1] = math.fabs(pos1[1] - pos2[1])
	diffVec[2] = math.fabs(pos1[2] - pos2[2])
	 
	return math.sqrt(diffVec[0] * diffVec[0] + diffVec[1] * diffVec[1] + diffVec[2] * diffVec[2])
		
		
	
