<?php
$product_conditions = array_merge(array('' => ''), $this->config->item('product_conditions'));
$transport_modes = array_merge(array('' => ''), $this->config->item('transport_modes'));
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));?>

<table id="material_inventory_inbound_detail_ref_list_table"></table>
<div id="material_inventory_inbound_detail_ref_list_table_nav"></div>

<?php
echo form_open('material/inventory_inbound/insert_detail',
	array(
		'name'	=> 'material_inventory_inbound_form_detail',
		'id'	=> 'material_inventory_inbound_form_detail'
	)
);?>
<table>
	<tr>
		<td colspan="2">
			<table class="form-table">
				<tbody>
					<tr><th width="100"><label for="material_inventory_inbound_form_detail_barcode">Barcode</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'barcode',
		'id' 		=> 'material_inventory_inbound_form_detail_barcode',
		'class'		=> 'required',
		'style'		=> "width:350px"
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td rowspan="3" id="material_inventory_inbound_form_detail_counter_label" style="padding-left:50px; font-size:100px;">
			0
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table class="form-table">
				<tbody>
					<tr><th width="100"><label for="material_inventory_inbound_form_detail_quantity">Quantity</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'quantity',
		'id' 		=> 'material_inventory_inbound_form_detail_quantity',
		'class'		=> 'required number',
		'style'		=> "width:60px; margin-right:10px;"
	)
);?>
							<label for="material_inventory_inbound_form_detail_quantity_box">Box</label>
<?php 
echo form_input(
	array(
		'name' 		=> 'quantity_box',
		'id' 		=> 'material_inventory_inbound_form_detail_quantity_box',
		'class'		=> 'required number',
		'style'		=> "width:40px",
		'value'		=> 1
	)
);?>
						</td>
					</tr>
					<tr><th><label for="material_inventory_inbound_form_detail_carton_no">Carton No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'carton_no',
		'id' 		=> 'material_inventory_inbound_form_detail_carton_no',
		'class'		=> 'required'
	)
);?>
						</td>
					</tr>
					<tr><th><label for="material_inventory_inbound_form_detail_packed_date">Packed Date</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'packed_date',
		'id' 		=> 'material_inventory_inbound_form_detail_packed_date',
		'style'		=> "width:70px"
	)
);?>
							ymd(<?php echo date('ymd');?>)
						</td>
					</tr>
					<tr><th><label for="material_inventory_inbound_form_detail_expired_date">Expired Date</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'expired_date',
		'id' 		=> 'material_inventory_inbound_form_detail_expired_date',
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
					<tr><th width="100"><label for="material_inventory_inbound_form_detail_pallet">Pallet</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'pallet',
		'id' 		=> 'material_inventory_inbound_form_detail_pallet',
		'class'		=> 'required',
		'style'		=> "width:180px"
	)
);?>
							<button id="material_inventory_inbound_detail_clear_pallet_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" style="height:20px;" title="Clear">
								<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
								<span class="ui-button-text">Clear</span>
							</button>
						</td>
					</tr>
					<tr><th><label for="material_inventory_inbound_form_detail_grid_code">Grid</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'grid_code',
		'id' 		=> 'material_inventory_inbound_form_detail_grid_code',
		'style'		=> "width:80px"
	)
);?>
							<button id="material_inventory_inbound_detail_clear_grid_code_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" style="height:20px;" title="Clear">
								<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
								<span class="ui-button-text">Clear</span>
							</button>
						</td>
					</tr>
					<tr><th><label for="material_inventory_inbound_form_detail_lot_no">Lot No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'lot_no',
		'id' 		=> 'material_inventory_inbound_form_detail_lot_no',
		'style'		=> "width:100px"
	)
);?>
						</td>
					</tr>
					<tr><th><label for="material_inventory_inbound_form_detail_volume">Volume</label></th>
						<td><label for="material_inventory_inbound_form_detail_volume_length">L</label>
<?php 
echo form_input(
	array(
		'name' 		=> 'volume_length',
		'id' 		=> 'material_inventory_inbound_form_detail_volume_length',
		'class'		=> 'required number',
		'style'		=> "width:60px"
	)
);?>
							m,
							<label for="material_inventory_inbound_form_detail_volume_width">W</label>
<?php 
echo form_input(
	array(
		'name' 		=> 'volume_width',
		'id' 		=> 'material_inventory_inbound_form_detail_volume_width',
		'class'		=> 'required number',
		'style'		=> "width:60px"
	)
);?>
							m,
							<label for="material_inventory_inbound_form_detail_volume_height">H</label>
<?php 
echo form_input(
	array(
		'name' 		=> 'volume_height',
		'id' 		=> 'material_inventory_inbound_form_detail_volume_height',
		'class'		=> 'required number',
		'style'		=> "width:60px"
	)
);?>
							m
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_inbound_form_detail_condition">Condition</label></th>
						<td>
<?php 
echo form_dropdown('condition', $product_conditions, '', 'id="material_inventory_inbound_form_detail_condition"');?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
<?php 
if (is_authorized('material/inventory_inbound', 'insert')){?>
			<button id="material_inventory_inbound_detail_process_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
				<span class="ui-button-icon-primary ui-icon ui-icon-check"></span>
				<span class="ui-button-text">Process</span>
			</button>
<?php 
}?>
		</td>
	</tr>
</table>
<?php 
echo form_close();?>

<table id="material_inventory_inbound_detail_list_table"></table>
<div id="material_inventory_inbound_detail_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventory_inbound_detail_ref_list_load_table('material_inventory_inbound_detail_ref_list_table');
	material_inventory_inbound_detail_list_load_table('material_inventory_inbound_detail_list_table');
	
	jQuery("#material_inventory_inbound_form_detail").validate({
		submitHandler: function(form){
			var ref_id = jQuery("#material_inventory_inbound_detail_ref_list_table").getGridParam("selrow");
			var ref_row_data = jQuery("#material_inventory_inbound_detail_ref_list_table").getRowData(ref_id);
			
			jQuery("#material_inventory_inbound_form_detail").ajaxSubmit({
				dataType: "json",
				async : false,
				data : {
					m_inventory_inbound_id			: <?php echo $id;?>,
					m_inventory_receivedetail_id	: ref_row_data.id
				},
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					if (data.response == false)
					{
						jquery_show_message(data.value, null, "ui-icon-close", function(){
							material_inventory_inbound_detail_clear_field();
						});
					}
					else
					{
						jQuery("#material_inventory_inbound_detail_list_table").trigger('reloadGrid', [{current:true}]);
						material_inventory_inbound_detail_load_counter(data.value.counter);
						material_inventory_inbound_detail_clear_field();
					}
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jQuery("#material_inventory_inbound_detail_process_btn").click(function(e){
		e.preventDefault();
		jQuery('#material_inventory_inbound_form_detail').submit();
	});
	
	jQuery('#material_inventory_inbound_form_detail_barcode').change(function(){
		var ref_id = jQuery("#material_inventory_inbound_detail_ref_list_table").getGridParam("selrow");
		var ref_row_data = jQuery("#material_inventory_inbound_detail_ref_list_table").getRowData(ref_id);
		
		jQuery.ajax({
			url: "<?php echo site_url('material/inventory_inbound/parse_barcode');?>",
			type: "GET",
			dataType: "json",
			async : false,
			data : {
				m_inventory_receivedetail_id	: ref_row_data.id,
				barcode							: jQuery('#material_inventory_inbound_form_detail_barcode').val()
			},
			error: jquery_ajax_error_handler,
			beforeSend: function(jqXHR, settings){
				jquery_blockui();
			},
			success: function(data, textStatus, jqXHR){
				jQuery('#material_inventory_inbound_form_detail_quantity').val(data.quantity);
				jQuery('#material_inventory_inbound_form_detail_carton_no').val(data.carton_no);
				jQuery('#material_inventory_inbound_form_detail_packed_date').val(data.packed_date);
				
				if (data.quantity != null && data.carton_no != null && data.packed_date != null)
					jQuery('#material_inventory_inbound_form_detail_expired_date').focus();
				else if (data.quantity != null && data.carton_no == null && data.packed_date != null)
					jQuery('#material_inventory_inbound_form_detail_carton_no').focus();
				else if (data.quantity != null && data.carton_no != null && data.packed_date == null)
					jQuery('#material_inventory_inbound_form_detail_packed_date').focus();
			},
			complete: function(jqXHR, textStatus){
				jquery_unblockui();
			}
		});
	});
	
	jQuery('#material_inventory_inbound_detail_clear_pallet_btn').click(function(e){
		e.preventDefault();
		jQuery('#material_inventory_inbound_form_detail_pallet').val('');
	});
	
	jQuery('#material_inventory_inbound_detail_clear_grid_code_btn').click(function(e){
		e.preventDefault();
		jQuery('#material_inventory_inbound_form_detail_grid_code').val('');
	});
});

var material_inventory_inbound_detail_ref_list_selected_id = null;
function material_inventory_inbound_detail_ref_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		rowNum: <?php echo $this->config->item('jqgrid_limit_per_page');?>, 
		rowList: [<?php echo $this->config->item('jqgrid_limit_pages');?>], 
		caption: "Select Receive",
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('material/inventory_inbound/get_list_detail_ref_json');?>", 
		colNames: [
			'Id', 
			'Product Code', 
			'Name', 
			'UOM', 
			'Receive No', 
			'Receive Date', 
			'Box',
			'Quantity',
			'Condition',
			'Length',
			'Width',
			'Height',
			'Order In No', 
			'Order In Date', 
			'Business Partner',
			'Vehicle No',
			'Driver',
			'Transport'
		], 
		colModel: [
			{name:'id', index:'ird.id', hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:220, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_uom', index:'pro.uom', width:100, frozen:true,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'m_inventory_receive_code', index:'ir.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'m_inventory_receive_date', index:'ir.receive_date'}),
			{name:'quantity_box', index:'ird.quantity_box', width:70, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'quantity', index:'ird.quantity', width:90, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'condition', index:'ird.condition', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_conditions);?>}
			},
			{name:'m_product_volume_length', index:'pro.volume_length', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'m_product_volume_width', index:'pro.volume_width', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'m_product_volume_height', index:'pro.volume_height', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_orderin_code', index:'oi.code', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'c_orderin_date', index:'oi.orderin_date'}),
			{name:'c_businesspartner_name', index:'bp.name', width:180, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_inventory_receive_vehicle_no', index:'ir.vehicle_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_inventory_receive_vehicle_driver', index:'ir.vehicle_driver', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_inventory_receive_transport_mode', index:'ir.transport_mode', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($transport_modes);?>}
			}
		],
		onSelectRow: function(rowid, status, e){
			if (material_inventory_inbound_detail_ref_list_selected_id == null || material_inventory_inbound_detail_ref_list_selected_id != rowid)
			{
				var row_data = jQuery('#' + table_id).getRowData(rowid);
				if (row_data)
				{
					jQuery('#material_inventory_inbound_form_detail_volume_length').val(row_data.m_product_volume_length);
					jQuery('#material_inventory_inbound_form_detail_volume_width').val(row_data.m_product_volume_width);
					jQuery('#material_inventory_inbound_form_detail_volume_height').val(row_data.m_product_volume_height);
				}
				
				material_inventory_inbound_detail_ref_list_selected_id = rowid;
			}
		},
		sortname: 'ird.created', 
		sortorder: "desc"
	});
	
	jQuery("#" + table_id).jqGrid('filterToolbar', {
		stringResult : true, 
		searchOperators : true
	});
	
	jQuery("#" + table_id).jqGrid('setGroupHeaders', {
		useColSpanStyle: true, 
		groupHeaders:[
			{startColumnName: 'm_product_volume_length', numberOfColumns: 3, titleText: 'Volume'}
		]
	});
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(100);
}

function material_inventory_inbound_detail_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_inbound/get_list_detail_json');?>", 
		postData : {
			id : <?php echo $id;?>
		},
		colNames: [
			'Id', 
			'Barcode', 
			'Box',
			'Quantity',
			'Carton No',
			'Packed Date',
			'Expired Date',
			'Pallet',
			'Grid',
			'Lot No',
			'Condition',
			'Length',
			'Width',
			'Height',
			'Scan Date',
			''
		], 
		colModel: [
			{name:'id', index:'iid.id', key:true, hidden:true, frozen:true},
			{name:'barcode', index:'iid.barcode', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box', index:'iid.quantity_box', width:50, frozen:true, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity', index:'iid.quantity', width:80, frozen:true, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'carton_no', index:'iid.carton_no', width:80, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'packed_date', index:'iid.packed_date'}),
			jqgrid_column_date(table_id, {name:'expired_date', index:'iid.expired_date'}),
			{name:'pallet', index:'iid.pallet', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'grid_code', index:'gri.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'lot_no', index:'iid.lot_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'condition', index:'iid.condition', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_conditions);?>}
			},
			{name:'volume_length', index:'iid.volume_length', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'volume_width', index:'iid.volume_width', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'volume_height', index:'iid.volume_height', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'created', index:'iid.created', search:false}),
			{name:'cmd_action', index:'cmd_action', width:26, search:false, sortable:false}
		],
		afterInsertRow : function(rowid, rowdata, rowelem){
			jQuery("#" + table_id).jqGrid('setRowData', rowid, {
				cmd_action: 
					  '<button onclick="material_inventory_inbound_detail_list_remove(\'' + table_id + '\', ' + rowid.toString() + ')" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" style="height:20px;" title="Remove">'
					+ '	<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>'
					+ '	<span class="ui-button-text">Remove</span>'
					+ '</button>'
			});
		},
		onSelectRow: function(rowid, status, e){
			var row_data = jQuery('#' + table_id).getRowData(rowid);
			
			jQuery('#material_inventory_inbound_form_detail_grid_code').val(row_data.grid_code);
			jQuery('#material_inventory_inbound_form_detail_pallet').val(row_data.pallet);
			
			material_inventory_inbound_detail_load_counter(null, <?php echo $id;?>, row_data.pallet);
		},
		pager: '#' + table_id + '_nav', 
		sortname: 'iid.id', 
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
	
	jQuery("#" + table_id).jqGrid('setGroupHeaders', {
		useColSpanStyle: true, 
		groupHeaders:[
			{startColumnName: 'volume_length', numberOfColumns: 3, titleText: 'Volume'}
		]
	});
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(170);
}

function material_inventory_inbound_detail_list_remove(table_id, id){
	jquery_show_confirm("Are your sure ?", function(){
		var _delete_url = "<?php echo site_url('material/inventory_inbound/delete_detail/@id');?>";
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
				{
					jQuery("#" + table_id).trigger('reloadGrid', [{current:true}]);
					material_inventory_inbound_detail_load_counter(data.value.counter);
				}
			},
			complete: function(jqXHR, textStatus){
				jquery_unblockui();
			}
		});
	});
}

var material_inventory_inbound_detail_load_counter_last = {m_inventory_inbound_id : null, pallet : null};

function material_inventory_inbound_detail_load_counter(counter, m_inventory_inbound_id, pallet){
	if (!counter)
	{
		if (	material_inventory_inbound_detail_load_counter_last.m_inventory_inbound_id == m_inventory_inbound_id
			&&	material_inventory_inbound_detail_load_counter_last.pallet == pallet)
			return;
		
		jQuery.ajax({
			url: "<?php echo site_url('material/inventory_inbound/get_detail_counter');?>",
			data : {
				m_inventory_inbound_id	: m_inventory_inbound_id,
				pallet					: pallet
			},
			type: "GET",
			dataType: "json",
			error: jquery_ajax_error_handler,
			success: function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					counter = data.value;
					jQuery('#material_inventory_inbound_form_detail_counter_label').text(counter);
					
					material_inventory_inbound_detail_load_counter_last.m_inventory_inbound_id = m_inventory_inbound_id;
					material_inventory_inbound_detail_load_counter_last.pallet = pallet;
				}
			}
		});
	}
	else
		jQuery('#material_inventory_inbound_form_detail_counter_label').text(counter);
}

function material_inventory_inbound_detail_clear_field(){
	jQuery('#material_inventory_inbound_form_detail_barcode').val('');
	jQuery('#material_inventory_inbound_form_detail_barcode').focus();
	jQuery('#material_inventory_inbound_form_detail_quantity').val('');
	jQuery('#material_inventory_inbound_form_detail_quantity_box').val('1');
	jQuery('#material_inventory_inbound_form_detail_carton_no').val('');
	jQuery('#material_inventory_inbound_form_detail_packed_date').val('');
	jQuery('#material_inventory_inbound_form_detail_expired_date').val('');
	jQuery('#material_inventory_inbound_form_detail_lot_no').val('');
	jQuery('#material_inventory_inbound_form_detail_condition').val('');
}
</script>