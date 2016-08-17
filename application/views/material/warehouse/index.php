<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix content-commandbar">
<?php 
if (is_authorized('material/warehouse', 'insert')){?>
	<button id="material_warehouse_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('material/warehouse', 'update')){?>
	<button id="material_warehouse_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('material/warehouse', 'delete')){?>
	<button id="material_warehouse_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}
if (is_authorized('material/warehouse', 'insert') && is_authorized('material/warehouse', 'update')){?>
	<button id="material_warehouse_default_warehouse_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">Generate Default Warehouse</span>
	</button>
<?php
}?>
</div>

<table id="material_warehouse_list_table"></table>
<div id="material_warehouse_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	material_warehouse_list_load_table('material_warehouse_list_table');
	
	jQuery("#material_warehouse_new_btn").click(function(){
		jquery_dialog_form_open('material_warehouse_form_container', "<?php echo site_url('material/warehouse/form');?>", {
			form_action : "material/warehouse/insert"
		}, 
		function(form_dialog){
			material_warehouse_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#material_warehouse_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Warehouse", 
			width : 400
		});
	});
	
	jQuery("#material_warehouse_edit_btn").click(function(){
		var id = jQuery("#material_warehouse_list_table").getGridParam("selrow");
		if (id)
		{
			jquery_dialog_form_open('material_warehouse_form_container', "<?php echo site_url('material/warehouse/form');?>", {
				form_action : "material/warehouse/update",
				id : id
			}, 
			function(form_dialog){
				material_warehouse_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#material_warehouse_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Warehouse", 
				width : 400
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_warehouse_delete_btn").click(function(){
		var id = jQuery("#material_warehouse_list_table").getGridParam("selrow");
		if (id)
		{
			var _delete_url = "<?php echo site_url('material/warehouse/delete/@id');?>";
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
							jQuery("#material_warehouse_list_table").trigger('reloadGrid', [{current:true}]);
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
	
	jQuery("#material_warehouse_default_warehouse_btn").click(function(){
		jquery_show_confirm("Are your sure ?", function(){
			jQuery.ajax({
				url: "<?php echo site_url('material/warehouse/generate_default_warehouse');?>",
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
						jQuery("#material_warehouse_list_table").trigger('reloadGrid', [{current:true}]);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		});
	});
});

function material_warehouse_list_load_table(table_id){
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
		url: "<?php echo site_url('material/warehouse/get_list_json');?>", 
		editurl: "<?php echo site_url('material/warehouse/jqgrid_cud');?>",
		colNames: [
			'',
			'Id', 
			'Code', 
			'Name'
		], 
		colModel: [
			{name:'act',index:'act', width:25, sortable:false, search:false},
			{name:'id', index:'wh.id', key:true, hidden:true, frozen:true},
			{name:'code', index:'wh.code', width:150, frozen:true, editable:true, editrules:{required:true}, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'name', index:'wh.name', width:400, editable:true, editrules:{required:true}, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		gridComplete: function(){
			var ids = jQuery("#" + table_id).jqGrid('getDataIDs');
			for (var row = 0; row < ids.length; row++)
			{
				var id = ids[row];
				
				var el_id = 'material_warehouse_grid_' + id.toString() + '_btn';
				jQuery("#" + table_id).jqGrid('setRowData', id, {
					act : '<button id="' + el_id + '" data-value="' + id.toString() + '">Grid</button>'
				});
				
				jQuery('#' + el_id).button({
					icons: {
						primary: "ui-icon-grip-dotted-horizontal"
					},
					text: false
				})
				.click(function(e){
					e.preventDefault();
					var data_value = jQuery(this).attr('data-value');
					material_warehouse_list_grid(data_value);
				});
			} 
		},
		pager: '#' + table_id + '_nav', 
		sortname: 'wh.name', 
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

function material_warehouse_list_grid(m_warehouse_id){
	jquery_dialog_form_open('material_warehouse_grid_list_container', "<?php echo site_url('material/grid/index/1');?>", {
		m_warehouse_id : m_warehouse_id
	},
	null,
	{
		title : "Grid", 
		width : 900
	});
}
</script>