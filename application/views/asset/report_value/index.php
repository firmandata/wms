<?php
$currencies = array_merge(array('' => ''), $this->config->item('currencies'));
$asset_types = array_merge(array('' => ''), $this->config->item('asset_types'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix content-commandbar">
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('asset/report_value/index',
	array(
		'name'	=> 'asset_report_value_list_form',
		'id'	=> 'asset_report_value_list_form'
	)
);?>
	<label for="asset_report_value_list_form_month">Period</label>
<?php 
$months = $this->config->item('months');
$month_options = array();
foreach($months as $month=>$month_name)
{
	$month_options[$month] = $month_name;
}
echo form_dropdown('month', $month_options, date('n'), 'id="asset_report_value_list_form_month" class="required"');

$year_options = array();
for ($year_begin = date('Y') + 9; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('year', $year_options, date('Y'), 'id="asset_report_value_list_form_year" class="required"');
echo form_close();?>
</div>

<table id="asset_report_value_list_table"></table>
<div id="asset_report_value_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	asset_report_value_list_load_table('asset_report_value_list_table');
	
	jQuery('#asset_report_value_list_form').validate({
		submitHandler: function(form){
			jQuery('#asset_report_value_list_table').setGridParam({
				postData : asset_report_value_list_get_param()
			});
			jQuery('#asset_report_value_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#asset_report_value_list_form_month,#asset_report_value_list_form_year').change(function(){
		jQuery('#asset_report_value_list_form').submit();
	});
});

function asset_report_value_list_load_table(table_id){
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
		url: "<?php echo site_url('asset/report_value/get_list_json');?>", 
		postData : asset_report_value_list_get_param(),
		hidegrid: false,
		colNames: [
			'Id', 
			'Code',
			'Name',
			'Voucher No',
			'Product',
			'Type',
			'Region',
			'Department',
			'Location',
			'User',
			'Supplier',
			'Purchase Date',
			'Currency',
			'Purchase Price',
			'Quantity',
			'Book',
			'Market',
			'Depr.Accumulated'
		], 
		colModel: [
			{name:'id', index:'ast.id', key:true, hidden:true, frozen:true},
			{name:'code', index:'ast.code', width:80, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'name', index:'ast.name', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'voucher_no', index:'ast.voucher_no', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'type', index:'ast.type', width:75, align:'center',
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($asset_types);?>}
			},
			{name:'c_region_name', index:'rgn.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_department_name', index:'dep.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_location_name', index:'loc.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_user_name', index:'bp_user.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_supplier_name', index:'bp_sup.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'purchase_date', index:'ast.purchase_date'}),
			{name:'currency', index:'ast.currency', width:75, align:'center',
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($currencies);?>}
			},
			{name:'purchase_price', index:'ast.purchase_price', width:110, formatter:'number', formatoptions:{decimalPlaces: 2}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, align:'right'},
			{name:'quantity', index:'ast.quantity', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, align:'right'},
			{name:'book_value', index:'ast.book_value', width:110, formatter:'number', formatoptions:{decimalPlaces: 2}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, align:'right'},
			{name:'market_value', index:'ast.market_value', width:110, formatter:'number', formatoptions:{decimalPlaces: 2}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, align:'right'},
			{name:'depreciation_accumulated', index:'ast.depreciation_accumulated', width:110, formatter:'number', formatoptions:{decimalPlaces: 2}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, align:'right'}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'name', 
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
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -193));
}

function asset_report_value_list_get_param(){
	return {
		month	: jQuery('#asset_report_value_list_form_month').val(),
		year	: jQuery('#asset_report_value_list_form_year').val()
	};
}
</script>