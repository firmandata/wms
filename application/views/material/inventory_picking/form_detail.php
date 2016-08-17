<?php
$orderout_origins = array_merge(array('' => ''), $this->config->item('orderout_origins'));
$product_conditions = array_merge(array('' => ''), $this->config->item('product_conditions'));
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));?>

<table id="material_inventory_picking_detail_ref_list_table"></table>
<div id="material_inventory_picking_detail_ref_list_table_nav"></div>

<?php
echo form_open('material/inventory_picking/insert_detail',
	array(
		'name'	=> 'material_inventory_picking_form_detail',
		'id'	=> 'material_inventory_picking_form_detail'
	)
);?>
<table>
	<tr>
		<td colspan="2">
			<table class="form-table">
				<tbody>
					<tr><th width="100"><label for="material_inventory_picking_form_detail_barcode">Barcode</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'barcode',
		'id' 		=> 'material_inventory_picking_form_detail_barcode',
		'style'		=> "width:350px"
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td rowspan="3" id="material_inventory_picking_form_detail_counter_label" style="padding-left:50px; font-size:100px;">
			0
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table class="form-table">
				<tbody>
					<tr><th width="100"><label for="material_inventory_picking_form_detail_pallet">Pallet</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'pallet',
		'id' 		=> 'material_inventory_picking_form_detail_pallet',
		'style'		=> "width:180px"
	)
);?>
						</td>
					</tr>
					<tr><th><label for="material_inventory_picking_form_detail_grid_code">Grid</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'grid_code',
		'id' 		=> 'material_inventory_picking_form_detail_grid_code',
		'style'		=> "width:80px"
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_picking_form_detail_condition">Condition</label></th>
						<td>
<?php 
echo form_dropdown('condition', $product_conditions, '', 'id="material_inventory_picking_form_detail_condition"');?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<tbody>
					<tr><th width="100"><label for="material_inventory_picking_form_detail_quantity_box">Box</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'quantity_box',
		'id' 		=> 'material_inventory_picking_form_detail_quantity_box',
		'class'		=> 'number',
		'style'		=> "width:50px"
	)
);?>
						</td>
					</tr>
					<tr><th><label for="material_inventory_picking_form_detail_packed_group">Packed Group</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'packed_group',
		'id' 		=> 'material_inventory_picking_form_detail_packed_group',
		'style'		=> "width:100px"
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
if (is_authorized('material/inventory_picking', 'insert')){?>
			<button id="material_inventory_picking_detail_process_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
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

<table id="material_inventory_picking_detail_list_table"></table>
<div id="material_inventory_picking_detail_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventory_picking_detail_ref_list_load_table('material_inventory_picking_detail_ref_list_table');
	material_inventory_picking_detail_list_load_table('material_inventory_picking_detail_list_table');
	
	jQuery("#material_inventory_picking_form_detail").validate({
		submitHandler: function(form){
			var ref_id = jQuery("#material_inventory_picking_detail_ref_list_table").getGridParam("selrow");
			var ref_row_data = jQuery("#material_inventory_picking_detail_ref_list_table").getRowData(ref_id);
			
			jQuery("#material_inventory_picking_form_detail").ajaxSubmit({
				dataType: "json",
				async : false,
				data : {
					m_inventory_picking_id	: <?php echo $id;?>,
					m_inventory_picklist_id	: ref_row_data.id
				},
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					if (data.response == false)
					{
						jquery_show_message(data.value, null, "ui-icon-close", function(){
							// material_inventory_picking_detail_clear_field();
						});
					}
					else
					{
						jQuery("#material_inventory_picking_detail_list_table").trigger('reloadGrid', [{current:true}]);
						material_inventory_picking_detail_clear_field();
					}
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jQuery("#material_inventory_picking_detail_process_btn").click(function(e){
		e.preventDefault();
		jQuery('#material_inventory_picking_form_detail').submit();
	});
});

function material_inventory_picking_detail_ref_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		rowNum: <?php echo $this->config->item('jqgrid_limit_per_page');?>, 
		rowList: [<?php echo $this->config->item('jqgrid_limit_pages');?>], 
		caption: "Select Pick List",
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('material/inventory_picking/get_list_form_ref_json');?>", 
		colNames: [
			'Id', 
			'Pick List No', 
			'Date', 
			'Order Out Code', 
			'Box',
			'Quantity',
			'Business Partner', 
			'Order Out Date',
			'Request Arrival',
			'Project'
		], 
		colModel: [
			{name:'id', index:'ipl.id', hidden:true, frozen:true},
			{name:'code', index:'ipl.code', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'picklist_date', index:'ipl.picklist_date'}),
			{name:'c_orderout_code', index:'oo.code', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box', index:'quantity_box', width:70, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'quantity', index:'quantity', width:90, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_businesspartner_name', index:'bp.name', width:180, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'c_orderout_orderout_date', index:'oo.orderout_date'}),
			jqgrid_column_date(table_id, {name:'c_orderout_request_arrive_date', index:'oo.request_arrive_date'}),
			{name:'c_project_name', index:'prj.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		sortname: 'ipl.created', 
		sortorder: "desc"
	});
	
	jQuery("#" + table_id).jqGrid('filterToolbar', {
		stringResult : true, 
		searchOperators : true
	});
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(100);
}

function material_inventory_picking_detail_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_picking/get_list_form_json');?>", 
		postData : {
			id : <?php echo $id;?>
		},
		colNames: [
			'Id', 
			'Inventory Picking Id', 
			'Inventory Picklist Id', 
			'Product Id',
			'Product Code', 
			'Name', 
			'Barcode', 
			'Pallet',
			'Grid Id',
			'Grid',
			'Box',
			'Quantity',
			'UOM',
			'Condition',
			'Packed Group',
			''
		], 
		colModel: [
			{name:'id', index:'ipld.id', hidden:true, frozen:true},
			{name:'m_inventory_picking_id', index:'ipgd.m_inventory_picking_id', hidden:true, frozen:true},
			{name:'m_inventory_picklist_id', index:'ipld.m_inventory_picklist_id', hidden:true, frozen:true},
			{name:'m_product_id', index:'ipld.m_product_id', hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:220, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'barcode', index:'ipld.barcode', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'pallet', index:'ipld.pallet', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_grid_id', index:'ipld.m_grid_id', hidden:true, frozen:true},
			{name:'m_grid_code', index:'gri.code', width:80, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box', index:'quantity_box', width:70, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity', index:'quantity', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'m_product_uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'condition', index:'ipld.condition', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_conditions);?>}
			},
			{name:'packed_group', index:'ipgd.packed_group', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'cmd_action', index:'cmd_action', width:26, search:false, sortable:false}
		],
		afterInsertRow : function(rowid, rowdata, rowelem){
			jQuery("#" + table_id).jqGrid('setRowData', rowid, {
				cmd_action: 
					  '<button onclick="material_inventory_picking_detail_list_remove(\'' + table_id + '\', ' + rowid.toString() + ')" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" style="height:20px;" title="Remove">'
					+ '	<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>'
					+ '	<span class="ui-button-text">Remove</span>'
					+ '</button>'
			});
		},
		pager: '#' + table_id + '_nav', 
		sortname: 'ipld.id', 
		sortorder: "desc",
		loadComplete: function(data){
			var row_total = jQuery("#" + table_id).getGridParam('records');
			jQuery('#material_inventory_picking_form_detail_counter_label').text(row_total);
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
	jQuery("#" + table_id).setGridHeight(170);
}

function material_inventory_picking_detail_list_remove(table_id, id){
	jquery_show_confirm("Are your sure ?", function(){
		var row_data = jQuery("#material_inventory_picking_detail_list_table").getRowData(id);
		
		jQuery.ajax({
			url: "<?php echo site_url('material/inventory_picking/delete_detail');?>",
			type: "POST",
			dataType: "json",
			data : {
				m_inventory_picking_id	: row_data.m_inventory_picking_id,
				m_inventory_picklist_id	: row_data.m_inventory_picklist_id,
				m_product_id			: row_data.m_product_id,
				m_grid_id				: row_data.m_grid_id,
				barcode					: row_data.barcode,
				pallet					: row_data.pallet,
				condition				: row_data.condition,
				packed_group			: row_data.packed_group
			},
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

function material_inventory_picking_detail_clear_field(){
	jQuery('#material_inventory_picking_form_detail_barcode').val('');
	jQuery('#material_inventory_picking_form_detail_barcode').focus();
	jQuery('#material_inventory_picking_form_detail_quantity_box').val('');
	jQuery('#material_inventory_picking_form_detail_grid_code').val('');
	jQuery('#material_inventory_picking_form_detail_pallet').val('');
	jQuery('#material_inventory_picking_form_detail_condition').val('');
}
</script>