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
			<td><?php echo (!empty($record->move_date) ? date($this->config->item('server_display_date_format'), strtotime($record->move_date)) : '');?></td>
		</tr>
		<tr>
			<th>Notes</th>
			<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
		</tr>
	</tbody>
</table>
<br/>
<table id="asset_asset_move_detail_full_list_table"></table>
<div id="asset_asset_move_detail_full_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	asset_asset_move_detail_full_list_load_table('asset_asset_move_detail_full_list_table');
});

function asset_asset_move_detail_full_list_load_table(table_id){
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
		url: "<?php echo site_url('asset/asset_move/get_list_detail_json');?>", 
		postData : {
			id : <?php echo (!empty($record) ? $record->id : 0);?>
		},
		colNames: [
			'Asset Move Detail Id', 
			'Asset Move Id', 
			'Asset Id', 
			'Code', 
			'Name', 
			'Product Id',
			'Product Name',
			'Location From Id',
			'Location From',
			'Location From Desc',
			'Location To Id',
			'Location To',
			'Location To Desc',
			'Scan Date'
		], 
		colModel: [
			{name:'id', index:'amd.id', frozen:true, hidden:true},
			{name:'a_asset_move_id', index:'amd.a_asset_move_id', frozen:true, hidden:true},
			{name:'a_asset_id', index:'amd.a_asset_id', frozen:true, hidden:true},
			{name:'a_asset_code', index:'ast.code', width:80, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'a_asset_name', index:'ast.name', width:130, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_id', index:'ast.m_product_id', hidden:true},
			{name:'m_product_name', index:'pro.name', width:200, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_locationfrom_id', index:'amd.c_locationfrom_id', hidden:true},
			{name:'c_locationfrom_code', index:'locf.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_locationfrom_name', index:'locf.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_locationto_id', index:'amd.c_locationto_id', hidden:true},
			{name:'c_locationto_code', index:'loct.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_locationto_name', index:'loct.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_datetime(table_id, {name:'created', index:'created', search:false})
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'id', 
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