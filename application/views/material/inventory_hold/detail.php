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
			<td><?php echo (!empty($record->hold_date) ? date($this->config->item('server_display_date_format'), strtotime($record->hold_date)) : '');?></td>
		</tr>
		<tr>
			<th>Notes</th>
			<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
		</tr>
	</tbody>
</table>
<br/>
<table id="material_inventory_hold_detail_full_list_table"></table>
<div id="material_inventory_hold_detail_full_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	material_inventory_hold_detail_full_list_load_table('material_inventory_hold_detail_full_list_table');
});

function material_inventory_hold_detail_full_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_hold/get_list_detail_json');?>", 
		postData : {
			id : <?php echo (!empty($record) ? $record->id : 0);?>
		},
		colNames: [
			'Inventory Hold Id', 
			'Pallet',
			'Barcode', 
			'Product Id',
			'Product Code',
			'Name',
			'Grid Id',
			'Grid',
			'Box',
			'Quantity',
			'Hold',
			'Scan Date'
		], 
		colModel: [
			{name:'m_inventory_hold_id', index:'ihd.m_inventory_hold_id', frozen:true, hidden:true},
			{name:'pallet', index:'ihd.pallet', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'barcode', index:'ihd.barcode', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_id', index:'ihd.m_product_id', hidden:true},
			{name:'m_product_code', index:'pro.code', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:200, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_grid_id', index:'ihd.m_grid_id', hidden:true},
			{name:'m_grid_code', index:'gri.code', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box_from', index:'quantity_box_from', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_from', index:'quantity_from', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'is_hold', index:'ihd.is_hold', width:80, formatter:jqgrid_boolean_formatter, align: 'center',
			 stype:'select', 
			 searchoptions:{
				sopt:jqgird_search_string_operators, clearSearch:false,
				dataUrl: "<?php echo site_url('material/inventory_hold/get_is_hold_dropdown/search_hold_is_hold');?>"
			 }
			},
			jqgrid_column_date(table_id, {name:'created', index:'created', search:false})
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'm_inventory_holddetail_id', 
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