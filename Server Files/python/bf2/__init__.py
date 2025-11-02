import host
import sys

from bf2.Timer import Timer

class GameStatus:
	Playing = 1
	EndGame = 2
	PreGame = 3
	Paused = 4
	RestartServer = 5
	NotConnected = 6

playerManager = None
objectManager = None
triggerManager = None
gameLogic = None
serverSettings = None

g_debug = 0


# set up singletons
import bf2.PlayerManager
import bf2.ObjectManager
import bf2.TriggerManager
import bf2.GameLogic
playerManager = bf2.PlayerManager.PlayerManager()
objectManager = bf2.ObjectManager.ObjectManager()
triggerManager = bf2.TriggerManager.TriggerManager()
gameLogic = bf2.GameLogic.GameLogic()
serverSettings = bf2.GameLogic.ServerSettings()

# these are for wrapping purposes when converting c++ pointers into python objects
playerConvFunc = playerManager.getPlayerByIndex

class fake_stream:
	"""Implements stdout and stderr on top of BF2's log"""
	def __init__(self, name):
		self.name = name
		self.buf = ''

	def write(self, str):
		if len(str) == 0: return
		self.buf += str
		if str[-1] == '\n':
			host.log(self.name + ': ' + self.buf[0:-1])
			self.buf = ''

	def flush(self): pass
	def close(self): pass

class fake_stream2:
	"""Implements stdout and stderr on top of BF2's log"""
	def __init__(self, name):
		self.buf = [str(name), ': '] 

	def write(self, str):
		if len(str) == 0: return
		if str[-1] != '\n':
			self.buf.append (str)
		else:
			self.buf.append (str[0:-1])
			host.log("".join (self.buf))
			self.buf = [] 

	def flush(self): pass
	def close(self): pass

def init_module():
	# set up stdout and stderr to map to the host's logging function
	sys.stdout = fake_stream('stdout')
	sys.stderr = fake_stream('stderr')

	import game.scoringCommon
	game.scoringCommon.init()
	
	try:
		import bf2.stats.stats
	except ImportError:
		print "Official stats module not found."
	else:
		bf2.stats.stats.init()

	try:
		import bf2.stats.endofround
	except ImportError:
		print "Endofround module not found."
	else:
		bf2.stats.endofround.init()


	if not gameLogic.isAIGame():
	
		try:
			import bf2.stats.snapshot
		except ImportError:
			print "Snapshot module not found."
		else:
			bf2.stats.snapshot.init()
		
		try:
			import bf2.stats.rank
		except ImportError:
			print "Rank awarding module not found."
		else:
			bf2.stats.rank.init()

		try:
			import bf2.stats.medals
		except ImportError:
			print "Medal awarding module not found."
		else:
			bf2.stats.medals.init()
		
	try:
		import bf2.stats.unlocks
	except ImportError:
		print "Unlock awarding module not found."
	else:
		bf2.stats.unlocks.init()
		
	try:
		import bf2.stats.fragalyzer_log
	except ImportError:
		print "Fragalyzer log module not found."
	else:
		bf2.stats.fragalyzer_log.init()
	
