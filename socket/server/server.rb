#!/usr/bin/ruby
require 'socket'
require 'util'
require 'thread'
#I think I have bug on time make the file always think updated in 1 sec
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
    print "Closing... writting data#{@@file_manager}\n"
    $db.write_all(@@file_manager)
  end

  def self.listen(client)
    queue = ''
    loop do
      break if client.closed?
      #sleep(0.01)
      #p 'dead '
      queue += client.recv(0x10)
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

  def self.check_notify #detect all files deleted or upload
    @@file_data = Util.gen_local_file_data($main_dir) #now
    #delete
    @@file_manager.each{|file_name,val|
      print "oao #{file_name}\n"
      if @@file_data[file_name.rm_main].nil? #delete
        msg = {
          :action => :delete,
          :data => {
            :file_name => file_name.rm_main,
            :time => Time.now #Time.now is issue...
          }
        }
        print "detect file #{file_name} delete\n"
        self.manager(file_name,:delete, msg[:data][:time], msg, nil)
      end
    }
    @@file_data.each{|file_name,val|
      if @@file_manager[file_name].nil? #upload
       # file_name = $main_dir+msg[:data][:file_name]
       # Server.manager(file_name,:update, msg[:data][:time], msg, client)
       # print "updating file \"#{file_name}\"...   "
       # File.open(file_name, 'wb'){|f|f.write(msg[:data][:file])}
       # print "done\n"
      end
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
Signal.trap("USR1") do
  Server.check_notify
end
main