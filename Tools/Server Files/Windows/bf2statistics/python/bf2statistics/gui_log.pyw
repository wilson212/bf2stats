import socket, struct

HOST = 'localhost'
PORT = 2501

def f_conns( sockets ):
    return [sock for sock in sockets if isinstance( sock, connection )]
def f_socks( sockets ):
    return [sock for sock in sockets if not isinstance( sock, connection )]

class connection( object ):
    def __init__( self, s, addr ):
        self.socket = s
        self.addr = addr
        self.fileno = s.fileno
        self.recv_buffer = str()
        self.send_buffer = str()
        self.messages = list()
    def send( self ):
        sent = self.socket.send( self.send_buffer )
        self.send_buffer = self.send_buffer[sent:]
    def recv( self ):
        data = self.socket.recv( 1024 )
        self.recv_buffer += data
    def update( self ):
        read, write, error = select.select( [self.socket], [self.socket], [self.socket], 0 )
        if read: self.recv()
        if write and self.send_buffer: self.send()
    def close( self ):
        self.socket.close()

    def __iter__( self ):
        self.recv_buffer
        while 1:
            buffer_len = len( self.recv_buffer )
            if buffer_len > 4:
                msg_size = struct.unpack( 'i', self.recv_buffer[:4] )[0]
                if buffer_len >= 4+msg_size:
                    yield self.recv_buffer[4:4+msg_size]
                    self.recv_buffer = self.recv_buffer[4+msg_size:]
                else:
                    return
            else:
                return
    def push( self, msg ):
        self.send_buffer += struct.pack( 'i', len( msg ) )
        self.send_buffer += msg

class server:
    def __init__( self, host='', port=PORT ):
        acceptor = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        acceptor.bind(('', port))
        acceptor.listen(1)
        self.sockets = [acceptor]
        self.on_disconnect=lambda conn:None
        self.on_connect=lambda conn:None

    def connections( self ):
        for c in list( self.sockets ):
            if isinstance( c, connection ):
                yield c

    def __iter__( self ):
        need_write = [sock for sock in f_conns( self.sockets ) if sock.send_buffer]
        readable, writable, errors = select.select( self.sockets, need_write, self.sockets, 0 )
        for acceptor in f_socks( readable ):
            conn, addr = acceptor.accept()
            conn = connection( conn, addr )
            self.on_connect( conn )
            self.sockets.append( conn )
        for error in errors:
            self.sockets.remove( error )
        for read in f_conns( readable ):
            try:
                read.recv()
            except socket.error:
                self.remove( read )
            for msg in read:
                yield msg, read
        for write in writable:
            try:
                write.send()
            except socket.error:
                self.remove( write )
    def remove( self, connection ):
        if connection in self.sockets:
            self.on_disconnect( connection )
            connection.close()
            self.sockets.remove( connection )

class network_stream:
    def __init__( self, sock, prefix='n' ):
        self.sock = sock
        self.prefix = prefix
    def write( self, msg ):
        msg = self.prefix+msg
        msg = struct.pack( 'i', len( msg ) )+msg
        self.sock.send( msg )

def connect( server, port ):
    sock = socket.socket( socket.AF_INET, socket.SOCK_STREAM )
    sock.connect( (server,port) )
    return connection( sock, server )

def patch_sys():
    import sys
    sock = socket.socket( socket.AF_INET, socket.SOCK_STREAM )
    sock.connect( (HOST,PORT) )
    sys.stdout = network_stream( sock, 'n' )
    sys.stderr = network_stream( sock, 'e' )

if __name__ == '__main__':
    import Tix, select

    class application( Tix.Tk ):
        def __init__( self ):
            Tix.Tk.__init__( self )
            self.title( 'BF2 Python Log' )
            self.out = Tix.ScrolledText( self )
            self.out.text.tag_config( 'normal', foreground='#000000' )
            self.out.text.tag_config( 'error', foreground='#FC0000' )
            self.out.text.tag_config( 'server', foreground='#008F00' )
            self.out.text.configure( wrap='none', state=Tix.DISABLED )
            self.out.pack( fill=Tix.BOTH, expand=True )
            self.server = server()
            self.server.on_connect = self.on_connect
            self.after( 10, self.netupdate )
        def netupdate( self ):
            for msg, connection in self.server:
                self.out.text.configure( state=Tix.NORMAL )
                if msg[0] == 'n':
                    self.out.text.insert( 'end', msg[1:], 'normal' )
                elif msg[0] == 'e':
                    self.out.text.insert( 'end', msg[1:], 'error' )
                self.out.text.configure( state=Tix.DISABLED )
                self.out.text.see( 'end' )
            self.after( 10, self.netupdate )
        def on_connect( self, conn ):
            for c in self.server.connections():
                self.server.remove( c )
            self.out.text.configure( state=Tix.NORMAL )
            self.out.text.insert( 'end','\nconnect from: %s:%s\n'%conn.addr, 'server' )
            self.out.text.configure( state=Tix.DISABLED )


    app = application()
    app.mainloop()
else:
    patch_sys()
