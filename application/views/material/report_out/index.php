<?php
$orderin_origins = array_merge(array('' => ''), $this->config->item('orderin_origins'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
	<button id="material_report_out_excel_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Excel</span>
	</button>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('material/report_out/index',
	array(
		'name'	=> 'material_report_out_list_form',
		'id'	=> 'material_report_out_list_form'
	)
);?>
	<label for="material_report_out_list_form_date">Date</label>
<?php
echo form_input(
	array(
		'name' 	=> 'date_from',
		'id' 	=> 'material_report_out_list_form_date',
		'class'	=> 'date required',
		'style'	=> 'width:75px;',
		'value'	=> date($this->config->item('server_display_date_format'))
	)
);?>
	<label for="material_report_out_list_to_date">To</label>
<?php
echo form_input(
	array(
		'name' 	=> 'date_to',
		'id' 	=> 'material_report_out_list_to_date',
		'class'	=> 'date required',
		'style'	=> 'width:75px;',
		'value'	=> date($this->config->item('server_display_date_format'))
	)
);
echo form_close();?>
</div>

<table id="material_report_out_list_table"></table>
<div id="material_report_out_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_report_out_list_load_table('material_report_out_list_table');
	
	jQuery('#material_report_out_list_form').validate({
		submitHandler: function(form){
			jQuery('#material_report_out_list_table').setGridParam({
				postData : material_report_out_list_get_param()
			});
			jQuery('#material_report_out_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#material_report_out_list_form_date,#material_report_out_list_to_date').change(function(){
		jQuery('#material_report_out_list_form').submit();
	});
	
	jQuery("#material_report_out_excel_btn").click(function(e){
		e.preventDefault();
		
		var filters = jQuery('#material_report_out_list_table').jqGrid('getGridParam', 'postData');
		filters.rows = 0;
		filters.page = 0;
		
		var params = jQuery.param(filters);
		document.location.href = "<?php echo site_url('material/report_out/get_list_excel');?>?" + params;
	});
});

function material_report_out_list_load_table(table_id){
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
		url: "<?php echo site_url('material/report_out/get_list_json');?>", 
		postData : material_report_out_list_get_param(),
		colNames: [
			'OUTBOUND DATE',
			'DO/SO',
			'SUPPLIER/VENDOR',
			'EXTERNAL DO',
			'NOTES',
			'NOTRUK',
			'SOPIR',
			'PALLET',
			'ITEM',
			'DESC',
			'QTY BOX',
			'QTY KG',
			'LOT',
			'NO FAKTUR',
			'MU'
		], 
		colModel: [
			jqgrid_column_date(table_id, {name:'shipment_date', index:'isp.shipment_date', frozen:true, search:false}),
			{name:'m_inventory_shipment_code', index:'isp.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_name', index:'bp.name', width:180, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_orderout_external_no', index:'oo.external_no', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'notes', index:'ispd.notes', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_inventory_shipment_vehicle_no', index:'isp.vehicle_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_inventory_shipment_vehicle_driver', index:'isp.vehicle_driver', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_inventory_picklistdetail_pallet', index:'ipld.pallet', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_code', index:'pro.code', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:180, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box', index:'ispd.quantity_box', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, align:'right'},
			{name:'quantity', index:'ispd.quantity', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, align:'right'},
			{name:'m_inventory_picklistdetail_lot_no', index:'ipld.lot_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_orderout_no_surat_jalan', index:'oo.no_surat_jalan', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_orderout_marketing_unit', index:'oo.marketing_unit', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'isp.shipment_date', 
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

function material_report_out_list_get_param(){
	var params = {
		date_from	: material_report_out_list_get_date('material_report_out_list_form_date'),
		date_to		: material_report_out_list_get_date('material_report_out_list_to_date')
	};
	
	return params;
}

function material_report_out_list_get_date(elem_id){
	var date = jQuery('#' + elem_id).val();
	if (jQuery.trim(date) != '')
	{
		var _date = new Date(getDateFromFormat(date, client_validate_date_format));
		date = formatDate(_date, server_client_parse_validate_date_format);
	}
	return date;
}
</script>