# ------------------------------------------------------------------------------
# omero 2006-02-27
# ------------------------------------------------------------------------------

import socket, string

CRLF = "\r\n"

def http_get(host, port = 80, document = "/"):

	try:
		http = miniclient(host, port)

	except Exception, e:

		if e[0] == 111:
			print	"Connection refused by server %s on port %d" % (host,port)

		raise

	http.writeline("GET %s HTTP/1.1" % str(document))
	http.writeline("Host: %s" % host)
	http.writeline("User-Agent: GameSpyHTTP/1.0")
	http.writeline("Connection: close") # do not keep-alive
	http.writeline("")
	http.shutdown() # be nice, tell the http server we're done sending the request

	# Determine Status
	statusCode = 0
	status = string.split(http.readline())
	if status[0] != "HTTP/1.1":
		print "MiniClient: Unknown status response (%s)" % str(status[0])
	
	try:
		statusCode = string.atoi(status[1])
	except ValueError:
		print "MiniClient: Non-numeric status code (%s)" % str(status[1])
	
	#Extract Headers
	headers = []
	while 1:
		line = http.readline()
		if not line:
			break
		headers.append(line)
	
	http.close() # all done

	#Check we got a valid HTTP response
	if statusCode == 200:
		return http.read()
	else:
		return "E\nH\terr\nD\tHTTP Error %s \"%s\"\n$\tERR\t$" % (str(statusCode), str(status[2]))
	


def http_postSnapshot(host, port = 80, document = "/", snapshot = ""):

	try:
		http = miniclient(host, port)

	except Exception, e:

		if e[0]	== 111:
			print	"Connection refused by server %s on port %d" % (host,port)
		
		raise

	try:
		http.writeline("POST %s HTTP/1.1" % str(document))
		http.writeline("HOST: %s" % str(host))
		http.writeline("User-Agent: GameSpyHTTP/1.0")
		http.writeline("Content-Type: application/x-www-form-urlencoded")
		http.writeline("Content-Length: %s" % str(len(snapshot)))
		http.writeline("Connection: close")
		http.writeline("")
		http.writeline(str(snapshot))
		http.writeline("")
		http.shutdown() # be nice, tell the http server we're done sending the request

		# Check that SnapShot Arrives.
		# Determine Status
		statusCode = 0
		status = string.split(http.readline())
		if status[0] != "HTTP/1.1":
			print "MiniClient: Unknown status response (%s)" % str(status[0])
		
		try:
			statusCode = string.atoi(status[1])
		except ValueError:
			print "MiniClient: Non-numeric status code (%s)" % str(status[1])
		
		#Extract Headers
		headers = []
		while 1:
			line = http.readline()
			if not line:
				break
			headers.append(line)
			
		http.close() # all done
		
		if statusCode == 200:
			return http.read()
		else:
			return "E\nH\terr\nD\tHTTP Error %s \"%s\"\n$\tERR\t$" % (str(statusCode), str(status[2]))

	except Exception, e:
		raise

class miniclient:
	"Client support class for simple Internet protocols."

	def __init__(self, host, port):
		"Connect to an Internet server."
		

		self.sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
		self.sock.settimeout(30)

		try:
			self.sock.connect((host, port))
			self.file = self.sock.makefile("rb")

		except socket.error, e:

			#if e[0]	== 111:
			#	print	"Connection refused by server %s on port %d" % (host,port)
			raise


	def writeline(self, line):
		"Send a line to the server."
		
		try:
			# Updated to sendall to resolve partial data transfer errors
			self.sock.sendall(line + CRLF) # unbuffered write

		except socket.error, e:
			if e[0] == 32 : #broken pipe
				self.sock.close() # mutual close
				self.sock = None
			
			raise e

		except socket.timeout:
			self.sock.close() # mutual close
			self.sock = None
			raise

	def readline(self):
		"Read a line from the server.  Strip trailing CR and/or LF."
		
		s = self.file.readline()
		
		if not s:
			raise EOFError
		
		if s[-2:] == CRLF:
			s = s[:-2]
		
		elif s[-1:] in CRLF:
			s = s[:-1]
		
		return s


	def read(self, maxbytes = None):
		"Read data from server."
		
		if maxbytes is None:
			return self.file.read()
		
		else:
			return self.file.read(maxbytes)


	def shutdown(self):
		
		if self.sock:
			self.sock.shutdown(1)


	def close(self):
		
		if self.sock:
			self.sock.close()
			self.sock = None
