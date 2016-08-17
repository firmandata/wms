<?php
$product_conditions = array_merge(array('' => ''), $this->config->item('product_conditions'));
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));

echo form_open('custom/inventory_inbound/insert',
	array(
		'name'	=> 'custom_inventory_inbound_form',
		'id'	=> 'custom_inventory_inbound_form'
	)
);?>
<div id="custom_inventory_inbound_form_container" class="ui-widget ui-widget-content" style="height:190px;">
	<table>
		<tr>
			<td colspan="2">
				<table class="form-table">
					<tbody>
						<tr><th width="100"><label for="custom_inventory_inbound_form_code">Product Code</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'product_code',
		'id' 		=> 'custom_inventory_inbound_form_product_code',
		'class'		=> 'required'
	)
);?>
							</td>
						</tr>
						<tr><th><label for="custom_inventory_inbound_form_barcode">Barcode</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'barcode',
		'id' 		=> 'custom_inventory_inbound_form_barcode',
		'class'		=> 'required',
		'style'		=> "width:350px"
	)
);?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td rowspan="3" id="custom_inventory_inbound_form_counter_label" style="padding-left:50px; font-size:100px;">
				0
			</td>
		</tr>
		<tr>
			<td valign="top">
				<table class="form-table">
					<tbody>
						<tr><th width="100"><label for="custom_inventory_inbound_form_quantity">Quantity</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'quantity',
		'id' 		=> 'custom_inventory_inbound_form_quantity',
		'class'		=> 'required number',
		'style'		=> "width:70px"
	)
);?>
							</td>
						</tr>
						<tr><th><label for="custom_inventory_inbound_form_carton_no">Carton No</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'carton_no',
		'id' 		=> 'custom_inventory_inbound_form_carton_no',
		'class'		=> 'required'
	)
);?>
							</td>
						</tr>
						<tr><th><label for="custom_inventory_inbound_form_packed_date">Packed Date</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'packed_date',
		'id' 		=> 'custom_inventory_inbound_form_packed_date',
		'style'		=> "width:70px"
	)
);?>
								ymd(<?php echo date('ymd');?>)
							</td>
						</tr>
						<tr><th><label for="custom_inventory_inbound_form_expired_date">Expired Date</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'expired_date',
		'id' 		=> 'custom_inventory_inbound_form_expired_date',
		'style'		=> "width:70px"
	)
);?>
								ymd(<?php echo date('ymd');?>)
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td valign="top">
				<table class="form-table">
					<tbody>
						<tr><th width="100"><label for="custom_inventory_inbound_form_pallet">Pallet</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'pallet',
		'id' 		=> 'custom_inventory_inbound_form_pallet',
		'class'		=> 'required',
		'style'		=> "width:180px"
	)
);?>
							</td>
						</tr>
						<tr><th><label for="custom_inventory_inbound_form_grid_code">Grid</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'grid_code',
		'id' 		=> 'custom_inventory_inbound_form_grid_code',
		'style'		=> "width:80px"
	)
);?>
							</td>
						</tr>
						<tr><th><label for="custom_inventory_inbound_form_lot_no">Lot No</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'lot_no',
		'id' 		=> 'custom_inventory_inbound_form_lot_no',
		'style'		=> "width:100px"
	)
);?>
							</td>
						</tr>
						<tr>
							<th><label for="custom_inventory_inbound_form_condition">condition</label></th>
							<td>
<?php 
echo form_dropdown('condition', $product_conditions, '', 'id="custom_inventory_inbound_form_condition"');?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
<?php 
if (is_authorized('custom/inventory_inbound', 'insert')){?>
				<button id="custom_inventory_inbound_process_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
					<span class="ui-button-icon-primary ui-icon ui-icon-check"></span>
					<span class="ui-button-text">Process</span>
				</button>
<?php 
}?>
			</td>
		</tr>
	</table>
</div>
<?php 
echo form_close();?>
<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
<?php 
echo form_open('custom/inventory_inbound/index',
	array(
		'name'	=> 'custom_inventory_inbound_list_form',
		'id'	=> 'custom_inventory_inbound_list_form'
	)
);?>
	<label for="custom_inventory_inbound_list_form_from">Scan Date</label>
<?php
echo form_input(
	array(
		'name' 	=> 'from',
		'id' 	=> 'custom_inventory_inbound_list_form_from',
		'class'	=> 'date',
		'style'	=> 'width:75px;',
		'value'	=> $from_date
	)
);?>
	<label for="custom_inventory_inbound_list_form_to">To</label>
<?php 
echo form_input(
	array(
		'name' 	=> 'to',
		'id' 	=> 'custom_inventory_inbound_list_form_to',
		'class'	=> 'date',
		'style'	=> 'width:75px;',
		'value'	=> $to_date
	)
);?>
	<button id="custom_inventory_inbound_excel_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Excel</span>
	</button>
<?php
echo form_close();?>
</div>

<table id="custom_inventory_inbound_list_table"></table>
<div id="custom_inventory_inbound_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	custom_inventory_inbound_list_load_table('custom_inventory_inbound_list_table');
	
	jQuery("#custom_inventory_inbound_form").validate({
		submitHandler: function(form){
			jQuery("#custom_inventory_inbound_form").ajaxSubmit({
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
							//custom_inventory_inbound_clear_field();
						});
					}
					else
					{
						jQuery("#custom_inventory_inbound_list_table").trigger('reloadGrid', [{current:true}]);
						custom_inventory_inbound_clear_field();
					}
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jQuery("#custom_inventory_inbound_process_btn").click(function(e){
		e.preventDefault();
		jQuery('#custom_inventory_inbound_form').submit();
	});
	
	jQuery('#custom_inventory_inbound_list_form').validate({
		submitHandler: function(form){
			jQuery('#custom_inventory_inbound_list_table').setGridParam({
				postData : custom_inventory_inbound_list_get_param()
			});
			jQuery('#custom_inventory_inbound_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#custom_inventory_inbound_form_product_code,#custom_inventory_inbound_form_barcode').change(function(){
		jQuery.ajax({
			url: "<?php echo site_url('custom/inventory_inbound/parse_barcode');?>",
			type: "GET",
			dataType: "json",
			async : false,
			data : {
				product_code	: jQuery('#custom_inventory_inbound_form_product_code').val(),
				barcode			: jQuery('#custom_inventory_inbound_form_barcode').val()
			},
			error: jquery_ajax_error_handler,
			beforeSend: function(jqXHR, settings){
				jquery_blockui();
			},
			success: function(data, textStatus, jqXHR){
				jQuery('#custom_inventory_inbound_form_quantity').val(data.quantity);
				jQuery('#custom_inventory_inbound_form_carton_no').val(data.carton_no);
				jQuery('#custom_inventory_inbound_form_packed_date').val(data.packed_date);
				
				if (data.quantity != null && data.carton_no != null && data.packed_date != null)
					jQuery('#custom_inventory_inbound_form_expired_date').focus();
				else if (data.quantity != null && data.carton_no == null && data.packed_date != null)
					jQuery('#custom_inventory_inbound_form_carton_no').focus();
				else if (data.quantity != null && data.carton_no != null && data.packed_date == null)
					jQuery('#custom_inventory_inbound_form_packed_date').focus();
			},
			complete: function(jqXHR, textStatus){
				jquery_unblockui();
			}
		});
	});
	
	jQuery('#custom_inventory_inbound_list_form_from,#custom_inventory_inbound_list_form_to').change(function(){
		jQuery('#custom_inventory_inbound_list_form').submit();
	});
	
	jQuery("#custom_inventory_inbound_excel_btn").click(function(e){
		e.preventDefault();
		
		var filters = jQuery('#custom_inventory_inbound_list_table').jqGrid('getGridParam', 'postData');
		filters.rows = 0;
		filters.page = 0;
		
		var params = jQuery.param(filters);
		document.location.href = "<?php echo site_url('custom/inventory_inbound/get_excel');?>?" + params;
	});
});

function custom_inventory_inbound_list_load_table(table_id){
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
		url: "<?php echo site_url('custom/inventory_inbound/get_list_json');?>", 
		postData : custom_inventory_inbound_list_get_param(),
		colNames: [
			'Id', 
			'Barcode', 
			'Product Code', 
			'Name',
			'Quantity',
			'UOM',
			'Carton No',
			'Packed Date',
			'Expired Date',
			'Pallet',
			'Grid',
			'Lot No',
			'Condition',
			'Scan Date',
			''
		], 
		colModel: [
			{name:'id', index:'iid.id', key:true, hidden:true, frozen:true},
			{name:'barcode', index:'iid.barcode', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'product_code', index:'pro.code', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'product_name', index:'pro.name', width:220, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity', index:'iid.quantity', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'product_uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'carton_no', index:'iid.carton_no', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'packed_date', index:'iid.packed_date'}),
			jqgrid_column_date(table_id, {name:'expired_date', index:'iid.expired_date'}),
			{name:'pallet', index:'iid.pallet', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'grid_code', index:'gri.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'lot_no', index:'iid.lot_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'condition', index:'iid.condition', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_conditions);?>}
			},
			jqgrid_column_date(table_id, {name:'created', index:'iid.created', search:false}),
			{name:'cmd_action', index:'cmd_action', width:26, search:false, sortable:false}
		],
		afterInsertRow : function(rowid, rowdata, rowelem){
			jQuery("#" + table_id).jqGrid('setRowData', rowid, {
				cmd_action: 
					  '<button onclick="custom_inventory_inbound_list_remove(\'' + table_id + '\', ' + rowid.toString() + ')" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" style="height:20px;" title="Remove">'
					+ '	<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>'
					+ '	<span class="ui-button-text">Remove</span>'
					+ '</button>'
			});
		},
		pager: '#' + table_id + '_nav', 
		sortname: 'iid.created', 
		sortorder: "desc",
		loadComplete: function(data){
			var row_total = jQuery("#" + table_id).getGridParam('records');
			jQuery('#custom_inventory_inbound_form_counter_label').text(row_total);
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
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -357));
}

function custom_inventory_inbound_list_remove(table_id, id){
	jquery_show_confirm("Are your sure ?", function(){
		var _delete_url = "<?php echo site_url('custom/inventory_inbound/delete/@id');?>";
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

function custom_inventory_inbound_list_get_param(){
	var params = {
		from	: null,
		to		: null
	};
	
	var from = jQuery('#custom_inventory_inbound_list_form_from').val();
	if (jQuery.trim(from) != '')
	{
		var _date = new Date(getDateFromFormat(from, client_validate_date_format));
		params.from = formatDate(_date, server_client_parse_validate_date_format);
	}
	
	var to = jQuery('#custom_inventory_inbound_list_form_to').val();
	if (jQuery.trim(to) != '')
	{
		var _date = new Date(getDateFromFormat(to, client_validate_date_format));
		params.to = formatDate(_date, server_client_parse_validate_date_format);
	}
	
	return params;
}

function custom_inventory_inbound_clear_field(){
	jQuery('#custom_inventory_inbound_form_product_code').val('');
	jQuery('#custom_inventory_inbound_form_product_code').focus();
	jQuery('#custom_inventory_inbound_form_barcode').val('');
	jQuery('#custom_inventory_inbound_form_quantity').val('');
	jQuery('#custom_inventory_inbound_form_grid_code').val('');
	jQuery('#custom_inventory_inbound_form_carton_no').val('');
	jQuery('#custom_inventory_inbound_form_packed_date').val('');
	jQuery('#custom_inventory_inbound_form_expired_date').val('');
	jQuery('#custom_inventory_inbound_form_pallet').val('');
	jQuery('#custom_inventory_inbound_form_lot_no').val('');
	jQuery('#custom_inventory_inbound_form_condition').val('');
}
</script>