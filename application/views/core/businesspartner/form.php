<?php
$business_partner_types = array_merge(array('' => ''), $this->config->item('business_partner_types'));
$business_partner_models = array_merge(array('' => ''), $this->config->item('business_partner_models'));

echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'core_businesspartner_form',
		'id'	=> 'core_businesspartner_form'
	)
);?>
	<table width="100%">
		<tr>
			<td valign="top">
				<table class="form-table">
					<thead>
						<tr>
							<td colspan="2" class="form-table-title">Information</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th width="120"><label for="core_businesspartner_form_type">Type</label></th>
							<td>
<?php 
echo form_dropdown('type', $business_partner_types, (!empty($record) ? $record->type : ''), 'id="core_businesspartner_form_type"');?>
							</td>
						</tr>
						<tr>
							<th><label for="core_businesspartner_form_model">Model</label></th>
							<td>
<?php 
echo form_dropdown('model', $business_partner_models, (!empty($record) ? $record->model : ''), 'id="core_businesspartner_form_model"');?>
							</td>
						</tr>
						<tr>
							<th><label for="core_businesspartner_form_code">Code</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'core_businesspartner_form_code',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->code : '')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="core_businesspartner_form_name">Name</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'name',
		'id' 	=> 'core_businesspartner_form_name',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->name : '')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="core_businesspartner_form_initial_name">Initial Name</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'initial_name',
		'id' 	=> 'core_businesspartner_form_initial_initial_name',
		'class'	=> '',
		'value'	=> (!empty($record) ? $record->initial_name : '')
	)
);?>
							</td>
						</tr>
						<tr id="core_businesspartner_form_c_region">
							<th><label for="core_businesspartner_form_c_region_id_caption">Region</label></th>
							<td><input type="hidden" name="c_region_id" id="core_businesspartner_form_c_region_id" value="<?php echo (!empty($record) ? $record->c_region_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_region_text : '');?>"/></td>
						</tr>
						<tr id="core_businesspartner_form_c_department">
							<th><label for="core_businesspartner_form_c_department_id_caption">Department</label></th>
							<td><input type="hidden" name="c_department_id" id="core_businesspartner_form_c_department_id" value="<?php echo (!empty($record) ? $record->c_department_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_department_text : '');?>"/></td>
						</tr>
						<tr id="core_businesspartner_form_personal_position_container">
							<th><label for="core_businesspartner_form_personal_position">Personal Position</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'personal_position',
		'id' 	=> 'core_businesspartner_form_personal_position',
		'value'	=> (!empty($record) ? $record->personal_position : '')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="core_businesspartner_form_credit_limit">Credit Limit</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'credit_limit',
		'id' 	=> 'core_businesspartner_form_credit_limit',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->credit_limit : '0')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="core_businesspartner_form_notes">Notes</label></th>
							<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'core_businesspartner_form_notes',
		'rows'	=> 3, 'cols' => 30,
		'value'	=> (!empty($record) ? $record->notes : '')
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
							<td colspan="2" class="form-table-title">Contact</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th><label for="core_businesspartner_form_address">Address</label></th>
							<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'address',
		'id' 	=> 'core_businesspartner_form_address',
		'rows'	=> 3, 'cols' => 30,
		'value'	=> (!empty($record) ? $record->address : '')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="core_businesspartner_form_phone_no">Phone No</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'phone_no',
		'id' 	=> 'core_businesspartner_form_phone_no',
		'value'	=> (!empty($record) ? $record->phone_no : '')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="core_businesspartner_form_fax_no">Fax No</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'fax_no',
		'id' 	=> 'core_businesspartner_form_fax_no',
		'value'	=> (!empty($record) ? $record->fax_no : '')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="core_businesspartner_form_pic">PIC</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'pic',
		'id' 	=> 'core_businesspartner_form_pic',
		'value'	=> (!empty($record) ? $record->pic : '')
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
var core_businesspartner_on_sucess;

jQuery(function(){
	jQuery("#core_businesspartner_form").validate({
		submitHandler: function(form){
			jQuery("#core_businesspartner_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					core_businesspartner_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jquery_autocomplete_build("#core_businesspartner_form_c_region_id", "<?php echo site_url('core/businesspartner/get_region_autocomplete_list_json');?>", {
		width : 200
	},
	{
		autoFocus : false
	});
	
	jquery_autocomplete_build("#core_businesspartner_form_c_department_id", "<?php echo site_url('core/businesspartner/get_department_autocomplete_list_json');?>", {
		width : 200
	},
	{
		autoFocus : false
	});
	
	jQuery('#core_businesspartner_form_type').change(function(){
		core_businesspartner_form_init_form();
	});
	core_businesspartner_form_init_form();
});

function core_businesspartner_form_submit(on_success){
	core_businesspartner_on_sucess = on_success;
	jQuery('#core_businesspartner_form').submit();
}

function core_businesspartner_form_init_form(){
	var type = jQuery('#core_businesspartner_form_type').val();
	if (type == 'SUPPLIER' || type == 'CUSTOMER')
	{
		jQuery('#core_businesspartner_form_c_region').hide();
		jQuery('#core_businesspartner_form_c_department').hide();
	}
	else
	{
		jQuery('#core_businesspartner_form_c_region').show();
		jQuery('#core_businesspartner_form_c_department').show();
	}
	
	if (type == 'EMPLOYEE')
	{
		jQuery('#core_businesspartner_form_model').val('PERSONAL');
		jQuery("#core_businesspartner_form_model option[value!='PERSONAL']").hide();
		jQuery('#core_businesspartner_form_personal_position_container').show();
	}
	else
	{
		jQuery("#core_businesspartner_form_model option[value!='PERSONAL']").show();
		jQuery('#core_businesspartner_form_personal_position_container').hide();
	}
}
</script>