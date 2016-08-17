<?php 
echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'asset_asset_transfer_form',
		'id'	=> 'asset_asset_transfer_form'
	)
);?>
<table class="form-table">
	<thead>
		<tr>
			<td colspan="2" class="form-table-title">Information</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th width="120"><label for="asset_asset_transfer_form_code">No</label></th>
			<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'asset_asset_transfer_form_code',
		'class'	=> 'required',
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->code : '')
	)
);?>
			</td>
		</tr>
		<tr>
			<th><label for="asset_asset_transfer_form_transfer_date">Date</label></th>
			<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'transfer_date',
		'id' 	=> 'asset_asset_transfer_form_transfer_date',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->transfer_date) ? date($this->config->item('server_display_date_format'), strtotime($record->transfer_date)) : '')
	)
);?>
			</td>
		</tr>
		<tr>
			<th><label for="asset_asset_transfer_form_notes">Notes</label></th>
			<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'asset_asset_transfer_form_notes',
		'value'	=> (!empty($record) ? $record->notes : ''),
		'cols'	=> 30, 'rows'	=> 2
	)
);?>
			</td>
		</tr>
	</tbody>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var asset_asset_transfer_on_sucess;

jQuery(function(){
	jquery_ready_load();
	
	jQuery("#asset_asset_transfer_form").validate({
		submitHandler: function(form){
			jQuery("#asset_asset_transfer_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					asset_asset_transfer_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
});

function asset_asset_transfer_form_submit(on_success){
	asset_asset_transfer_on_sucess = on_success;
	jQuery('#asset_asset_transfer_form').submit();
}
</script>