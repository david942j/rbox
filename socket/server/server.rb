#!/usr/bin/ruby
require 'socket'
require 'util'
require 'thread'

$debug = false
$main_dir = '../sync'
class Server
  @@client = {}
  @@cache_time = {}
  def self.start
    @@s = TCPServer.new $port
    loop do
      client = @@s.accept
      @@client[client.object_id] = Thread.new do
        Server.listen client
      end
    end
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
      break if msg[:action]=='close'
      Server.exec(msg, client)
    end
    print "client dead\n"
    @@client.delete(client.object_id)
  end

  def self.exec(msg, client)
    if msg[:action] == 'init'
      msg[:data].each{|f,obj|
        if obj[:exists]
          main_f = $main_dir+f
          self.request_file f,client if !File.exists?(main_f) || @@cache_time[main_f].nil? || @@cache_time[main_f] < obj[:time]
          sleep(0.1)
        end
      }
    elsif msg[:action] == 'update'
      file_name = $main_dir+msg[:data][:file_name]
      @@cache_time[file_name] = msg[:data][:time]
      print "updating file \"#{file_name}\"...   "
      File.open(file_name, 'wb'){|f|f.write(msg[:data][:file])}
      print "done\n"
    elsif msg[:action] == 'delete'
      file_name = $main_dir+msg[:data][:file_name]
      print "deleting file \"#{file_name}\"...   "
      %x(rm #{file_name})
      print "done\n"
    else
      raise
    end
  end

  def self.send(data, client)
    return if client.closed?
    print "sending #{data}\n"
    str = YAML.dump(data)
    client.write(Util.int_to_bytes(str.length).to_s+str)
  end

  def self.request_file(f, client)
    self.send({:action=>'request', :data=>{:file_name=>f}}, client)
  end
end

def main
  Server.start
end

main