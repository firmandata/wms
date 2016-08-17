<div class="content-right-header-toolbar ui-state-default ui-corner-all ui-helper-clearfix">
	<button id="asset_asset_transfer_detail_full_list_letter_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Letter</span>
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
			<td><?php echo (!empty($record->transfer_date) ? date($this->config->item('server_display_date_format'), strtotime($record->transfer_date)) : '');?></td>
		</tr>
		<tr>
			<th>Notes</th>
			<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
		</tr>
	</tbody>
</table>
<br/>
<table id="asset_asset_transfer_detail_full_list_table"></table>
<div id="asset_asset_transfer_detail_full_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	asset_asset_transfer_detail_full_list_load_table('asset_asset_transfer_detail_full_list_table');
	
	jQuery('#asset_asset_transfer_detail_full_list_letter_btn').click(function(){
		window.open("<?php echo site_url('asset/asset_transfer/letter');?>?id=<?php echo (!empty($record) ? $record->id : '');?>");
	});
});

function asset_asset_transfer_detail_full_list_load_table(table_id){
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
		url: "<?php echo site_url('asset/asset_transfer/get_list_detail_json');?>", 
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
			'User From Id',
			'User From',
			'User From Desc',
			'User To Id',
			'User To',
			'User To Desc',
			'Dept From Id',
			'Dept From',
			'Dept From Desc',
			'Dept To Id',
			'Dept To',
			'Dept To Desc',
			'Notes',
			'Scan Date'
		], 
		colModel: [
			{name:'id', index:'atd.id', frozen:true, hidden:true},
			{name:'a_asset_transfer_id', index:'atd.a_asset_transfer_id', frozen:true, hidden:true},
			{name:'a_asset_id', index:'atd.a_asset_id', frozen:true, hidden:true},
			{name:'a_asset_code', index:'ast.code', width:80, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'a_asset_name', index:'ast.name', width:130, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_id', index:'ast.m_product_id', hidden:true},
			{name:'m_product_name', index:'pro.name', width:200, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_userfrom_id', index:'atd.c_businesspartner_userfrom_id', hidden:true},
			{name:'c_businesspartner_userfrom_code', index:'bpf.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_userfrom_name', index:'bpf.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_userto_id', index:'atd.c_businesspartner_userto_id', hidden:true},
			{name:'c_businesspartner_userto_code', index:'bpt.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_userto_name', index:'bpt.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_departmentfrom_id', index:'atd.c_departmentfrom_id', hidden:true},
			{name:'c_departmentfrom_code', index:'depf.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_departmentfrom_name', index:'depf.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_departmentto_id', index:'atd.c_departmentto_id', hidden:true},
			{name:'c_departmentto_code', index:'dept.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_departmentto_name', index:'dept.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'notes', index:'atd.notes', width:180, classes:'jqgrid-nowrap-cell', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
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