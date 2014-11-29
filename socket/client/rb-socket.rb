require 'sync-file'
require 'socket'
require '../server/util'

$ip = File.read('../IP')[0...-1]
$port = 12456
class RbSocket
  @@s = nil
  def self.connect
    return if @@s !=nil && !@@s.closed?
    @@s = TCPSocket.new($ip, $port)
    Thread.new do
      RbSocket.read
    end
    raise if @@s.closed?
    sleep(0.1)
  end

  def self.send(data)
    self.connect
    sleep(0.02)
    #print "sending #{data}\n"
    str = YAML.dump(data)
    Util.int_to_bytes(str.length).each{|c|@@s.write(c)}
    @@s.write(str)
  end

  def self.read
    queue = ''
    loop do
      break if @@s.closed?
      sleep(0.01)
      queue += @@s.recv($batch_size)
      msg = Util.parse_msg(queue)
      next if msg === -1
      SyncFile.exec(msg)
    end
    p "server dead"
  end

  def self.close
    @@s.close
  end
end