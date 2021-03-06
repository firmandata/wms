<?php
echo form_open_multipart($form_action,
	array(
		'name'	=> 'core_orderin_upload_form',
		'id'	=> 'core_orderin_upload_form'
	)
);?>
<table class="form-table">
	<tbody>
		<tr>
			<th><label for="core_orderin_upload_form_file">File</label></th>
			<td>
<?php 
echo form_upload(
	array(
		'name' 	=> 'file',
		'id' 	=> 'core_orderin_upload_form_file',
		'class'	=> 'required'
	)
);?>
			</td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td><a href="<?php echo base_url('docs/template/order/in/order_in.xls');?>">Download Template</a></td>
		</tr>
	</tbody>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var core_orderin_upload_on_sucess;

jQuery(function(){
	jquery_ready_load();
	
	jQuery("#core_orderin_upload_form").validate({
		submitHandler: function(form){
			jQuery("#core_orderin_upload_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					core_orderin_upload_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
});

function core_orderin_upload_form_submit(on_success){
	core_orderin_upload_on_sucess = on_success;
	jQuery('#core_orderin_upload_form').submit();
}
</script>