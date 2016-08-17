<?php
$this->load->helper('date');?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
<?php 
if (is_authorized('material/inventory_sample', 'insert')){?>
	<button id="material_inventory_sample_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('material/inventory_sample', 'update')){?>
	<button id="material_inventory_sample_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('material/inventory_sample', 'delete')){?>
	<button id="material_inventory_sample_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}?>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('material/inventory_sample/index',
	array(
		'name'	=> 'material_inventory_sample_list_form',
		'id'	=> 'material_inventory_sample_list_form'
	)
);?>
	<label for="material_inventory_sample_list_form_from_month">Date</label>
<?php 
$previous_periode = add_date(date('Y-m-d'), 0, -1);

$months = $this->config->item('months');
$month_options = array();
foreach($months as $month=>$month_name)
{
	$month_options[$month] = $month_name;
}
echo form_dropdown('from_month', $month_options, date('n', strtotime($previous_periode)), 'id="material_inventory_sample_list_form_from_month" class="required"');

$year_options = array();
for ($year_begin = date('Y') + 1; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('from_year', $year_options, date('Y', strtotime($previous_periode)), 'id="material_inventory_sample_list_form_from_year" class="required"');
?>
	<label for="material_inventory_sample_list_form_to_month">To</label>
<?php 
echo form_dropdown('to_month', $month_options, date('n'), 'id="material_inventory_sample_list_form_to_month" class="required"');
echo form_dropdown('to_year', $year_options, date('Y'), 'id="material_inventory_sample_list_form_to_year" class="required"');
echo form_close();?>
</div>

<table id="material_inventory_sample_list_table"></table>
<div id="material_inventory_sample_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventory_sample_list_load_table('material_inventory_sample_list_table');
	
	jQuery("#material_inventory_sample_new_btn").click(function(){
		jquery_dialog_form_open('material_inventory_sample_form_container', "<?php echo site_url('material/inventory_sample/form');?>", {
			form_action : "material/inventory_sample/insert"
		}, 
		function(form_dialog){
			material_inventory_sample_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#material_inventory_sample_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Sample", 
			width : 1000,
			height: 600
		});
	});
	
	jQuery("#material_inventory_sample_edit_btn").click(function(){
		var id = jQuery("#material_inventory_sample_list_table").getGridParam("selrow");
		if (id)
		{
			jquery_dialog_form_open('material_inventory_sample_form_container', "<?php echo site_url('material/inventory_sample/form');?>", {
				form_action : "material/inventory_sample/update",
				id : id
			}, 
			function(form_dialog){
				material_inventory_sample_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#material_inventory_sample_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Sample", 
				width : 1000,
				height: 600
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_inventory_sample_delete_btn").click(function(){
		var id = jQuery("#material_inventory_sample_list_table").getGridParam("selrow");
		if (id)
		{
			var _delete_url = "<?php echo site_url('material/inventory_sample/delete/@id');?>";
			_delete_url = _delete_url.replace(/@id/g, id);
			
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
							jQuery("#material_inventory_sample_list_table").trigger('reloadGrid', [{current:true}]);
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
	
	jQuery('#material_inventory_sample_list_form').validate({
		submitHandler: function(form){
			jQuery('#material_inventory_sample_list_table').setGridParam({
				postData : material_inventory_sample_list_get_param()
			});
			jQuery('#material_inventory_sample_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#material_inventory_sample_list_form_from_month,#material_inventory_sample_list_form_from_year,#material_inventory_sample_list_form_to_month,#material_inventory_sample_list_form_to_year').change(function(){
		jQuery('#material_inventory_sample_list_form').submit();
	});
});

function material_inventory_sample_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_sample/get_list_json');?>", 
		postData : material_inventory_sample_list_get_param(),
		colNames: [
			'Id', 
			'No', 
			'Supervisor', 
			'Date',
			'Project'
		], 
		colModel: [
			{name:'id', index:'isa.id', key:true, hidden:true, frozen:true},
			{name:'code', index:'isa.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:material_inventory_sample_list_detail(\'' + rowObject.id + '\')">' + cellvalue + '</a>';
			 }
			},
			{name:'supervisor', index:'isa.supervisor', width:180, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'sampling_date', index:'isa.sampling_date'}),
			{name:'c_project_name', index:'prj.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'isa.sampling_date', 
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

function material_inventory_sample_list_get_param(){
	return {
		from_month	: jQuery('#material_inventory_sample_list_form_from_month').val(),
		from_year	: jQuery('#material_inventory_sample_list_form_from_year').val(),
		to_month	: jQuery('#material_inventory_sample_list_form_to_month').val(),
		to_year		: jQuery('#material_inventory_sample_list_form_to_year').val()
	};
}

function material_inventory_sample_list_detail(id){
	jquery_dialog_form_open('material_inventory_sample_detail_container', "<?php echo site_url('material/inventory_sample/detail');?>", {
		id : id
	}, null,
	{
		title : "Sample", 
		width : 900
	});
}
</script>