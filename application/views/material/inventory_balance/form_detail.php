<?php
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));?>

<?php
echo form_open('material/inventory_balance/insert_detail',
	array(
		'name'	=> 'material_inventory_balance_form_detail',
		'id'	=> 'material_inventory_balance_form_detail'
	)
);?>
<table width="100%">
	<tr><td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">General</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="120">No</th>
						<td><?php echo (!empty($record) ? $record->code : '');?></td>
					</tr>
					<tr>
						<th>Date</th>
						<td><?php echo (!empty($record->balance_date) ? date($this->config->item('server_display_date_format'), strtotime($record->balance_date)) : '');?></td>
					</tr>
					<tr>
						<th>Location</th>
						<td><?php echo (!empty($record) ? $record->m_inventory_text : '');?></td>
					</tr>
					<tr>
						<th>Harvest Sequence</th>
						<td><?php echo (!empty($record) ? $record->harvest_sequence : '');?></td>
					</tr>
					<tr>
						<th>Actual Size</th>
						<td><?php echo (!empty($record) ? number_format_clear($record->product_size, 4) : '');?></td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="120">PIC</th>
						<td><?php echo (!empty($record) ? $record->pic : '');?></td>
					</tr>
					<tr>
						<th>Vehicle No</th>
						<td><?php echo (!empty($record) ? $record->vehicle_no : '');?></td>
					</tr>
					<tr>
						<th>Notes</th>
						<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
					</tr>
				</tbody>
			</table>
		</td>
		<td id="material_inventory_balance_form_detail_counter_label" width="30%" align="center" rowspan="2" style="padding-left:50px; font-size:100px;">
			0
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table class="form-table">
				<tbody>
					<tr><th width="100"><label for="material_inventory_balance_form_detail_m_product_id_caption">Product</label></th>
						<td><input type="hidden" name="m_product_id" id="material_inventory_balance_form_detail_m_product_id" class="required"/></td>
					</tr>
					<tr><th><label for="material_inventory_balance_form_detail_carton_no">Carton No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'carton_no',
		'id' 		=> 'material_inventory_balance_form_detail_carton_no',
		'class'		=> 'required',
		'style'		=> "width:60px;"
	)
);?>
						</td>
					</tr>
					<tr><th><label for="material_inventory_balance_form_detail_quantity">Quantity</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'quantity',
		'id' 		=> 'material_inventory_balance_form_detail_quantity',
		'class'		=> 'required number',
		'style'		=> "width:60px;"
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<tbody>
					<tr><th width="100"><label for="material_inventory_balance_form_detail_notes">Notes</label></th>
						<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'material_inventory_balance_form_detail_notes',
		'cols'	=> 30, 'rows'	=> 2
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
<?php 
if (is_authorized('material/inventory_balance', 'insert')){?>
			<button id="material_inventory_balance_detail_process_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
				<span class="ui-button-icon-primary ui-icon ui-icon-check"></span>
				<span class="ui-button-text">Process</span>
			</button>
<?php 
}?>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
<?php 
echo form_close();?>

<table id="material_inventory_balance_detail_list_table"></table>
<div id="material_inventory_balance_detail_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventory_balance_detail_load_summary(null, <?php echo $id;?>);
	material_inventory_balance_detail_list_load_table('material_inventory_balance_detail_list_table');
	
	jquery_autocomplete_build("#material_inventory_balance_form_detail_m_product_id", "<?php echo site_url('material/inventory_balance/get_m_product_autocomplete_list_json');?>", {
		width : 220
	});
	
	jQuery("#material_inventory_balance_form_detail").validate({
		submitHandler: function(form){
			jQuery("#material_inventory_balance_form_detail").ajaxSubmit({
				dataType: "json",
				async : false,
				data : {
					m_inventory_balance_id	: <?php echo $id;?>
				},
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					if (data.response == false)
					{
						jquery_show_message(data.value, null, "ui-icon-close", function(){
							material_inventory_balance_detail_clear_field();
							material_inventory_balance_detail_load_summary(null, <?php echo $id;?>);
						});
					}
					else
					{
						jQuery("#material_inventory_balance_detail_list_table").trigger('reloadGrid', [{current:true}]);
						material_inventory_balance_detail_clear_field();
						material_inventory_balance_detail_load_summary(data.value.summary);
					}
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jQuery("#material_inventory_balance_detail_process_btn").click(function(e){
		e.preventDefault();
		jQuery('#material_inventory_balance_form_detail').submit();
	});
});

function material_inventory_balance_detail_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_balance/get_list_detail_json');?>", 
		postData : {
			id : <?php echo $id;?>
		},
		colNames: [
			'Id', 
			'Product',
			'Carton No',
			'Quantity',
			'UOM',
			'Notes',
			''
		], 
		colModel: [
			{name:'id', index:'ibd.id', key:true, hidden:true, frozen:true},
			{name:'m_product_name', index:'pro.name', width:200, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'carton_no', index:'ibd.carton_no', width:100, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity', index:'ibd.quantity', width:100, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'m_product_uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'notes', index:'ibd.notes', classes:'jqgrid-nowrap-cell', width:300, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'cmd_action', index:'cmd_action', width:26, search:false, sortable:false}
		],
		afterInsertRow : function(rowid, rowdata, rowelem){
			jQuery("#" + table_id).jqGrid('setRowData', rowid, {
				cmd_action: 
					  '<button onclick="material_inventory_balance_detail_list_remove(\'' + table_id + '\', ' + rowid.toString() + ')" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" style="height:20px;" title="Remove">'
					+ '	<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>'
					+ '	<span class="ui-button-text">Remove</span>'
					+ '</button>'
			});
		},
		pager: '#' + table_id + '_nav', 
		sortname: 'ibd.id', 
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
	jQuery("#" + table_id).setGridHeight(220);
}

function material_inventory_balance_detail_list_remove(table_id, id){
	jquery_show_confirm("Are your sure ?", function(){
		var _delete_url = "<?php echo site_url('material/inventory_balance/delete_detail/@id');?>";
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
					material_inventory_balance_detail_load_summary(data.value.summary);
				}
			},
			complete: function(jqXHR, textStatus){
				jquery_unblockui();
			}
		});
	});
}

function material_inventory_balance_detail_load_summary(summary, m_inventory_balance_id){
	if (summary == null)
	{
		jQuery.ajax({
			url: "<?php echo site_url('material/inventory_balance/get_detail_summary');?>",
			data : {
				m_inventory_balance_id	: m_inventory_balance_id
			},
			type: "GET",
			dataType: "json",
			error: jquery_ajax_error_handler,
			success: function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					summary = data.value;
					jQuery('#material_inventory_balance_form_detail_counter_label').text(summary.counter);
					jQuery('#material_inventory_balance_form_detail_carton_no').val(summary.carton_no);
				}
			}
		});
	}
	else
	{
		jQuery('#material_inventory_balance_form_detail_counter_label').text(summary.counter);
		jQuery('#material_inventory_balance_form_detail_carton_no').val(summary.carton_no);
	}
}

function material_inventory_balance_detail_clear_field(){
	jQuery('#material_inventory_balance_form_detail_carton_no').val('');
	jQuery('#material_inventory_balance_form_detail_quantity').val('');
	jQuery('#material_inventory_balance_form_detail_quantity').focus();
	jQuery('#material_inventory_balance_form_detail_notes').val('');
}
</script>