var FileDetailManager = new function() {
  var $modal;
  var obj;
  return thisObj = {
    set: function(_obj) {
      obj = _obj;
      $modal = $('#file-detail-modal');
      $modal.find('.modal-title').html(genTitle());
      $modal.find('.operation').html(genOperation());
      $modal.find('.modal-body').html(genContent());
      $('pre code').each(function(i, block) {
        hljs.highlightBlock(block);
      });
    }
  };
  function genTitle() {
    var ret = $('<span>');
    var img = $('<img>');
    if(obj.base64 != undefined) {
      img.addClass('thumb');
      img.attr('src', "data:image/"+obj.ext+";base64,"+obj.base64);
    }
    else {
      img.addClass('web_sprite');
      img.addClass('sprite_'+obj.class);
      img.attr('src', obj.spacer);
    }
    ret.append(img);
    ret.append('<span>'+obj.name+'</span>');
    return ret;
  }
  function genOperation() {
    return $('.operation[file="'+obj.name+'"]').html();
  }

  function genContent() {
    var ret;
    if(obj.base64) {
      ret = $('<div>').addClass('image-detail');
      var img = $('<img>');
      img.attr('src',"data:image/"+obj.ext+";base64,"+obj.base64);
      ret.append(img);
    }
    else if(obj.content){
      ret = $('<div>').addClass('text-detail');
      ret.append($('<pre>').append($('<code>').text(obj.content)));
    }
    else {
      ret = $('<div>').addClass('image-detail');
      var img = $('<img>');
      img.attr('src', obj.spacer128);
      ret.append(img);
      ret.append('<div>'+properMemorySize(obj.size)+'</div>');
    }
    return ret;
  }

  function properMemorySize(size) {
    if(size <= 100) // 100bytes
      return size+'bytes';
    else if(size < 1024*1024) // 1MB
      return (size/1024).toFixed(2) + 'KB';
    else return (size/1024/1024).toFixed(2) + 'MB';
  }
}
