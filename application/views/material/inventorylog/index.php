<?php
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
	
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('material/inventorylog/index',
	array(
		'name'	=> 'material_inventorylog_list_form',
		'id'	=> 'material_inventorylog_list_form'
	)
);?>
	<label for="material_inventorylog_list_form_date">Date</label>
<?php
echo form_input(
	array(
		'name' 	=> 'date_from',
		'id' 	=> 'material_inventorylog_list_form_date',
		'class'	=> 'date required',
		'style'	=> 'width:75px;',
		'value'	=> date($this->config->item('server_display_date_format'))
	)
);?>
	<label for="material_inventorylog_list_to_date">To</label>
<?php
echo form_input(
	array(
		'name' 	=> 'date_to',
		'id' 	=> 'material_inventorylog_list_to_date',
		'class'	=> 'date required',
		'style'	=> 'width:75px;',
		'value'	=> date($this->config->item('server_display_date_format'))
	)
);
echo form_close();?>
</div>

<table id="material_inventorylog_list_table"></table>
<div id="material_inventorylog_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventorylog_list_load_table('material_inventorylog_list_table');
	
	jQuery('#material_inventorylog_list_form').validate({
		submitHandler: function(form){
			jQuery('#material_inventorylog_list_table').setGridParam({
				postData : material_inventorylog_list_get_param()
			});
			jQuery('#material_inventorylog_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#material_inventorylog_list_form_date,#material_inventorylog_list_to_date').change(function(){
		jQuery('#material_inventorylog_list_form').submit();
	});
});

function material_inventorylog_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventorylog/get_list_json');?>", 
		postData : material_inventorylog_list_get_param(),
		colNames: [
			'Product Id', 
			'Product Code', 
			'Product Name',
			'UOM',
			'Start',
			'Change',
			'End',
			'Start',
			'Change',
			'End'
		], 
		colModel: [
			{name:'m_product_id', index:'pro.id', hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'quantity_box_start', index:'quantity_box_start', width:80, align:'right', search:false
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:material_inventorylog_list_detail(\'' + rowObject.m_product_id + '\', \'' + material_inventorylog_list_get_date('material_inventorylog_list_form_date') + '\', null, \'start\')">' + cellvalue + '</a>';
			 }
			},
			{name:'quantity_box_change', index:'quantity_box_change', width:80, align:'right', search:false
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:material_inventorylog_list_detail(\'' + rowObject.m_product_id + '\', \'' + material_inventorylog_list_get_date('material_inventorylog_list_form_date') + '\', \'' + material_inventorylog_list_get_date('material_inventorylog_list_to_date') + '\', \'change\')">' + cellvalue + '</a>';
			 }
			},
			{name:'quantity_box_end', index:'quantity_box_end', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_start', index:'quantity_start', width:80, align:'right', search:false
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:material_inventorylog_list_detail(\'' + rowObject.m_product_id + '\', \'' + material_inventorylog_list_get_date('material_inventorylog_list_form_date') + '\', null, \'start\')">' + cellvalue + '</a>';
			 }
			},
			{name:'quantity_change', index:'quantity_change', width:80, align:'right', search:false
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:material_inventorylog_list_detail(\'' + rowObject.m_product_id + '\', \'' + material_inventorylog_list_get_date('material_inventorylog_list_form_date') + '\', \'' + material_inventorylog_list_get_date('material_inventorylog_list_to_date') + '\', \'change\')">' + cellvalue + '</a>';
			 }
			},
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
	
	jQuery("#" + table_id).jqGrid('setGroupHeaders', {
		useColSpanStyle: true, 
		groupHeaders:[
			{startColumnName: 'quantity_box_start', numberOfColumns: 3, titleText: 'Exists Box'},
			{startColumnName: 'quantity_start', numberOfColumns: 3, titleText: 'Exists Quantity'}
		]
	});

	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -216));
}

function material_inventorylog_list_get_param(){
	var params = {
		date_from	: material_inventorylog_list_get_date('material_inventorylog_list_form_date'),
		date_to		: material_inventorylog_list_get_date('material_inventorylog_list_to_date')
	};
	
	return params;
}

function material_inventorylog_list_get_date(elem_id){
	var date = jQuery('#' + elem_id).val();
	if (jQuery.trim(date) != '')
	{
		var _date = new Date(getDateFromFormat(date, client_validate_date_format));
		date = formatDate(_date, server_client_parse_validate_date_format);
	}
	return date;
}

function material_inventorylog_list_detail(m_product_id, date_from, date_to, show_by){
	jquery_dialog_form_open('material_inventorylog_detail_container', "<?php echo site_url('material/inventorylog/detail');?>", {
		m_product_id	: m_product_id,
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