<?php
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
	<button id="material_report_in_excel_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Excel</span>
	</button>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('material/report_in/index',
	array(
		'name'	=> 'material_report_in_list_form',
		'id'	=> 'material_report_in_list_form'
	)
);?>
	<label for="material_report_in_list_form_date">Date</label>
<?php
echo form_input(
	array(
		'name' 	=> 'date_from',
		'id' 	=> 'material_report_in_list_form_date',
		'class'	=> 'date required',
		'style'	=> 'width:75px;',
		'value'	=> date($this->config->item('server_display_date_format'))
	)
);?>
	<label for="material_report_in_list_to_date">To</label>
<?php
echo form_input(
	array(
		'name' 	=> 'date_to',
		'id' 	=> 'material_report_in_list_to_date',
		'class'	=> 'date required',
		'style'	=> 'width:75px;',
		'value'	=> date($this->config->item('server_display_date_format'))
	)
);?>
<?php
echo form_close();?>
</div>

<table id="material_report_in_list_table"></table>
<div id="material_report_in_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_report_in_list_load_table('material_report_in_list_table');
	
	jQuery('#material_report_in_list_form').validate({
		submitHandler: function(form){
			jQuery('#material_report_in_list_table').setGridParam({
				postData : material_report_in_list_get_param()
			});
			jQuery('#material_report_in_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#material_report_in_list_form_date,#material_report_in_list_to_date').change(function(){
		jQuery('#material_report_in_list_form').submit();
	});
	
	jQuery("#material_report_in_excel_btn").click(function(e){
		e.preventDefault();
		
		var filters = jQuery('#material_report_in_list_table').jqGrid('getGridParam', 'postData');
		filters.rows = 0;
		filters.page = 0;
		
		var params = jQuery.param(filters);
		document.location.href = "<?php echo site_url('material/report_in/get_list_excel');?>?" + params;
	});
});

function material_report_in_list_load_table(table_id){
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
		url: "<?php echo site_url('material/report_in/get_list_json');?>", 
		postData : material_report_in_list_get_param(),
		colNames: [
			'INBOUND DATE',
			'PO/AR',
			'SUPPLIER/VENDOR', 
			'EXTERNAL PO',
			'NOTES',
			'NOTRUK',
			'SOPIR', 
			'PALLET',
			'ITEM',
			'DESC',
			'QTY BOX', 
			'QTY KG', 
			'UOM'
		], 
		colModel: [
			jqgrid_column_date(table_id, {name:'inbound_date', index:'ii.inbound_date', frozen:true, search:false}),
			{name:'m_inventory_receive_code', index:'ir.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_name', index:'bp.name', width:180, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'external_no', index:'oi.external_no', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'notes', index:'iid.notes', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_inventory_receive_vehicle_no', index:'ir.vehicle_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_inventory_receive_vehicle_driver', index:'ir.vehicle_driver', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'pallet', index:'iid.pallet', width:120},
			{name:'m_product_code', index:'pro.code', width:90},
			{name:'m_product_name', index:'pro.name', width:180},
			{name:'quantity_box', index:'quantity_box', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, search:false, align:'right'},
			{name:'quantity', index:'quantity', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, search:false, align:'right'},
			{name:'m_product_uom', index:'pro.uom', width:80,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'ii.inbound_date', 
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

function material_report_in_list_get_param(){
	var params = {
		date_from	: material_report_in_list_get_date('material_report_in_list_form_date'),
		date_to		: material_report_in_list_get_date('material_report_in_list_to_date')
	};
		
	return params;
}

function material_report_in_list_get_date(elem_id){
	var date = jQuery('#' + elem_id).val();
	if (jQuery.trim(date) != '')
	{
		var _date = new Date(getDateFromFormat(date, client_validate_date_format));
		date = formatDate(_date, server_client_parse_validate_date_format);
	}
	return date;
}
</script>