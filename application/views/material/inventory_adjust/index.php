<?php
$this->load->helper('date');

$inventory_adjust_types = array_merge(array('' => ''), $this->config->item('inventory_adjust_types'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
<?php 
if (is_authorized('material/inventory_adjust', 'insert')){?>
	<button id="material_inventory_adjust_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('material/inventory_adjust', 'update')){?>
	<button id="material_inventory_adjust_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}?>
	<button id="material_inventory_adjust_detail_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-folder-open"></span>
		<span class="ui-button-text">Enroll</span>
	</button>
<?php
if (is_authorized('material/inventory_adjust', 'delete')){?>
	<button id="material_inventory_adjust_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}?>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('material/inventory_adjust/index',
	array(
		'name'	=> 'material_inventory_adjust_list_form',
		'id'	=> 'material_inventory_adjust_list_form'
	)
);?>
	<label for="material_inventory_adjust_list_form_from_month">Date</label>
<?php 
$previous_periode = add_date(date('Y-m-d'), 0, -1);

$months = $this->config->item('months');
$month_options = array();
foreach($months as $month=>$month_name)
{
	$month_options[$month] = $month_name;
}
echo form_dropdown('from_month', $month_options, date('n', strtotime($previous_periode)), 'id="material_inventory_adjust_list_form_from_month" class="required"');

$year_options = array();
for ($year_begin = date('Y') + 1; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('from_year', $year_options, date('Y', strtotime($previous_periode)), 'id="material_inventory_adjust_list_form_from_year" class="required"');
?>
	<label for="material_inventory_adjust_list_form_to_month">To</label>
<?php 
echo form_dropdown('to_month', $month_options, date('n'), 'id="material_inventory_adjust_list_form_to_month" class="required"');
echo form_dropdown('to_year', $year_options, date('Y'), 'id="material_inventory_adjust_list_form_to_year" class="required"');
echo form_close();?>
</div>

<table id="material_inventory_adjust_list_table"></table>
<div id="material_inventory_adjust_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventory_adjust_list_load_table('material_inventory_adjust_list_table');
	
	jQuery("#material_inventory_adjust_new_btn").click(function(){
		jquery_dialog_form_open('material_inventory_adjust_form_container', "<?php echo site_url('material/inventory_adjust/form');?>", {
			form_action : "material/inventory_adjust/insert"
		}, 
		function(form_dialog){
			material_inventory_adjust_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#material_inventory_adjust_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Adjust", 
			width : 400
		});
	});
	
	jQuery("#material_inventory_adjust_edit_btn").click(function(){
		var id = jQuery("#material_inventory_adjust_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_adjust_list_table").getRowData(id);
			
			jquery_dialog_form_open('material_inventory_adjust_form_container', "<?php echo site_url('material/inventory_adjust/form');?>", {
				form_action : "material/inventory_adjust/update",
				id : row_data.id
			}, 
			function(form_dialog){
				material_inventory_adjust_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#material_inventory_adjust_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Adjust", 
				width : 460
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_inventory_adjust_detail_btn").click(function(){
		var id = jQuery("#material_inventory_adjust_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_adjust_list_table").getRowData(id);
			
			jquery_dialog_form_open('material_inventory_adjust_form_detail_container', "<?php echo site_url('material/inventory_adjust/form_detail');?>", {
				id : row_data.id
			}, 
			null,
			{
				title : "Enroll Adjust", 
				width : 1000
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_inventory_adjust_delete_btn").click(function(){
		var id = jQuery("#material_inventory_adjust_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_adjust_list_table").getRowData(id);
			
			var _delete_url = "<?php echo site_url('material/inventory_adjust/delete/@id');?>";
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
							jQuery("#material_inventory_adjust_list_table").trigger('reloadGrid', [{current:true}]);
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
	
	jQuery('#material_inventory_adjust_list_form').validate({
		submitHandler: function(form){
			jQuery('#material_inventory_adjust_list_table').setGridParam({
				postData : material_inventory_adjust_list_get_param()
			});
			jQuery('#material_inventory_adjust_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#material_inventory_adjust_list_form_from_month,#material_inventory_adjust_list_form_from_year,#material_inventory_adjust_list_form_to_month,#material_inventory_adjust_list_form_to_year').change(function(){
		jQuery('#material_inventory_adjust_list_form').submit();
	});
});

function material_inventory_adjust_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_adjust/get_list_json');?>", 
		postData : material_inventory_adjust_list_get_param(),
		colNames: [
			'Id', 
			'No', 
			'Date',
			'Type',
			'Product',
			'Box From',
			'Box To',
			'Quantity From',
			'Quantity To'
		], 
		colModel: [
			{name:'id', index:'ia.id', hidden:true, frozen:true},
			{name:'code', index:'ia.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:material_inventory_adjust_list_detail(\'' + rowObject.id + '\')">' + cellvalue + '</a>';
			 }
			},
			jqgrid_column_date(table_id, {name:'adjust_date', index:'ia.adjust_date'}),
			{name:'adjust_type', index:'ia.adjust_type', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($inventory_adjust_types);?>}
			},
			{name:'product', index:'product', width:60, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_box_from', index:'quantity_box_from', width:60, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_box_to', index:'quantity_box_to', width:60, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_from', index:'quantity_from', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity_to', index:'quantity_to', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'ia.adjust_date', 
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

function material_inventory_adjust_list_get_param(){
	return {
		from_month	: jQuery('#material_inventory_adjust_list_form_from_month').val(),
		from_year	: jQuery('#material_inventory_adjust_list_form_from_year').val(),
		to_month	: jQuery('#material_inventory_adjust_list_form_to_month').val(),
		to_year		: jQuery('#material_inventory_adjust_list_form_to_year').val()
	};
}

function material_inventory_adjust_list_detail(id){
	jquery_dialog_form_open('material_inventory_adjust_detail_container', "<?php echo site_url('material/inventory_adjust/detail');?>", {
		id : id
	}, null,
	{
		title : "Adjust", 
		width : 1000
	});
}
</script>