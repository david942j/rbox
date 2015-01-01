<?php
function is_image($ext) {
  switch(strtolower($ext)) {
    case 'png':
    case 'jpg':
    case 'jpeg':
    case 'gif':
    case 'bmp':
      return true;
  }
  return false;
}
function to_sprite_class($ext) {
  switch($ext) {
    case 'rb':
    case 'cpp':
    case 'py':
    case 'java':
      return 'code';
    case 'pdf': return 'pdf';
    case 'txt': return 'text';
  }
  return 'white';
}
function to_file_type($ext) {
  switch(to_sprite_class($ext)) {
    case 'code':
      return '程式碼';
    case 'pdf': 
    case 'text': return '文件';
  }
  return '檔案';
}