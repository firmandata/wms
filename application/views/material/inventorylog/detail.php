<?php
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));
$product_conditions = array_merge(array('' => ''), $this->config->item('product_conditions'));
$inventory_log_types = array_merge(array('' => ''), $this->config->item('inventory_log_types'));?>

<table id="material_inventorylog_detail_list_table"></table>
<div id="material_inventorylog_detail_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventorylog_detail_list_load_table('material_inventorylog_detail_list_table');
});

function material_inventorylog_detail_list_load_table(table_id){
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
		height: 450,
		url: "<?php echo site_url('material/inventorylog/get_list_detail_json');?>", 
		postData : material_inventorylog_detail_list_get_param(),
		colNames: [
			'Product Id', 
			'Date Time', 
			'Log Type',
			'Barcode',
			'Pallet',
			'Box',
			'Quantity',
			'Grid',
			'Warehouse',
			'Ref 1',
			'Ref 2',
			'Condition',
			'Notes'
		], 
		colModel: [
			{name:'m_product_id', index:'pro.id', hidden:true, frozen:true},
			jqgrid_column_datetime(table_id, {name:'created', index:'invl.created', search:false, frozen:true}),
			{name:'log_type', index:'invl.log_type', width:100, frozen:true,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($inventory_log_types);?>}
			},
			{name:'barcode', index:'invl.barcode', width:110, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'pallet', index:'invl.pallet', width:110, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box', index:'invl.quantity_box', width:80, frozen:true, formatter:'number', formatoptions:{decimalPlaces: 0}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, align:'right'},
			{name:'quantity', index:'invl.quantity', width:80, frozen:true, formatter:'number', formatoptions:{decimalPlaces: 4}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, align:'right'},
			{name:'m_grid_code', index:'grd.code', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_warehouse_name', index:'wh.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'ref1_code', index:'invl.ref1_code', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'ref2_code', index:'invl.ref2_code', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'condition', index:'invl.condition', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_conditions);?>}
			},
			{name:'notes', index:'invl.notes', width:300, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'pro.code', 
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
}

function material_inventorylog_detail_list_get_param(){
	return {
		m_product_id	: '<?php echo $m_product_id;?>',
		date_from		: '<?php echo $date_from;?>',
		date_to			: '<?php echo $date_to;?>',
		show_by			: '<?php echo $show_by;?>'
	};
}
</script>