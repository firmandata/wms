<?php
$grid_types = array_merge(array('' => ''), $this->config->item('grid_types'));
$grid_statuses = array_merge(array('' => ''), $this->config->item('grid_statuses'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix content-commandbar">
<?php 
if (is_authorized('material/grid', 'insert')){?>
	<button id="material_grid_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('material/grid', 'update')){?>
	<button id="material_grid_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('material/grid', 'delete')){?>
	<button id="material_grid_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}
if (is_authorized('material/grid', 'update')){?>
	<button id="material_grid_productgroup_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-flag"></span>
		<span class="ui-button-text">Set Product Group</span>
	</button>
<?php 
}?>
</div>

<table id="material_grid_list_table"></table>
<div id="material_grid_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	material_grid_list_load_table('material_grid_list_table');
	
	jQuery("#material_grid_new_btn").click(function(){
		jquery_dialog_form_open('material_grid_form_container', "<?php echo site_url('material/grid/form');?>", {
<?php
if ($m_warehouse_id)
{?>
			m_warehouse_id : <?php echo $m_warehouse_id;?>,
<?php
}?>
			form_action : "material/grid/insert"
		}, 
		function(form_dialog){
			material_grid_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#material_grid_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Grid", 
			width : 400
		});
	});
	
	jQuery("#material_grid_edit_btn").click(function(){
		var id = jQuery("#material_grid_list_table").getGridParam("selrow");
		if (id)
		{
			jquery_dialog_form_open('material_grid_form_container', "<?php echo site_url('material/grid/form');?>", {
<?php
if ($m_warehouse_id)
{?>
				m_warehouse_id : <?php echo $m_warehouse_id;?>,
<?php
}?>
				form_action : "material/grid/update",
				id : id
			}, 
			function(form_dialog){
				material_grid_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#material_grid_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Grid", 
				width : 400
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_grid_delete_btn").click(function(){
		var ids = jQuery("#material_grid_list_table").getGridParam("selarrrow");
		if (ids.length > 0)
		{
			jquery_show_confirm("Are your sure ?", function(){
				jQuery.ajax({
					url: "<?php echo site_url('material/grid/delete_by_ids');?>",
					data : {
						ids : ids
					},
					type: "POST",
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
							jQuery("#material_grid_list_table").trigger('reloadGrid', [{current:true}]);
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
	
	jQuery("#material_grid_productgroup_btn").click(function(){
		var ids = jQuery("#material_grid_list_table").getGridParam("selarrrow");
		if (ids.length > 0)
		{
			jquery_dialog_form_open('material_grid_productgroup_form_container', "<?php echo site_url('material/grid/form_productgroup');?>", 
			{
				ids : ids
			}, 
			function(form_dialog){
				material_grid_productgroup_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#material_grid_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			}, 
			{
				title : "Set Zone", 
				width : 435
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
});

function material_grid_list_load_table(table_id){
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
<?php
if ($simple)
{?>
		height: 400,
<?php
}?>
		url: "<?php echo site_url('material/grid/get_list_json');?>", 
<?php
if ($m_warehouse_id)
{?>
		postData : {
			m_warehouse_id : <?php echo $m_warehouse_id;?>
		},
<?php
}?>
		multiselect : true,
		colNames: [
			'Id', 
			'Warehouse',
			'Product Group',
			'Code', 
			'Row',
			'Col',
			'Level',
			'Type',
			'Length',
			'Width',
			'Height',
			'Status'
		], 
		colModel: [
			{name:'id', index:'grd.id', key:true, hidden:true, frozen:true},
			{name:'m_warehouse_name', index:'wh.name', width:150, frozen:true},
			{name:'m_productgroup_name', index:'prog.name', width:150, frozen:true},
			{name:'code', index:'grd.code', width:120, frozen:true},
			{name:'row', index:'grd.row', width:50, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, formatter:'integer', align: 'right'},
			{name:'col', index:'grd.col', width:50, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, formatter:'integer', align: 'right'},
			{name:'level', index:'grd.level', width:50, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, formatter:'integer', align: 'right'},
			{name:'type', index:'grd.type', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($grid_types);?>}
			},
			{name:'length', index:'grd.length', width:80, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, formatter:'number', formatoptions:{decimalPlaces: 0}, align: 'right'},
			{name:'width', index:'grd.width', width:80, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, formatter:'number', formatoptions:{decimalPlaces: 0}, align: 'right'},
			{name:'height', index:'grd.height', width:80, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, formatter:'number', formatoptions:{decimalPlaces: 0}, align: 'right'},
			{name:'status', index:'grd.status', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($grid_statuses);?>}
			}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'grd.code', 
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
<?php
if (!$simple)
{?>
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -164));
<?php
}?>
}
</script>