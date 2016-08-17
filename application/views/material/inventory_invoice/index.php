<?php
$this->load->helper('date');

$inventory_invoice_calculates = array_merge(array('' => ''), $this->config->item('inventory_invoice_calculates'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
<?php 
if (is_authorized('material/inventory_invoice', 'insert')){?>
	<button id="material_inventory_invoice_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('material/inventory_invoice', 'update')){?>
	<button id="material_inventory_invoice_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('material/inventory_invoice', 'delete')){?>
	<button id="material_inventory_invoice_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}?>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('material/inventory_invoice/index',
	array(
		'name'	=> 'material_inventory_invoice_list_form',
		'id'	=> 'material_inventory_invoice_list_form'
	)
);?>
	<label for="material_inventory_invoice_list_form_from_month">Date</label>
<?php 
$previous_periode = add_date(date('Y-m-d'), 0, -1);

$months = $this->config->item('months');
$month_options = array();
foreach($months as $month=>$month_name)
{
	$month_options[$month] = $month_name;
}
echo form_dropdown('from_month', $month_options, date('n', strtotime($previous_periode)), 'id="material_inventory_invoice_list_form_from_month" class="required"');

$year_options = array();
for ($year_begin = date('Y') + 1; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('from_year', $year_options, date('Y', strtotime($previous_periode)), 'id="material_inventory_invoice_list_form_from_year" class="required"');
?>
	<label for="material_inventory_invoice_list_form_to_month">To</label>
<?php 
echo form_dropdown('to_month', $month_options, date('n'), 'id="material_inventory_invoice_list_form_to_month" class="required"');
echo form_dropdown('to_year', $year_options, date('Y'), 'id="material_inventory_invoice_list_form_to_year" class="required"');
echo form_close();?>
</div>

<table id="material_inventory_invoice_list_table"></table>
<div id="material_inventory_invoice_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventory_invoice_list_load_table('material_inventory_invoice_list_table');
	
	jQuery("#material_inventory_invoice_new_btn").click(function(){
		jquery_dialog_form_open('material_inventory_invoice_form_container', "<?php echo site_url('material/inventory_invoice/form');?>", {
			form_action : "material/inventory_invoice/insert"
		}, 
		function(form_dialog){
			material_inventory_invoice_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#material_inventory_invoice_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Invoice", 
			width : 750
		});
	});
	
	jQuery("#material_inventory_invoice_edit_btn").click(function(){
		var id = jQuery("#material_inventory_invoice_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_invoice_list_table").getRowData(id);
			
			jquery_dialog_form_open('material_inventory_invoice_form_container', "<?php echo site_url('material/inventory_invoice/form');?>", {
				form_action : "material/inventory_invoice/update",
				id : row_data.id
			}, 
			function(form_dialog){
				material_inventory_invoice_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#material_inventory_invoice_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Invoice", 
				width : 750
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_inventory_invoice_delete_btn").click(function(){
		var id = jQuery("#material_inventory_invoice_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_invoice_list_table").getRowData(id);
			
			var _delete_url = "<?php echo site_url('material/inventory_invoice/delete/@id');?>";
			_delete_url = _delete_url.replace(/@id/g, row_data.id);
			
			jquery_show_confirm("Are your sure ?", function(){
				jQuery.ajax({
					url: _delete_url,
					type: "GET",
					dataType: "json",
					async : false,
					error: jquery_ajax_error_handler,
					beforeSend: function(jqXHR, settings){
						jquery_blockui();
					},
					success: function(data, textStatus, jqXHR){
						if (data.response == false)
							jquery_show_message(data.value, null, "ui-icon-close");
						else
							jQuery("#material_inventory_invoice_list_table").trigger('reloadGrid', [{current:true}]);
					},
					complete: function(jqXHR, textStatus){
						jquery_unblockui();
					}
				});
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery('#material_inventory_invoice_list_form').validate({
		submitHandler: function(form){
			jQuery('#material_inventory_invoice_list_table').setGridParam({
				postData : material_inventory_invoice_list_get_param()
			});
			jQuery('#material_inventory_invoice_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#material_inventory_invoice_list_form_from_month,#material_inventory_invoice_list_form_from_year,#material_inventory_invoice_list_form_to_month,#material_inventory_invoice_list_form_to_year').change(function(){
		jQuery('#material_inventory_invoice_list_form').submit();
	});
});

function material_inventory_invoice_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_invoice/get_list_json');?>", 
		postData : material_inventory_invoice_list_get_param(),
		colNames: [
			'Id', 
			'No', 
			'Business Partner',
			'Date',
			'Period From',
			'Period To',
			'Handling In',
			'Price Per Unit',
			'Handling Out',
			'Price Per Unit',
			'Handling Storage',
			'Price Per Unit',
			'Calculate',
			'Parts Number',
			'Weight',
			'Amount'
		], 
		colModel: [
			{name:'id', index:'invo.id', hidden:true, frozen:true},
			{name:'code', index:'invo.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:material_inventory_invoice_list_detail(\'' + rowObject.id + '\')">' + cellvalue + '</a>';
			 }
			},
			{name:'c_businesspartner_name', index:'bp.name', width:180, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'invoice_date', index:'invo.invoice_date'}),
			jqgrid_column_date(table_id, {name:'period_from', index:'invo.period_from'}),
			jqgrid_column_date(table_id, {name:'period_to', index:'invo.period_to'}),
			{name:'invoice_handling_in', index:'invo.invoice_handling_in', width:100, formatter:jqgrid_boolean_formatter,
			 stype: 'select',
			 searchoptions:{
				sopt:['eq','ne','bw','bn','lt','le','gt','ge','in','ni','ew','en','cn','nc'],
				clearSearch:false,
				dataUrl: "<?php echo site_url('material/inventory_invoice/get_boolean_dropdown/search_invoice_handling_in');?>"
			 }
			},
			{name:'invoice_handling_in_price', index:'invo.invoice_handling_in_price', width:100, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'invoice_handling_out', index:'invo.invoice_handling_out', width:100, formatter:jqgrid_boolean_formatter,
			 stype: 'select',
			 searchoptions:{
				sopt:['eq','ne','bw','bn','lt','le','gt','ge','in','ni','ew','en','cn','nc'],
				clearSearch:false,
				dataUrl: "<?php echo site_url('material/inventory_invoice/get_boolean_dropdown/search_invoice_handling_out');?>"
			 }
			},
			{name:'invoice_handling_out_price', index:'invo.invoice_handling_out_price', width:100, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'invoice_handling_storage', index:'invo.invoice_handling_storage', width:100, formatter:jqgrid_boolean_formatter,
			 stype: 'select',
			 searchoptions:{
				sopt:['eq','ne','bw','bn','lt','le','gt','ge','in','ni','ew','en','cn','nc'],
				clearSearch:false,
				dataUrl: "<?php echo site_url('material/inventory_invoice/get_boolean_dropdown/search_invoice_handling_storage');?>"
			 }
			},
			{name:'invoice_handling_storage_price', index:'invo.invoice_handling_storage_price', width:100, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'invoice_calculate', index:'invo.invoice_calculate', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($inventory_invoice_calculates);?>}
			},
			{name:'parts_num', index:'parts_num', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'weight', index:'weight', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'amount', index:'amount', width:120, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'invo.invoice_date', 
		sortorder: "desc"
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

function material_inventory_invoice_list_get_param(){
	return {
		from_month	: jQuery('#material_inventory_invoice_list_form_from_month').val(),
		from_year	: jQuery('#material_inventory_invoice_list_form_from_year').val(),
		to_month	: jQuery('#material_inventory_invoice_list_form_to_month').val(),
		to_year		: jQuery('#material_inventory_invoice_list_form_to_year').val()
	};
}

function material_inventory_invoice_list_detail(id){
	var params = jQuery.param({
		id : id
	});
	window.open("<?php echo site_url('material/inventory_invoice/printout');?>?" + params);
}
</script>