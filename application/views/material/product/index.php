<?php
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));
$product_origins = array_merge(array('' => ''), $this->config->item('product_origins'));
$product_types = array_merge(array('' => ''), $this->config->item('product_types'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix content-commandbar">
<?php 
if (is_authorized('material/product', 'insert')){?>
	<button id="material_product_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('material/product', 'update')){?>
	<button id="material_product_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('material/product', 'delete')){?>
	<button id="material_product_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}
if (is_authorized('material/product', 'update')){?>
	<button id="material_product_productgroup_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-flag"></span>
		<span class="ui-button-text">Set Group</span>
	</button>
<?php 
}?>
</div>

<table id="material_product_list_table"></table>
<div id="material_product_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	material_product_list_load_table('material_product_list_table');
	
	jQuery("#material_product_new_btn").click(function(){
		jquery_dialog_form_open('material_product_form_container', "<?php echo site_url('material/product/form');?>", {
			form_action : "material/product/insert"
		}, 
		function(form_dialog){
			material_product_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#material_product_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Product", 
			width : 700
		});
	});
	
	jQuery("#material_product_edit_btn").click(function(){
		var id = jQuery("#material_product_list_table").getGridParam("selrow");
		if (id)
		{
			jquery_dialog_form_open('material_product_form_container', "<?php echo site_url('material/product/form');?>", {
				form_action : "material/product/update",
				id : id
			}, 
			function(form_dialog){
				material_product_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#material_product_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Product", 
				width : 700
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_product_delete_btn").click(function(){
		var ids = jQuery("#material_product_list_table").getGridParam("selarrrow");
		if (ids.length > 0)
		{
			jquery_show_confirm("Are your sure ?", function(){
				jQuery.ajax({
					url: "<?php echo site_url('material/product/delete_by_ids');?>",
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
							jQuery("#material_product_list_table").trigger('reloadGrid', [{current:true}]);
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
	
	jQuery("#material_product_productgroup_btn").click(function(){
		var ids = jQuery("#material_product_list_table").getGridParam("selarrrow");
		if (ids.length > 0)
		{
			jquery_dialog_form_open('material_product_productgroup_form_container', "<?php echo site_url('material/product/form_productgroup');?>", 
			{
				ids : ids
			}, 
			function(form_dialog){
				material_product_productgroup_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#material_product_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			}, 
			{
				title : "Set Group", 
				width : 435
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
});

function material_product_list_load_table(table_id){
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
		url: "<?php echo site_url('material/product/get_list_json');?>", 
		editurl: "<?php echo site_url('material/product/jqgrid_cud');?>",
		multiselect : true,
		colNames: [
			'Id', 
			'Code', 
			'Name',
			'Group',
			'UOM',
			'Pack',
			'Origin',
			'Brand',
			'Type',
			'Netto',
			'Minimum Stock',
			'Length',
			'Width',
			'Height',
			'Barcode Length',
			'Quantity Start',
			'Quantity End',
			'Quantity Point Start',
			'Quantity Point End',
			'Quantity Divider',
			'SKU Start',
			'SKU End',
			'Carton Start',
			'Carton End',
			'Packed Date Start',
			'Packed Date End'
		], 
		colModel: [
			{name:'id', index:'pro.id', key:true, hidden:true, frozen:true},
			{name:'code', index:'pro.code', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'name', index:'pro.name', width:250, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_productgroup_name', index:'prog.name', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'pack', index:'pro.pack', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'origin', index:'pro.origin', width:120,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_origins);?>}
			},
			{name:'brand', index:'pro.brand', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'type', index:'pro.type', width:120,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_types);?>}
			},
			{name:'netto', index:'pro.netto', width:100, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'minimum_stock', index:'pro.minimum_stock', width:100, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'volume_length', index:'pro.volume_length', width:100, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'volume_width', index:'pro.volume_width', width:100, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'volume_height', index:'pro.volume_height', width:100, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_barcode_length', index:'cpro.barcode_length', width:100, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_quantity_start', index:'cpro.quantity_start', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_quantity_end', index:'cpro.quantity_end', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_quantity_point_start', index:'cpro.quantity_point_start', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_quantity_point_end', index:'cpro.quantity_point_end', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_quantity_divider', index:'cpro.quantity_point_end', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_sku_start', index:'cpro.sku_start', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_sku_end', index:'cpro.sku_end', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_carton_start', index:'cpro.carton_start', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_carton_end', index:'cpro.carton_end', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_packed_date_start', index:'cpro.packed_date_start', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_packed_date_end', index:'cpro.packed_date_end', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'pro.name', 
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
	
	jQuery("#" + table_id).jqGrid('setGroupHeaders', {
		useColSpanStyle: true, 
		groupHeaders:[
			{startColumnName: 'volume_length', numberOfColumns: 3, titleText: 'Volume'},
			{startColumnName: 'c_barcode_length', numberOfColumns: 12, titleText: 'Barcode Configuration'}
		]
	});
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -184));
}
</script>