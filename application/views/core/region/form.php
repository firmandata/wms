<?php
echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'core_region_form',
		'id'	=> 'core_region_form'
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
						<th width="80"><label for="core_region_form_code">Code</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'core_region_form_code',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->code : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_region_form_name">Name</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'name',
		'id' 	=> 'core_region_form_name',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->name : '')
	)
);?>
						</td>
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
						<th width="80"><label for="core_region_form_address">Address</label></th>
						<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'address',
		'id' 	=> 'core_region_form_address',
		'rows'	=> 3, 'cols' => 30,
		'value'	=> (!empty($record) ? $record->address : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_region_form_address_city">City</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'address_city',
		'id' 	=> 'core_region_form_address_city',
		'value'	=> (!empty($record) ? $record->address_city : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_region_form_phone_no">Phone No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'phone_no',
		'id' 	=> 'core_region_form_phone_no',
		'value'	=> (!empty($record) ? $record->phone_no : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_region_form_fax_no">Fax No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'fax_no',
		'id' 	=> 'core_region_form_fax_no',
		'value'	=> (!empty($record) ? $record->fax_no : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_region_form_notes">Notes</label></th>
						<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'core_region_form_notes',
		'rows'	=> 3, 'cols' => 30,
		'value'	=> (!empty($record) ? $record->notes : '')
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
var core_region_on_sucess;

jQuery(function(){
	jQuery("#core_region_form").validate({
		submitHandler: function(form){
			jQuery("#core_region_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					core_region_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
});

function core_region_form_submit(on_success){
	core_region_on_sucess = on_success;
	jQuery('#core_region_form').submit();
}
</script>