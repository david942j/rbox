<?php include 'header.php'; ?>
<h3>檔案列表</h3>
<div class='file-list'>
  <table class='table table-hover'>
    <thead>
      <tr>
        <th>檔名</th>
        <th>類型</th>
        <th>上次修改</th>
      </tr>
    </thead>
    <tbody>
    <? foreach($files as $file) { ?>
      <tr>
        <td valign='center'>
          <? if(isset($file['base64'])) { ?>
            <img class='thumb' src="data:image/<?= $file['ext']?>;base64,<?= $file['base64'] ?>"/>
          <? }else{ ?>
            <img class="web_sprite sprite_<?= to_sprite_class($file['ext']) ?>" src="<?= image_url().'icon_spacer.gif'?>"/>
          <? } ?>
          <?= $file['name'] ?>
        </td>
        <td><?= to_file_type($file['ext']) ?></td>
        <td><?= $file['modify_time'] ?></td>
      </tr>
    <? } ?>
    </tbody>
  </table>
</div>
<?php include 'footer.php'; ?>
