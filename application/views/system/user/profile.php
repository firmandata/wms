<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
	<button id="system_user_profile_save_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-disk"></span>
		<span class="ui-button-text">Save</span>
	</button>
	<button id="system_user_profile_cancel_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-cancel"></span>
		<span class="ui-button-text">Reset</span>
	</button>
</div>

<div id="system_user_profile_form_container" class="ui-widget ui-widget-content ui-corner-all">
<?php 
echo form_open('system/user/profile_update',
	array(
		'name'	=> 'system_user_profile_form',
		'id'	=> 'system_user_profile_form'
	)
);?>
	<table class="form-table">
		<thead>
			<tr>
				<td colspan="2" class="form-table-title">User Application</td>
			</tr>
		</thead>
		<tbody>
		    <tr>
				<th width="200"><label for="system_user_profile_form_username">Username</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'username',
		'id' 		=> 'system_user_profile_form_username',
		'disabled'	=> 'disabled',
		'value'		=> set_value('username', $user->username)
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="system_user_profile_form_password">Password</label></th>
				<td>
<?php 
echo form_password(
	array(
		'name'	=> 'password',
		'id'	=> 'system_user_profile_form_password'
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="system_user_profile_form_password_confirm">Password Confirm</label></th>
				<td>
<?php 
echo form_password(
	array(
		'name'		=> 'password_confirm',
		'id'		=> 'system_user_profile_form_password_confirm',
		'equalto'	=> '#system_user_profile_form_password'
	)
);?>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="form-table">
		<thead>
			<tr>
				<td colspan="2" class="form-table-title">Information</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th width="200"><label for="system_user_profile_form_name">Name</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'name',
		'id' 	=> 'system_user_profile_form_name',
		'class'	=> 'required',
		'value'	=> set_value('name', $user->name)
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="system_user_profile_form_email">Email</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'email',
		'id' 	=> 'system_user_profile_form_email',
		'class'	=> 'required email',
		'value'	=> set_value('email', $user->email)
	)
);?>
				</td>
			</tr>
		</tbody>
	</table>
<?php echo form_close();?>
</div>

<script type="text/javascript">
jQuery(function(){
	jQuery("#system_user_profile_save_btn").click(function(){
		jQuery('#system_user_profile_form').submit();
	});
	
	jQuery("#system_user_profile_cancel_btn").click(function(){
		jQuery("#system_user_profile_form").resetForm();
	});
	
	jQuery("#system_user_profile_form").validate({
		submitHandler: function(form){
			jQuery("#system_user_profile_form").ajaxSubmit({
				dataType: "json",
				error: jquery_ajax_error_handler,
				beforeSubmit: function(arr, $form, options){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
						jquery_show_message("Profile saved !", null, "ui-icon-info");
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
});
</script>