<?php
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));?>

<table id="material_assembly_form_inventory_list_table"></table>
<div id="material_assembly_form_inventory_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	material_inventory_assembly_form_inventory_list_load_table('material_assembly_form_inventory_list_table');
});

function material_inventory_assembly_form_inventory_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		multiselect: true,
		rowNum: <?php echo $this->config->item('jqgrid_limit_per_page');?>, 
		rowList: [<?php echo $this->config->item('jqgrid_limit_pages');?>], 
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false,
			id: '0'
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('material/inventory_assembly/get_form_inventory_list_json');?>", 
		colNames: [
			'Product Id', 
			'Product Code', 
			'Product Name',
			'Product Netto',
			'Grid Id',
			'Grid',
			'Warehouse',
			'Product Group',
			'Exist',
			'Allocated',
			'Picked',
			'Onhand',
			'Exist',
			'Allocated',
			'Picked',
			'Onhand',
			'UOM',
			'Pack',
			'Barcode',
			'Pallet',
			'Carton No',
			'Lot No',
			'Length',
			'Width',
			'Height',
			'Project Id',
			'Project',
			'Business Partner Id',
			'Business Partner'
		], 
		colModel: [
			{name:'m_product_id', index:'pro.id', hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_netto', index:'pro.netto', hidden:true, frozen:true},
			{name:'m_grid_id', index:'grd.id', hidden:true, frozen:true},
			{name:'m_grid_code', index:'grd.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_warehouse_name', index:'wh.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_productgroup_name', index:'prog.name', width:110, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box_exist', index:'quantity_box_exist', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_box_allocated', index:'quantity_box_allocated', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_box_picked', index:'quantity_box_picked', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_box_onhand', index:'quantity_box_onhand', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_exist', index:'quantity_exist', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity_allocated', index:'quantity_allocated', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity_picked', index:'quantity_picked', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity_onhand', index:'quantity_onhand', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'m_product_uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'m_product_pack', index:'pro.pack', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'barcode', index:'inv.barcode', width:180, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'pallet', index:'inv.pallet', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'carton_no', index:'inv.carton_no', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'lot_no', index:'inv.lot_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'volume_length', index:'inv.volume_length', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'volume_width', index:'inv.volume_width', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'volume_height', index:'inv.volume_height', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_project_id', index:'prj.id', hidden:true},
			{name:'c_project_name', index:'prj.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_id', index:'bp.id', hidden:true},
			{name:'c_businesspartner_name', index:'bp.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		// sortname: 'pro.code', 
		// sortorder: "asc"
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
			{startColumnName: 'quantity_box_exist', numberOfColumns: 4, titleText: 'Box'},
			{startColumnName: 'quantity_exist', numberOfColumns: 4, titleText: 'Quantity'}
		]
	});

	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(450);
}

function material_inventory_assembly_form_inventory_submit(on_success, jqDialog){
	var records = new Array();
	var record_ids = jQuery("#material_assembly_form_inventory_list_table").getGridParam('selarrrow');
	jQuery.each(record_ids, function(idx, value){
		records.push(jQuery("#material_assembly_form_inventory_list_table").getRowData(value));
	});
	if (records.length > 0)
	{
		on_success(records);
		jqDialog.dialog("close");
	}
	else
		jquery_show_message("Please select the row data !", null, "ui-icon-alert");
}
</script>