<?php
$this->load->helper('date');?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
<?php 
if (is_authorized('asset/asset_move', 'insert')){?>
	<button id="asset_asset_move_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('asset/asset_move', 'update')){?>
	<button id="asset_asset_move_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}?>
	<button id="asset_asset_move_detail_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-folder-open"></span>
		<span class="ui-button-text">Enroll</span>
	</button>
<?php
if (is_authorized('asset/asset_move', 'delete')){?>
	<button id="asset_asset_move_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}?>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('asset/asset_move/index',
	array(
		'name'	=> 'asset_asset_move_list_form',
		'id'	=> 'asset_asset_move_list_form'
	)
);?>
	<label for="asset_asset_move_list_form_from_month">Date</label>
<?php 
$previous_periode = add_date(date('Y-m-d'), 0, -1);

$months = $this->config->item('months');
$month_options = array();
foreach($months as $month=>$month_name)
{
	$month_options[$month] = $month_name;
}
echo form_dropdown('from_month', $month_options, date('n', strtotime($previous_periode)), 'id="asset_asset_move_list_form_from_month" class="required"');

$year_options = array();
for ($year_begin = date('Y') + 1; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('from_year', $year_options, date('Y', strtotime($previous_periode)), 'id="asset_asset_move_list_form_from_year" class="required"');
?>
	<label for="asset_asset_move_list_form_to_month">To</label>
<?php 
echo form_dropdown('to_month', $month_options, date('n'), 'id="asset_asset_move_list_form_to_month" class="required"');
echo form_dropdown('to_year', $year_options, date('Y'), 'id="asset_asset_move_list_form_to_year" class="required"');
echo form_close();?>
</div>

<table id="asset_asset_move_list_table"></table>
<div id="asset_asset_move_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	asset_asset_move_list_load_table('asset_asset_move_list_table');
	
	jQuery("#asset_asset_move_new_btn").click(function(){
		jquery_dialog_form_open('asset_asset_move_form_container', "<?php echo site_url('asset/asset_move/form');?>", {
			form_action : "asset/asset_move/insert"
		}, 
		function(form_dialog){
			asset_asset_move_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#asset_asset_move_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Move", 
			width : 420
		});
	});
	
	jQuery("#asset_asset_move_edit_btn").click(function(){
		var id = jQuery("#asset_asset_move_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#asset_asset_move_list_table").getRowData(id);
			
			jquery_dialog_form_open('asset_asset_move_form_container', "<?php echo site_url('asset/asset_move/form');?>", {
				form_action : "asset/asset_move/update",
				id : row_data.id
			}, 
			function(form_dialog){
				asset_asset_move_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#asset_asset_move_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Move", 
				width : 420
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#asset_asset_move_detail_btn").click(function(){
		var id = jQuery("#asset_asset_move_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#asset_asset_move_list_table").getRowData(id);
			
			jquery_dialog_form_open('asset_asset_move_form_detail_container', "<?php echo site_url('asset/asset_move/form_detail');?>", {
				id : row_data.id
			}, 
			null,
			{
				title : "Enroll Move", 
				width : 1000
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#asset_asset_move_delete_btn").click(function(){
		var id = jQuery("#asset_asset_move_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#asset_asset_move_list_table").getRowData(id);
			
			var _delete_url = "<?php echo site_url('asset/asset_move/delete/@id');?>";
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
							jQuery("#asset_asset_move_list_table").trigger('reloadGrid', [{current:true}]);
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
	
	jQuery('#asset_asset_move_list_form').validate({
		submitHandler: function(form){
			jQuery('#asset_asset_move_list_table').setGridParam({
				postData : asset_asset_move_list_get_param()
			});
			jQuery('#asset_asset_move_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#asset_asset_move_list_form_from_month,#asset_asset_move_list_form_from_year,#asset_asset_move_list_form_to_month,#asset_asset_move_list_form_to_year').change(function(){
		jQuery('#asset_asset_move_list_form').submit();
	});
});

function asset_asset_move_list_load_table(table_id){
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
		url: "<?php echo site_url('asset/asset_move/get_list_json');?>", 
		postData : asset_asset_move_list_get_param(),
		colNames: [
			'Id', 
			'No', 
			'Date',
			'Asset',
			'From',
			'To'
		], 
		colModel: [
			{name:'id', index:'am.id', hidden:true, frozen:true},
			{name:'code', index:'am.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:asset_asset_move_list_detail(\'' + rowObject.id + '\')">' + cellvalue + '</a>';
			 }
			},
			jqgrid_column_date(table_id, {name:'move_date', index:'am.move_date'}),
			{name:'asset', index:'asset', width:60, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'c_locationfrom_name', index:'locf.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_locationto_name', index:'loct.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'am.move_date', 
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

function asset_asset_move_list_get_param(){
	return {
		from_month	: jQuery('#asset_asset_move_list_form_from_month').val(),
		from_year	: jQuery('#asset_asset_move_list_form_from_year').val(),
		to_month	: jQuery('#asset_asset_move_list_form_to_month').val(),
		to_year		: jQuery('#asset_asset_move_list_form_to_year').val()
	};
}

function asset_asset_move_list_detail(id){
	jquery_dialog_form_open('asset_asset_move_detail_container', "<?php echo site_url('asset/asset_move/detail');?>", {
		id : id
	}, null,
	{
		title : "Move", 
		width : 1000
	});
}
</script>