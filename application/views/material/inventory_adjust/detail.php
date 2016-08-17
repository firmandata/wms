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
			<td><?php echo (!empty($record->adjust_date) ? date($this->config->item('server_display_date_format'), strtotime($record->adjust_date)) : '');?></td>
		</tr>
		<tr>
			<th>Type</th>
			<td><?php echo (!empty($record) ? $record->adjust_type : '');?></td>
		</tr>
		<tr>
			<th>Notes</th>
			<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
		</tr>
	</tbody>
</table>
<br/>
<table id="material_inventory_adjust_detail_full_list_table"></table>
<div id="material_inventory_adjust_detail_full_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	material_inventory_adjust_detail_full_list_load_table('material_inventory_adjust_detail_full_list_table');
});

function material_inventory_adjust_detail_full_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_adjust/get_list_detail_json');?>", 
		postData : {
			id : <?php echo (!empty($record) ? $record->id : 0);?>
		},
		colNames: [
			'Inventory Adjust Id', 
			'Pallet',
			'Barcode', 
			'Product Id',
			'Product Code',
			'Name',
			'Grid Id',
			'Grid',
			'Box From',
			'Box To',
			'Quantity From',
			'Quantity To',
			'Scan Date'
		], 
		colModel: [
			{name:'m_inventory_adjust_id', index:'iad.m_inventory_adjust_id', frozen:true, hidden:true},
			{name:'pallet', index:'iad.pallet', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'barcode', index:'iad.barcode', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_id', index:'iad.m_product_id', hidden:true},
			{name:'m_product_code', index:'pro.code', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:200, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_grid_id', index:'iad.m_grid_id', hidden:true},
			{name:'m_grid_code', index:'gri.code', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box_from', index:'quantity_box_from', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_from', index:'quantity_from', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity_box_to', index:'quantity_box_to', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_to', index:'quantity_to', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			jqgrid_column_date(table_id, {name:'created', index:'created', search:false})
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'm_inventory_adjustdetail_id', 
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
	jQuery("#" + table_id).setGridHeight(250);
}
</script>