require 'socket'
class Server
  @@client = {}
  def self.start
    @@s = TCPServer.new 12456
    Thread.new do
      loop do
        client = @@s.accept
        @@client[client.object_id] = Thread.new do
          loop do
            break if client.closed?
            str = client.recv(10)
            break if str=="__close__"
            p "get:#{str}"
          end
          p "client dead"
          @@client.delete(client.object_id)
        end
      end
    end
  end

end

def main
  Server.start
  loop{}
end

main