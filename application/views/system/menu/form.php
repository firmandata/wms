<?php 
echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'system_menu_form',
		'id'	=> 'system_menu_form'
	)
);?>
	<table>
		<tr><td colspan="2" valign="top">
				<table class="form-table">
					<thead>
						<tr>
							<td colspan="2" class="form-table-title">Information</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th width="100"><label for="system_menu_form_name">Name</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'name',
		'id' 	=> 'system_menu_form_name',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->name : '')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="system_menu_form_sequence">Sequence</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name'	=> 'sequence',
		'id'	=> 'system_menu_form_sequence',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->sequence : '')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="system_menu_form_url">URL</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'url',
		'id' 	=> 'system_menu_form_url',
		'value'	=> (!empty($record) ? $record->url : '')
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="system_menu_form_css">CSS</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'css',
		'id' 	=> 'system_menu_form_css',
		'value'	=> (!empty($record) ? $record->css : '')
	)
);?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr><td rowspan="2" valign="top">
				<div style="width:300px;">
					<table id="system_menu_position_list_table"></table>
					<div id="system_menu_position_list_table_nav"></div>
				</div>
			</td>
			<td valign="top">
				<div style="width:310px;">
					<table id="system_menu_control_list_table"></table>
					<div id="system_menu_control_list_table_nav"></div>
				</div>
			</td>
		</tr>
		<tr><td valign="top">
				<div style="width:310px;">
					<table id="system_menu_action_list_table"></table>
					<div id="system_menu_action_list_table_nav"></div>
				</div>
			</td>
		</tr>
	</table>
<?php echo form_close();?>

<script type="text/javascript">
var system_menu_on_sucess;

jQuery(function(){
	jQuery("#system_menu_form").validate({
		submitHandler: function(form){
			var _data = new Object;
			
			var parent_id = jQuery("input.system_menu_parent_id:checked").val();
			if (parent_id > 0)
				_data.parent_id = parent_id;
			else
				_data.parent_id = null;
			var sys_control_id = jQuery("#system_menu_control_list_table").getGridParam("selrow");
			if (sys_control_id)
				_data.sys_control_id = sys_control_id;
			else
				_data.sys_control_id = null;
			var sys_action_id = jQuery("#system_menu_action_list_table").getGridParam("selrow");
			if (sys_action_id)
				_data.sys_action_id = sys_action_id;
			else
				_data.sys_action_id = null;
			
			jQuery("#system_menu_form").ajaxSubmit({
				dataType: "json",
				data: _data,
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					system_menu_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	/* -- Load Parent List -- */
	system_menu_form_parent_list_load_table('system_menu_position_list_table', <?php echo !empty($record->parent_id) ? $record->parent_id : '0';?>);
	
	/* -- Load Control List -- */
	system_menu_form_control_list_load_table('system_menu_control_list_table', <?php echo !empty($record->sys_control_id) ? $record->sys_control_id : '0';?>);
	
	/* -- Load Action List -- */
	system_menu_form_action_list_load_table('system_menu_action_list_table', <?php echo !empty($record->sys_action_id) ? $record->sys_action_id : '0';?>);
});

function system_menu_form_submit(on_success){
	system_menu_on_sucess = on_success;
	jQuery('#system_menu_form').submit();
}

function system_menu_form_parent_list_load_table(table_id, select_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		shrinkToFit: false,
		multiselect: true,
		multiboxonly: true,
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		treeGrid: true,
		treeGridModel: 'adjacency',
		treeReader : {
		   level_field: "level",
		   parent_id_field: "parent_id",
		   leaf_field: "is_leaf",
		   expanded_field: "is_expanded"
		},
		ExpandColumn : 'name',
		url: "<?php echo site_url('system/menu/get_list_json');?>",
		caption: "Select the Parent",
		hidegrid: false,
		height: 337,
		gridComplete: function(){
			if (select_id)
				jQuery('#' + table_id).setSelection(select_id);
		},
		colNames: [
			'Id', 
			'',
			'Parent', 
		], 
		colModel: [
			{name:'id', index:'id', key:true, hidden:true},
			{name:'selector', index:'selector', align:'center', width:20, editable:false, sortable:false, 
			 formatter:function(cellvalue, options, rowObject){
				return '<input type="checkbox" class="system_menu_parent_id" name="parent_id[]" id="system_menu_parent_id_' + rowObject.id.toString() + '" value="' + rowObject.id.toString() + '" onchange="system_menu_form_parent_select(' + rowObject.id.toString() + ')"/>';
			 }
			},
			{name:'name', index:'name', width:250, editable:true, editrules:{required:true}}
		],
		onSelectRow: function(rowid, status, e){
			jQuery('#system_menu_parent_id_' + rowid.toString()).attr('checked', true);
			system_menu_form_parent_select(rowid);
		},
		pager: '#' + table_id + '_nav', 
		sortname: 'sequence', 
		sortorder: "asc"
	});
	
	jQuery("#" + table_id).jqGrid('navGrid', '#' + table_id + '_nav', 
	{
		/* -- Button Configuration -- */
		edit: false,
		add: false,
		del: false,
		search: false
	},
	{},
	{},
	{}, 
	{}
	);
}

function system_menu_form_parent_select(id){
	jQuery("input.system_menu_parent_id[value!='" + id.toString() + "']").attr('checked', false);
	var is_checked = jQuery("input.system_menu_parent_id[value='" + id.toString() + "']").is(':checked');
	if (is_checked)
		jQuery('#system_menu_position_list_table').setSelection(id, false);
	else
		jQuery('#system_menu_position_list_table').resetSelection();
}

function system_menu_form_control_list_load_table(table_id, select_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		multiselect: true,
		multiboxonly: true,
		pginput: false,
		rowNum: 1000, 
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('system/menu/get_control_list_json');?>", 
		editurl: "<?php echo site_url('system/control/jqgrid_cud');?>",
		caption: "Select Control Target",
		hidegrid: false,
		height: 130,
		gridComplete: function(){
			if (select_id)
				jQuery('#' + table_id).setSelection(select_id);
		},
		colNames: [
			'Id', 
			'Control'
		], 
		colModel: [
			{name:'id', index:'id', key:true, hidden:true, frozen:true},  
			{name:'name', index:'name', width:250, editable:true, editrules:{required:true}},
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'name', 
		sortorder: "asc"
	});
	
	jQuery("#" + table_id).jqGrid('navGrid', '#' + table_id + '_nav', 
	{
		/* -- Button Configuration -- */
<?php 
if (is_authorized('system/control', 'update'))
{?>
		edit: true,
<?php 
}
else
{?>
		edit: false,
<?php
}
if (is_authorized('system/control', 'insert'))
{?>
		add: true,
<?php 
}
else
{?>
		add: false,
<?php
}
if (is_authorized('system/control', 'delete'))
{?>
		del: true
<?php 
}
else
{?>
		del: false
<?php 
}?>
	},
	{
		/* -- Edit Configuration -- */
		afterSubmit: jqgrid_form_after_submit, 
		beforeSubmit: jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true,
		bottominfo: "*) Required"
	},
	{
		/* -- Add Configuration -- */
		afterSubmit : jqgrid_form_after_submit, 
		beforeSubmit : jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true,
		bottominfo: "*) Required"
	},
	{
		/* -- Delete Configuration -- */
		afterSubmit : jqgrid_form_after_submit, 
		beforeSubmit: jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true
	}
	);
	
	jQuery('#cb_' + table_id).hide();
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
}

function system_menu_form_action_list_load_table(table_id, select_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		multiselect: true,
		multiboxonly: true,
		pginput: false,
		rowNum: 1000, 
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('system/menu/get_action_list_json');?>", 
		editurl: "<?php echo site_url('system/action/jqgrid_cud');?>",
		caption: "Select Action Target",
		hidegrid: false,
		height: 130,
		gridComplete: function(){
			if (select_id)
				jQuery('#' + table_id).setSelection(select_id);
		},
		colNames: [
			'Id', 
			'Action'
		], 
		colModel: [
			{name:'id', index:'id', key:true, hidden:true, frozen:true},  
			{name:'name', index:'name', width:250, editable:true, editrules:{required:true}},
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'name', 
		sortorder: "asc"
	});
	
	jQuery("#" + table_id).jqGrid('navGrid', '#' + table_id + '_nav', 
	{
		/* -- Button Configuration -- */
<?php 
if (is_authorized('system/action', 'update'))
{?>
		edit: true,
<?php 
}
else
{?>
		edit: false,
<?php
}
if (is_authorized('system/action', 'insert'))
{?>
		add: true,
<?php 
}
else
{?>
		add: false,
<?php
}
if (is_authorized('system/action', 'delete'))
{?>
		del: true
<?php 
}
else
{?>
		del: false
<?php 
}?>
	},
	{
		/* -- Edit Configuration -- */
		afterSubmit: jqgrid_form_after_submit, 
		beforeSubmit: jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true,
		bottominfo: "*) Required"
	},
	{
		/* -- Add Configuration -- */
		afterSubmit : jqgrid_form_after_submit, 
		beforeSubmit : jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true,
		bottominfo: "*) Required"
	},
	{
		/* -- Delete Configuration -- */
		afterSubmit : jqgrid_form_after_submit, 
		beforeSubmit: jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true
	}
	);
	
	jQuery('#cb_' + table_id).hide();
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
}
</script>