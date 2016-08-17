<?php
echo form_open_multipart($form_action,
	  array(
		'name'	=> 'material_inventory_inbound_upload_form',
		'id'	=> 'material_inventory_inbound_upload_form'
	  )
	, array(
		'id'	=> $id
	  )
);?>
<table class="form-table">
	<tbody>
		<tr>
			<th><label for="material_inventory_inbound_upload_form_file">File</label></th>
			<td>
<?php 
echo form_upload(
	array(
		'name' 	=> 'file',
		'id' 	=> 'material_inventory_inbound_upload_form_file',
		'class'	=> 'required'
	)
);?>
			</td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td><a href="<?php echo base_url('docs/template/inventory/inbound/inbound.xls');?>">Download Template</a></td>
		</tr>
	</tbody>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var material_inventory_inbound_upload_on_sucess;

jQuery(function(){
	jquery_ready_load();
	
	jQuery("#material_inventory_inbound_upload_form").validate({
		submitHandler: function(form){
			jQuery("#material_inventory_inbound_upload_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					material_inventory_inbound_upload_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
});

function material_inventory_inbound_upload_form_submit(on_success){
	material_inventory_inbound_upload_on_sucess = on_success;
	jQuery('#material_inventory_inbound_upload_form').submit();
}
</script>