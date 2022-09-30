#
# Based on code from: http://bf2.fun-o-matic.org/index.php/Scripts:ReserveSlots
#

import host
import string
import bf2.PlayerManager
from bf2.BF2StatisticsConfig import enableClanManager, serverMode, criteria_data, http_backend_addr, http_backend_port
from bf2.stats.miniclient import miniclient, http_get
from bf2 import g_debug

scriptName = "Clan Manager"
whitelistPlayers = []
blacklistPlayers = []
greylistPlayers = []
mode = serverMode

def init():
	if enableClanManager == 1:
		print "Clan Manager module initialized (%d)" % mode
		host.registerHandler('PlayerConnect', onPlayerConnect, 1)
		host.registerGameStatusHandler(onGameStatusChanged)

def getPlayerListData(asp_URI):
	playerListData = []
	data = http_get( http_backend_addr, http_backend_port, asp_URI )
	
	if data and data[0] == 'O':
		if g_debug: print "Received Clan list data is VALID, length %d" % int(len(data))
		datalines = data.splitlines()
		skip = True
		i = 0
		for dataline in datalines:
			# the first dataline retrieved only contains count and timestamp,
			# do nothing and mark the skip flag to false.
			# all subsequent datalines will be processed normally
			if dataline[0] == 'D' and skip:
				skip = False
			elif dataline[0] == 'D':
				items = dataline.split('\t')
				playerListData += [int(items[1])]
				if g_debug: print "List Member: %s (%s)" % (str(items[2]), str(items[1]))
	else:
		print "Received list Member data is INVALID, length %d" % int(len(data))
	
	return playerListData


def onGameStatusChanged(status):
	global whitelistPlayers, blacklistPlayers, greylistPlayers
	
	if g_debug: print "Clan Manager Detected Games Status Change (%d)!" % int(status)
	if status == bf2.GameStatus.PreGame:
		# Get Blacklist
		if g_debug: print "Retrieving Clan BlackList via HTTP/1.1 miniclient"
		queryStr = ''
		for criteriaItem in criteria_data:	# Build Criteria
				if criteriaItem[0] == 'banned':
					queryStr = '&' + criteriaItem[0] + '=' + str(criteriaItem[1])
		asp_URI = '/ASP/getclaninfo.aspx?type=0' + queryStr
		if g_debug: print "URI: %s" % (asp_URI)
		blacklistPlayers = getPlayerListData(asp_URI)
		
		if mode > 0:
			# Get Whitelist
			if g_debug: print "Retrieving Clan WhiteList via HTTP/1.1 miniclient"
			queryStr = ''
			for criteriaItem in criteria_data:	# Build Criteria
				if criteriaItem[0] == 'clantag':
					queryStr = '&' + criteriaItem[0] + '=' + str(criteriaItem[1])
			asp_URI = '/ASP/getclaninfo.aspx?type=1' + queryStr
			if g_debug: print "URI: %s" % (asp_URI)
			whitelistPlayers = getPlayerListData(asp_URI)
		
		if mode > 1:
			# Get Greylist
			if g_debug: print "Retrieving Clan GreyList via HTTP/1.1 miniclient"
			queryStr = ''
			for criteriaItem in criteria_data:	# Build Criteria
				queryStr += '&' + criteriaItem[0] + '=' + str(criteriaItem[1])
			asp_URI = '/ASP/getclaninfo.aspx?type=2' + queryStr
			if g_debug: print "URI: %s" % (asp_URI)
			greylistPlayers = getPlayerListData(asp_URI)
		
	elif status == bf2.GameStatus.EndGame:
		# Clear Clan Member Lists
		whitelistPlayers = []
		blacklistPlayers = []
		greylistPlayers = []

	
def onPlayerConnect(aPlayer):
	global whitelistPlayers, blacklistPlayers, greylistPlayers, mode
	
	# Ignore AI players
	if aPlayer.isAIPlayer():
		return
	
	if g_debug: print "Checking Player (%s)" % str(aPlayer.getName())
	# Check Blacklist (Kick these players regardless!)
	if aPlayer.getProfileId() in blacklistPlayers:
		if g_debug: print "Player (%s) kicked: Blacklisted!!!" % str(aPlayer.getName())
		host.rcon_invoke('game.sayAll "' + scriptName + ': Player ' + aPlayer.getName() + ' kicked: Blacklisted!!!' + '"')
		host.rcon_invoke('admin.kickPlayer ' + str(aPlayer.index))
		return
	
	# Access Check
	# Mode: Clan ONLY (Must be on Clan List to Join Server)
	if mode == 1:
		if not aPlayer.getProfileId() in whitelistPlayers:
			if g_debug: print "Player (%s) on Clan Member List!!!" % str(aPlayer.getName())
		else:	
			if g_debug: print "Player (%s) kicked: *NOT* on Clan Member List!" % str(aPlayer.getName())
			host.rcon_invoke('game.sayAll "' + scriptName + ': Player ' + aPlayer.getName() + ' kicked: *NOT* on Clan Member List!' + '"')
			host.rcon_invoke('admin.kickPlayer ' + str(aPlayer.index))
			return		
	
	elif mode == 2:	# Mode: Priority Proving Grounds (Clan Members AND those players that meet the minimum requirements.Clan Members get priority)
		if bf2.playerManager.getNumberOfPlayers() ==  bf2.serverSettings.getMaxPlayers():
			if not aPlayer.getProfileId() in whitelistPlayers:
				if g_debug: print "Player (%s) kicked: Server Full, Not on Clan Member List!" % str(aPlayer.getName())
				host.rcon_invoke('game.sayAll "' + scriptName + ': Player ' + aPlayer.getName() + ' kicked: Sorry, no room for Non-Clan Members!' + '"')
				host.rcon_invoke('admin.kickPlayer ' + str(aPlayer.index))
			else:	
				if g_debug: print "Player (%s) is on Clan Member List, kicking someone else!" % str(aPlayer.getName())
				currPlayers = bf2.playerManager.getPlayers()
				foundCommander = 0
				for i in currPlayers:
					# Look for someone to kick
					if not i.getProfileId() in whitelistPlayers:
						# Found someone, check if he's the commander
						if i.isCommander():
							commander = i
							foundCommander = 1
						else:
							host.rcon_invoke('game.sayAll "' + scriptName + ': Player ' + i.getName() + ' kicked for Clan Member ' + aPlayer.getName() + '"')
							host.rcon_invoke('admin.kickPlayer ' + str(i.index))
							return
				
				# If we checked everyone and only the commander remains... bye bye
				if foundCommander == 1:
					host.rcon_invoke('game.sayAll "' + scriptName + ': Player ' + commander.getName() + ' kicked for Clan Member ' + aPlayer.getName() + '"')
					host.rcon_invoke('admin.kickPlayer ' + str(commander.index))
					return
				else:
					# No one to kick =(
					return
				
		else:
			if aPlayer.getProfileId() in whitelistPlayers:
				if g_debug: print "Player (%s) on Clan Member List!!!" % str(aPlayer.getName())
			elif aPlayer.getProfileId() in greylistPlayers:
				if g_debug: print "Player (%s) meets minimum requirements!!!" % str(aPlayer.getName())
			else:
				if g_debug: print "Player (%s) kicked: Does *NOT* meet requirements!" % str(aPlayer.getName())
				host.rcon_invoke('game.sayAll "' + scriptName + ': Player ' + aPlayer.getName() + ' kicked: Does *NOT* meet requirements: KICKED!' + '"')
				host.rcon_invoke('admin.kickPlayer ' + str(aPlayer.index))
	
	elif mode == 3:	# Mode: Proving Grounds (Clan Members AND those players that meet the minimum requirements)
		if aPlayer.getProfileId() in whitelistPlayers:
			if g_debug: print "Player (%s) on Clan Member List!!!" % str(aPlayer.getName())
		elif aPlayer.getProfileId() in greylistPlayers:
			if g_debug: print "Player (%s) meets minimum requirements!!!" % str(aPlayer.getName())
		else:
			if g_debug: print "Player (%s) kicked: Does *NOT* meet requirements!" % str(aPlayer.getName())
			host.rcon_invoke('game.sayAll "' + scriptName + ': Player ' + aPlayer.getName() + ' kicked: Does *NOT* meet requirements: KICKED!' + '"')
			host.rcon_invoke('admin.kickPlayer ' + str(aPlayer.index))
	
	elif mode == 4:	# Mode: Experts ONLY (Only players that meet the minimum requirements)
		if aPlayer.getProfileId() in greylistPlayers:
			if g_debug: print "Player (%s) meets minimum requirements!!!" % str(aPlayer.getName())
		else:
			if g_debug: print "Player (%s) kicked: Does *NOT* meet requirements!" % str(aPlayer.getName())
			host.rcon_invoke('game.sayAll "' + scriptName + ': Player ' + aPlayer.getName() + ' kicked: Does *NOT* meet requirements: KICKED!' + '"')
			host.rcon_invoke('admin.kickPlayer ' + str(aPlayer.index))
	
	else:	# Mode: Public (Free-for-All. No Restrictions!)
		# It's a Free-for-all, nothing to do...
		return
