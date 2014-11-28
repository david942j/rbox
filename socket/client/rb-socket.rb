require 'sync-file'
require 'socket'
$ip = File.read('../IP')[0...-1]
$port = 12456
class RbSocket
  @@s = nil
  def self.connect
    return if @@s !=nil && !@@s.closed?
    @@s = TCPSocket.new($ip, $port)
    raise if @@s.closed?
    sleep(0.1)
  end
  def self.send(arr)
    self.connect
    sleep(0.05)
    p "sending #{arr}"
    @@s.write(arr)
  end

  def self.close
    @@s.close
  end
end