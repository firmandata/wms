<div class="content-right-header-toolbar ui-state-default ui-corner-all ui-helper-clearfix">
	<button id="material_report_stock_detail_excel_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Excel</span>
	</button>
</div>

<table>
	<tr><td valign="top">
			<table class="form-table">
				<tbody>
					<tr>
						<th width="80px">Code</th>
						<td><?php echo (!empty($m_product) ? $m_product->m_product_code : '');?></td>
					</tr>
					<tr>
						<th>Name</th>
						<td><?php echo (!empty($m_product) ? $m_product->m_product_name : '');?></td>
					</tr>
					<tr>
						<th>Zone</th>
						<td><?php echo (!empty($m_product) ? $m_product->m_productgroup_name : '');?></td>
					</tr>
					<tr>
						<th>UOM</th>
						<td><?php echo (!empty($m_product) ? $m_product->m_product_uom : '');?></td>
					</tr>
					<tr>
						<th>Project</th>
						<td><?php echo (!empty($m_product) ? $m_product->c_project_name : '');?></td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<tbody>
					<tr>
						<th width="80px">Start</th>
						<td><?php echo (!empty($m_product) ? number_format_clear($m_product->quantity_start, 4) : '');?></td>
					</tr>
					<tr>
						<th>In</th>
						<td><?php echo (!empty($m_product) ? number_format_clear($m_product->quantity_in, 4) : '');?></td>
					</tr>
					<tr>
						<th>Out</th>
						<td><?php echo (!empty($m_product) ? number_format_clear($m_product->quantity_out, 4) : '');?></td>
					</tr>
					<tr>
						<th>End</th>
						<td><?php echo (!empty($m_product) ? number_format_clear($m_product->quantity_end, 4) : '');?></td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
<table>
<?php
$inventory_log_types = array_merge(array('' => ''), $this->config->item('inventory_log_types'));?>

<table id="material_report_stock_detail_list_table"></table>
<div id="material_report_stock_detail_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_report_stock_detail_list_load_table('material_report_stock_detail_list_table');
	
	jQuery("#material_report_stock_detail_excel_btn").click(function(e){
		e.preventDefault();
		
		var filters = jQuery('#material_report_stock_detail_list_table').jqGrid('getGridParam', 'postData');
		filters.rows = 0;
		filters.page = 0;
		
		var params = jQuery.param(filters);
		document.location.href = "<?php echo site_url('material/report_stock/get_list_detail_excel');?>?" + params;
	});
});

function material_report_stock_detail_list_load_table(table_id){
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
		height: 350,
		url: "<?php echo site_url('material/report_stock/get_list_detail_json');?>", 
		postData : material_report_stock_detail_list_get_param(),
		colNames: [
			'Date Time', 
			'Log Type',
			'Pallet',
			'Barcode',
			'Grid',
			'Ref 1',
			'Ref 2',
			'In',
			'Out',
			'Notes',
			'Allocated',
			'Picked'
		], 
		colModel: [
			jqgrid_column_datetime(table_id, {name:'created', index:'invl.created', frozen:true, search:false}),
			{name:'log_type', index:'invl.log_type', width:100, frozen:true,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($inventory_log_types);?>}
			},
			{name:'pallet', index:'invl.pallet', width:110, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'barcode', index:'invl.barcode', width:110, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_grid_code', index:'grd.code', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'ref1_code', index:'invl.ref1_code', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'ref2_code', index:'invl.ref2_code', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_in', index:'quantity_in', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, search:false, align:'right'},
			{name:'quantity_out', index:'quantity_out', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, search:false, align:'right'},
			{name:'notes', index:'invl.notes', width:300, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_allocated', index:'invl.quantity_allocated', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, align:'right'},
			{name:'quantity_picked', index:'invl.quantity_picked', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, align:'right'}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'invl.created', 
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

function material_report_stock_detail_list_get_param(){
	return {
		m_product_id	: '<?php echo $m_product_id;?>',
		c_project_id	: '<?php echo $c_project_id;?>',
		date_from		: '<?php echo $date_from;?>',
		date_to			: '<?php echo $date_to;?>',
		show_by			: '<?php echo $show_by;?>'
	};
}
</script>