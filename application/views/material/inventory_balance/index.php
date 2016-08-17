<?php
$this->load->helper('date');

$transport_modes = array_merge(array('' => ''), $this->config->item('transport_modes'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
<?php 
if (is_authorized('material/inventory_balance', 'insert')){?>
	<button id="material_inventory_balance_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('material/inventory_balance', 'update')){?>
	<button id="material_inventory_balance_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}?>
	<button id="material_inventory_balance_detail_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-folder-open"></span>
		<span class="ui-button-text">Enroll</span>
	</button>
<?php
if (is_authorized('material/inventory_balance', 'delete')){?>
	<button id="material_inventory_balance_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}
if (is_authorized('material/inventory_balance', 'insert')){?>
	<button id="material_inventory_balance_upload_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-arrowthickstop-1-n"></span>
		<span class="ui-button-text">Upload</span>
	</button>
<?php 
}?>
	<button id="material_inventory_balance_printout_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">View Printout</span>
	</button>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('material/inventory_balance/index',
	array(
		'name'	=> 'material_inventory_balance_list_form',
		'id'	=> 'material_inventory_balance_list_form'
	)
);?>
	<label for="material_inventory_balance_list_form_from_month">Date</label>
<?php 
$previous_periode = add_date(date('Y-m-d'), 0, -1);

$months = $this->config->item('months');
$month_options = array();
foreach($months as $month=>$month_name)
{
	$month_options[$month] = $month_name;
}
echo form_dropdown('from_month', $month_options, date('n', strtotime($previous_periode)), 'id="material_inventory_balance_list_form_from_month" class="required"');

$year_options = array();
for ($year_begin = date('Y') + 1; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('from_year', $year_options, date('Y', strtotime($previous_periode)), 'id="material_inventory_balance_list_form_from_year" class="required"');
?>
	<label for="material_inventory_balance_list_form_to_month">To</label>
<?php 
echo form_dropdown('to_month', $month_options, date('n'), 'id="material_inventory_balance_list_form_to_month" class="required"');
echo form_dropdown('to_year', $year_options, date('Y'), 'id="material_inventory_balance_list_form_to_year" class="required"');
echo form_close();?>
</div>

<table id="material_inventory_balance_list_table"></table>
<div id="material_inventory_balance_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventory_balance_list_load_table('material_inventory_balance_list_table');
	
	jQuery("#material_inventory_balance_new_btn").click(function(){
		jquery_dialog_form_open('material_inventory_balance_form_container', "<?php echo site_url('material/inventory_balance/form');?>", {
			form_action : "material/inventory_balance/insert"
		}, 
		function(form_dialog){
			material_inventory_balance_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#material_inventory_balance_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Balance", 
			width : 600
		});
	});
	
	jQuery("#material_inventory_balance_edit_btn").click(function(){
		var id = jQuery("#material_inventory_balance_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_balance_list_table").getRowData(id);
			
			jquery_dialog_form_open('material_inventory_balance_form_container', "<?php echo site_url('material/inventory_balance/form');?>", {
				form_action : "material/inventory_balance/update",
				id : row_data.id
			}, 
			function(form_dialog){
				material_inventory_balance_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#material_inventory_balance_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Balance", 
				width : 600
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_inventory_balance_detail_btn").click(function(){
		var id = jQuery("#material_inventory_balance_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_balance_list_table").getRowData(id);
			
			jquery_dialog_form_open('material_inventory_balance_form_detail_container', "<?php echo site_url('material/inventory_balance/form_detail');?>", {
				id : row_data.id
			}, 
			null,
			{
				title : "Enroll Balance", 
				width : 1015
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_inventory_balance_delete_btn").click(function(){
		var id = jQuery("#material_inventory_balance_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_balance_list_table").getRowData(id);
			
			var _delete_url = "<?php echo site_url('material/inventory_balance/delete/@id');?>";
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
							jQuery("#material_inventory_balance_list_table").trigger('reloadGrid', [{current:true}]);
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
	
	jQuery("#material_inventory_balance_upload_btn").click(function(){
		var id = jQuery("#material_inventory_balance_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_balance_list_table").getRowData(id);
			
			jquery_dialog_form_open('material_inventory_balance_upload_form_container', "<?php echo site_url('material/inventory_balance/form_upload');?>", {
				  form_action : "material/inventory_balance/upload"
				, id : row_data.id
			}, 
			function(form_dialog){
				material_inventory_balance_upload_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						if (data.value)
						{
							jquery_show_message(data.value, "Found an Error", "ui-icon-circle-close", function(){
								form_dialog.dialog("close");
								jQuery("#material_inventory_balance_list_table").trigger("reloadGrid", [{current:true}]);
							});
						}
						else
						{
							form_dialog.dialog("close");
							jQuery("#material_inventory_balance_list_table").trigger("reloadGrid", [{current:true}]);
						}
					}
				});
			},
			{
				title : "Upload Balance"
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_inventory_balance_printout_btn").click(function(){
		var id = jQuery("#material_inventory_balance_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_balance_list_table").getRowData(id);
			window.open("<?php echo site_url('material/inventory_balance/detail_printout');?>?id=" + row_data.id);
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery('#material_inventory_balance_list_form').validate({
		submitHandler: function(form){
			jQuery('#material_inventory_balance_list_table').setGridParam({
				postData : material_inventory_balance_list_get_param()
			});
			jQuery('#material_inventory_balance_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#material_inventory_balance_list_form_from_month,#material_inventory_balance_list_form_from_year,#material_inventory_balance_list_form_to_month,#material_inventory_balance_list_form_to_year').change(function(){
		jQuery('#material_inventory_balance_list_form').submit();
	});
});

function material_inventory_balance_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_balance/get_list_json');?>", 
		postData : material_inventory_balance_list_get_param(),
		colNames: [
			'Id', 
			'No', 
			'Date', 
			'Location',
			'Location Group',
			'Product Size',
			'Quantity', 
			'Harvest Seq.', 
			'PIC', 
			'Vehicle No',
			'Project'
		], 
		colModel: [
			{name:'id', index:'ib.id', hidden:true, frozen:true},
			{name:'code', index:'ib.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:material_inventory_balance_list_detail(\'' + rowObject.id + '\')">' + cellvalue + '</a>';
			 }
			},
			jqgrid_column_date(table_id, {name:'balance_date', index:'ib.balance_date'}),
			{name:'m_grid_code', index:'gri.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_warehouse_name', index:'wh.name', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'product_size', index:'ib.product_size', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'quantity', index:'quantity', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'harvest_sequence', index:'ib.harvest_sequence', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'pic', index:'ib.pic', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'vehicle_no', index:'ib.vehicle_no', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_project_name', index:'prj.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'ib.balance_date', 
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

function material_inventory_balance_list_get_param(){
	return {
		from_month	: jQuery('#material_inventory_balance_list_form_from_month').val(),
		from_year	: jQuery('#material_inventory_balance_list_form_from_year').val(),
		to_month	: jQuery('#material_inventory_balance_list_form_to_month').val(),
		to_year		: jQuery('#material_inventory_balance_list_form_to_year').val()
	};
}

function material_inventory_balance_list_detail(id){
	jquery_dialog_form_open('material_inventory_balance_detail_container', "<?php echo site_url('material/inventory_balance/detail');?>", {
		id : id
	}, null,
	{
		title : "Balance Note", 
		width : 1050
	});
}
</script>