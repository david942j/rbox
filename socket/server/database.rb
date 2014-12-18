require 'yaml'
class Database
  def initialize(datafile)
    @datafile = datafile
  end
  @lock = false
  def read(file_name)
    return {} if !File.file?(@datafile)
    while @lock
      sleep(0.01)
    end
    all=YAML.load(File.read(@datafile))
    return all[file_name]
  end

  def read_all
    return {} if !File.file?(@datafile)
    while @lock
      sleep(0.01)
    end
    return YAML.load(File.read(@datafile))
  end

  def write(file_name, data)
    while @lock
      sleep(0.01)
    end
    @lock=true
    all=YAML.load(File.read(@datafile))
    all[file_name] = data
    File.write(@datafile, YAML.dump(all))
    @lock=false
  end

  def write_all(all)
    while @lock
      sleep(0.01)
    end
    @lock=true
    File.open(@datafile, 'w') { |file| file.write(YAML.dump(all)) }
    @lock=false
  end
end
