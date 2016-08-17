<?php
echo form_open('material/inventory_hold/insert_detail',
	array(
		'name'	=> 'material_inventory_hold_form_detail',
		'id'	=> 'material_inventory_hold_form_detail'
	)
);?>
<table>
	<tr>
		<td><table class="form-table">
				<tbody>
					<tr><th width="100"><label for="material_inventory_hold_form_detail_m_product_code">Product Code</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'm_product_code',
		'id' 		=> 'material_inventory_hold_form_detail_m_product_code'
	)
);?>
						</td>
					</tr>
					<tr><th><label for="material_inventory_hold_form_detail_barcode">Barcode</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'barcode',
		'id' 		=> 'material_inventory_hold_form_detail_barcode',
		'style'		=> "width:220px"
	)
);?>
						</td>
					</tr>
					<tr><th><label for="material_inventory_hold_form_detail_pallet">Pallet</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'pallet',
		'id' 		=> 'material_inventory_hold_form_detail_pallet',
		'style'		=> "width:180px"
	)
);?>
						</td>
					</tr>
					<tr><th><label for="material_inventory_hold_form_detail_m_grid_code">Grid</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'm_grid_code',
		'id' 		=> 'material_inventory_hold_form_detail_m_grid_code',
		'style'		=> "width:80px"
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td rowspan="3" id="material_inventory_hold_form_detail_counter_label" style="padding-left:50px; font-size:100px;">
			0
		</td>
	</tr>
	<tr>
		<td align="center">
<?php 
if (is_authorized('material/inventory_hold', 'insert')){?>
			<button id="material_inventory_hold_detail_process_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
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

<table id="material_inventory_hold_detail_list_table"></table>
<div id="material_inventory_hold_detail_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventory_hold_detail_list_load_table('material_inventory_hold_detail_list_table');
	
	jQuery("#material_inventory_hold_form_detail").validate({
		submitHandler: function(form){
			jQuery("#material_inventory_hold_form_detail").ajaxSubmit({
				dataType: "json",
				async : false,
				data : {
					m_inventory_hold_id	: <?php echo $id;?>
				},
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					if (data.response == false)
					{
						jquery_show_message(data.value, null, "ui-icon-close", function(){
							material_inventory_hold_detail_clear_field();
						});
					}
					else
					{
						jQuery("#material_inventory_hold_detail_list_table").trigger('reloadGrid', [{current:true}]);
						material_inventory_hold_detail_clear_field();
					}
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jQuery("#material_inventory_hold_detail_process_btn").click(function(e){
		e.preventDefault();
		jQuery('#material_inventory_hold_form_detail').submit();
	});
});

function material_inventory_hold_detail_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_hold/get_list_detail_json');?>", 
		postData : {
			id : <?php echo $id;?>
		},
		colNames: [
			'Inventory Hold Id', 
			'Pallet',
			'Barcode', 
			'Product Id',
			'Product Code',
			'Name',
			'Grid Id',
			'Grid',
			'Box',
			'Quantity',
			'Hold',
			'Scan Date',
			''
		], 
		colModel: [
			{name:'m_inventory_hold_id', index:'ihd.m_inventory_hold_id', frozen:true, hidden:true},
			{name:'pallet', index:'ihd.pallet', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'barcode', index:'ihd.barcode', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_id', index:'ihd.m_product_id', hidden:true},
			{name:'m_product_code', index:'pro.code', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:200, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_grid_id', index:'ihd.m_grid_id', hidden:true},
			{name:'m_grid_code', index:'gri.code', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box_from', index:'quantity_box_from', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_from', index:'quantity_from', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'is_hold', index:'ihd.is_hold', width:80, formatter:jqgrid_boolean_formatter, align: 'center',
			 stype:'select', 
			 searchoptions:{
				sopt:jqgird_search_string_operators, clearSearch:false,
				dataUrl: "<?php echo site_url('material/inventory_hold/get_is_hold_dropdown/search_hold_is_hold');?>"
			 }
			},
			jqgrid_column_date(table_id, {name:'created', index:'created', search:false}),
			{name:'cmd_action', index:'cmd_action', width:55, search:false, sortable:false}
		],
		afterInsertRow : function(rowid, rowdata, rowelem){
			jQuery("#" + table_id).jqGrid('setRowData', rowid, {
				cmd_action: 
					  '<button onclick="material_inventory_hold_detail_list_remove(\'' + table_id + '\', ' + rowid.toString() + ')" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" style="height:20px;" title="Remove">'
					+ '	<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>'
					+ '	<span class="ui-button-text">Remove</span>'
					+ '</button>'
					+ '<button onclick="material_inventory_hold_detail_list_' + (rowdata.is_hold ? 'unhold' : 'rehold') + '(\'' + table_id + '\', ' + rowid.toString() + ')" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" style="height:20px;" title="' + (rowdata.is_hold ? 'Unhold' : 'Rehold') + '">'
					+ '	<span class="ui-button-icon-primary ui-icon ' + (rowdata.is_hold ? 'ui-icon-unlocked' : 'ui-icon-locked') + '"></span>'
					+ '	<span class="ui-button-text">' + (rowdata.is_hold ? 'Unhold' : 'Rehold') + '</span>'
					+ '</button>'
			});
		},
		pager: '#' + table_id + '_nav', 
		sortname: 'm_inventory_holddetail_id', 
		sortorder: "desc",
		loadComplete: function(data){
			var row_total = jQuery("#" + table_id).getGridParam('records');
			jQuery('#material_inventory_hold_form_detail_counter_label').text(row_total);
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
	jQuery("#" + table_id).setGridHeight(250);
}

function material_inventory_hold_detail_list_remove(table_id, id){
	jquery_show_confirm("Are your sure ?", function(){
		var row_data = jQuery("#material_inventory_hold_detail_list_table").getRowData(id);
		
		jQuery.ajax({
			url: "<?php echo site_url('material/inventory_hold/delete_detail');?>",
			type: "POST",
			dataType: "json",
			data : {
				m_inventory_hold_id		: row_data.m_inventory_hold_id,
				barcode					: row_data.barcode,
				m_product_id			: row_data.m_product_id,
				pallet					: row_data.pallet,
				m_grid_id				: row_data.m_grid_id
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

function material_inventory_hold_detail_list_unhold(table_id, id){
	jquery_show_confirm("Are your sure ?", function(){
		var row_data = jQuery("#material_inventory_hold_detail_list_table").getRowData(id);
		
		jQuery.ajax({
			url: "<?php echo site_url('material/inventory_hold/unhold_detail');?>",
			type: "POST",
			dataType: "json",
			data : {
				m_inventory_hold_id		: row_data.m_inventory_hold_id,
				barcode					: row_data.barcode,
				m_product_id			: row_data.m_product_id,
				pallet					: row_data.pallet,
				m_grid_id				: row_data.m_grid_id
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

function material_inventory_hold_detail_list_rehold(table_id, id){
	jquery_show_confirm("Are your sure ?", function(){
		var row_data = jQuery("#material_inventory_hold_detail_list_table").getRowData(id);
		
		jQuery.ajax({
			url: "<?php echo site_url('material/inventory_hold/rehold_detail');?>",
			type: "POST",
			dataType: "json",
			data : {
				m_inventory_hold_id		: row_data.m_inventory_hold_id,
				barcode					: row_data.barcode,
				m_product_id			: row_data.m_product_id,
				pallet					: row_data.pallet,
				m_grid_id				: row_data.m_grid_id
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

function material_inventory_hold_detail_clear_field(){
	jQuery('#material_inventory_hold_form_detail_m_product_code').val('');
	jQuery('#material_inventory_hold_form_detail_m_product_code').focus();
	jQuery('#material_inventory_hold_form_detail_barcode').val('');
	jQuery('#material_inventory_hold_form_detail_pallet').val('');
	jQuery('#material_inventory_hold_form_detail_m_grid_code').val('');
	jQuery('#material_inventory_hold_form_detail_quantity').val('');
}
</script>