require 'rb-socket'

class SyncFile
  def self.connect
    RbSocket.send("__init__")
  end

  def self.update_file(file_name)
    RbSocket.send(file_name)
  end

  def self.delete_file(file_name)
    
  end

  def self.update_dir(dir)

  end

  def self.delete_dir(dir)

  end

  def self.close
    RbSocket.send("__close__")
    RbSocket.close
  end
end