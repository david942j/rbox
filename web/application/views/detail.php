{
  <? if(isset($file['base64'])) { ?>
  "base64":"<?= $file['base64'] ?>",
  <?}else {?>
  "class":"<?= to_sprite_class($file['ext']) ?>",
  "spacer":"<?=image_url().'icon_spacer.gif'?>",
    <? if(showable($file['src'])) {?>
    "content":<?= json_encode(file_get_contents($file['src'])) ?>,
    <? }else { ?>
    "size": <?= filesize($file['src']) ?>,
    "spacer128": "<?=image_url().'page_white.png'?>",
    <? } ?>
  <? } ?>
  "name":"<?= $file['name'] ?>",
  "ext": "<?= $file['ext']?>"
}
