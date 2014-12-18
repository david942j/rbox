require 'rb-socket'
require '../server/util'
$file_data = {}
class SyncFile
  def self.connect
    $file_data = Util.gen_local_file_data($main_dir)
    RbSocket.send({:action=>'init', :data=>$file_data})
  end

  def self.exec(msg) #exec msg from server
    if msg[:action] == 'request'
      file_name = $main_dir+msg[:data][:file_name]
      p "exec request #{file_name}"
      return error("server request #{file_name} not exists.") if !File.exists?(file_name)
      self.send_file(file_name)
    else
      raise
    end
  end

  def self.send_file(file_name)
    modify_time = File.atime(file_name).asctime rescue nil
    return error("modify_time is nil") if modify_time.nil?
    return error("updated in 1 sec") if $file_data[file_name.rm_main] != nil && $file_data[file_name.rm_main][:time] >= modify_time
    $file_data[file_name.rm_main] = Util.file_data_hash(file_name)
    file = File.open(file_name, "rb") {|io| io.read } rescue nil
    return error("file #{file_name} not exists") if file.nil?
    hash = {
      :action => 'update',
      :data => {
        :file_name => file_name.rm_main,
        :file => file,
        :time => modify_time
      }
    }
    return RbSocket.send(hash)
    #sleep(2)
  end

  def self.update_file(file_name)
    #RbSocket.send(file_name)
    raise
  end

  def self.delete_file(file_name)
    $file_data.delete(file_name.rm_main)
    hash = {
      :action => 'delete',
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
    RbSocket.send({:action=>'close'})
    RbSocket.close
  end
end