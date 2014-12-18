require 'rubygems'
require 'rb-inotify'
require 'sync-file'
$debug = false

class Watcher
  @@notifier = {}
  def self.register(dir) #register directory only
    p "registing #{dir}"
    return false if @@notifier[dir] != nil
    @@notifier[dir] = [INotify::Notifier.new]
    @@notifier[dir] << Thread.new do
      @@notifier[dir][0].watch(dir, :create, :delete, :modify, :moved_from, :access) do |event|
        flags = event.flags
        file_name = event.watcher.path+'/'+event.name
        p "#{event.name} #{flags}"
        if flags.include?(:isdir) #directory
          if flags.include?(:create)
            Watcher.register(file_name)
          elsif flags.include?(:delete) or flags.include?(:moved_from)
            Watcher.remove(file_name)
          end
        else #file
          if flags.include?(:create) || flags.include?(:modify) || flags.include?(:access)
            print "send #{file_name} done\n" if SyncFile.send_file(file_name)
          elsif flags.include?(:delete) or flags.include?(:moved_from)
            SyncFile.delete_file(file_name)
          end
        end
        p event if $debug
      end
      @@notifier[dir][0].run
    end
  end

  def self.remove(dir)
    return if @@notifier[dir].nil?
    p "removing... #{dir}"
    @@notifier[dir][0].close
    Thread.kill(@@notifier[dir][1])
    @@notifier.delete(dir)
    SyncFile.delete_dir(dir)
  end
end

=begin
notifier = INotify::Notifier.new

notifier.watch(".", :create, :delete, :modify) do |event|
  event.flags.each do |flag|
    flag = flag.to_s
    p flag
    p event
    puts case flag
      when 'create' then "#{event.name} create"
      when 'delete' then "#{event.name} delete"
      when 'modify' then "#{event.name} change"
    end
  end
end
=end
#notifier.run
=begin
require 'socket'

SIZE = 1024
TCPSocket.open('192.168.1.159', 12345) do |socket|
  File.open('./meow.png', 'rb') do |file|
  	while chunk = file.read(SIZE)
  	  socket.write(chunk)
    end
  end
end
=end
#c = Curl::Easy.new("192.168.1.181:3000")
#c.multipart_form_post = true
#c.http_post(Curl::PostField.file('thing[file]', 'watcher.rb'))
