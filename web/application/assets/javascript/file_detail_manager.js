var FileDetailManager = new function() {
  var $modal;
  var obj;
  return thisObj = {
    set: function(_obj) {
      obj = _obj;
      $modal = $('#file-detail-modal');
      $modal.find('.modal-title').html(genTitle());
      $modal.find('.modal-body').html(genContent());
      $('pre code').each(function(i, block) {
        console.log(block);
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

  function genContent() {
    var ret = $('<pre>'), code = $('<code>');
    code.text(obj.content);
    ret.append(code);
    return ret;
  }
}
/*
function setFileDetail(obj) {
  var $modal = $('#file-detail-modal');
  <? if(isset($file['base64'])) { ?>
            <img class='thumb' src="data:image/<?= $file['ext']?>;base64,<?= $file['base64'] ?>"/>
          <? }else{ ?>
            <img class="web_sprite sprite_<?= to_sprite_class($file['ext']) ?>" src="<?= image_url().'icon_spacer.gif'?>"/>
          <? } ?>
          <span class='filename' onclick="showFileDetail('<?= $file['name'] ?>')"><?= $file['name']?> </span>
  $modal.find('modal-title').html();
}
*/