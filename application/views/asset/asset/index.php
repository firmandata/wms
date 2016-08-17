<?php
$this->load->helper('date');

$currencies = array_merge(array('' => ''), $this->config->item('currencies'));
$asset_types = array_merge(array('' => ''), $this->config->item('asset_types'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix content-commandbar">
<?php 
if (is_authorized('asset/asset', 'insert')){?>
	<button id="asset_asset_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('asset/asset', 'update')){?>
	<button id="asset_asset_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('asset/asset', 'delete')){?>
	<button id="asset_asset_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}?>
	<button id="asset_asset_barcode_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-tag"></span>
		<span class="ui-button-text">Barcode</span>
	</button>
	<button id="asset_asset_letter_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Letter</span>
	</button>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('asset/asset/index',
	array(
		'name'	=> 'asset_asset_list_form',
		'id'	=> 'asset_asset_list_form'
	)
);?>
	<label for="asset_asset_list_form_from_month">Date</label>
<?php 
$previous_periode = add_date(date('Y-m-d'), 0, -1);

$months = $this->config->item('months');
$month_options = array();
foreach($months as $month=>$month_name)
{
	$month_options[$month] = $month_name;
}
echo form_dropdown('from_month', $month_options, date('n', strtotime($previous_periode)), 'id="asset_asset_list_form_from_month" class="required"');

$year_options = array();
for ($year_begin = date('Y') + 1; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('from_year', $year_options, date('Y', strtotime($previous_periode)), 'id="asset_asset_list_form_from_year" class="required"');
?>
	<label for="asset_asset_list_form_to_month">To</label>
<?php 
echo form_dropdown('to_month', $month_options, date('n'), 'id="asset_asset_list_form_to_month" class="required"');
echo form_dropdown('to_year', $year_options, date('Y'), 'id="asset_asset_list_form_to_year" class="required"');
echo form_close();?>
</div>

<table id="asset_asset_list_table"></table>
<div id="asset_asset_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	asset_asset_list_load_table('asset_asset_list_table');
	
	jQuery("#asset_asset_new_btn").click(function(){
		jquery_dialog_form_open('asset_asset_form_container', "<?php echo site_url('asset/asset/form');?>", {
			form_action : "asset/asset/insert"
		}, 
		function(form_dialog){
			asset_asset_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#asset_asset_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Asset", 
			width : 800
		});
	});
	
	jQuery("#asset_asset_edit_btn").click(function(){
		var id = jQuery("#asset_asset_list_table").getGridParam("selrow");
		if (id)
		{
			jquery_dialog_form_open('asset_asset_form_container', "<?php echo site_url('asset/asset/form');?>", {
				form_action : "asset/asset/update",
				id : id
			}, 
			function(form_dialog){
				asset_asset_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#asset_asset_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Asset", 
				width : 800
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#asset_asset_delete_btn").click(function(){
		var ids = jQuery("#asset_asset_list_table").getGridParam("selarrrow");
		if (ids.length > 0)
		{
			jquery_show_confirm("Are your sure to delete the selected row" + (ids.length > 1 ? "s" : "") + " ?", function(){
				jQuery.ajax({
					url: "<?php echo site_url('asset/asset/delete_by_ids');?>",
					data : {
						ids : ids
					},
					type: "POST",
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
							jQuery("#asset_asset_list_table").trigger('reloadGrid', [{current:true}]);
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
	
	jQuery("#asset_asset_barcode_btn").click(function(){
		var ids = jQuery("#asset_asset_list_table").getGridParam("selarrrow");
		if (ids.length > 0)
		{
			var url_param_encode = jQuery.param({ids : ids});
			var url_param_decode = decodeURIComponent(url_param_encode);
			window.open("<?php echo site_url('asset/asset/barcode_label');?>?" + url_param_decode);
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#asset_asset_letter_btn").click(function(){
		var ids = jQuery("#asset_asset_list_table").getGridParam("selarrrow");
		if (ids.length > 0)
		{
			var url_param_encode = jQuery.param({ids : ids});
			var url_param_decode = decodeURIComponent(url_param_encode);
			window.open("<?php echo site_url('asset/asset/letter');?>?" + url_param_decode);
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery('#asset_asset_list_form').validate({
		submitHandler: function(form){
			jQuery('#asset_asset_list_table').setGridParam({
				postData : asset_asset_list_get_param()
			});
			jQuery('#asset_asset_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#asset_asset_list_form_from_month,#asset_asset_list_form_from_year,#asset_asset_list_form_to_month,#asset_asset_list_form_to_year').change(function(){
		jQuery('#asset_asset_list_form').submit();
	});
});

function asset_asset_list_load_table(table_id){
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
		url: "<?php echo site_url('asset/asset/get_list_json');?>", 
		postData : asset_asset_list_get_param(),
		hidegrid: false,
		multiselect: true,
		colNames: [
			'Id', 
			'Code',
			'Name',
			'Voucher No',
			'Product',
			'Type',
			'Region',
			'Department',
			'Location',
			'User',
			'Supplier',
			'Purchase Date',
			'Currency',
			'Purchase Price',
			'Quantity'
		], 
		colModel: [
			{name:'id', index:'ast.id', key:true, hidden:true, frozen:true},
			{name:'code', index:'ast.code', width:80, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:asset_asset_detail(\'' + rowObject.id + '\')">' + cellvalue + '</a>';
			 }
			},
			{name:'name', index:'ast.name', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'voucher_no', index:'ast.voucher_no', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'type', index:'ast.type', width:75, align:'center',
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($asset_types);?>}
			},
			{name:'c_region_name', index:'rgn.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_department_name', index:'dep.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_location_name', index:'loc.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_user_name', index:'bp_user.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_supplier_name', index:'bp_sup.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'purchase_date', index:'ast.purchase_date'}),
			{name:'currency', index:'ast.currency', width:75, align:'center',
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($currencies);?>}
			},
			{name:'purchase_price', index:'ast.purchase_price', width:110, formatter:'number', formatoptions:{decimalPlaces: 2}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, align:'right'},
			{name:'quantity', index:'ast.quantity', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}, align:'right'}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'ast.code', 
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
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -193));
}

function asset_asset_list_get_param(){
	return {
		from_month	: jQuery('#asset_asset_list_form_from_month').val(),
		from_year	: jQuery('#asset_asset_list_form_from_year').val(),
		to_month	: jQuery('#asset_asset_list_form_to_month').val(),
		to_year		: jQuery('#asset_asset_list_form_to_year').val()
	};
}

function asset_asset_detail(id){
	jquery_dialog_form_open('asset_asset_detail_container', "<?php echo site_url('asset/asset/detail');?>", {
		id : id
	}, null,
	{
		title : "Asset View", 
		width : 800
	});
}
</script>