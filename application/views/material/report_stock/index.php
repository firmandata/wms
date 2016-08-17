<?php
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
	<button id="material_report_stock_excel_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Excel</span>
	</button>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('material/report_stock/index',
	array(
		'name'	=> 'material_report_stock_list_form',
		'id'	=> 'material_report_stock_list_form'
	)
);?>
	<label for="material_report_stock_list_form_date">Date</label>
<?php
echo form_input(
	array(
		'name' 	=> 'date_from',
		'id' 	=> 'material_report_stock_list_form_date',
		'class'	=> 'date required',
		'style'	=> 'width:75px;',
		'value'	=> date($this->config->item('server_display_date_format'))
	)
);?>
	<label for="material_report_stock_list_to_date">To</label>
<?php
echo form_input(
	array(
		'name' 	=> 'date_to',
		'id' 	=> 'material_report_stock_list_to_date',
		'class'	=> 'date required',
		'style'	=> 'width:75px;',
		'value'	=> date($this->config->item('server_display_date_format'))
	)
);
echo form_close();?>
</div>

<table id="material_report_stock_list_table"></table>
<div id="material_report_stock_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_report_stock_list_load_table('material_report_stock_list_table');
	
	jQuery('#material_report_stock_list_form').validate({
		submitHandler: function(form){
			jQuery('#material_report_stock_list_table').setGridParam({
				postData : material_report_stock_list_get_param()
			});
			jQuery('#material_report_stock_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#material_report_stock_list_form_date,#material_report_stock_list_to_date').change(function(){
		jQuery('#material_report_stock_list_form').submit();
	});
	
	jQuery("#material_report_stock_excel_btn").click(function(e){
		e.preventDefault();
		
		var filters = jQuery('#material_report_stock_list_table').jqGrid('getGridParam', 'postData');
		filters.rows = 0;
		filters.page = 0;
		
		var params = jQuery.param(filters);
		document.location.href = "<?php echo site_url('material/report_stock/get_list_excel');?>?" + params;
	});
});

function material_report_stock_list_load_table(table_id){
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
		url: "<?php echo site_url('material/report_stock/get_list_json');?>", 
		postData : material_report_stock_list_get_param(),
		colNames: [
			'Product Id', 
			'Product Code', 
			'Product Name',
			'UOM',
			'Group',
			'Project Id',
			'Project',
			'Start',
			'In',
			'Out',
			'End'
		], 
		colModel: [
			{name:'m_product_id', index:'invl.m_product_id', hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:material_report_stock_list_detail(\'' + rowObject.m_product_id + '\', \'' + (rowObject.c_project_id ? rowObject.c_project_id : '') + '\', \'' + material_report_stock_list_get_date('material_report_stock_list_form_date') + '\', \'' + material_report_stock_list_get_date('material_report_stock_list_to_date') + '\', \'change\')">' + cellvalue + '</a>';
			 }
			},
			{name:'m_product_name', index:'pro.name', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'m_productgroup_name', index:'prog.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_project_id', index:'invl.c_project_id', hidden:true},
			{name:'c_project_name', index:'prj.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_start', index:'quantity_start', width:80, align:'right', search:false, formatter:'number', formatoptions:{decimalPlaces: 4}},
			{name:'quantity_in', index:'quantity_in', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity_out', index:'quantity_out', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity_end', index:'quantity_end', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'pro.code', 
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

function material_report_stock_list_get_param(){
	var params = {
		date_from	: material_report_stock_list_get_date('material_report_stock_list_form_date'),
		date_to		: material_report_stock_list_get_date('material_report_stock_list_to_date')
	};
	
	return params;
}

function material_report_stock_list_get_date(elem_id){
	var date = jQuery('#' + elem_id).val();
	if (jQuery.trim(date) != '')
	{
		var _date = new Date(getDateFromFormat(date, client_validate_date_format));
		date = formatDate(_date, server_client_parse_validate_date_format);
	}
	return date;
}

function material_report_stock_list_detail(m_product_id, c_project_id, date_from, date_to, show_by){
	jquery_dialog_form_open('material_report_stock_detail_container', "<?php echo site_url('material/report_stock/detail');?>", {
		m_product_id	: m_product_id,
		c_project_id	: c_project_id,
		date_from		: date_from,
		date_to			: date_to,
		show_by			: show_by
	}, null,
	{
		title : "Log Detail", 
		width : 1020
	});
}
</script>