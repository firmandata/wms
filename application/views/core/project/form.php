<?php
$project_categories = array_merge(array('' => ''), $this->config->item('project_categories'));

echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'core_project_form',
		'id'	=> 'core_project_form'
	)
);?>
<table width="100%">
	<tr><td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="120"><label for="core_project_form_code">Code</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'core_project_form_code',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->code : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_project_form_name">Name</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'name',
		'id' 	=> 'core_project_form_name',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->name : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_project_form_c_businesspartner_id_caption">Business Partner</label></th>
						<td><input type="hidden" name="c_businesspartner_id" id="core_project_form_c_businesspartner_id" value="<?php echo (!empty($record) ? $record->c_businesspartner_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_businesspartner_text : '');?>"/></td>
					</tr>
					<tr>
						<th><label for="core_project_form_category">Category</label></th>
						<td>
<?php 
echo form_dropdown('category', $project_categories, (!empty($record) ? $record->category : ''), 'id="core_project_form_category"');?>
						</td>
					</tr>
					<tr>
						<th><label for="core_project_form_pic">PIC</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'pic',
		'id' 	=> 'core_project_form_pic',
		'value'	=> (!empty($record) ? $record->pic : '')
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<div style="width:310px;">
				<table id="core_project_usergroup_list_table"></table>
				<div id="core_project_usergroup_list_table_nav"></div>
			</div>
		</td>
	</tr>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var core_project_on_sucess;

jQuery(function(){
	jQuery("#core_project_form").validate({
		submitHandler: function(form){
			var _data = new Object;
			_data.sys_usergroup_ids = jQuery("#core_project_usergroup_list_table").jqGrid('getGridParam', 'selarrrow');
			
			jQuery("#core_project_form").ajaxSubmit({
				dataType: "json",
				data: _data,
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					core_project_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jquery_autocomplete_build("#core_project_form_c_businesspartner_id", "<?php echo site_url('core/project/get_businesspartner_autocomplete_list_json');?>", {
		width : 200
	},
	{
		autoFocus : false
	});
	
	/* -- Load User Group List -- */
	core_project_form_usergroup_list_load_table('core_project_usergroup_list_table', <?php echo !empty($record) ? json_encode($record->cus_c_project_sys_usergroups) : '[]';?>);
});

function core_project_form_submit(on_success){
	core_project_on_sucess = on_success;
	jQuery('#core_project_form').submit();
}

function core_project_form_usergroup_list_load_table(table_id, cus_c_project_sys_usergroups){
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
		url: "<?php echo site_url('core/project/get_usergroup_list_json');?>", 
		editurl: "<?php echo site_url('system/accesscontrol/jqgrid_cud');?>",
		caption: "Select Access Control",
		hidegrid: false,
		height: 150,
		gridComplete: function(){
			if (cus_c_project_sys_usergroups)
			{
				jQuery.each(cus_c_project_sys_usergroups, function(idx, sys_usergroup_user){
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