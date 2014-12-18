#!/usr/bin/ruby
require 'watcher'
$main_dir = '.'
$db = Database.new('../client.db')
def main
  SyncFile.connect
  Watcher.register($main_dir)
  Dir[$main_dir+'/*'].each{|f|
    Watcher.register(f) if File.directory? f
  }
  loop{} 
  ensure
    SyncFile.close
end

main
