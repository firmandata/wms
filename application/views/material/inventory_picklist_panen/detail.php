<?php
$product_conditions = array_merge(array('' => ''), $this->config->item('product_conditions'));
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));
$product_types = array_merge(array('' => ''), $this->config->item('product_types'));
$product_casings = array_merge(array('' => ''), $this->config->item('product_casings'));
$status_inventory_picking = array_merge(array('' => ''), $this->config->item('status_inventory_picking'));?>

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
						<td><?php echo (!empty($record->picklist_date) ? date($this->config->item('server_display_date_format'), strtotime($record->picklist_date)) : '');?></td>
					</tr>
					<tr>
						<th>Notes</th>
						<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
					</tr>
					<tr>
						<th>Picking Status</th>
						<td><?php echo (!empty($record) ? $record->status_inventory_picking : '');?></td>
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
						<th width="120">Shipment Type</th>
						<td><?php echo (!empty($record) ? $record->shipment_type : '');?></td>
					</tr>
					<tr>
						<th>Transport Mode</th>
						<td><?php echo (!empty($record) ? $record->transport_mode : '');?></td>
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
				</tbody>
			</table>
		</td>
	</tr>
</table>
<br/>
<table id="material_inventory_picklist_detail_full_list_table"></table>
<div id="material_inventory_picklist_detail_full_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	material_inventory_picklist_detail_full_list_load_table('material_inventory_picklist_detail_full_list_table');
});

function material_inventory_picklist_detail_full_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_picklist_panen/get_list_detail_json');?>", 
		postData : {
			id : <?php echo (!empty($record) ? $record->id : 0);?>
		},
		colNames: [
			'Id', 
			'Product Code',
			'Name',
			'Barcode', 
			'Location',
			'Quantity',
			'UOM',
			'Carton No',
			'Size',
			'Picking Status',
			'Order Out No', 
			'Order Out Date',
			'EHT', 
			'Business Partner',
			'Project'
		], 
		colModel: [
			{name:'id', index:'ipld.id', key:true, hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:220, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'barcode', index:'ipld.barcode', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_grid_code', index:'gri.code', width:80, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity', index:'quantity', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'m_product_uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'carton_no', index:'ipld.carton_no', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'product_size', index:'ipld.product_size', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'status_inventory_picking', index:'ipld.status_inventory_picking', width:110, align:'center', 
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($status_inventory_picking);?>},
			 cellattr: function(rowId, val, rawObject, cm, rdata){
				var color = null;
				if (rawObject.status_inventory_picking == 'NO PICKING')
					color = 'red';
				else if (rawObject.status_inventory_picking == 'COMPLETE')
					color = 'green';
				else if (rawObject.status_inventory_picking == 'INCOMPLETE')
					color = 'orange';
				else
					color = 'yellow';
				if (color)
					return ' style="background-color:' + color + '; font-weight:bold;" ';
				else
					return;
			 }
			},
			{name:'c_orderout_code', index:'oo.code', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'c_orderout_date', index:'oo.orderout_date'}),
			jqgrid_column_date(table_id, {name:'c_orderout_estimation_harvest_time', index:'oo.c_orderout_estimation_harvest_time'}),
			{name:'c_businesspartner_name', index:'bp.name', width:180, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_project_name', index:'prj.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'ipld.id', 
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
	jQuery("#" + table_id).setGridHeight(400);
}
</script>