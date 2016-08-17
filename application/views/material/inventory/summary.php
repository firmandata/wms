<div class="content-right-header-toolbar ui-state-default ui-corner-all ui-helper-clearfix">
	<button id="material_inventory_summary_excel_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Excel</span>
	</button>
</div>

<table id="material_inventory_summary_list_table"></table>
<div id="material_inventory_summary_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventory_summary_list_load_table('material_inventory_summary_list_table');
	
	jQuery("#material_inventory_summary_excel_btn").click(function(e){
		e.preventDefault();
		
		var filters = jQuery('#material_inventory_summary_list_table').jqGrid('getGridParam', 'postData');
		filters.rows = 0;
		filters.page = 0;
		
		var params = jQuery.param(filters);
		document.location.href = "<?php echo site_url('material/inventory/get_summary_list_excel');?>?" + params;
	});
});

function material_inventory_summary_list_load_table(table_id){
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
			id: '0'
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('material/inventory/get_summary_list_json');?>", 
		colNames: [
			'Product Id', 
			'Product Code', 
			'Product Name',
			'Grid',
			'Exist',
			'Allocated',
			'Picked',
			'Onhand',
			'Exist',
			'Allocated',
			'Picked',
			'Onhand',
			'Pallet',
			'Age',
			'Business Partner',
			'Project'
		], 
		colModel: [
			{name:'m_product_id', index:'pro.id', hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_grid_code', index:'grd.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box_exist', index:'quantity_box_exist', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_box_allocated', index:'quantity_box_allocated', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_box_picked', index:'quantity_box_picked', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_box_onhand', index:'quantity_box_onhand', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_exist', index:'quantity_exist', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity_allocated', index:'quantity_allocated', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity_picked', index:'quantity_picked', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity_onhand', index:'quantity_onhand', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'pallet', index:'inv.pallet', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'inventory_age', index:'inventory_age', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'c_businesspartner_name', index:'bp.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_project_name', index:'prj.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav'
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
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -281));
}
</script>