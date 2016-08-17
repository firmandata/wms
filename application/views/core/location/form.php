<?php
echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'core_location_form',
		'id'	=> 'core_location_form'
	)
);?>
<table width="100%">
	<tr>
		<td valign="top">
			<table class="form-table" width="100%">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">General</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="120"><label for="core_location_form_code">Code</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'core_location_form_code',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->code : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_location_form_name">Name</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'name',
		'id' 	=> 'core_location_form_name',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->name : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_location_form_c_region_id_caption">Region</label></th>
						<td><input type="hidden" name="c_region_id" id="core_location_form_c_region_id" class="required" value="<?php echo (!empty($record) ? $record->c_region_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_region_text : '');?>"/></td>
					</tr>
					<tr>
						<th><label for="core_location_form_c_department_id_caption">Department</label></th>
						<td><input type="hidden" name="c_department_id" id="core_location_form_c_department_id" value="<?php echo (!empty($record) ? $record->c_department_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_department_text : '');?>"/></td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table" width="100%">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th><label for="core_location_form_address_floor">Floor</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'address_floor',
		'id' 	=> 'core_location_form_address_floor',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->address_floor : '')
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var core_location_on_sucess;

jQuery(function(){
	jQuery("#core_location_form").validate({
		submitHandler: function(form){
			jQuery("#core_location_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					core_location_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jquery_autocomplete_build("#core_location_form_c_region_id", "<?php echo site_url('core/location/get_region_autocomplete_list_json');?>", {
		width : 200
	},
	{
		autoFocus : true
	});
	
	jquery_autocomplete_build("#core_location_form_c_department_id", "<?php echo site_url('core/location/get_department_autocomplete_list_json');?>", {
		width : 200
	},
	{
		autoFocus : false
	});
});

function core_location_form_submit(on_success){
	core_location_on_sucess = on_success;
	jQuery('#core_location_form').submit();
}
</script>