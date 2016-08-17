<?php
$this->load->helper('date');

$transport_modes = array_merge(array('' => ''), $this->config->item('transport_modes'));
$status_inventory_inbound = array_merge(array('' => ''), $this->config->item('status_inventory_inbound'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
<?php 
if (is_authorized('material/inventory_receive', 'insert')){?>
	<button id="material_inventory_receive_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('material/inventory_receive', 'update')){?>
	<button id="material_inventory_receive_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('material/inventory_receive', 'delete')){?>
	<button id="material_inventory_receive_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}
if (is_authorized('material/inventory_receive', 'insert')){?>
	<button id="material_inventory_receive_upload_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-arrowthickstop-1-n"></span>
		<span class="ui-button-text">Upload</span>
	</button>
<?php 
}
if (is_authorized('material/inventory_receive', 'insert') && is_authorized('material/inventory_receive', 'update') && is_authorized('material/inventory_receive', 'delete')){?>
	<button id="material_inventory_receive_forecast_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Forecast</span>
	</button>
<?php 
}?>
	<button id="material_inventory_receive_label_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-clipboard"></span>
		<span class="ui-button-text">Label</span>
	</button>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('material/inventory_receive/index',
	array(
		'name'	=> 'material_inventory_receive_list_form',
		'id'	=> 'material_inventory_receive_list_form'
	)
);?>
	<label for="material_inventory_receive_list_form_from_month">Date</label>
<?php 
$previous_periode = add_date(date('Y-m-d'), 0, -1);

$months = $this->config->item('months');
$month_options = array();
foreach($months as $month=>$month_name)
{
	$month_options[$month] = $month_name;
}
echo form_dropdown('from_month', $month_options, date('n', strtotime($previous_periode)), 'id="material_inventory_receive_list_form_from_month" class="required"');

$year_options = array();
for ($year_begin = date('Y') + 1; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('from_year', $year_options, date('Y', strtotime($previous_periode)), 'id="material_inventory_receive_list_form_from_year" class="required"');
?>
	<label for="material_inventory_receive_list_form_to_month">To</label>
<?php 
echo form_dropdown('to_month', $month_options, date('n'), 'id="material_inventory_receive_list_form_to_month" class="required"');
echo form_dropdown('to_year', $year_options, date('Y'), 'id="material_inventory_receive_list_form_to_year" class="required"');
echo form_close();?>
</div>

<table id="material_inventory_receive_list_table"></table>
<div id="material_inventory_receive_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventory_receive_list_load_table('material_inventory_receive_list_table');
	
	jQuery("#material_inventory_receive_new_btn").click(function(){
		jquery_dialog_form_open('material_inventory_receive_form_container', "<?php echo site_url('material/inventory_receive/form');?>", {
			form_action : "material/inventory_receive/insert"
		}, 
		function(form_dialog){
			material_inventory_receive_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#material_inventory_receive_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Receive", 
			width : 830,
			height: 550
		});
	});
	
	jQuery("#material_inventory_receive_edit_btn").click(function(){
		var id = jQuery("#material_inventory_receive_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_receive_list_table").getRowData(id);
			
			jquery_dialog_form_open('material_inventory_receive_form_container', "<?php echo site_url('material/inventory_receive/form');?>", {
				form_action : "material/inventory_receive/update",
				id : row_data.id
			}, 
			function(form_dialog){
				material_inventory_receive_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#material_inventory_receive_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Receive", 
				width : 830,
				height: 550
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_inventory_receive_delete_btn").click(function(){
		var id = jQuery("#material_inventory_receive_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_receive_list_table").getRowData(id);
			
			var _delete_url = "<?php echo site_url('material/inventory_receive/delete/@id');?>";
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
							jQuery("#material_inventory_receive_list_table").trigger('reloadGrid', [{current:true}]);
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
	
	jQuery("#material_inventory_receive_upload_btn").click(function(){
		jquery_dialog_form_open('material_inventory_receive_upload_form_container', "<?php echo site_url('material/inventory_receive/form_upload');?>", {
			form_action : "material/inventory_receive/upload"
		}, 
		function(form_dialog){
			material_inventory_receive_upload_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					if (data.value)
					{
						jquery_show_message(data.value, "Found an Error", "ui-icon-circle-close", function(){
							form_dialog.dialog("close");
							jQuery("#material_inventory_receive_list_table").trigger("reloadGrid", [{current:true}]);
						});
					}
					else
					{
						form_dialog.dialog("close");
						jQuery("#material_inventory_receive_list_table").trigger("reloadGrid", [{current:true}]);
					}
				}
			});
		},
		{
			title : "Upload Receive"
		});
	});
	
	jQuery("#material_inventory_receive_forecast_btn").click(function(){
		var id = jQuery("#material_inventory_receive_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_receive_list_table").getRowData(id);
			material_inventory_receive_forecast_create(row_data.id, 0);
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_inventory_receive_label_btn").click(function(){
		var id = jQuery("#material_inventory_receive_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_receive_list_table").getRowData(id);
			
			var params = jQuery.param({
				id : row_data.id
			});
			window.open("<?php echo site_url('material/inventory_receive/label_document');?>?" + params);
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery('#material_inventory_receive_list_form').validate({
		submitHandler: function(form){
			jQuery('#material_inventory_receive_list_table').setGridParam({
				postData : material_inventory_receive_list_get_param()
			});
			jQuery('#material_inventory_receive_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#material_inventory_receive_list_form_from_month,#material_inventory_receive_list_form_from_year,#material_inventory_receive_list_form_to_month,#material_inventory_receive_list_form_to_year').change(function(){
		jQuery('#material_inventory_receive_list_form').submit();
	});
});

function material_inventory_receive_forecast_create(id, is_force_regenerate){
	var _forecast_create_url = "<?php echo site_url('material/inventory_receive/forecast_create/@id');?>";
	_forecast_create_url = _forecast_create_url.replace(/@id/g, id);
	
	jquery_show_confirm("Are your sure ?", function(){
		jQuery.ajax({
			url: _forecast_create_url,
			type: "POST",
			data: {
				is_force_regenerate : is_force_regenerate
			},
			dataType: "json",
			async : false,
			error: jquery_ajax_error_handler,
			beforeSend: function(jqXHR, settings){
				jquery_blockui();
			},
			success: function(data, textStatus, jqXHR){
				if (data.response == false)
				{
					if (data.value == 'ALREADY_EXISTS')
					{
						jquery_show_confirm("Forecast is already with code " + data.data.code + ", want you regenerate ?", 
							function(){
								material_inventory_receive_forecast_create(id, 1);
							},
							function(){
								var params = jQuery.param({
									id : data.data.id
								});
								window.open("<?php echo site_url('material/inventory_receive/forecast_document');?>?" + params);
							}
						);
					}
					else
						jquery_show_message(data.value, null, "ui-icon-close");
				}
				else
				{
					var params = jQuery.param({
						id : data.value
					});
					window.open("<?php echo site_url('material/inventory_receive/forecast_document');?>?" + params);
				}
			},
			complete: function(jqXHR, textStatus){
				jquery_unblockui();
			}
		});
	});
}

function material_inventory_receive_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_receive/get_list_json');?>", 
		postData : material_inventory_receive_list_get_param(),
		colNames: [
			'Id', 
			'No', 
			'Date', 
			'Order In No', 
			'Order In Date', 
			'Business Partner',
			'Project', 
			'Inbound Status',
			'Vehicle No',
			'Driver',
			'Transport'
		], 
		colModel: [
			{name:'id', index:'ir.id', hidden:true, frozen:true},
			{name:'code', index:'ir.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:material_inventory_receive_list_detail(\'' + rowObject.id + '\')">' + cellvalue + '</a>';
			 }
			},
			jqgrid_column_date(table_id, {name:'receive_date', index:'ir.receive_date'}),
			{name:'c_orderin_code', index:'oi.code', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'c_orderin_date', index:'oi.orderin_date'}),
			{name:'c_businesspartner_name', index:'bp.name', width:180, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_project_name', index:'prj.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'status_inventory_inbound', index:'ir.status_inventory_inbound', width:110, align:'center', 
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($status_inventory_inbound);?>},
			 cellattr: function(rowId, val, rawObject, cm, rdata){
				var color = null;
				if (rawObject.status_inventory_inbound == 'NO INBOUND')
					color = 'red';
				else if (rawObject.status_inventory_inbound == 'COMPLETE')
					color = 'green';
				else if (rawObject.status_inventory_inbound == 'INCOMPLETE')
					color = 'orange';
				else
					color = 'yellow';
				if (color)
					return ' style="background-color:' + color + '; font-weight:bold;" ';
				else
					return;
			 }
			},
			{name:'vehicle_no', index:'ir.vehicle_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'vehicle_driver', index:'ir.vehicle_driver', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'transport_mode', index:'ir.transport_mode', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($transport_modes);?>}
			}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'ir.receive_date', 
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

function material_inventory_receive_list_get_param(){
	return {
		from_month	: jQuery('#material_inventory_receive_list_form_from_month').val(),
		from_year	: jQuery('#material_inventory_receive_list_form_from_year').val(),
		to_month	: jQuery('#material_inventory_receive_list_form_to_month').val(),
		to_year		: jQuery('#material_inventory_receive_list_form_to_year').val()
	};
}

function material_inventory_receive_list_detail(id){
	jquery_dialog_form_open('material_inventory_receive_detail_container', "<?php echo site_url('material/inventory_receive/detail');?>", {
		id : id
	}, null,
	{
		title : "Receive", 
		width : 1020
	});
}
</script>