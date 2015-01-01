<?php include 'header.php'; ?>
<h3>檔案列表</h3>
<div class='file-list'>
  <table class='table table-hover'>
    <th>
      <td>檔名</td>
      <td>類型</td>
      <td>上次修改</td>
    </th>
    <? foreach($files as $key) { ?>
      <tr>
        <td><?= $key ?></td>
      </tr>
    <? } ?>
  </table>
  <? print_r($files) ?>
</div>
<?php include 'footer.php'; ?>
