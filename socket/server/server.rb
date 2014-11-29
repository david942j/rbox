require 'socket'
require 'util'

$debug = true
$batch_size = 0x1000
$main_dir = '../sync'
class Server
  @@client = {}
  def self.start
    @@s = TCPServer.new 12456
    Thread.new do
      loop do
        client = @@s.accept
        @@client[client.object_id] = Thread.new do
          Server.listen client
        end
      end
    end
  end

  def self.listen(client)
    queue = ''
    loop do
      break if client.closed?
      sleep(0.01)
      queue += client.recv($batch_size)
      msg = Util.parse_msg(queue)
      next if msg === -1
      p msg
      break if msg[:action]=='close'
      Server.exec(msg, client)
    end
    p "client dead"
    @@client.delete(client.object_id)
  end

  def self.exec(msg, client)
    if msg[:action] == 'init'
      msg[:data].each{|f,obj|
        if obj[:exists]
          print "#{f} exists, time=#{obj[:time]}\n"
          self.request_file f,client if ! File.exists?($main_dir+f)
        end
      }
    elsif msg[:action] == 'update'
      file_name = $main_dir+msg[:data][:file_name]
      File.open(file_name, 'wb'){|f|f.write(msg[:data][:file])}
      print "updated file #{file_name}\n"
    else
      raise
    end
  end

  def self.send(data, client)
    return if client.closed?
    print "sending #{data}\n"
    str = YAML.dump(data)
    Util.int_to_bytes(str.length).each{|c|client.write(c)}
    client.write(str)
  end

  def self.request_file(f, client)
    self.send({:action=>'request', :data=>{:file_name=>f}}, client)
  end
end

def main
  Server.start
  loop{}
end

main