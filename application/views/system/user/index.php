<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix content-commandbar">
<?php 
if (is_authorized('system/user', 'insert')){?>
	<button id="system_user_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('system/user', 'update')){?>
	<button id="system_user_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('system/user', 'delete')){?>
	<button id="system_user_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}?>
</div>

<table id="system_user_list_table"></table>
<div id="system_user_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	system_user_list_load_table('system_user_list_table');
	
	jQuery("#system_user_new_btn").click(function(){
		jquery_dialog_form_open('system_user_form_container', "<?php echo site_url('system/user/form');?>", {
			form_action : "system/user/insert"
		}, 
		function(form_dialog){
			system_user_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#system_user_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create User", 
			width : 620
		});
	});
	
	jQuery("#system_user_edit_btn").click(function(){
		var id = jQuery("#system_user_list_table").getGridParam("selrow");
		if (id)
		{
			jquery_dialog_form_open('system_user_form_container', "<?php echo site_url('system/user/form');?>", {
				form_action : "system/user/update",
				id : id
			}, 
			function(form_dialog){
				system_user_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#system_user_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit User", 
				width : 620
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#system_user_delete_btn").click(function(){
		var id = jQuery("#system_user_list_table").getGridParam("selrow");
		if (id)
		{
			var _delete_url = "<?php echo site_url('system/user/delete/@id');?>";
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
							jQuery("#system_user_list_table").trigger('reloadGrid', [{current:true}]);
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
});

function system_user_list_load_table(table_id){
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
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('system/user/get_list_json');?>", 
		colNames: [
			'Id', 
			'Username', 
			'Name', 
			'Email', 
			'Active'
		], 
		colModel: [
			{name:'id', index:'usr.id', key:true, hidden:true, frozen:true},  
			{name:'username', index:'usr.username', width:200, frozen:true, searchoptions:{sopt:jqgird_search_string_operators,clearSearch:false}}, 
			{name:'name', index:'usr.name', width:250, frozen:true, searchoptions:{sopt:jqgird_search_string_operators,clearSearch:false}},
			{name:'email', index:'usr.email', width:320, searchoptions:{sopt:jqgird_search_string_operators,clearSearch:false}}, 
			{name:'is_active', index:'usr.is_active', width:100, formatter:jqgrid_boolean_formatter,
			 stype: 'select',
			 searchoptions:{
				sopt:['eq','ne','bw','bn','lt','le','gt','ge','in','ni','ew','en','cn','nc'],
				clearSearch:false,
				dataUrl: "<?php echo site_url('system/user/get_user_active_dropdown/search_user_active');?>"
			 }
			}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'usr.username', 
		sortorder: "asc"
	});
	
	jQuery("#" + table_id).jqGrid('filterToolbar', {
		stringResult : true, 
		searchOperators : true
	});
	
	jQuery("#" + table_id).jqGrid('navGrid', '#' + table_id + '_nav', {
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
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -164));
}
</script>