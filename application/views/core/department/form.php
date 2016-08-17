<?php
echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'core_department_form',
		'id'	=> 'core_department_form'
	)
);?>
<table class="form-table" width="100%">
	<tbody>
		<tr>
			<th width="80"><label for="core_department_form_code">Code</label></th>
			<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'core_department_form_code',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->code : '')
	)
);?>
			</td>
		</tr>
		<tr>
			<th><label for="core_department_form_name">Name</label></th>
			<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'name',
		'id' 	=> 'core_department_form_name',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->name : '')
	)
);?>
			</td>
		</tr>
	</tbody>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var core_department_on_sucess;

jQuery(function(){
	jQuery("#core_department_form").validate({
		submitHandler: function(form){
			jQuery("#core_department_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					core_department_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
});

function core_department_form_submit(on_success){
	core_department_on_sucess = on_success;
	jQuery('#core_department_form').submit();
}
</script>