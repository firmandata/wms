<?php
$product_conditions = array_merge(array('' => ''), $this->config->item('product_conditions'));
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-all ui-helper-clearfix">
	<button id="material_inventory_shipment_detail_full_excel_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Excel</span>
	</button>
</div>

<table>
	<tr>
		<td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="100">No</th>
						<td><?php echo (!empty($record) ? $record->code : '');?></td>
					</tr>
					<tr>
						<th>Date</th>
						<td><?php echo (!empty($record->shipment_date) ? date($this->config->item('server_display_date_format'), strtotime($record->shipment_date)) : '');?></td>
					</tr>
					<tr>
						<th>Shipment Type</th>
						<td><?php echo (!empty($record) ? $record->shipment_type : '');?></td>
					</tr>
					<tr>
						<th>Transport Mode</th>
						<td><?php echo (!empty($record) ? $record->transport_mode : '');?></td>
					</tr>
					<tr>
						<th>Notes</th>
						<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">More</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="100">Request Arrival Date</th>
						<td><?php echo (!empty($record->request_arrive_date) ? date($this->config->item('server_display_date_format'), strtotime($record->request_arrive_date)) : '');?></td>
					</tr>
					<tr>
						<th>Estimated Time Arrival</th>
						<td><?php echo (!empty($record->estimated_time_arrive) ? date($this->config->item('server_display_datetime_format'), strtotime($record->estimated_time_arrive)) : '');?></td>
					</tr>
					<tr>
						<th>Shipment To</th>
						<td><?php echo (!empty($record) ? $record->shipment_to : '');?></td>
					</tr>
					<tr>
						<th>Vehicle No</th>
						<td><?php echo (!empty($record) ? $record->vehicle_no : '');?></td>
					</tr>
					<tr>
						<th>Driver Name</th>
						<td><?php echo (!empty($record) ? $record->vehicle_driver : '');?></td>
					</tr>
					<tr>
						<th>Police Name</th>
						<td><?php echo (!empty($record) ? $record->police_name : '');?></td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
<br/>
<table id="material_inventory_shipment_detail_full_list_table"></table>
<div id="material_inventory_shipment_detail_full_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	jQuery("#material_inventory_shipment_detail_full_excel_btn").click(function(e){
		e.preventDefault();
		
		var filters = jQuery('#material_inventory_shipment_detail_full_list_table').jqGrid('getGridParam', 'postData');
		filters.rows = 0;
		filters.page = 0;
		
		var params = jQuery.param(filters);
		document.location.href = "<?php echo site_url('material/inventory_shipment/get_list_detail_excel');?>?" + params;
	});
	
	material_inventory_shipment_detail_full_list_load_table('material_inventory_shipment_detail_full_list_table');
});

function material_inventory_shipment_detail_full_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_shipment/get_list_detail_json');?>", 
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
			'Pallet',
			'Condition',
			'Packed Group',
			'Picking No', 
			'Picking Date',
			'Pick List No', 
			'Pick List Date',
			'Order Out No', 
			'Order Out Date',
			'Request Arrival', 
			'Business Partner',
			'Project'
		], 
		colModel: [
			{name:'id', index:'ispd.id', key:true, hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:220, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'barcode', index:'ipld.barcode', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box', index:'quantity_box', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity', index:'quantity', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'m_product_uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'pallet', index:'ipld.pallet', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'condition', index:'ipld.condition', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_conditions);?>}
			},
			{name:'packed_group', index:'ispd.packed_group', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_inventory_picking_code', index:'ipg.code', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'m_inventory_picking_date', index:'ipg.picking_date'}),
			{name:'m_inventory_picklist_code', index:'ipl.code', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'m_inventory_picklist_date', index:'ipl.picklist_date'}),
			{name:'c_orderout_code', index:'oo.code', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'c_orderout_date', index:'oo.orderout_date'}),
			jqgrid_column_date(table_id, {name:'request_arrive_date', index:'oo.request_arrive_date'}),
			{name:'c_businesspartner_name', index:'bp.name', width:180, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_project_name', index:'prj.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'ispd.id', 
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