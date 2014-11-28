require 'watcher'

def main
  SyncFile.connect
  Watcher.register('.')
  loop{} 
  ensure
    SyncFile.close 
end

main