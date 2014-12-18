#!/usr/bin/ruby
require 'socket'
require 'util'
require 'thread'

$debug = false
$main_dir = '../sync'
$db = Database.new('rbox.db')
class Server
  @@client = {}
  @@file_manager = $db.read_all
  def self.start
    @@s = TCPServer.new $port
    loop do
      client = @@s.accept
      @@client[client.object_id] = Thread.new do
        Server.listen client
      end
    end
  end

  def self.close
    $db.write_all(@@file_manager)
  end

  def self.listen(client)
    queue = ''
    loop do
      break if client.closed?
      #sleep(0.01)
      #p 'dead '
      queue += client.recv(1)
      #p 'here'
      msg = Util.parse_msg(queue)
      next if msg === -1
      p msg if $debug
      break if msg[:action]==:close
      Server.exec(msg, client)
    end
    print "client dead\n"
    @@client.delete(client.object_id)
  end

  def self.exec(msg, client)
    if msg[:action] == :init
      msg[:data].each{|f,obj|
        main_f = $main_dir+f
        self.request_file f,client if !File.exists?(main_f) || 
                                   @@file_manager[main_f].nil? ||
                                   @@file_manager[main_f][:time] < obj[:time]
        sleep(0.1)
      }
    elsif msg[:action] == :update
      file_name = $main_dir+msg[:data][:file_name]
      Server.manager(file_name,:update, msg[:data][:time], msg, client)
      print "updating file \"#{file_name}\"...   "
      File.open(file_name, 'wb'){|f|f.write(msg[:data][:file])}
      print "done\n"
    elsif msg[:action] == :delete
      file_name = $main_dir+msg[:data][:file_name]
      Server.manager(file_name,:delete, msg[:data][:time], msg, client)
      print "deleting file \"#{file_name}\"...   "
      %x(rm #{file_name})
      print "done\n"
    else
      raise
    end
  end

  def self.manager(name, action, time, msg, except)
    @@file_manager[name] = {:action=>action, :time=>time}
    @@client.each{|object_id,_|
      next if object_id == except.object_id
      client = ObjectSpace._id2ref(object_id)
      self.send(msg,client)
    }
  end

  def self.send(data, client)
    return if client.closed?
    print "sending #{data}\n"
    str = YAML.dump(data)
    client.write(Util.int_to_bytes(str.length).to_s+str)
  end

  def self.request_file(f, client)
    self.send({:action=>:request, :data=>{:file_name=>f}}, client)
  end
end

def main
  Server.start
ensure
  Server.close
end

main