<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix content-commandbar">
<?php 
if (is_authorized('system/accesscontrol', 'insert')){?>
	<button id="system_accesscontrol_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('system/accesscontrol', 'update')){?>
	<button id="system_accesscontrol_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('system/accesscontrol', 'delete')){?>
	<button id="system_accesscontrol_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}?>
	<button id="system_accesscontrol_control_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Control Parameters</span>
	</button>
	<button id="system_accesscontrol_action_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-flag"></span>
		<span class="ui-button-text">Action Parameters</span>
	</button>
</div>

<table id="system_accesscontrol_list_table"></table>
<div id="system_accesscontrol_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	system_accesscontrol_list_load_table('system_accesscontrol_list_table');
	
	jQuery("#system_accesscontrol_new_btn").click(function(){
		jquery_dialog_form_open('system_accesscontrol_form_container', "<?php echo site_url('system/accesscontrol/form');?>", {
			form_action : 'system/accesscontrol/insert'
		},
		function(form_dialog){
			system_accesscontrol_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#system_accesscontrol_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Access Control", 
			width : 757
		});
	});
	
	jQuery("#system_accesscontrol_edit_btn").click(function(){
		var id = jQuery("#system_accesscontrol_list_table").getGridParam("selrow");
		if (id)
		{
			jquery_dialog_form_open('system_accesscontrol_form_container', "<?php echo site_url('system/accesscontrol/form');?>", {
				form_action : 'system/accesscontrol/update', 
				id : id
			},
			function(form_dialog){
				system_accesscontrol_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#system_accesscontrol_list_table").trigger("reloadGrid", [{current:true}]);
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
	});
	
	jQuery("#system_accesscontrol_delete_btn").click(function(){
		var id = jQuery("#system_accesscontrol_list_table").getGridParam("selrow");
		if (id)
		{
			var _delete_url = "<?php echo site_url('system/accesscontrol/delete/@id');?>";
			_delete_url = _delete_url.replace(/@id/g, id);
			
			jquery_show_confirm("Are your sure ?", function(){
				jQuery.ajax({
					url: _delete_url,
					type: "GET",
					dataType: "json",
					async : false,
					error: jquery_ajax_error_handler,
					beforeSend: function(jqXHR, settings){
						jquery_blockui();
					},
					success: function(data, textStatus, jqXHR){
						if (data.response == false)
							jquery_show_message(data.value, null, "ui-icon-close");
						else
							jQuery("#system_accesscontrol_list_table").trigger('reloadGrid', [{current:true}]);
					},
					complete: function(jqXHR, textStatus){
						jquery_unblockui();
					}
				});
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#system_accesscontrol_control_btn").click(function(){
		jquery_dialog_form_open('system_accesscontrol_control_form_container', "<?php echo site_url('system/accesscontrol/form_control');?>", null, null, {
			title : "Control Parameters",
			width : 350
		});
	});
	
	jQuery("#system_accesscontrol_action_btn").click(function(){
		jquery_dialog_form_open('system_accesscontrol_action_form_container', "<?php echo site_url('system/accesscontrol/form_action');?>", null, null, {
			title : "Action Parameters",
			width : 350
		});
	});
});

function system_accesscontrol_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		rowNum: <?php echo $this->config->item('jqgrid_limit_per_page');?>, 
		rowList: [<?php echo $this->config->item('jqgrid_limit_pages');?>], 
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false,
			id: "[User Group Id]"
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('system/accesscontrol/get_list_json');?>", 
		colNames: [
			'User Group Id', 
			'Name'
		], 
		colModel: [
			{name:'id', index:'usr_grp.id', key:true, hidden:true, frozen:true},  
			{name:'name', index:'usr_grp.name', width:250}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'usr_grp.name', 
		sortorder: "asc"
	});
	
	jQuery("#" + table_id).jqGrid('navGrid', '#' + table_id + '_nav', 
	{
		/* -- Button Configuration -- */
		edit: false,
		add: false,
		del: false
	},
	{},
	{},
	{}, 
	{
		/* -- Searching Configuration -- */
		multipleSearch: true, 
		multipleGroup: true, 
		showQuery: true
	});
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id)));
}
</script>