function DropUploader(container, upload_url) {
  var $container = container, $filequeue, $filelist;
  var thisObj = this;
  thisObj.init = function() {
    $filequeue = $container.find(".filelist.queue");
    $filelist = $container.find(".filelist.complete");
    $container.find(".dropped").dropper({
      action: upload_url,
      maxSize: 0xA00000 // 10MB
    }).on("start.dropper", onStart)
      .on("complete.dropper", onComplete)
      .on("fileStart.dropper", onFileStart)
      .on("fileProgress.dropper", onFileProgress)
      .on("fileComplete.dropper", onFileComplete)
      .on("fileError.dropper", onFileError);
    $(window).one("pronto.load", function() {
      $container.find(".dropped").dropper("destroy").off(".dropper");
    });
    $('#upload-modal').on('hide.bs.modal',function() {
      if($filelist.find('li').length > 0)location.reload();
    });
  };
  function onStart(e, files) {
    console.log("Start");
    var html = '';
    for (var i = 0; i < files.length; i++) {
      html += '<li data-index="' + files[i].index + '"><span class="file">' + files[i].name + '</span><span class="progress">Queued</span></li>';
    }
    $filequeue.append(html);
  }
  function onComplete(e) {
    console.log("Complete");
    // All done!
  }
  function onFileStart(e, file) {
    console.log("File Start");
    $filequeue.find("li[data-index=" + file.index + "]")
    .find(".progress").text("0%");
  }
  function onFileProgress(e, file, percent) {
    console.log("File Progress");
    $filequeue.find("li[data-index=" + file.index + "]")
    .find(".progress").text(percent + "%");
  }
  function onFileComplete(e, file, response) {
    console.log("File Complete");
    if (response.trim() === "" || response.toLowerCase().indexOf("error") > -1) {
      $filequeue.find("li[data-index=" + file.index + "]").addClass("error")
      .find(".progress").text(response.trim());
    } else {
      var $target = $filequeue.find("li[data-index=" + file.index + "]");
      $target.find(".file").text(file.name);
      $target.find(".progress").remove();
      $target.appendTo($filelist);
    }
  }
  function onFileError(e, file, error) {
    console.log("File Error");
    $filequeue.find("li[data-index=" + file.index + "]").addClass("error")
    .find(".progress").text("Error: " + error);
  }
  thisObj.init();
  return thisObj;
}
