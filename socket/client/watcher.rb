#!usr/bin/ruby
require 'rubygems'
require 'rb-inotify'
require 'curb'

class Watcher
  @notifier = {}
  def self.register(dir)
    dir = File.expand_path(dir)
    @notifier[dir] = INotify::Notifier.new
    @notifier[dir].watch(dir, :create, :delete, :modify) do |event|
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
    @notifier[dir].run
  end
end

Watcher.register('.')
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