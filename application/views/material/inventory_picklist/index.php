<?php
$this->load->helper('date');

$status_inventory_picking = array_merge(array('' => ''), $this->config->item('status_inventory_picking'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
<?php 
if (is_authorized('material/inventory_picklist', 'insert')){?>
	<button id="material_inventory_picklist_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('material/inventory_picklist', 'update')){?>
	<button id="material_inventory_picklist_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}?>
	<button id="material_inventory_picklist_detail_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-folder-open"></span>
		<span class="ui-button-text">Enroll</span>
	</button>
<?php
if (is_authorized('material/inventory_picklist', 'delete')){?>
	<button id="material_inventory_picklist_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}?>
	<button id="material_inventory_picklist_picking_document_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Picking Document</span>
	</button>
	<button id="material_inventory_picklist_tally_sheet_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Tally Sheet</span>
	</button>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('material/inventory_picklist/index',
	array(
		'name'	=> 'material_inventory_picklist_list_form',
		'id'	=> 'material_inventory_picklist_list_form'
	)
);?>
	<label for="material_inventory_picklist_list_form_from_month">Date</label>
<?php 
$previous_periode = add_date(date('Y-m-d'), 0, -1);

$months = $this->config->item('months');
$month_options = array();
foreach($months as $month=>$month_name)
{
	$month_options[$month] = $month_name;
}
echo form_dropdown('from_month', $month_options, date('n', strtotime($previous_periode)), 'id="material_inventory_picklist_list_form_from_month" class="required"');

$year_options = array();
for ($year_begin = date('Y') + 1; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('from_year', $year_options, date('Y', strtotime($previous_periode)), 'id="material_inventory_picklist_list_form_from_year" class="required"');
?>
	<label for="material_inventory_picklist_list_form_to_month">To</label>
<?php 
echo form_dropdown('to_month', $month_options, date('n'), 'id="material_inventory_picklist_list_form_to_month" class="required"');
echo form_dropdown('to_year', $year_options, date('Y'), 'id="material_inventory_picklist_list_form_to_year" class="required"');
echo form_close();?>
</div>

<table id="material_inventory_picklist_list_table"></table>
<div id="material_inventory_picklist_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventory_picklist_list_load_table('material_inventory_picklist_list_table');
	
	jQuery("#material_inventory_picklist_new_btn").click(function(){
		jquery_dialog_form_open('material_inventory_picklist_form_container', "<?php echo site_url('material/inventory_picklist/form');?>", {
			form_action : "material/inventory_picklist/insert"
		}, 
		function(form_dialog){
			material_inventory_picklist_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#material_inventory_picklist_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Pick List", 
			width : 350
		});
	});
	
	jQuery("#material_inventory_picklist_edit_btn").click(function(){
		var id = jQuery("#material_inventory_picklist_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_picklist_list_table").getRowData(id);
			
			jquery_dialog_form_open('material_inventory_picklist_form_container', "<?php echo site_url('material/inventory_picklist/form');?>", {
				form_action : "material/inventory_picklist/update",
				id : row_data.id
			}, 
			function(form_dialog){
				material_inventory_picklist_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#material_inventory_picklist_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Pick List", 
				width : 350
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_inventory_picklist_detail_btn").click(function(){
		var id = jQuery("#material_inventory_picklist_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_picklist_list_table").getRowData(id);
			
			jquery_dialog_form_open('material_inventory_picklist_form_detail_container', "<?php echo site_url('material/inventory_picklist/form_detail');?>", {
				id : row_data.id
			}, 
			null,
			{
				title : "Enroll Pick List", 
				width : 1015
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_inventory_picklist_delete_btn").click(function(){
		var id = jQuery("#material_inventory_picklist_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_picklist_list_table").getRowData(id);
			
			var _delete_url = "<?php echo site_url('material/inventory_picklist/delete/@id');?>";
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
							jQuery("#material_inventory_picklist_list_table").trigger('reloadGrid', [{current:true}]);
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
	
	jQuery('#material_inventory_picklist_list_form').validate({
		submitHandler: function(form){
			jQuery('#material_inventory_picklist_list_table').setGridParam({
				postData : material_inventory_picklist_list_get_param()
			});
			jQuery('#material_inventory_picklist_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#material_inventory_picklist_list_form_from_month,#material_inventory_picklist_list_form_from_year,#material_inventory_picklist_list_form_to_month,#material_inventory_picklist_list_form_to_year').change(function(){
		jQuery('#material_inventory_picklist_list_form').submit();
	});
	
	jQuery("#material_inventory_picklist_picking_document_btn").click(function(){
		var id = jQuery("#material_inventory_picklist_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_picklist_list_table").getRowData(id);
			window.open("<?php echo site_url('material/inventory_picklist/picking_document');?>?id=" + row_data.id + "&c_orderout_id=" + row_data.c_orderout_id);
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_inventory_picklist_tally_sheet_btn").click(function(){
		var id = jQuery("#material_inventory_picklist_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#material_inventory_picklist_list_table").getRowData(id);
			
			var params = jQuery.param({
				id : row_data.id
			});
			window.open("<?php echo site_url('material/inventory_picklist/tally_sheet');?>?" + params);
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
});

function material_inventory_picklist_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_picklist/get_list_json');?>", 
		postData : material_inventory_picklist_list_get_param(),
		colNames: [
			'Id', 
			'Order Out Id', 
			'No', 
			'Date', 
			'Order Out No', 
			'Order Out Date',
			'Request Arrival', 
			'Business Partner',
			'Project',
			'Picking Status'
		], 
		colModel: [
			{name:'id', index:'ipl.id', hidden:true, frozen:true},
			{name:'c_orderout_id', index:'ood.c_orderout_id', hidden:true, frozen:true},
			{name:'code', index:'ipl.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:material_inventory_picklist_list_detail(\'' + rowObject.id + '\')">' + cellvalue + '</a>';
			 }
			},
			jqgrid_column_date(table_id, {name:'picklist_date', index:'ipl.picklist_date'}),
			{name:'c_orderout_code', index:'oo.code', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'c_orderout_date', index:'oo.orderout_date'}),
			jqgrid_column_date(table_id, {name:'c_orderout_request_arrive_date', index:'oo.request_arrive_date'}),
			{name:'c_businesspartner_name', index:'bp.name', width:180, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_project_name', index:'prj.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'status_inventory_picking', index:'ipl.status_inventory_picking', width:110, align:'center', 
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($status_inventory_picking);?>},
			 cellattr: function(rowId, val, rawObject, cm, rdata){
				var color = null;
				if (rawObject.status_inventory_picking == 'NO PICKING')
					color = 'red';
				else if (rawObject.status_inventory_picking == 'COMPLETE')
					color = 'green';
				else if (rawObject.status_inventory_picking == 'INCOMPLETE')
					color = 'orange';
				else
					color = 'yellow';
				if (color)
					return ' style="background-color:' + color + '; font-weight:bold;" ';
				else
					return;
			 }
			}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'ipl.picklist_date', 
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

function material_inventory_picklist_list_get_param(){
	return {
		from_month	: jQuery('#material_inventory_picklist_list_form_from_month').val(),
		from_year	: jQuery('#material_inventory_picklist_list_form_from_year').val(),
		to_month	: jQuery('#material_inventory_picklist_list_form_to_month').val(),
		to_year		: jQuery('#material_inventory_picklist_list_form_to_year').val()
	};
}

function material_inventory_picklist_list_detail(id){
	jquery_dialog_form_open('material_inventory_picklist_detail_container', "<?php echo site_url('material/inventory_picklist/detail');?>", {
		id : id
	}, null,
	{
		title : "Pick List", 
		width : 1000
	});
}
</script>