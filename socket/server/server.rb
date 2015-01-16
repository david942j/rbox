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
  @@lock = {}
  @@file_manager = $db.read_all
  def self.start
    @@s = TCPServer.new $port
    loop do
      client = @@s.accept
      @@lock[client.object_id] = Mutex.new
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
      sleep(0.01)
      #p 'dead '
      queue = client.read($size_t)
      len = Util.bytes_to_int(queue[0...$size_t])
      queue += client.read(len)
      #p 'here'
      msg = Util.parse_msg(queue)
      next if msg === -1
      p msg if $debug
      break if msg[:action]==:close
      Server.exec(msg, client)
    end
    print "client dead\n"
    @@client.delete(client.object_id)
    @@lock.delete(client.object_id)
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

  def self.check_deleted #detect all files deleted
    @@file_data = Util.gen_local_file_data($main_dir) #now
    #delete
    @@file_manager.each{|file_name,val|
      if val[:action] != :delete && @@file_data[file_name.rm_main].nil? #delete
        msg = Util.make_message(:delete, file_name.rm_main, Time.now)
        print "detect file #{file_name} delete\n"
        self.manager(file_name,:delete, msg[:data][:time], msg, nil)
      end
    }
  end

  def self.check_uploaded #detect all files uploaded
    @@file_data = Util.gen_local_file_data($main_dir) #now
    #uploaded
    @@file_data.each{|file_name,val|
      if @@file_manager[$main_dir+file_name].nil? || @@file_manager[$main_dir+file_name][:action]==:delete #new
        print "find new file #{file_name}\n"

        file_name = $main_dir+file_name
        modify_time = File.atime(file_name).to_i rescue nil
        file = File.open(file_name, "rb") {|io| io.read } rescue nil
        
        msg = Util.make_message(:update, file_name.rm_main, modify_time, file)
        self.manager(file_name,:update, msg[:data][:time], msg, file)
      end
    }
  end

  def self.send(data, client)
    return if client.closed?
    print "sending #{data[:action]} #{data[:data][:file_name]}\n"
    str = data.inspect#YAML.dump(data)
    @@lock[client.object_id].synchronize{
      client.write(Util.int_to_bytes(str.length).to_s+str)
    }
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
  Server.check_deleted
end
Signal.trap("USR2") do
  Server.check_uploaded
end
main