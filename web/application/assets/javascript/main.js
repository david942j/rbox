function showFileDetail(filename) {
  $.post('index.php/files/detail', {file: filename},function(e){
    var obj = JSON.parse(e);
    if(obj['name'] == undefined)return alert('error') || false;
    FileDetailManager.set(obj);
    $('#file-detail-modal').modal();
  });
}

function deleteFile(filename) {
  if(confirm('確定要刪除'+filename+'?')==false)return false;
  console.log('delete');
  $.post('index.php/files/delete', {file:filename},function(e) {
    if(e=='error')alert(e);
    console.log(e);
    location.reload();
  });
  return true;
}

