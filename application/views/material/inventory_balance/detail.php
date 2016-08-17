<?php
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-all ui-helper-clearfix">
	<button id="material_inventory_balance_detail_full_excel_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Excel</span>
	</button>
</div>

<table>
	<tr><td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">General</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="120">No</th>
						<td><?php echo (!empty($record) ? $record->code : '');?></td>
					</tr>
					<tr>
						<th>Date</th>
						<td><?php echo (!empty($record->balance_date) ? date($this->config->item('server_display_date_format'), strtotime($record->balance_date)) : '');?></td>
					</tr>
					<tr>
						<th>Location</th>
						<td><?php echo (!empty($record) ? $record->m_inventory_text : '');?></td>
					</tr>
					<tr>
						<th>Harvest Sequence</th>
						<td><?php echo (!empty($record) ? $record->harvest_sequence : '');?></td>
					</tr>
					<tr>
						<th>Actual Size</th>
						<td><?php echo (!empty($record) ? number_format_clear($record->product_size, 4) : '');?></td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="120">PIC</th>
						<td><?php echo (!empty($record) ? $record->pic : '');?></td>
					</tr>
					<tr>
						<th>Vehicle No</th>
						<td><?php echo (!empty($record) ? $record->vehicle_no : '');?></td>
					</tr>
					<tr>
						<th>Notes</th>
						<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
<br/>
<table id="material_inventory_balance_detail_full_list_table"></table>
<div id="material_inventory_balance_detail_full_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	jQuery("#material_inventory_balance_detail_full_excel_btn").click(function(e){
		e.preventDefault();
		
		var filters = jQuery('#material_inventory_balance_detail_full_list_table').jqGrid('getGridParam', 'postData');
		filters.rows = 0;
		filters.page = 0;
		
		var params = jQuery.param(filters);
		document.location.href = "<?php echo site_url('material/inventory_balance/get_list_detail_full_excel');?>?" + params;
	});
	
	material_inventory_balance_detail_full_list_load_table('material_inventory_balance_detail_full_list_table');
});

function material_inventory_balance_detail_full_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_balance/get_list_detail_full_json');?>", 
		postData : {
			id : <?php echo (!empty($record) ? $record->id : 0);?>
		},
		colNames: [
			'Id', 
			'Product',
			'Carton No',
			'Quantity',
			'UOM',
			'Notes'
		], 
		colModel: [
			{name:'id', index:'ibd.id', key:true, hidden:true, frozen:true},
			{name:'m_product_name', index:'pro.name', width:200, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'carton_no', index:'ibd.carton_no', width:100, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity', index:'ibd.quantity', width:100, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'m_product_uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'notes', index:'ibd.notes', classes:'jqgrid-nowrap-cell', width:300, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'ibd.id', 
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
	jQuery("#" + table_id).setGridHeight(300);
}
</script>