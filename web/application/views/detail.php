{
  <? if(isset($file['base64'])) { ?>
  "base64":"<?= $file['base64'] ?>",
  <?}else {?>
  "class":"<?= to_sprite_class($file['ext']) ?>",
  "spacer":"<?=image_url().'icon_spacer.gif'?>",
  "content":<?= json_encode(file_get_contents($file['src'])) // crash with break line?>,
  <? } ?>
  "name":"<?= $file['name'] ?>",
  "ext": "<?= $file['ext']?>"
}
