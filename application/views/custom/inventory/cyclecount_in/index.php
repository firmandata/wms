<?php 
echo form_open('custom/inventory_cyclecount_in/insert',
	array(
		'name'	=> 'custom_inventory_cyclecount_in_form',
		'id'	=> 'custom_inventory_cyclecount_in_form'
	)
);?>
<div id="custom_inventory_cyclecount_in_form_container" class="ui-widget ui-widget-content" style="height:175px;">
	<table class="form-table">
		<tbody>
			<tr><th width="100"><label for="custom_inventory_cyclecount_in_form_sku">SKU</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'sku',
		'id' 		=> 'custom_inventory_cyclecount_in_form_sku',
		'class'		=> 'required'
	)
);?>
				</td>
				<td id="custom_inventory_cyclecount_in_form_counter_label" rowspan="5" style="padding-left:20px; font-size:100px;">
					0
				</td>
			</tr>
			<tr><th><label for="custom_inventory_cyclecount_in_form_barcode">Barcode</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'barcode',
		'id' 		=> 'custom_inventory_cyclecount_in_form_barcode',
		'class'		=> 'required',
		'style'		=> "width:350px"
	)
);?>
				</td>
			</tr>
			<tr><th><label for="custom_inventory_cyclecount_in_form_quantity">Quantity</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'quantity',
		'id' 		=> 'custom_inventory_cyclecount_in_form_quantity',
		'class'		=> 'required number',
		'style'		=> "width:70px"
	)
);?>
				</td>
			</tr>
			<tr><th><label for="custom_inventory_cyclecount_in_form_carton_no">Carton No</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'carton_no',
		'id' 		=> 'custom_inventory_cyclecount_in_form_carton_no',
		'class'		=> 'required'
	)
);?>
				</td>
			</tr>
			<tr><th><label for="custom_inventory_cyclecount_in_form_date_packed">Date Packed</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'date_packed',
		'id' 		=> 'custom_inventory_cyclecount_in_form_date_packed',
		'class'		=> 'required',
		'style'		=> "width:70px"
	)
);?>
				</td>
			</tr>
			<tr><th><label for="custom_inventory_cyclecount_in_form_pallet">Pallet</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'pallet',
		'id' 		=> 'custom_inventory_cyclecount_in_form_pallet',
		'class'		=> 'required'
	)
);?>
				</td>
			</tr>
			<tr>
				<td colspan="3" align="center">
<?php 
if (is_authorized('custom/inventory_cyclecount_in', 'insert')){?>
	<button id="custom_inventory_cyclecount_in_process_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-check"></span>
		<span class="ui-button-text">Process</span>
	</button>
<?php 
}?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php 
echo form_close();?>
<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
<?php 
echo form_open('custom_inventory_cyclecount_in/index',
	array(
		'name'	=> 'custom_inventory_cyclecount_in_list_form',
		'id'	=> 'custom_inventory_cyclecount_in_list_form'
	)
);?>
	<label for="custom_inventory_cyclecount_in_list_form_from">Scan Date</label>
<?php
echo form_input(
	array(
		'name' 	=> 'from',
		'id' 	=> 'custom_inventory_cyclecount_in_list_form_from',
		'class'	=> 'date',
		'style'	=> 'width:75px;',
		'value'	=> $from_date
	)
);?>
	<label for="custom_inventory_cyclecount_in_list_form_to">To</label>
<?php 
echo form_input(
	array(
		'name' 	=> 'to',
		'id' 	=> 'custom_inventory_cyclecount_in_list_form_to',
		'class'	=> 'date',
		'style'	=> 'width:75px;',
		'value'	=> $to_date
	)
);?>
	<button id="custom_inventory_cyclecount_in_excel_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Excel</span>
	</button>
<?php
echo form_close();?>
</div>

<table id="custom_inventory_cyclecount_in_list_table"></table>
<div id="custom_inventory_cyclecount_in_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	custom_inventory_cyclecount_in_list_load_table('custom_inventory_cyclecount_in_list_table');
	
	jQuery("#custom_inventory_cyclecount_in_form").validate({
		submitHandler: function(form){
			jQuery("#custom_inventory_cyclecount_in_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					if (data.response == false)
					{
						jquery_show_message(data.value, null, "ui-icon-close", function(){
							custom_inventory_cyclecount_in_clear_field();
						});
					}
					else
					{
						jQuery("#custom_inventory_cyclecount_in_list_table").trigger('reloadGrid', [{current:true}]);
						custom_inventory_cyclecount_in_clear_field();
					}
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jQuery("#custom_inventory_cyclecount_in_process_btn").click(function(e){
		e.preventDefault();
		jQuery('#custom_inventory_cyclecount_in_form').submit();
	});
	
	jQuery('#custom_inventory_cyclecount_in_list_form').validate({
		submitHandler: function(form){
			jQuery('#custom_inventory_cyclecount_in_list_table').setGridParam({
				postData : custom_inventory_cyclecount_in_list_get_param()
			});
			jQuery('#custom_inventory_cyclecount_in_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#custom_inventory_cyclecount_in_form_sku,#custom_inventory_cyclecount_in_form_barcode').change(function(){
		jQuery.ajax({
			url: "<?php echo site_url('custom/inventory_cyclecount_in/parse_barcode');?>",
			type: "GET",
			dataType: "json",
			async : false,
			data : {
				sku : jQuery('#custom_inventory_cyclecount_in_form_sku').val(),
				barcode : jQuery('#custom_inventory_cyclecount_in_form_barcode').val()
			},
			error: jquery_ajax_error_handler,
			beforeSend: function(jqXHR, settings){
				jquery_blockui();
			},
			success: function(data, textStatus, jqXHR){
				jQuery('#custom_inventory_cyclecount_in_form_quantity').val(data.quantity);
				jQuery('#custom_inventory_cyclecount_in_form_carton_no').val(data.carton_no);
				jQuery('#custom_inventory_cyclecount_in_form_date_packed').val(data.date_packed);
				jQuery('#custom_inventory_cyclecount_in_form_pallet').val(data.pallet);
				
				if (data.quantity != null && data.carton_no != null && data.date_packed != null && data.pallet != null)
					jQuery('#custom_inventory_cyclecount_in_process_btn').focus();
			},
			complete: function(jqXHR, textStatus){
				jquery_unblockui();
			}
		});
	});
	
	jQuery('#custom_inventory_cyclecount_in_list_form_from,#custom_inventory_cyclecount_in_list_form_to').change(function(){
		jQuery('#custom_inventory_cyclecount_in_list_form').submit();
	});
	
	jQuery("#custom_inventory_cyclecount_in_excel_btn").click(function(e){
		e.preventDefault();
		
		var filters = jQuery('#custom_inventory_cyclecount_in_list_table').jqGrid('getGridParam', 'postData');
		filters.rows = 0;
		filters.page = 0;
		
		var params = jQuery.param(filters);
		document.location.href = "<?php echo site_url('custom/inventory_cyclecount_in/get_excel');?>?" + params;
	});
});

function custom_inventory_cyclecount_in_list_load_table(table_id){
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
		url: "<?php echo site_url('custom/inventory_cyclecount_in/get_list_json');?>", 
		postData : custom_inventory_cyclecount_in_list_get_param(),
		colNames: [
			'Id', 
			'Barcode', 
			'SKU', 
			'Description',
			'Quantity',
			'Carton No',
			'Date Packed',
			'Pallet',
			'Date',
			''
		], 
		colModel: [
			{name:'id', index:'cc.id', key:true, hidden:true, frozen:true},
			{name:'barcode', index:'cc.barcode', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'sku', index:'pro.sku', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'description', index:'pro.description', width:220, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity', index:'cc.quantity', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'carton_no', index:'cc.carton_no', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'date_packed', index:'cc.date_packed'}),
			{name:'pallet', index:'cc.pallet', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'updated', index:'cc.updated', search:false}),
			{name:'cmd_action', index:'cmd_action', width:26, search:false, sortable:false}
		],
		afterInsertRow : function(rowid, rowdata, rowelem){
			jQuery("#" + table_id).jqGrid('setRowData', rowid, {
				cmd_action: 
					  '<button onclick="custom_inventory_cyclecount_in_list_remove(\'' + table_id + '\', ' + rowid.toString() + ')" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" style="height:20px;" title="Remove">'
					+ '	<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>'
					+ '	<span class="ui-button-text">Remove</span>'
					+ '</button>'
			});
		},
		pager: '#' + table_id + '_nav', 
		sortname: 'cc.updated', 
		sortorder: "desc",
		loadComplete: function(data){
			var row_total = jQuery("#" + table_id).getGridParam('records');
			jQuery('#custom_inventory_cyclecount_in_form_counter_label').text(row_total);
		}
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
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -341));
}

function custom_inventory_cyclecount_in_list_remove(table_id, id){
	jquery_show_confirm("Are your sure ?", function(){
		var _delete_url = "<?php echo site_url('custom/inventory_cyclecount_in/delete/@id');?>";
		_delete_url = _delete_url.replace(/@id/g, id);
		
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
					jQuery("#" + table_id).trigger('reloadGrid', [{current:true}]);
			},
			complete: function(jqXHR, textStatus){
				jquery_unblockui();
			}
		});
	});
}

function custom_inventory_cyclecount_in_list_get_param(){
	var params = {
		from	: null,
		to		: null
	};
	
	var from = jQuery('#custom_inventory_cyclecount_in_list_form_from').val();
	if (jQuery.trim(from) != '')
	{
		var _date = new Date(getDateFromFormat(from, client_validate_date_format));
		params.from = formatDate(_date, server_client_parse_validate_date_format);
	}
	
	var to = jQuery('#custom_inventory_cyclecount_in_list_form_to').val();
	if (jQuery.trim(to) != '')
	{
		var _date = new Date(getDateFromFormat(to, client_validate_date_format));
		params.to = formatDate(_date, server_client_parse_validate_date_format);
	}
	
	return params;
}

function custom_inventory_cyclecount_in_clear_field(){
	jQuery('#custom_inventory_cyclecount_in_form_sku').val('');
	jQuery('#custom_inventory_cyclecount_in_form_sku').focus();
	jQuery('#custom_inventory_cyclecount_in_form_barcode').val('');
	jQuery('#custom_inventory_cyclecount_in_form_quantity').val('');
	jQuery('#custom_inventory_cyclecount_in_form_carton_no').val('');
	jQuery('#custom_inventory_cyclecount_in_form_date_packed').val('');
	jQuery('#custom_inventory_cyclecount_in_form_pallet').val('');
}
</script>