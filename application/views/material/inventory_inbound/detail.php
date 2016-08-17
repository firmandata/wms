<?php
$product_conditions = array_merge(array('' => ''), $this->config->item('product_conditions'));
$transport_modes = array_merge(array('' => ''), $this->config->item('transport_modes'));
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-all ui-helper-clearfix">
	<button id="material_inventory_inbound_detail_full_excel_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Excel</span>
	</button>
</div>

<table class="form-table">
	<thead>
		<tr>
			<td colspan="2" class="form-table-title">Information</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th width="120">No</th>
			<td><?php echo (!empty($record) ? $record->code : '');?></td>
		</tr>
		<tr>
			<th>Date</th>
			<td><?php echo (!empty($record->inbound_date) ? date($this->config->item('server_display_date_format'), strtotime($record->inbound_date)) : '');?></td>
		</tr>
		<tr>
			<th>Notes</th>
			<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
		</tr>
	</tbody>
</table>
<br/>
<table id="material_inventory_inbound_detail_full_list_table"></table>
<div id="material_inventory_inbound_detail_full_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	jQuery("#material_inventory_inbound_detail_full_excel_btn").click(function(e){
		e.preventDefault();
		
		var filters = jQuery('#material_inventory_inbound_detail_full_list_table').jqGrid('getGridParam', 'postData');
		filters.rows = 0;
		filters.page = 0;
		
		var params = jQuery.param(filters);
		document.location.href = "<?php echo site_url('material/inventory_inbound/get_list_detail_full_excel');?>?" + params;
	});
	
	material_inventory_inbound_detail_full_list_load_table('material_inventory_inbound_detail_full_list_table');
});

function material_inventory_inbound_detail_full_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_inbound/get_list_detail_full_json');?>", 
		postData : {
			id : <?php echo (!empty($record) ? $record->id : 0);?>
		},
		colNames: [
			'Id', 
			'Product Code',
			'Name',
			'Barcode', 
			'Box',
			'Quantity',
			'UOM',
			'Carton No',
			'Packed Date',
			'Expired Date',
			'Pallet',
			'Grid',
			'Lot No',
			'Length',
			'Width',
			'Height',
			'Condition',
			'Pack',
			'Scan Date',
			'Receive No', 
			'Receive Date', 
			'Order In No', 
			'Order In Date', 
			'Business Partner',
			'Project',
			'Vehicle No',
			'Driver',
			'Transport'
		], 
		colModel: [
			{name:'id', index:'iid.id', key:true, hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:220, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'barcode', index:'iid.barcode', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box', index:'iid.quantity_box', width:80, frozen:true, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity', index:'iid.quantity', width:80, frozen:true, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'m_product_uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'carton_no', index:'iid.carton_no', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'packed_date', index:'iid.packed_date'}),
			jqgrid_column_date(table_id, {name:'expired_date', index:'iid.expired_date'}),
			{name:'pallet', index:'iid.pallet', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'grid_code', index:'gri.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'lot_no', index:'iid.lot_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'volume_length', index:'iid.volume_length', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'volume_width', index:'iid.volume_width', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'volume_height', index:'iid.volume_height', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'condition', index:'iid.condition', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_conditions);?>}
			},
			{name:'m_product_pack', index:'pro.pack', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'created', index:'iid.created', search:false}),
			{name:'m_inventory_receive_code', index:'ir.code', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'m_inventory_receive_date', index:'ir.receive_date'}),
			{name:'c_orderin_code', index:'oi.code', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'c_orderin_date', index:'oi.orderin_date'}),
			{name:'c_businesspartner_name', index:'bp.name', width:180, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_project_name', index:'prj.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_inventory_receive_vehicle_no', index:'ir.vehicle_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_inventory_receive_vehicle_driver', index:'ir.vehicle_driver', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_inventory_receive_transport_mode', index:'ir.transport_mode', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($transport_modes);?>}
			}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'iid.id', 
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
			{startColumnName: 'volume_length', numberOfColumns: 3, titleText: 'Volume'}
		]
	});
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(400);
}
</script>