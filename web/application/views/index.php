<?php include 'header.php'; ?>
<h3>檔案列表</h3>
<div class='upload-btn' data-toggle='modal' data-target='#upload-modal'>
  <img class='web_sprite_s sprite_upload' src="<?= image_url().'icon_spacer.gif'?>"/>
  <span>檔案上傳</span>
</div>

<div class='file-list'>
  <table class='table table-hover'>
    <thead>
      <tr>
        <th>檔名</th>
        <th>類型</th>
        <th>上次修改</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
    <? foreach($files as $file) { ?>
      <tr class='file-row'>
        <td>
          <? if(isset($file['base64'])) { ?>
            <img class='thumb' src="data:image/<?= $file['ext']?>;base64,<?= $file['base64'] ?>"/>
          <? }else{ ?>
            <img class="web_sprite sprite_<?= to_sprite_class($file['ext']) ?>" src="<?= image_url().'icon_spacer.gif'?>"/>
          <? } ?>
          <span class='filename' onclick="showFileDetail('<?= $file['name'] ?>')"><?= $file['name']?> </span>
        </td>
        <td><?= to_file_type($file['ext']) ?></td>
        <td><?= $file['modify_time'] ?></td>
        <td file="<?= $file['name']?>" class='operation' style='opacity: 0.3'>
          <a href="<?= site_url().'/files/get?file='.$file['name'] ?>">
            <span class='button'>
              <img class='web_sprite_s sprite_download' src="<?= image_url().'icon_spacer.gif'?>"/>
              <span class='text-success'>下載</span>
            </span>
          </a>
          <span class='button' onclick='deleteFile("<?= $file['name'] ?>")'>
            <img  style='margin-left: 12px' class='web_sprite_s sprite_delete' src="<?= image_url().'icon_spacer.gif'?>"/>
            <span class='text-danger'>刪除</span>
          </span>
        </td>
      </tr>
    <? } ?>
    </tbody>
  </table>
</div>
<?php include 'footer.php'; ?>
