function showFileDetail(filename) {
  $('#file-detail-modal').modal();
}
function deleteFile(filename) {
  if(confirm('確定要刪除'+filename+'?')==false)return false;
  console.log('delete');
  return true;
}