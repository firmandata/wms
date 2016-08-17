<?php 
$inventory_picklist_schedule_phase = array_merge(array('' => ''), $this->config->item('inventory_picklist_schedule_phase'));

echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'material_inventory_picklist_form',
		'id'	=> 'material_inventory_picklist_form'
	)
);?>
<table>
	<tr><td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">General</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="70"><label for="material_inventory_picklist_form_code">No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'material_inventory_picklist_form_code',
		'class'	=> (!empty($record) ? 'required' : ''),
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->code : ''),
		'placeholder'	=> '## Auto ##'
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_picklist_form_picklist_date">Date</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'picklist_date',
		'id' 	=> 'material_inventory_picklist_form_picklist_date',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->picklist_date) ? date($this->config->item('server_display_date_format'), strtotime($record->picklist_date)) : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_picklist_form_notes">Notes</label></th>
						<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'material_inventory_picklist_form_notes',
		'value'	=> (!empty($record) ? $record->notes : ''),
		'cols'	=> 30, 'rows'	=> 2
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th><label for="material_inventory_picklist_form_supervisor">Supervisor</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'supervisor',
		'id' 	=> 'material_inventory_picklist_form_supervisor',
		'value'	=> (!empty($record) ? $record->supervisor : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_picklist_form_schedule_time">Time</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'schedule_time',
		'id' 	=> 'material_inventory_picklist_form_schedule_time',
		'class'	=> 'required time',
		'style'	=> 'width:60px;',
		'value'	=> (!empty($record->schedule_time) ? date($this->config->item('server_display_time_format'), strtotime($record->schedule_time)) : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_picklist_form_schedule_phase">Phase</label></th>
						<td>
<?php 
echo form_dropdown('schedule_phase', $inventory_picklist_schedule_phase, (!empty($record) ? $record->schedule_phase : ''), 'id="material_inventory_picklist_form_schedule_phase"');?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_picklist_form_m_grid_caption">Location</label></th>
						<td><input type="hidden" name="m_grid_id" id="material_inventory_picklist_form_m_grid_id" class="required" value="<?php echo (!empty($record) ? $record->m_grid_id : '');?>" data-text="<?php echo (!empty($record) ? $record->m_grid_text : '');?>"/></td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var material_inventory_picklist_on_sucess;

jQuery(function(){
	jquery_ready_load();
	
	jQuery("#material_inventory_picklist_form").validate({
		submitHandler: function(form){
			jQuery("#material_inventory_picklist_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					material_inventory_picklist_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jquery_autocomplete_build("#material_inventory_picklist_form_m_grid_id", "<?php echo site_url('material/inventory_picklist_pakan/get_grid_autocomplete_list_json');?>", {
		width : 100
	});
});

function material_inventory_picklist_form_submit(on_success){
	material_inventory_picklist_on_sucess = on_success;
	jQuery('#material_inventory_picklist_form').submit();
}
</script>