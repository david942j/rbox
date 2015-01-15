require '../server/database'
$size_t = 4
$batch_size = 0x100000
$port = 12456
class String
  def rm_main
    return self[$main_dir.length..-1]
  end
end
class Util
  def self.make_message(action, file_name, time, file=nil)
    msg = {
      :action => action,
      :data => {
        :file_name => file_name,
        :time => time.to_i
      }
    }
    msg[:data][:file] = file if file != nil
    return msg
  end

  def self.bytes_to_int(str)
    return str.bytes.inject(0){|s,c|s=(s<<8)+c;s}
  end

  def self.int_to_bytes(len)
    3.downto(0).inject([]){|arr,i|arr << ((len/2**(8*i))&255).chr;arr}
  end

  def self.gen_local_file_data(dir)
    hash = {}
    Dir[dir+"/**/*"].each do |file|
      hash[file.rm_main] = Util.file_data_hash(file)
    end
    return hash
  end

  def self.file_data_hash(file)
    return {:action=>:update, :time=>File.atime(file).to_i} rescue nil
  end

  def self.parse_msg(queue)
    return -1 if queue.length < $size_t
    len = Util.bytes_to_int(queue[0...$size_t])
    return -1 if queue.length < len+$size_t
    msg = queue[$size_t...(len+$size_t)]
    queue.slice!(0, len+$size_t)
    return eval(msg) #=^o w o^=
  end

  def self.error(msg)
    print msg+"\n"
    return false
  end
end
def error(msg)
  Util.error(msg)
end