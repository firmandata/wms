<?php
echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'material_inventory_hold_form',
		'id'	=> 'material_inventory_hold_form'
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
			<th width="100"><label for="material_inventory_hold_form_code">No</label></th>
			<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'material_inventory_hold_form_code',
		'class'	=> 'required',
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->code : '')
	)
);?>
			</td>
		</tr>
		<tr>
			<th><label for="material_inventory_hold_form_hold_date">Date</label></th>
			<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'hold_date',
		'id' 	=> 'material_inventory_hold_form_hold_date',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->hold_date) ? date($this->config->item('server_display_date_format'), strtotime($record->hold_date)) : '')
	)
);?>
			</td>
		</tr>
		<tr>
			<th><label for="material_inventory_hold_form_notes">Notes</label></th>
			<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'material_inventory_hold_form_notes',
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
var material_inventory_hold_on_sucess;

jQuery(function(){
	jquery_ready_load();
	
	jQuery("#material_inventory_hold_form").validate({
		submitHandler: function(form){
			jQuery("#material_inventory_hold_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					material_inventory_hold_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
});

function material_inventory_hold_form_submit(on_success){
	material_inventory_hold_on_sucess = on_success;
	jQuery('#material_inventory_hold_form').submit();
}
</script>