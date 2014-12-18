<?php include 'header.php' ?>
<div class='wire-body'>
	<h3>網路資訊</h3>
	<table class='table'>
		<thead>
			<tr>
				<th>#</th>
				<th>Device</th>
		        <th>DHCP</th>
		        <th>IP</th>
		        <th>Mask</th>
		        <th>Gateway</th>
		        <th>DNS server</th>
		        <th>Operating</th>
			</tr>
		</thead>
		<tbody>
			<? $i=1; ?>
			<?foreach($network->result() as $row) {?>
			<tr pid="<?=$i;?>">
				<td class='hide'><input id='old_device' name='old_device' value="<?=$row->device;?>"/></td>
				<td><?=$i;?></td>
				<td><div><?=$row->device;?></div><input name='device' class='hide' value="<?=$row->device;?>"/></td>
				<td><div><?=$row->dynamic_flag==1?'Yes':'No';?></div><input name='dhcp' class='hide' value="<?=$row->dynamic_flag==1?'Yes':'No';?>"/></td>
				<td><div><?=$row->ip_addr;?></div><input name='ip-addr' class='hide' value="<?=$row->ip_addr;?>"/></td>
				<td><div><?=$row->subnet_mask;?></div><input name='mask' class='hide' value="<?=$row->subnet_mask;?>"/></td>
				<td><div><?=$row->gateway;?></div><input name='gateway' class='hide' value="<?=$row->gateway;?>"/></td>
				<td><div><?=$row->dns;?></div><input name='dns' class='hide' value="<?=$row->dns;?>"/></td>
				<td>
					<span class='change-submit-btn btn btn-success hide' onclick="change_submit(<?=$i;?>)">確認</span>
					<span class='change-btn btn btn-info' onclick="change(<?=$i;?>)">修改</span>
					<span class='btn btn-danger delete-btn' onclick="delete_ap(<?=$i;?>)">刪除</span>
					<span class='btn btn-warning delete-btn' onclick="do_load_setting(<?=$i;?>)">套用</span>
				</td>
				<?$i++;?>
			</tr>
			<? } ?>
		</tbody>
	</table>
</div>
<div class='btn btn-primary' data-toggle="modal" data-target="#new-network-modal">新增網路</div>
<div id='new-network-modal' class="modal fade hide" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">新增網路</h4>
      </div>
      <div class="modal-body">
        <form id='new-network-form' role='form' method='post' action="<?=site_url()?>/system/new_network" data-remote="true">
        	<div class="form-group">
			    <label for="device">Device</label>
			    <input name='device' type="text" class="form-control" id="device" placeholder="Device name(e.g. usb1)">
			</div>
			<div class="checkbox">
		   		<label>
		      		<input type="checkbox" name="dhcp">Dynamic IP</input>
		   		</label>
		  	</div>
		  	<div class="form-group">
			    <label for="ip-addr">IP Address</label>
			    <input name='ip-addr' type="text" class="form-control" id="ip-addr" placeholder="static IP">
			</div>
			<div class="form-group">
			    <label for="mask">Subnet Mask</label>
			    <input name='mask' type="text" class="form-control" id="mask" placeholder="e.g. 255.255.255.0">
			</div>
			<div class="form-group">
			    <label for="gateway">Default Gateway </label>
			    <input name='gateway' type="text" class="form-control" id="gateway" placeholder="e.g. 192.168.1.1">
			</div>
			<div class="form-group">
			    <label for="dns">DNS Server</label>
			    <input name='dns' type="text" class="form-control" id="dns" placeholder="e.g. 8.8.8.8">
			</div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="$('#new-network-form').submit();">Submit</button>
      </div>
    </div>
  </div>
</div>
<script>
	$(function(){
	    $('#new-network-form').submit(function(evnt){
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
		$.post('<?=site_url()?>/system/change_network',
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
		$.post('<?=site_url()?>/system/delete_network',
			data,
			function(e) {
				if(e=='error')return alert('error');
				location.reload();
			}
		)
	}
	function do_load_setting(pid) {
		var r = confirm("會更改網路設定 確定修改?");
		if(r==true) {
			var data = {};
			$('tr[pid='+pid+'] input').each(function() {
				data[$(this).attr('name')] = $(this).val();
			});
			console.log(data);
			$.post('<?= site_url() ?>/system/do_network_setting',data,function(e) {
				console.log(e);
				alert('success');
			});
		}
		else return;
	}
</script>
<?php include 'footer.php' ?>
