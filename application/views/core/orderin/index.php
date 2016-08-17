<?php
$this->load->helper('date');

$orderin_origins = array_merge(array('' => ''), $this->config->item('orderin_origins'));
$status_inventory_receive = array_merge(array('' => ''), $this->config->item('status_inventory_receive'));?>

<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
<?php 
if (is_authorized('core/orderin', 'insert')){?>
	<button id="core_orderin_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('core/orderin', 'update')){?>
	<button id="core_orderin_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('core/orderin', 'delete')){?>
	<button id="core_orderin_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}
if (is_authorized('core/orderin', 'insert')){?>
	<button id="core_orderin_upload_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-arrowthickstop-1-n"></span>
		<span class="ui-button-text">Upload</span>
	</button>
<?php 
}
if (is_authorized('core/businesspartner', 'index')){?>
	<button id="core_orderin_c_businesspartner_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Business Partner</span>
	</button>
<?php 
}?>
</div>

<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('core/orderin/index',
	array(
		'name'	=> 'core_orderin_list_form',
		'id'	=> 'core_orderin_list_form'
	)
);?>
	<label for="core_orderin_list_form_from_month">Date</label>
<?php 
$previous_periode = add_date(date('Y-m-d'), 0, -1);

$months = $this->config->item('months');
$month_options = array();
foreach($months as $month=>$month_name)
{
	$month_options[$month] = $month_name;
}
echo form_dropdown('from_month', $month_options, date('n', strtotime($previous_periode)), 'id="core_orderin_list_form_from_month" class="required"');

$year_options = array();
for ($year_begin = date('Y') + 1; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('from_year', $year_options, date('Y', strtotime($previous_periode)), 'id="core_orderin_list_form_from_year" class="required"');
?>
	<label for="core_orderin_list_form_to_month">To</label>
<?php 
echo form_dropdown('to_month', $month_options, date('n'), 'id="core_orderin_list_form_to_month" class="required"');
echo form_dropdown('to_year', $year_options, date('Y'), 'id="core_orderin_list_form_to_year" class="required"');
echo form_close();?>
</div>

<table id="core_orderin_list_table"></table>
<div id="core_orderin_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	core_orderin_list_load_table('core_orderin_list_table');
	
	jQuery("#core_orderin_new_btn").click(function(){
		jquery_dialog_form_open('core_orderin_form_container', "<?php echo site_url('core/orderin/form');?>", {
			form_action : "core/orderin/insert"
		}, 
		function(form_dialog){
			core_orderin_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#core_orderin_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Order In", 
			width : 730,
			height: 500
		});
	});
	
	jQuery("#core_orderin_edit_btn").click(function(){
		var id = jQuery("#core_orderin_list_table").getGridParam("selrow");
		if (id)
		{
			jquery_dialog_form_open('core_orderin_form_container', "<?php echo site_url('core/orderin/form');?>", {
				form_action : "core/orderin/update",
				id : id
			}, 
			function(form_dialog){
				core_orderin_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#core_orderin_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Order In", 
				width : 730,
				height: 550
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#core_orderin_delete_btn").click(function(){
		var id = jQuery("#core_orderin_list_table").getGridParam("selrow");
		if (id)
		{
			var _delete_url = "<?php echo site_url('core/orderin/delete/@id');?>";
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
							jQuery("#core_orderin_list_table").trigger('reloadGrid', [{current:true}]);
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
	
	jQuery("#core_orderin_upload_btn").click(function(){
		jquery_dialog_form_open('core_orderin_upload_form_container', "<?php echo site_url('core/orderin/form_upload');?>", {
			form_action : "core/orderin/upload"
		}, 
		function(form_dialog){
			core_orderin_upload_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					if (data.value)
					{
						jquery_show_message(data.value, "Found an Error", "ui-icon-circle-close", function(){
							form_dialog.dialog("close");
							jQuery("#core_orderin_list_table").trigger("reloadGrid", [{current:true}]);
						});
					}
					else
					{
						form_dialog.dialog("close");
						jQuery("#core_orderin_list_table").trigger("reloadGrid", [{current:true}]);
					}
				}
			});
		},
		{
			title : "Upload Order In"
		});
	});
	
	jQuery("#core_orderin_c_businesspartner_btn").click(function(){
		jquery_dialog_form_open('core_orderin_c_businesspartner_form_container', "<?php echo site_url('core/businesspartner/index_simple');?>", null, null, {
			title : "Business Partners", 
			width : 790
		});
	});

	jQuery('#core_orderin_list_form').validate({
		submitHandler: function(form){
			jQuery('#core_orderin_list_table').setGridParam({
				postData : core_orderin_list_get_param()
			});
			jQuery('#core_orderin_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#core_orderin_list_form_from_month,#core_orderin_list_form_from_year,#core_orderin_list_form_to_month,#core_orderin_list_form_to_year').change(function(){
		jQuery('#core_orderin_list_form').submit();
	});
});

function core_orderin_list_load_table(table_id){
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
		url: "<?php echo site_url('core/orderin/get_list_json');?>", 
		postData : core_orderin_list_get_param(),
		colNames: [
			'Id', 
			'No', 
			'Business Partner', 
			'Date',
			'Receive Status',
			'Project', 
			'Origin',
			'Bill of Load',
			'External No'
		], 
		colModel: [
			{name:'id', index:'oi.id', key:true, hidden:true, frozen:true},
			{name:'code', index:'oi.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}
			 , formatter: function (cellvalue, options, rowObject){
				return '<a href="javascript:core_orderin_list_detail(\'' + rowObject.id + '\')">' + cellvalue + '</a>';
			 }
			},
			{name:'c_businesspartner_name', index:'bp.name', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'orderin_date', index:'oi.orderin_date'}),
			{name:'status_inventory_receive', index:'oi.status_inventory_receive', width:110, align:'center', 
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($status_inventory_receive);?>},
			 cellattr: function(rowId, val, rawObject, cm, rdata){
				var color = null;
				if (rawObject.status_inventory_receive == 'NO RECEIVE')
					color = 'red';
				else if (rawObject.status_inventory_receive == 'COMPLETE')
					color = 'green';
				else if (rawObject.status_inventory_receive == 'INCOMPLETE')
					color = 'orange';
				else
					color = 'yellow';
				if (color)
					return ' style="background-color:' + color + '; font-weight:bold;" ';
				else
					return;
			 }
			},
			{name:'c_project_name', index:'prj.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'origin', index:'oi.origin', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($orderin_origins);?>}
			},
			{name:'bol_no', index:'oi.bol_no', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'external_no', index:'oi.external_no', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'oi.orderin_date', 
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

function core_orderin_list_get_param(){
	return {
		from_month	: jQuery('#core_orderin_list_form_from_month').val(),
		from_year	: jQuery('#core_orderin_list_form_from_year').val(),
		to_month	: jQuery('#core_orderin_list_form_to_month').val(),
		to_year		: jQuery('#core_orderin_list_form_to_year').val()
	};
}

function core_orderin_list_detail(id){
	jquery_dialog_form_open('core_orderin_detail_container', "<?php echo site_url('core/orderin/detail');?>", {
		id : id
	}, null,
	{
		title : "Order In", 
		width : 850
	});
}
</script>