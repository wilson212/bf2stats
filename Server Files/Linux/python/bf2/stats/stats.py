import host
import bf2.PlayerManager
import fpformat
from constants import *
from bf2 import g_debug

#omero, 2006-04-13
from bf2.BF2StatisticsConfig import pm_ai_player_addr, debug_fraglog_enable

#omero, 2006-04-13
#assign the bots an ip
def fixPlayerAddress(player):
	if player.isAIPlayer():
		return pm_ai_player_addr
	else:
		return player.getAddress()


#omero, 2006-05-01
#borrowed from fragalizer_log.py module	
def getPosStr(orgPos):
	worldSize = bf2.gameLogic.getWorldSize();
	scale = [512.0 / worldSize[0], 1, 512.0 / worldSize[1]]
	pos = [orgPos[0] * scale[0], orgPos[1] * scale[1], orgPos[2] * scale[2]]
	res = str(fpformat.fix(pos[0], 3)) + "," + str(fpformat.fix(pos[1], 3)) + "," + str(fpformat.fix(pos[2], 3))
	return res


	
roundArmies = [None, None, None]

sendEndOfRoundStats = False
def getSendEndOfRoundStats():
	global sendEndOfRoundStats
	return sendEndOfRoundStats

def setSendEndOfRoundStats( value ):
	global sendEndOfRoundStats
	sendEndOfRoundStats = value

playerConnectionOrderIterator = 0
def getPlayerConnectionOrderIterator():
	global playerConnectionOrderIterator
	return playerConnectionOrderIterator

def setPlayerConnectionOrderIterator(value):
	global playerConnectionOrderIterator
	playerConnectionOrderIterator = value


sessionPlayerStatsMap = {}
def getStatsMap():
	global sessionPlayerStatsMap
	return sessionPlayerStatsMap

def setStatsMap(map):
	global sessionPlayerStatsMap
	sessionPlayerStatsMap = map



def init():
	host.registerHandler('PlayerConnect', onPlayerConnect, 1)
	host.registerHandler('PlayerDisconnect', onPlayerDisconnect, 1)
	host.registerHandler('Reset', onReset, 1)

	host.registerGameStatusHandler(onGameStatusChanged)
	
	print "Persistant stats module initialized."
	
	

def onGameStatusChanged(status):
	if status == bf2.GameStatus.Playing:

		# find highest player still connected
		highestPid = -1
		for pid in sessionPlayerStatsMap:
			if pid > highestPid:
				highestPid = pid
		
		global playerConnectionOrderIterator
		playerConnectionOrderIterator = highestPid + 1
		print "Reset orderiterator to %d based on highest pid kept" % playerConnectionOrderIterator

		# Reconnect players
		if len(sessionPlayerStatsMap) == 0:
			print "Reloading players"
			for p in bf2.playerManager.getPlayers():
				onPlayerConnect(p)
	
		global army
		roundArmies[1] = getArmy(bf2.gameLogic.getTeamName(1))
		roundArmies[2] = getArmy(bf2.gameLogic.getTeamName(2))

		# All other hooks	
		host.registerHandler('PlayerKilled', onPlayerKilled)
		host.registerHandler('PlayerDeath', onPlayerDeath)
		host.registerHandler('EnterVehicle', onEnterVehicle)
		host.registerHandler('ExitVehicle', onExitVehicle)
		host.registerHandler('PickupKit', onPickupKit)
		host.registerHandler('DropKit', onDropKit)
		host.registerHandler('PlayerChangedSquad', onPlayerChangedSquad)
		host.registerHandler('ChangedCommander', onChangedCommander)
		host.registerHandler('ChangedSquadLeader', onChangedSquadLeader)
		host.registerHandler('PlayerChangeWeapon', onPlayerChangeWeapon)
		host.registerHandler('PlayerBanned', onPlayerBanned)
		host.registerHandler('PlayerKicked', onPlayerKicked)
		host.registerHandler('PlayerSpawn', onPlayerSpawn)
		host.registerHandler('DeployGrapplingHook', onDeployGrapplingHook)
		host.registerHandler('DeployZipLine', onDeployZipLine)
		host.registerHandler('DeployTactical', onDeployTactical)
		
		for s in sessionPlayerStatsMap.itervalues():
			s.reset()

		for p in bf2.playerManager.getPlayers():
			p.stats.reinit(p)
			p.stats.wasHereAtStart = 1
			
		bf2.playerManager.enableScoreEvents()

		# stats have all been cleared enable next end of round stats
		setSendEndOfRoundStats( True )

# Added by Chump - for fragalyzer
		if debug_fraglog_enable: bf2.stats.fragalyzer_log.enable()

	elif status == bf2.GameStatus.EndGame:

		# finalize stats and send snapshot
		for p in bf2.playerManager.getPlayers():
			p.stats.wasHereAtEnd = 1
			finalizePlayer(p)
			
		# check ensure we only send the end of round stats once
		if getSendEndOfRoundStats():
			# show end-of-round information
			setSendEndOfRoundStats( False )
			bf2.stats.endofround.invoke()
		
# Added by Chump - for bf2statistics stats
		# if not ranked, clean out stats vectors
		#if not host.ss_getParam('ranked'):
		#	playerConnectionOrderIterator = 0
		#	sessionPlayerStatsMap.clear()



def onReset(data):
	for s in sessionPlayerStatsMap.itervalues():
		s.reset()

	for p in bf2.playerManager.getPlayers():
		p.stats.reinit(p)
		p.stats.wasHereAtStart = 1



class PlayerStat: 
	def __init__(self, player):
		self.profileId = player.getProfileId()
		self.playerId = player.index
		self.id = self.playerId
		self.isAIPlayer = player.isAIPlayer()
		self.connectionOrderNr = 0
		self.rank = 0
		
		self.reinit(player)
		self.reset()

	def reinit(self, player):
		self.name = player.getName()
		#omero, 2006-04-13
		#self.ipaddr = player.getAddress()
		self.ipaddr = fixPlayerAddress(player)
		self.localScore = player.score		
	
	def reset(self):
		self.connectAt = date()
		self.timeOnLine = 0
		
		self.score 	= 0
		self.cmdScore 	= 0
		self.teamScore 	= 0
		self.skillScore = 0
		self.kills 	= 0
		self.teamkills 	= 0
		self.deaths 	= 0
				
		self.vehicles = {}
		for v in range(0, NUM_VEHICLE_TYPES + 1):
			if not v in self.vehicles:
				self.vehicles[v] = VehicleStat(v)
			else:
				self.vehicles[v].reset()
	
		self.weapons = {}
		for w in range(0, NUM_WEAPON_TYPES + 1):
			if not w in self.weapons:
				self.weapons[w] = WeaponStat(w)
			else:
				self.weapons[w].reset()
	
		self.kits = {}
		for k in range(0, NUM_KIT_TYPES + 1):
			if not k in self.kits:
				self.kits[k] = KitStat(k)
			else:			
				self.kits[k].reset()
	
		self.killedByPlayer = {}
		self.killedPlayer = {}

		self.team = 0

		self.localScore.reset()
		
		self.bulletsFired = 0
		self.bulletsHit = 0

		self.currentKillStreak = 0
		self.longestKillStreak = 0
		self.currentDeathStreak = 0
		self.longestDeathStreak = 0
		self.wasHereAtStart = 0
		self.wasHereAtEnd = 0
		self.complete = 0
		self.medals = None
		
		self.spawnedTeam = 3
		self.spawnedAt = 0
		self.becameCmdAt = 0
		self.becameSqlAt = 0
		self.joinedSquadAt = 0
		self.rawTimePlayed = 0
		self.rawTimeAsCmd = 0
		self.rawTimeAsSql = 0
		self.rawTimeInSquad = 0
		
		self.timesBanned = 0
		self.timesKicked = 0
		
		self.timeAsArmy = {}
		for a in range(0, NUM_ARMIES + 1):
			self.timeAsArmy[a] = 0
			
		self.currentWeaponType = NUM_WEAPON_TYPES

	def __getattr__(self, name):
		if name in self.__dict__: return self.__dict__[name]
		elif name == 'timePlayed':
			if self.spawnedAt:
				timeDiff = date() - self.spawnedAt
				self.rawTimePlayed += timeDiff
				self.timeAsArmy[roundArmies[self.spawnedTeam]] += timeDiff
				self.spawnedAt = date()
			return self.rawTimePlayed
		elif name == 'timeAsCmd':
			if self.becameCmdAt:
				self.rawTimeAsCmd += date() - self.becameCmdAt 
				self.becameCmdAt = date()
			return self.rawTimeAsCmd
		elif name == 'timeAsSql':
			if self.becameSqlAt:
				self.rawTimeAsSql += date() - self.becameSqlAt 
				self.becameSqlAt = date()
			return self.rawTimeAsSql
		elif name == 'timeInSquad':
			if self.joinedSquadAt:
				self.rawTimeInSquad += date() - self.joinedSquadAt 
				self.joinedSquadAt = date()
			return self.rawTimeInSquad
		elif name == 'accuracy':
			if self.bulletsFired == 0:
				return 0
			else:
				return 1.0 * self.bulletsHit / self.bulletsFired
		else:
			raise AttributeError, name
						
	# when same player rejoins server
	def reconnect(self, player):
		self.connectAt = date()
		#self.ipaddr = player.getAddress()
		self.ipaddr = fixPlayerAddress(player)
		
		bf2.playerManager.disableScoreEvents()
		
		print "Reattaching score object from old dead player %d to new player %d" % (self.localScore.index, player.index)

		player.score = self.localScore
		player.score.index = player.index

		player.score.score 	= self.score
		player.score.cmdScore 	= self.cmdScore
		player.score.rplScore 	= self.teamScore
		player.score.skillScore = self.skillScore
		player.score.kills 	= self.kills
		player.score.TKs 	= self.teamkills
		player.score.deaths 	= self.deaths

		player.score.rank	= self.rank

		bf2.playerManager.enableScoreEvents()	
		
	# calculate final stats values for this player (disconnected or end of round)
	def finalize(self, player):
	
		self.copyPlayerData(player)
		
		if self.currentWeaponType != NUM_WEAPON_TYPES:
			self.weapons[self.currentWeaponType].exit(player)
			self.currentWeaponType = NUM_WEAPON_TYPES

		stopSpawned(player)	
		stopInSquad(player)
		stopAsSql(player)
		stopAsCmd(player)

# Added by Chump - for bf2statistics stats
		#if self.wasHereAtStart == 1 and self.wasHereAtEnd == 1:
		if self.wasHereAtEnd == 1:
			self.complete = 1
			
		# sum up vehicles & kits
		collectBulletsFired(player)
		finalizeBulletsFired(player)
			
		for v in player.stats.vehicles.itervalues():
			if v.enterAt != 0: v.exit(player)
		for v in player.stats.kits.itervalues():
			if v.enterAt != 0: v.exit(player)
		for v in player.stats.weapons.itervalues():
			if v.enterAt != 0: v.exit(player)
					
	# copy data to player-stats, as player might not be available after this
	def copyPlayerData(self, player):
		self.timeOnLine += date() - self.connectAt

		self.localScore = player.score
		
		self.score 		= player.score.score
		self.cmdScore 	= player.score.cmdScore	
		self.teamScore 	= player.score.rplScore
		self.skillScore = player.score.skillScore

		if self.score < 0: 	self.score = 0
		if self.cmdScore < 0: 	self.cmdScore = 0
		if self.teamScore < 0: 	self.teamScore = 0
		if self.skillScore < 0: self.skillScore = 0
				
		self.kills 		= player.score.kills
		self.teamkills 	= player.score.TKs
		self.deaths 	= player.score.deaths

		self.rank = player.score.rank
		self.army = roundArmies[player.getTeam()]
		self.team = player.getTeam()

# Added by Chump - for bf2statistics stats (plus de-indenting)
		#if host.ss_getParam('ranked'):
		if hasattr(player, 'medals'):
			self.medals = player.medals
		else:
			if g_debug: print "Player had no medal stats. pid=", player.index

			

class ObjectStat: 
	def __init__(self, type):
		self.reset()
		self.type = type
	
	def reset(self):	
		
		# reset all non-global
		self.kills = 0
		self.killedBy = 0
		self.rawTimeInObject = 0
		self.deaths = 0
		self.score = 0
		self.bulletsFired = 0
		self.bulletsFiredTemp = 0
		self.bulletsHit = 0
		self.bulletsHitTemp = 0
		self.enterAt = 0
		self.enterScore = 0
		self.deployed = 0
		
	def enter(self, player):
		self.enterAt = date()
		self.enterScore = player.score.score
		if g_debug: print "STATS ObjectStat: %s Enter type=%s --- enterAt:%s score:%d" % (player.getName(), self.type, self.enterAt, self.enterScore) 
	
	def exit(self, player):
		if self.enterAt == 0: return
		time = self.timeInObject
		self.enterAt = 0
		
		self.score += player.score.score - self.enterScore
		self.enterScore = 0
		if g_debug: print "STATS ObjectStat: %s Exit type=%s --- timeInObject:%s score:%d" % (player.getName(), self.type, time, self.score) 
		
	def __getattr__(self, name):
		if name in self.__dict__: return self.__dict__[name]
		elif name == 'timeInObject' or name == 'rtime':
			if self.enterAt:
				self.rawTimeInObject += date() - self.enterAt
				self.enterAt = date()
			return self.rawTimeInObject
		elif name == 'accuracy':
			if self.bulletsFired == 0:
				return 0
			else:
				return 1.0 * self.bulletsHit / self.bulletsFired
		else:
			raise AttributeError, name
		


class VehicleStat(ObjectStat): 
	def __init__(self, type):
		ObjectStat.__init__(self, type)
		self.reset()
	
	def reset(self):	
		ObjectStat.reset(self)
		self.roadKills = 0
		


class KitStat(ObjectStat):
	def __init__(self, type):
		ObjectStat.__init__(self, type)
		self.reset()
	
	def reset(self):
		ObjectStat.reset(self)



class WeaponStat(ObjectStat):
	def __init__(self, type):
		ObjectStat.__init__(self, type)
		self.reset()
		
	def reset(self):
		ObjectStat.reset(self)
		
	def enter(self, player):
		if player.stats.currentWeaponType != NUM_WEAPON_TYPES:
			player.stats.weapons[player.stats.currentWeaponType].exit(player)
		player.stats.currentWeaponType = self.type

		ObjectStat.enter(self, player)
	
	def exit(self,player):
		time = date() - self.enterAt

		ObjectStat.exit(self, player)

	
	
def date():
	return host.timer_getWallTime()	
	


def finalizePlayer(player):
	player.stats.finalize(player)



def onPlayerConnect(player):

	# see if player already has a record
	player.stats = None
	connectingProfileId = player.getProfileId()
	for stats in sessionPlayerStatsMap.itervalues():
		if connectingProfileId > 0 and connectingProfileId == stats.profileId:
			print "Found old player record, profileId ", str(stats.profileId)
			player.stats = stats
			player.stats.reconnect(player)
	
	if not player.stats:
	
		print "Creating new record for player profileId ", str(connectingProfileId)
		
		# add stats record
		global playerConnectionOrderIterator
		id = playerConnectionOrderIterator
		
		newPlayerStats = PlayerStat(player)
		
		sessionPlayerStatsMap[id] = newPlayerStats
		player.stats = sessionPlayerStatsMap[id]
		
		player.stats.connectionOrderNr = playerConnectionOrderIterator
		playerConnectionOrderIterator += 1
		
	player.score.rank = player.stats.rank
	

	
def onPlayerDisconnect(player):
	finalizePlayer(player)


#*********************************************************
#  XPACK SPECIFIC
#*********************************************************
def onDeployGrapplingHook(player):
	if player == None: return
	
	if g_debug: print "STATS.py:  Deployed Grappling hook"
	player.stats.weapons[WEAPON_TYPE_GRAPPLINGHOOK].deployed += 1
	return
	
def onDeployZipLine(player):
	if player == None: return
	
	if g_debug: print "STATS.py:  Deployed Zipline"
	player.stats.weapons[WEAPON_TYPE_ZIPLINE].deployed += 1
	return
	
def onDeployTactical(player):
	if player == None: return
	
	if g_debug: print "STATS.py:  Deployed Tactical"
	player.stats.weapons[WEAPON_TYPE_TACTICAL].deployed += 1
	return
	
#*********************************************************
#*********************************************************
#*********************************************************
	
def onEnterVehicle(player, vehicle, freeSoldier = False):

	if player == None: return
	
	vehicleType = getVehicleType(vehicle.templateName)
	if vehicleType != VEHICLE_TYPE_SOLDIER:
		rootVehicle = bf2.objectManager.getRootParent(vehicle)
		vehicleType = getVehicleType(rootVehicle.templateName)
	else:
		rootVehicle = vehicle
	
	if vehicleType != VEHICLE_TYPE_SOLDIER:
		for w in player.stats.weapons.itervalues():
			w.exit(player)

	weapon = player.getPrimaryWeapon()
	if weapon:
		weaponType = getWeaponType(weapon.templateName)
		player.stats.weapons[weaponType].enter(player)

	if not vehicleType in player.stats.vehicles:
		player.stats.vehicles[vehicleType] = VehicleStat()
		
	player.stats.vehicles[vehicleType].enter(player)
	if vehicleType != VEHICLE_TYPE_UNKNOWN:
		player.stats.lastVehicleType = vehicleType
		
	weapon = player.getPrimaryWeapon()
	if weapon:
		player.stats.lastWeaponType = getWeaponType(weapon.templateName)
	
	collectBulletsFired(player)
	
	if g_debug: 		
		pos = getPosStr(vehicle.getPosition())
		print "STATS onEnterVehicle:  Enter vehicle=%s type=%s Pos=%s" % ( rootVehicle.templateName, vehicleType, pos )

def onExitVehicle(player, vehicle):
	
	vehicleType = getVehicleType(vehicle.templateName)
	if vehicleType != VEHICLE_TYPE_SOLDIER:
		rootVehicle = bf2.objectManager.getRootParent(vehicle)
		vehicleType = getVehicleType(rootVehicle.templateName)
		
		# keep track of last driver, for road kill scoring purposes
		if rootVehicle == vehicle:
			vehicle.lastDrivingPlayerIndex = player.index
		
	else: rootVehicle = vehicle

	weapon = player.getPrimaryWeapon()
	if weapon:
		weaponType = getWeaponType(weapon.templateName)
		player.stats.weapons[weaponType].enter(player)
	
	player.stats.vehicles[vehicleType].exit(player)

	weapon = player.getPrimaryWeapon()
	if weapon:
		player.stats.lastWeaponType = getWeaponType(weapon.templateName)

	collectBulletsFired(player)
	
	pos = getPosStr(vehicle.getPosition())
	if g_debug: print "STATS onExitVehicle:  Exit vehicle=%s type=%s Pos=%s" % ( rootVehicle.templateName, vehicleType, pos ) 


def onPlayerSpawn(player, soldier):
	if g_debug: print "STATS onPlayerSpawn %s" % ( player.getName() )
	
	startSpawned(player)
	if player.getSquadId() != 0: startInSquad(player)
	if player.isSquadLeader(): startAsSql(player)		
	if player.isCommander(): startAsCmd(player)

	onEnterVehicle(player, soldier)
	player.soldier = soldier
	
	
def onPickupKit(player, kit):
	if g_debug: print "STATS onPickupKit %s kit=%s " % ( player.getName(), kit.templateName )

	kitType = getKitType(kit.templateName)

	if not kitType in player.stats.kits:
		player.stats.kits[kitType] = KitStat()

	if g_debug: print "STATS onPickupKit %s triggering --> player.stats.kits[kitType].enter(player)" % ( player.getName() )
	player.stats.kits[kitType].enter(player)
	player.stats.lastKitType = kitType
	
	weapon = player.getPrimaryWeapon()
	if weapon:
		player.stats.lastWeaponType = getWeaponType(weapon.templateName)


	
def onDropKit(player, kit):
	if g_debug: print "STATS onDropKit %s kit=%s " % ( player.getName(), kit.templateName )
	
	kitType = getKitType(kit.templateName)
	
	if g_debug: print "STATS onDropKit %s triggering --> player.stats.kits[kitType].exit(player)" % ( player.getName() )
	player.stats.kits[kitType].exit(player)
	
	for w in player.stats.weapons.itervalues():
		w.exit(player)
	
	collectBulletsFired(player)



def onPlayerChangeWeapon(player, oldWeapon, newWeapon):
	if g_debug: print "STATS onPlayerChangeWeapon %s " % ( player.getName() )
	
	if oldWeapon:
		oldWeaponType = getWeaponType(oldWeapon.templateName)
		
		if g_debug: print "STATS onPlayerChangeWeapon %s oldWeapon=%s triggering --> player.stats.weapons[oldWeaponType].exit(player) " % ( player.getName(), oldWeapon.templateName )
		player.stats.weapons[oldWeaponType].exit(player)
		
	if newWeapon:
		newWeaponType = getWeaponType(newWeapon.templateName)
		
		if g_debug: print "STATS onPlayerChangeWeapon %s newWeapon=%s triggering --> player.stats.weapons[newWeaponType].exit(player) " % ( player.getName(), newWeapon.templateName )
		player.stats.weapons[newWeaponType].enter(player)
		player.stats.lastWeaponType = newWeaponType

		

def onPlayerKilled(victim, attacker, weapon, assists, object):
	# check if killed by vehicle in motion
	killedByEmptyVehicle = False
	if attacker == None and weapon == None and object != None:
		if hasattr(object, 'lastDrivingPlayerIndex'):
			attacker = bf2.playerManager.getPlayerByIndex(object.lastDrivingPlayerIndex)
			killedByEmptyVehicle = True

	#omero,2006-05-01
	#known informations
	#This is only for debug info, so lets not do anything unless
	#  we are actually debugging. ;)
	if g_debug:
		vctm_name=victim.getName()
		vctm_vehv=victim.getVehicle()
		vctm_veht=getVehicleType(vctm_vehv.templateName)
		if vctm_veht != VEHICLE_TYPE_SOLDIER:
			vctm_vehr=bf2.objectManager.getRootParent(vctm_vehv)
			vctm_vehn=str(vctm_vehr.templateName)
			vctm_veht=getVehicleType(vctm_vehn)
		else:
			vctm_vehr=vctm_vehv
			vctm_vehn=str(vctm_vehr.templateName)
			vctm_veht=getVehicleType(vctm_vehn)
	
		atkr_name='None'
		atkr_vehn='None'
		atkr_veht='None'
		if attacker and not killedByEmptyVehicle:
			atkr_name=attacker.getName()
			atkr_vehv=attacker.getVehicle()
			atkr_veht=getVehicleType(atkr_vehv.templateName)
			if atkr_veht != VEHICLE_TYPE_SOLDIER:
				atkr_vehr=bf2.objectManager.getRootParent(atkr_vehv)
				atkr_vehn=str(atkr_vehr.templateName)
				atkr_veht=getVehicleType(atkr_vehn)
			else:
				atkr_vehr=atkr_vehv
				atkr_vehn=str(atkr_vehr.templateName)
				atkr_veht=getVehicleType(atkr_vehn)
		
		if attacker and killedByEmptyVehicle:
			atkr_name=attacker.getName()
			atkr_vehn=object.templateName
			atkr_veht=getVehicleType(atkr_vehn)
			
			
		if attacker == None and object != None:
			if g_debug: print "STATS onPlayerKilled %s (maybe) killed by ARTILLERY" % ( victim.getName() )
		
		if attacker == None and weapon == None and object != None:
			if g_debug: print "STATS onPlayerKilled %s (maybe) killed by EMPTYMOVINGVEHICLE" % ( victim.getName() )
	
		
		if victim and attacker:
			if victim.getName() == attacker.getName():
				if g_debug: print "STATS onPlayerKilled %s killed by SUICIDE!" % ( victim.getName() )
	
			if victim.getTeam() == attacker.getTeam():
				if g_debug: print "STATS onPlayerKilled %s killed by MATES!" % ( victim.getName() )
		
		
		if weapon == None and object != None:
			if g_debug: print "STATS onPlayerKilled no weapon but object=" + object.templateName
						
		if weapon:
			if g_debug: print "STATS onPlayerKilled weapon=%s" %( weapon.templateName )
	
		if object:
			if g_debug: print "STATS onPlayerKilled object=%s" % ( object.templateName )
		
		wepn_name='None'
		if weapon:
			wepn_name=weapon.templateName
		
		elif object:
			wepn_name=object.templateName
		
		else:
			if g_debug: print "STATS onPlayerKilled no weapon nor object"

			
	# killed by enemy
	if attacker != None:
	
		# no kill stats for teamkills / suicides!
		if attacker.getTeam() != victim.getTeam():
	
			# streaks
			attacker.stats.currentKillStreak += 1
			if attacker.stats.currentKillStreak > attacker.stats.longestKillStreak:
				attacker.stats.longestKillStreak = attacker.stats.currentKillStreak
	
			# end current death streak
			attacker.stats.currentDeathStreak = 0  

			# killedBy
			if attacker != None:
				if not victim.stats.connectionOrderNr in attacker.stats.killedPlayer:
					attacker.stats.killedPlayer[victim.stats.connectionOrderNr] = 0
				attacker.stats.killedPlayer[victim.stats.connectionOrderNr] += 1
			
				if not attacker.stats.connectionOrderNr in victim.stats.killedByPlayer:
					victim.stats.killedByPlayer[attacker.stats.connectionOrderNr] = 0
				victim.stats.killedByPlayer[attacker.stats.connectionOrderNr] += 1
		
		
			# weapon stats
			if weapon != None:
				weaponType = getWeaponType(weapon.templateName)
				
				if attacker != None:
					attacker.stats.weapons[weaponType].kills += 1

				if victim != None:
					victim.stats.weapons[weaponType].killedBy += 1
					
			# vehicle stats
			vehicleType = None
			if killedByEmptyVehicle:
				vehicleType = getVehicleType(object.templateName)
			else:
				vehicle = attacker.getVehicle()
				vehicleType = getVehicleType(vehicle.templateName)
				
				if vehicleType != VEHICLE_TYPE_SOLDIER:
					rootVehicle = bf2.objectManager.getRootParent(vehicle)
					if rootVehicle != None:
						vehicleType = getVehicleType(rootVehicle.templateName)
		
			if vehicleType != None:		
				if attacker != None:
					attacker.stats.vehicles[vehicleType].kills += 1
				if victim != None:
					victim.stats.vehicles[vehicleType].killedBy += 1
	
				# road kill
				if weapon == None and object != None:
					attacker.stats.vehicles[vehicleType].roadKills += 1
		
		
			# kit stats
			if attacker != None:
				kit = attacker.getKit()
				if kit != None:
					kitTemplateName = kit.templateName
					kitType = getKitType(kitTemplateName)
				elif hasattr(attacker, 'lastKitType'):
					kitType = attacker.lastKitType
				else:
					return
							
				attacker.stats.kits[kitType].kills += 1
	
	# death stats are handled in onPlayerDeath.
	
	#omero, 2006-05-01
	if g_debug: print "STATS onPlayerKilled vctm=%s atkr=%s wepn=%s atkr_vehn=%s atkr_veht=%s" % ( vctm_name, atkr_name, wepn_name, atkr_vehn, atkr_veht )

	collectBulletsFired(attacker)



def onPlayerDeath(victim, vehicle):

	# vehicle is already exited, as this happens before actual death. That doesnt stop us from dying in it.
	rootVehicle = bf2.objectManager.getRootParent(vehicle)
	vehicleType = getVehicleType(rootVehicle.templateName)
	
	if g_debug: print "STATS onPlayerDeath %s rootVehicle=%s" % ( victim.getName(), rootVehicle.templateName )

	stopSpawned(victim)
	stopInSquad(victim)
	stopAsSql(victim)
	stopAsCmd(victim)
		
	if g_debug: print "STATS onPlayerDeath %s triggering ---> onExitVehicle(victim, victim.soldier)" % ( victim.getName() )
	onExitVehicle(victim, victim.soldier)
	finalizeBulletsFired(victim)
	clearBulletsFired(victim)
		
	# streaks
	victim.stats.currentDeathStreak += 1
	if victim.stats.currentDeathStreak > victim.stats.longestDeathStreak: 
		victim.stats.longestDeathStreak = victim.stats.currentDeathStreak

	# end current kill streak
	victim.stats.currentKillStreak = 0 

	victim.stats.vehicles[vehicleType].deaths += 1

	# kit is already dropped, so we have to get the last kit used
	victim.stats.kits[victim.stats.lastKitType].deaths += 1
		
	# weapon is already dropped, so we gave to get the last weapon used
	victim.stats.weapons[victim.stats.lastWeaponType].deaths += 1



# update accuracy on weapon, kit and vehicle
def collectBulletsFired(player):
	if player == None: return
	
	# count bullets fired
	bulletsFired = player.score.bulletsFired
	totBulletsFired = 0
	kitBulletsFired = 0
	for b in bulletsFired:
		templateName = b[0]
		nr = b[1]

		weaponType = getWeaponType(templateName)
		player.stats.weapons[weaponType].bulletsFiredTemp = nr
		totBulletsFired += nr

		# only count kit stats for soldier-type weapons
		if weaponType != WEAPON_TYPE_UNKNOWN:
			kitBulletsFired += nr
					
	
	# count bullets hit 
	bulletsHit = player.score.bulletsGivingDamage
	totBulletsHit = 0
	kitBulletsHit = 0
	for b in bulletsHit:
		templateName = b[0]
		nr = b[1]

		weaponType = getWeaponType(templateName)
		player.stats.weapons[weaponType].bulletsHitTemp = nr
		totBulletsHit += nr

		# only count kit stats for soldier-type weapons
		if weaponType != WEAPON_TYPE_UNKNOWN:
			kitBulletsHit += nr
	

	# dont bother giving kit stats if we're in a vehicle
	kit = player.getKit()
	if kit != None:
		kitType = getKitType(kit.templateName)
		player.stats.kits[kitType].bulletsFiredTemp = kitBulletsFired
		player.stats.kits[kitType].bulletsHitTemp = kitBulletsHit

	vehicle = player.getVehicle()
	if vehicle != None:
		rootVehicle = bf2.objectManager.getRootParent(vehicle)
		vehicleType = getVehicleType(rootVehicle.templateName)

		player.stats.vehicles[vehicleType].bulletsFiredTemp = totBulletsFired
		player.stats.vehicles[vehicleType].bulletsHitTemp = totBulletsHit
		


def clearBulletsFired(player):

# Added by Chump - for fragalyzer
	#bf2.stats.fragalyzer_log.dumpAccuracy(player)

	bulletsFired = player.score.bulletsFiredAndClear
	bulletsHit = player.score.bulletsGivingDamageAndClear



def finalizeBulletsFired(player):
	for v in player.stats.vehicles.itervalues():
		v.bulletsFired += v.bulletsFiredTemp
		v.bulletsHit += v.bulletsHitTemp
		v.bulletsFiredTemp = 0
		v.bulletsHitTemp = 0
	for w in player.stats.weapons.itervalues():
		w.bulletsFired += w.bulletsFiredTemp
		w.bulletsHit += w.bulletsHitTemp
		player.stats.bulletsFired += w.bulletsFiredTemp
		player.stats.bulletsHit += w.bulletsHitTemp
		w.bulletsFiredTemp = 0
		w.bulletsHitTemp = 0
	for k in player.stats.kits.itervalues():
		k.bulletsFired += k.bulletsFiredTemp
		k.bulletsHit += k.bulletsHitTemp
		k.bulletsFiredTemp = 0
		k.bulletsHitTemp = 0



def startSpawned(player): 
	player.stats.spawnedAt = date()
	player.stats.spawnedTeam = player.getTeam()
	
def startInSquad(player): player.stats.joinedSquadAt = date()
def startAsSql(player): player.stats.becameSqlAt = date()
def startAsCmd(player): player.stats.becameCmdAt = date()



def stopSpawned(player):
	time = player.stats.timePlayed
	player.stats.spawnedAt = 0
	player.stats.spawnedTeam = 3

def stopInSquad(player): 
	time = player.stats.timeInSquad
	player.stats.joinedSquadAt = 0

def stopAsSql(player): 
	time = player.stats.timeAsSql
	player.stats.becameSqlAt = 0

def stopAsCmd(player): 
	time = player.stats.timeAsCmd
	player.stats.becameCmdAt = 0
	
	
def onChangedCommander(team, oldCmd, newCmd):
	if newCmd and newCmd.stats.spawnedAt != 0: startAsCmd(newCmd)
	if oldCmd: stopAsCmd(oldCmd)
	
	if g_debug: print "STATS onChangeCommander team=%s oldCmd=%s newCmd=%s" % ( team, oldCmd.getName(), newCmd.getName() )

def onChangedSquadLeader(squad, oldSql, newSql):
	if newSql and newSql.stats.spawnedAt != 0: startAsSql(newSql)
	if oldSql: stopAsSql(oldSql)
	
	#This is only for debug info, so lets not do anything unless
	#  we are actually debugging. ;)
	if g_debug:
		osql_name = 'None'
		oteam = 'None'
		nsql_name = 'None'
		nteam = 'None'
	
		if oldSql != None:
			osql_name = oldSql.getName()
			oteam = oldSql.getTeam()
		
		if newSql != None:
			nsql_name = newSql.getName()
			nteam = newSql.getTeam()
		
		if g_debug: print "STATS onChangeSquadLeader squad=%s oteam=%s oldSql=%s nteam=%s newSql=%s" % ( squad, oteam, osql_name, nteam, nsql_name )

	
def onPlayerChangedSquad(player, oldSquad, newSquad):

	if g_debug: print "STATS onChangeSquad %s oldSqd=%s newSqd=%s" % ( player.getName(), oldSquad, newSquad )
	
	if oldSquad == newSquad:
		return

	if player.stats.spawnedAt == 0:
		return
	
	if oldSquad == 0: startInSquad(player)
	if newSquad == 0: 
		stopAsSql(player)
		stopInSquad(player)
	

	
def onPlayerBanned(player, time, type):
	
	# dont count round bans
	if type == 2: return
	
	player.stats.timesBanned += 1
	


def onPlayerKicked(player):
	player.stats.timesKicked += 1
