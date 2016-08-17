<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix content-commandbar">
<?php 
if (is_authorized('system/menu', 'insert')){?>
	<button id="system_menu_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('system/menu', 'update')){?>
	<button id="system_menu_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('system/menu', 'delete')){?>
	<button id="system_menu_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}?>
</div>

<table id="system_menu_list_table"></table>
<div id="system_menu_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	system_menu_list_load_table('system_menu_list_table');
	
	jQuery("#system_menu_new_btn").click(function(){
		jquery_dialog_form_open('system_menu_form_container', "<?php echo site_url('system/menu/form');?>", {
			form_action : "system/menu/insert"
		}, 
		function(form_dialog){
			system_menu_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#system_menu_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Menu", 
			width : 640
		});
	});
	
	jQuery("#system_menu_edit_btn").click(function(){
		var id = jQuery("#system_menu_list_table").getGridParam("selrow");
		if (id)
		{
			jquery_dialog_form_open('system_menu_form_container', "<?php echo site_url('system/menu/form');?>", {
				form_action : "system/menu/update",
				id : id
			}, 
			function(form_dialog){
				system_menu_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#system_menu_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Menu", 
				width : 640
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#system_menu_delete_btn").click(function(){
		var id = jQuery("#system_menu_list_table").getGridParam("selrow");
		if (id)
		{
			var _delete_url = "<?php echo site_url('system/menu/delete/@id');?>";
			_delete_url = _delete_url.replace(/@id/g, id);
			
			jquery_show_confirm("Are your sure ?", function(){
				jQuery.ajax({
					url: _delete_url,
					type: "GET",
					dataType: "json",
					async : false,
					error: jquery_ajax_error_handler,
					beforeSend: function(jqXHR, settings)
					{
						jquery_blockui();
					},
					success: function(data, textStatus, jqXHR)
					{
						if (data.response == false)
							jquery_show_message(data.value, null, "ui-icon-close");
						else
							jQuery("#system_menu_list_table").trigger('reloadGrid', [{current:true}]);
					},
					complete: function(jqXHR, textStatus)
					{
						jquery_unblockui();
					}
				});
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
});

function system_menu_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		shrinkToFit: false,
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
		colNames: 
		[
			'Id', 
			'Name', 
			'Control', 
			'Action',
			'URL',
			'CSS'
		], 
		colModel: 
		[
			{name:'id', index:'id', key:true, hidden:true, frozen:true},
			{name:'name', index:'name', width:250, editable:true, editrules:{required:true}, frozen:true},  
			{name:'control_name', index:'control_name', width:200, editable:true}, 
			{name:'action_name', index:'action_name', width:150, editable:true}, 
			{name:'url', index:'url', width:200, editable:true}, 
			{name:'css', index:'css', width:90, editable:true}
		],
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
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id)));
}
</script>