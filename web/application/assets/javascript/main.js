function showFileDetail(filename) {
  $('#file-detail-modal').modal();
}
function deleteFile(filename) {
  if(confirm('確定要刪除'+filename+'?')==false)return false;
  console.log('delete');
  $.post('index.php/files/delete', {file:filename},function(e) {
    if(e=='error')alert(e);
    location.reload();
  });
  return true;
}