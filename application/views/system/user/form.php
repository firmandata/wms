<?php 
echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'system_user_form',
		'id'	=> 'system_user_form'
	)
);?>
	<table width="100%">
		<tr>
			<td valign="top">
				<table class="form-table">
					<thead>
						<tr>
							<td colspan="2" class="form-table-title">User Application</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th width="120"><label for="system_user_form_username">Username</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'username',
		'id' 	=> 'system_user_form_username',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->username : '')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="system_user_form_password">Password</label></th>
							<td>
<?php 
echo form_password(
	array(
		'name'	=> 'password',
		'id'	=> 'system_user_form_password',
		'class'	=> (!empty($record) ? '' : 'required')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="system_user_form_password_confirm">Password Confirm</label></th>
							<td>
<?php 
echo form_password(
	array(
		'name'		=> 'password_confirm',
		'id'		=> 'system_user_form_password_confirm',
		'equalto'	=> '#system_user_form_password',
		'class'		=> (!empty($record) ? '' : 'required')
	)
);?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td valign="top" rowspan="2">
				<div style="width:310px;">
					<table id="system_user_usergroup_list_table"></table>
					<div id="system_user_usergroup_list_table_nav"></div>
				</div>
			</td>
		</tr>
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
							<th width="120"><label for="system_user_form_name">Name</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'name',
		'id' 	=> 'system_user_form_name',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->name : '')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="system_user_form_email">Email</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'email',
		'id' 	=> 'system_user_form_email',
		'class'	=> 'required email',
		'value'	=> (!empty($record) ? $record->email : '')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="system_user_form_is_active">Active</label></th>
							<td>
<?php 
echo form_checkbox(
	array(
		'name'		=> 'is_active',
		'id'		=> 'system_user_form_is_active',
		'value'		=> '1',
		'checked'	=> (!empty($record) ? $record->is_active : 0)
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
var system_user_on_sucess;

jQuery(function(){
	jQuery("#system_user_form").validate({
		submitHandler: function(form){
			var _data = new Object;
			_data.sys_usergroup_ids = jQuery("#system_user_usergroup_list_table").jqGrid('getGridParam', 'selarrrow');
			
			jQuery("#system_user_form").ajaxSubmit({
				dataType: "json",
				data: _data,
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					system_user_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	/* -- Load User Group List -- */
	system_user_form_usergroup_list_load_table('system_user_usergroup_list_table', <?php echo !empty($record) ? json_encode($record->sys_usergroup_users) : '[]';?>);
});

function system_user_form_submit(on_success){
	system_user_on_sucess = on_success;
	jQuery('#system_user_form').submit();
}

function system_user_form_usergroup_list_load_table(table_id, sys_usergroup_users){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		pginput: false,
		pgbuttons: false,
		multiselect: true,
		rowNum: 1000, 
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('system/user/get_usergroup_list_json');?>", 
		editurl: "<?php echo site_url('system/accesscontrol/jqgrid_cud');?>",
		caption: "Select Access Control",
		hidegrid: false,
		height: 150,
		gridComplete: function(){
			if (sys_usergroup_users)
			{
				jQuery.each(sys_usergroup_users, function(idx, sys_usergroup_user){
					jQuery('#' + table_id).setSelection(sys_usergroup_user.sys_usergroup_id);
				});
			}
		},
		colNames: [
			'Id', 
			'Access Control'
		], 
		colModel: [
			{name:'id', index:'id', key:true, hidden:true, frozen:true},  
			{name:'name', index:'name', width:230, editable:true, editrules:{required:true}},
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'name', 
		sortorder: "asc"
	});
	
	jQuery("#" + table_id).jqGrid('navGrid', '#' + table_id + '_nav', {
		/* -- Button Configuration -- */
		edit: false,
		add: false,
<?php 
if (is_authorized('system/accesscontrol', 'delete'))
{?>
		del: true,
<?php 
}
else
{?>
		del: false,
<?php 
}?>
	},
	{
		/* -- Edit Configuration -- */
	},
	{
		/* -- Add Configuration -- */
	},
	{
		/* -- Delete Configuration -- */
		afterSubmit : jqgrid_form_after_submit, 
		beforeSubmit: jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true
	}
	);
<?php
if (is_authorized('system/accesscontrol', 'insert') || is_authorized('system/accesscontrol', 'update'))
{?>
	jQuery("#" + table_id).navButtonAdd('#' + table_id + '_nav', {
		caption: "", 
		title: "Edit User Group",
		buttonicon: "ui-icon-pencil", 
		onClickButton: function(){ 
			var id = jQuery("#" + table_id).getGridParam("selrow");
			if (id)
			{
				jquery_dialog_form_open('system_usergroup_form_container', "<?php echo site_url('system/accesscontrol/form');?>", {
					form_action : "system/accesscontrol/update", 
					id : id
				}, 
				function(form_dialog){
					system_accesscontrol_form_submit(function(data, textStatus, jqXHR){
						if (data.response == false)
							jquery_show_message(data.value, null, "ui-icon-close");
						else
						{
							form_dialog.dialog("close");
							jQuery("#" + table_id).trigger("reloadGrid", [{current:true}]);
						}
					});
				},
				{
					title : "Edit Access Control", 
					width : 757
				});
			}
			else
				jquery_show_message("Please select the row data !", null, "ui-icon-alert");
		}, 
		position: "first"
	});
	
	jQuery("#" + table_id).navButtonAdd('#' + table_id + '_nav', {
		caption: "", 
		title: "Add New User Group",
		buttonicon: "ui-icon-plus", 
		onClickButton: function(){ 
			jquery_dialog_form_open('system_usergroup_form_container', "<?php echo site_url('system/accesscontrol/form');?>", {
				form_action : "system/accesscontrol/insert"
			}, 
			function(form_dialog){
				system_accesscontrol_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#" + table_id).trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Create Access Control", 
				width : 757
			});
		}, 
		position: "first"
	});
<?php 
}?>
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
}
</script>