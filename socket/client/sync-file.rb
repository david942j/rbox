require 'rb-socket'
require '../server/util'
class SyncFile
=begin
  1. sync local file and local database 
  2. sync local to server
  3. sync server to client
=end
  def self.connect
    self.sync_local_database_and_file
    #step 3
    RbSocket.send({:action=>:init, :data=>@@file_data})
  end

  def self.sync_local_database_and_file #detect file deleted or updated
    db_data = $db.read_all #db remember
    @@file_data = Util.gen_local_file_data($main_dir) #now
    #step 1+2
    db_data.each{|file,val|
      if @@file_data[file].nil? #delete
       self.delete_file($main_dir+file)
      elsif @@file_data[file][:time] > db_data[file][:time] #update
        self.update_file($main_dir+file)
      end
    }
    @@file_data.each{|file,val|
      if db_data[file].nil? #new
        self.update_file($main_dir+file)
      end
    }
  end

  def self.exec(msg) #exec msg from server
    if msg[:action] == :request
      file_name = $main_dir+msg[:data][:file_name]
      p "exec request #{file_name}"
      return error("server request #{file_name} not exists.") if !File.exists?(file_name)
      self.send_file(file_name)
    elsif msg[:action] == :update
      file_name = $main_dir+msg[:data][:file_name]
      return if @@file_data[file_name.rm_main] != nil && @@file_data[file_name.rm_main][:time] >= msg[:data][:time]
      print "Server ask: updating file \"#{file_name}\"...   "
      File.open(file_name, 'wb'){|f|f.write(msg[:data][:file])}
      print "done\n"
      @@file_data[file_name.rm_main] = Util.file_data_hash(file_name)
    elsif msg[:action] == :delete
      file_name = $main_dir+msg[:data][:file_name]
      print "Server ask: deleting file \"#{file_name}\"...   "
      %x(rm #{file_name})
      print "done\n"
      @@file_data.delete(file_name.rm_main)
    else
      raise
    end
  end

  def self.send_file(file_name)
    modify_time = File.atime(file_name).asctime rescue nil
    return error("modify_time is nil") if modify_time.nil?
    return error("updated in 1 sec") if @@file_data[file_name.rm_main] != nil && @@file_data[file_name.rm_main][:time] >= modify_time
    file = File.open(file_name, "rb") {|io| io.read } rescue nil
    return error("file #{file_name} not exists") if file.nil?
    return self.do_send_file(file_name, file, modify_time)
  end

  def self.update_file(file_name) #update file no matter how
    modify_time = File.atime(file_name).asctime rescue nil
    return error("in update_file, modify_time is nil") if modify_time.nil?
    file = File.open(file_name, "rb") {|io| io.read } rescue nil
    return error("in update_file, file #{file_name} not exists") if file.nil?
    return self.do_send_file(file_name, file, modify_time)
  end

  def self.do_send_file(file_name, file, modify_time) #pass all check
    @@file_data[file_name.rm_main] = Util.file_data_hash(file_name)
    hash = {
      :action => :update,
      :data => {
        :file_name => file_name.rm_main,
        :file => file,
        :time => modify_time
      }
    }
    return RbSocket.send(hash)
  end

  def self.delete_file(file_name)
    @@file_data.delete(file_name.rm_main)
    hash = {
      :action => :delete,
      :data => {
        :file_name => file_name.rm_main,
        :time => Time.now
      }
    }
    return RbSocket.send(hash)
  end

  def self.update_dir(dir)
    RbSocket.send(dir)
  end

  def self.delete_dir(dir)

  end

  def self.close
    $db.write_all(@@file_data)
    RbSocket.send({:action=>:close})
    RbSocket.close
  end
end