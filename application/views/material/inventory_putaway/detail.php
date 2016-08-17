<?php
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));?>

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
			<td><?php echo (!empty($record->putaway_date) ? date($this->config->item('server_display_date_format'), strtotime($record->putaway_date)) : '');?></td>
		</tr>
		<tr>
			<th>Notes</th>
			<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
		</tr>
	</tbody>
</table>
<br/>
<table id="material_inventory_putaway_detail_full_list_table"></table>
<div id="material_inventory_putaway_detail_full_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	material_inventory_putaway_detail_full_list_load_table('material_inventory_putaway_detail_full_list_table');
});

function material_inventory_putaway_detail_full_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_putaway/get_list_detail_full_json');?>", 
		postData : {
			id : <?php echo (!empty($record) ? $record->id : 0);?>
		},
		colNames: [
			'Inventory Putaway Id', 
			'Product Code',
			'Name',
			'Pallet',
			'Barcode', 
			'Grid Id',
			'Grid',
			'Box',
			'Quantity',
			'Carton No',
			'Lot No',
			'Scan Date'
		], 
		colModel: [
			{name:'m_inventory_putaway_id', index:'ipad.m_inventory_putaway_id', frozen:true, hidden:true},
			{name:'m_product_code', index:'pro.code', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:200, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'pallet', index:'ipad.pallet', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'barcode', index:'ipad.barcode', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_gridto_id', index:'ipad.m_gridto_id', hidden:true},
			{name:'m_gridto_code', index:'gri.code', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box_to', index:'quantity_box_to', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_to', index:'quantity_to', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'carton_no', index:'ipad.carton_no', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'lot_no', index:'ipad.lot_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'created', index:'created', search:false})
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'm_inventory_putawaydetail_id', 
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
	jQuery("#" + table_id).setGridHeight(350);
}
</script>