<?php include 'header.php'; ?>
<div>
	<h3>無線網路</h3>
	<table class='table'>
		<thead>
			<tr>
				<th>#</th>
				<th>SSID</th>
		        <th>Type</th>
		        <th>psk</th>
		        <th>Priority</th>
		        <th>Operating</th>
			</tr>
		</thead>
		<tbody>
			<? $i=1; ?>
			<?foreach($wpa_conf->result() as $row) {?>
			<tr pid="<?=$i;?>">
				<td class='hide'><input id='old_ssid' name='old_ssid' value="<?=$row->ssid;?>"/></td>
				<td><?=$i;?></td>
				<td><div><?=$row->ssid;?></div><input name='ssid' class='hide' value="<?=$row->ssid;?>"/></td>
				<td><div><?=$row->type;?></div><input name='type' class='hide' value="<?=$row->type;?>"/></td>
				<td><div><?=$row->psk;?></div><input name='psk' class='hide' value="<?=$row->psk;?>"/></td>
				<td><div><?=$row->priority;?></div><input name='priority' class='hide' value="<?=$row->priority;?>"/></td>
				<td>
					<span class='change-submit-btn btn btn-success hide' onclick="change_submit(<?=$i;?>)">確認</span>
					<span class='change-btn btn btn-info' onclick="change(<?=$i;?>)">修改</span>
					<span class='btn btn-danger delete-btn' onclick="delete_ap(<?=$i;?>)">刪除</span></td>
				<?$i++;?>
			</tr>
			<? } ?>
		</tbody>
	</table>
</div>

<div class='btn btn-primary' data-toggle="modal" data-target="#new-ap-modal">新增無線網路</div>

<div class='btn btn-warning' onclick="do_load_setting()">套用無線網路設定</div>

<div id='new-ap-modal' class="modal fade hide" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">新增無線網路</h4>
      </div>
      <div class="modal-body">
        <form id='new-ap-form' role='form' method='post' action="<?=site_url()?>/system/new_ap" data-remote="true">
        	<div class="form-group">
			    <label for="ssid">SSID</label>
			    <input name='ssid' type="text" class="form-control" id="ssid" placeholder="SSID">
			</div>
		  	<div class="form-group">
			    <label for="type">Security Mode</label>
			    <input name='type' type="text" class="form-control" id="type" placeholder="e.g. wpa">
			</div>
			<div class="form-group">
			    <label for="psk">Key</label>
			    <input name='psk' type="text" class="form-control" id="psk" placeholder="password">
			</div>
			<div class="form-group">
			    <label for="priority">Priority</label>
			    <input name='priority' type="text" class="form-control" id="priority" placeholder="e.g. 10">
			</div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="$('#new-ap-form').submit();">Submit</button>
      </div>
    </div>
  </div>
</div>
<script>
	$(function(){
	    $('#new-ap-form').submit(function(evnt){
	        $.post($(this).attr('action'),
	        	$(this).serializeHash(),
		        function (data) {
		        	if(data=='error')return alert('error');
		        	location.reload();
		        });
	        return false;
    	});
	});
	function change(pid) {
		$('tr[pid='+pid+']>td>div').hide();
		$('tr[pid='+pid+']>td>input').show();
		$('tr[pid='+pid+']>td>span.change-submit-btn').show();
		$('tr[pid='+pid+']>td>span.change-btn').hide();
		$('tr[pid='+pid+']>td>span.delete-btn').hide();
	}
	function change_submit(pid) {
		var data = {};
		$('tr[pid='+pid+'] input').each(function() {
			data[$(this).attr('name')] = $(this).val();
		});
		$.post('<?=site_url()?>/system/change_ap',
			data,
			function(e) {
				if(e=='error')return alert('error');
				location.reload();
			}
		)
	}
	function delete_ap(pid) {
		var data = {};
		$('tr[pid='+pid+'] input').each(function() {
			data[$(this).attr('name')] = $(this).val();
		});
		$.post('<?=site_url()?>/system/delete_ap',
			data,
			function(e) {
				if(e=='error')return alert('error');
				location.reload();
			}
		)
	}

	function do_load_setting() {
		var r = confirm("會更改系统設定檔 確定修改?");
		if(r==true) {
			$.post('<?= site_url() ?>/system/do_ap_setting',{},function(e) {
				//console.log(e);
			});
		}
		else return;
	}
</script>
<?php include 'footer.php'; ?>