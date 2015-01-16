require 'sync-file'
require 'socket'
require '../server/util'
require 'thread'

$ip = File.read('../IP')[0...-1]
class RbSocket
  @@s = nil
  @@lock = Mutex.new
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
    str = data.inspect
    print "sending #{str.length}\n"
    @@lock.synchronize{
      @@s.write(Util.int_to_bytes(str.length).to_s+str)
    }
    print "fin\n"
    return true
  end

  def self.read
    queue = ''
    loop do
      break if @@s.closed?
      sleep(0.01)
      queue = @@s.read($size_t)
      len = Util.bytes_to_int(queue[0...$size_t])
      queue += @@s.read(len)
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