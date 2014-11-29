require 'rb-socket'
require '../server/util'
$file_data = {}
class SyncFile
  def self.connect
    $file_data = Util.gen_local_file_data($main_dir)
    RbSocket.send({:action=>'init', :data=>$file_data})
  end

  def self.exec(msg) #exec msg from server
    if msg[:action]=='request'
      file_name = $main_dir+'/'+msg[:data][:file_name]
      return error("server request #{file_name} not exists.") if File.exists?(file_name)
      self.send_file(file_name)
    else
      raise
    end
  end

  def self.send_file(file_name)
    
  end

  def self.update_file(file_name)
    RbSocket.send(file_name)
  end

  def self.delete_file(file_name)
    
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