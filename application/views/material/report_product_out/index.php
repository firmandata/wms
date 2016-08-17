<?php
$months = $this->config->item('months');?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
	<button id="material_report_product_out_excel_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Excel</span>
	</button>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('material/report_product_out/index',
	array(
		'name'	=> 'material_report_product_out_list_form',
		'id'	=> 'material_report_product_out_list_form'
	)
);?>
	<label for="material_report_product_out_list_year">Year</label>
<?php 
$filter_years = array();
for ($filter_year = $this->config->item('filter_year_from'); $filter_year <= date('Y'); $filter_year++)
{
	$filter_years[$filter_year] = $filter_year;
}
echo form_dropdown('year', $filter_years, date('Y'), 'id="material_report_product_out_list_year"');
echo form_close();?>
</div>

<table id="material_report_product_out_list_table"></table>
<div id="material_report_product_out_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_report_product_out_list_load_table('material_report_product_out_list_table');
	
	jQuery('#material_report_product_out_list_form').validate({
		submitHandler: function(form){
			jQuery('#material_report_product_out_list_table').setGridParam({
				postData : material_report_product_out_list_get_param()
			});
			jQuery('#material_report_product_out_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#material_report_product_out_list_year').change(function(){
		jQuery('#material_report_product_out_list_form').submit();
	});
	
	jQuery("#material_report_product_out_excel_btn").click(function(e){
		e.preventDefault();
		
		var filters = jQuery('#material_report_product_out_list_table').jqGrid('getGridParam', 'postData');
		filters.rows = 0;
		filters.page = 0;
		
		var params = jQuery.param(filters);
		document.location.href = "<?php echo site_url('material/report_product_out/get_list_excel');?>?" + params;
	});
});

function material_report_product_out_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		rowNum: 100000, 
		pginput: false,
		pgbuttons: false,
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('material/report_product_out/get_list_json');?>", 
		postData : material_report_product_out_list_get_param(),
		colNames: [
			'Item ID',
			'Item Name',
<?php
foreach ($months as $month_key=>$month_value)
{?>
			'Out/Qty',
			'Out/DO',
			'Item Type',
<?php
}?>
			'Total DO',
			'Item Type'
		], 
		colModel: [
			{name:'m_product_code', index:'m_product_code', width:120, frozen:true, sortable:false},
			{name:'m_product_name', index:'m_product_name', width:180, frozen:true, sortable:false},
<?php
foreach ($months as $month_key=>$month_value)
{?>
			{name:'quantity_<?php echo $month_key;?>', index:'quantity_<?php echo $month_key;?>', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, sortable:false, align:'right'},
			{name:'count_<?php echo $month_key;?>_oo', index:'count_<?php echo $month_key;?>_oo', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, sortable:false, align:'right'},
			{name:'item_type_<?php echo $month_key;?>', index:'item_type_<?php echo $month_key;?>', width:100, align:'center', sortable:false
			 , formatter: function (cellvalue, options, rowObject){
				var value = '';
				if (cellvalue == 'FAST MOVING')
					value = "<font style=\"color:green;\">" + cellvalue + "</font>";
				else if (cellvalue == 'MOVING')
					value = "<font style=\"color:blue;\">" + cellvalue + "</font>";
				else if (cellvalue == 'NON MOVING')
					value = "<font style=\"color:red;\">" + cellvalue + "</font>";
				else
					value = cellvalue;
				return value;
			 }
			},
<?php
}?>
			{name:'count_oo', index:'count_oo', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, sortable:false, align:'right'},
			{name:'item_type', index:'item_type', width:100, align:'center', sortable:false
			 , formatter: function (cellvalue, options, rowObject){
				var value = '';
				if (cellvalue == 'FAST MOVING')
					value = "<font style=\"color:green;\">" + cellvalue + "</font>";
				else if (cellvalue == 'MOVING')
					value = "<font style=\"color:blue;\">" + cellvalue + "</font>";
				else if (cellvalue == 'NON MOVING')
					value = "<font style=\"color:red;\">" + cellvalue + "</font>";
				else
					value = cellvalue;
				return value;
			 }
			}
		],
		pager: '#' + table_id + '_nav'
	});
	
	jQuery("#" + table_id).jqGrid('navGrid', '#' + table_id + '_nav', {
		/* -- Button Configuration -- */
		edit: false,
		add: false,
		del: false,
		search: false
	});
	
	jQuery("#" + table_id).jqGrid('setGroupHeaders', {
		useColSpanStyle: true, 
		groupHeaders:[
<?php
foreach ($months as $month_key=>$month_value)
{?>
			{startColumnName: 'quantity_<?php echo $month_key;?>', numberOfColumns: 3, titleText: '<?php echo $month_value;?>'},
<?php
}?>
		]
	});
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -193));
}

function material_report_product_out_list_get_param(){
	var params = {
		year	: jQuery('#material_report_product_out_list_year').val()
	};
	
	return params;
}
</script>