<?php
$this->load->helper('date');?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
<?php 
if (is_authorized('asset/asset_transfer', 'insert')){?>
	<button id="asset_asset_transfer_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('asset/asset_transfer', 'update')){?>
	<button id="asset_asset_transfer_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}?>
	<button id="asset_asset_transfer_detail_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-folder-open"></span>
		<span class="ui-button-text">Enroll</span>
	</button>
<?php
if (is_authorized('asset/asset_transfer', 'delete')){?>
	<button id="asset_asset_transfer_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}?>
	<button id="asset_asset_transfer_letter_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Letter</span>
	</button>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('asset/asset_transfer/index',
	array(
		'name'	=> 'asset_asset_transfer_list_form',
		'id'	=> 'asset_asset_transfer_list_form'
	)
);?>
	<label for="asset_asset_transfer_list_form_from_month">Date</label>
<?php 
$previous_periode = add_date(date('Y-m-d'), 0, -1);

$months = $this->config->item('months');
$month_options = array();
foreach($months as $month=>$month_name)
{
	$month_options[$month] = $month_name;
}
echo form_dropdown('from_month', $month_options, date('n', strtotime($previous_periode)), 'id="asset_asset_transfer_list_form_from_month" class="required"');

$year_options = array();
for ($year_begin = date('Y') + 1; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('from_year', $year_options, date('Y', strtotime($previous_periode)), 'id="asset_asset_transfer_list_form_from_year" class="required"');
?>
	<label for="asset_asset_transfer_list_form_to_month">To</label>
<?php 
echo form_dropdown('to_month', $month_options, date('n'), 'id="asset_asset_transfer_list_form_to_month" class="required"');
echo form_dropdown('to_year', $year_options, date('Y'), 'id="asset_asset_transfer_list_form_to_year" class="required"');
echo form_close();?>
</div>

<table id="asset_asset_transfer_list_table"></table>
<div id="asset_asset_transfer_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	asset_asset_transfer_list_load_table('asset_asset_transfer_list_table');
	
	jQuery("#asset_asset_transfer_new_btn").click(function(){
		jquery_dialog_form_open('asset_asset_transfer_form_container', "<?php echo site_url('asset/asset_transfer/form');?>", {
			form_action : "asset/asset_transfer/insert"
		}, 
		function(form_dialog){
			asset_asset_transfer_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#asset_asset_transfer_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Transfer", 
			width : 420
		});
	});
	
	jQuery("#asset_asset_transfer_edit_btn").click(function(){
		var id = jQuery("#asset_asset_transfer_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#asset_asset_transfer_list_table").getRowData(id);
			
			jquery_dialog_form_open('asset_asset_transfer_form_container', "<?php echo site_url('asset/asset_transfer/form');?>", {
				form_action : "asset/asset_transfer/update",
				id : row_data.id
			}, 
			function(form_dialog){
				asset_asset_transfer_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#asset_asset_transfer_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Transfer", 
				width : 420
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#asset_asset_transfer_detail_btn").click(function(){
		var id = jQuery("#asset_asset_transfer_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#asset_asset_transfer_list_table").getRowData(id);
			
			jquery_dialog_form_open('asset_asset_transfer_form_detail_container', "<?php echo site_url('asset/asset_transfer/form_detail');?>", {
				id : row_data.id
			}, 
			null,
			{
				title : "Enroll Transfer", 
				width : 1000
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#asset_asset_transfer_delete_btn").click(function(){
		var id = jQuery("#asset_asset_transfer_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#asset_asset_transfer_list_table").getRowData(id);
			
			var _delete_url = "<?php echo site_url('asset/asset_transfer/delete/@id');?>";
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
							jQuery("#asset_asset_transfer_list_table").trigger('reloadGrid', [{current:true}]);
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
	
	jQuery("#asset_asset_transfer_letter_btn").click(function(){
		var id = jQuery("#asset_asset_transfer_list_table").getGridParam("selrow");
		if (id)
		{
			var row_data = jQuery("#asset_asset_transfer_list_table").getRowData(id);
			window.open("<?php echo site_url('asset/asset_transfer/letter');?>?id=" + row_data.id);
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery('#asset_asset_transfer_list_form').validate({
		submitHandler: function(form){
			jQuery('#asset_asset_transfer_list_table').setGridParam({
				postData : asset_asset_transfer_list_get_param()
			});
			jQuery('#asset_asset_transfer_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#asset_asset_transfer_list_form_from_month,#asset_asset_transfer_list_form_from_year,#asset_asset_transfer_list_form_to_month,#asset_asset_transfer_list_form_to_year').change(function(){
		jQuery('#asset_asset_transfer_list_form').submit();
	});
});

function asset_asset_transfer_list_load_table(table_id){
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
		url: "<?php echo site_url('asset/asset_transfer/get_list_json');?>", 
		postData : asset_asset_transfer_list_get_param(),
		colNames: [
			'Id', 
			'No', 
			'Date',
			'Asset',
			'User From',
			'User To',
			'Dept From',
			'Dept To'
		], 
		colModel: [
			{name:'id', index:'at.id', hidden:true, frozen:true},
			{name:'code', index:'at.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:asset_asset_transfer_list_detail(\'' + rowObject.id + '\')">' + cellvalue + '</a>';
			 }
			},
			jqgrid_column_date(table_id, {name:'transfer_date', index:'at.transfer_date'}),
			{name:'asset', index:'asset', width:60, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'c_businesspartner_userfrom_name', index:'bpf.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_userto_name', index:'bpt.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_departmentfrom_name', index:'depf.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_departmentto_name', index:'dept.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'at.transfer_date', 
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

function asset_asset_transfer_list_get_param(){
	return {
		from_month	: jQuery('#asset_asset_transfer_list_form_from_month').val(),
		from_year	: jQuery('#asset_asset_transfer_list_form_from_year').val(),
		to_month	: jQuery('#asset_asset_transfer_list_form_to_month').val(),
		to_year		: jQuery('#asset_asset_transfer_list_form_to_year').val()
	};
}

function asset_asset_transfer_list_detail(id){
	jquery_dialog_form_open('asset_asset_transfer_detail_container', "<?php echo site_url('asset/asset_transfer/detail');?>", {
		id : id
	}, null,
	{
		title : "Transfer", 
		width : 1000
	});
}
</script>