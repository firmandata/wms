<?php
echo form_open('material/inventory_putaway/insert_detail',
	array(
		'name'	=> 'material_inventory_putaway_form_detail',
		'id'	=> 'material_inventory_putaway_form_detail'
	)
);?>
<table>
	<tr>
		<td>
			<table class="form-table">
				<tbody>
					<tr><th width="70"><label for="material_inventory_putaway_form_detail_pallet">Pallet</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'pallet',
		'id' 		=> 'material_inventory_putaway_form_detail_pallet',
		'class'		=> 'required',
		'style'		=> "width:180px"
	)
);?>
						</td>
					</tr>
					<tr><th><label for="material_inventory_putaway_form_detail_m_gridto_code">Grid</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'm_gridto_code',
		'id' 		=> 'material_inventory_putaway_form_detail_m_gridto_code',
		'class'		=> 'required',
		'style'		=> "width:80px"
	)
);?>
							<button id="material_inventory_putaway_detail_suggestion_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
								<span class="ui-button-icon-primary ui-icon ui-icon-star"></span>
								<span class="ui-button-text">Suggestion</span>
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td rowspan="2" id="material_inventory_putaway_form_detail_counter_label" style="padding-left:50px; font-size:100px;">
			0
		</td>
	</tr>
	<tr>
		<td align="center">
<?php 
if (is_authorized('material/inventory_putaway', 'insert')){?>
			<button id="material_inventory_putaway_detail_process_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
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

<table id="material_inventory_putaway_detail_list_table"></table>
<div id="material_inventory_putaway_detail_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventory_putaway_detail_list_load_table('material_inventory_putaway_detail_list_table');
	
	jQuery("#material_inventory_putaway_form_detail").validate({
		submitHandler: function(form){
			jQuery("#material_inventory_putaway_form_detail").ajaxSubmit({
				dataType: "json",
				async : false,
				data : {
					m_inventory_putaway_id	: <?php echo $id;?>
				},
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					if (data.response == false)
					{
						jquery_show_message(data.value, null, "ui-icon-close", function(){
							material_inventory_putaway_detail_clear_field();
						});
					}
					else
					{
						jQuery("#material_inventory_putaway_detail_list_table").trigger('reloadGrid', [{current:true}]);
						material_inventory_putaway_detail_clear_field();
					}
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jQuery("#material_inventory_putaway_detail_suggestion_btn").click(function(e){
		e.preventDefault();
		
		jQuery.ajax({
			url: "<?php echo site_url('material/inventory_putaway/get_grid_default');?>",
			data: {
				pallet	: jQuery('#material_inventory_putaway_form_detail_pallet').val()
			},
			type: "GET",
			dataType: "json",
			error: jquery_ajax_error_handler,
			beforeSend: function(jqXHR, settings){
				jquery_blockui();
			},
			success: function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					if (data.data.length > 0)
					{
						var m_grid = data.data[0];
						jQuery('#material_inventory_putaway_form_detail_m_gridto_code').val(m_grid.code);
					}
				}
			},
			complete: function(jqXHR, textStatus){
				jquery_unblockui();
			}
		});
	});
	
	jQuery("#material_inventory_putaway_detail_process_btn").click(function(e){
		e.preventDefault();
		jQuery('#material_inventory_putaway_form_detail').submit();
	});
});

function material_inventory_putaway_detail_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_putaway/get_list_detail_json');?>", 
		postData : {
			id : <?php echo $id;?>
		},
		colNames: [
			'Inventory Putaway Id', 
			'Pallet',
			'Grid Id',
			'Grid',
			'Box',
			'Quantity',
			'Scan Date',
			''
		], 
		colModel: [
			{name:'m_inventory_putaway_id', index:'ipad.m_inventory_putaway_id', hidden:true},
			{name:'pallet', index:'ipad.pallet', width:250, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_gridto_id', index:'ipad.m_gridto_id', hidden:true},
			{name:'m_gridto_code', index:'gri.code', width:100, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box_to', index:'quantity_box_to', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_to', index:'quantity_to', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			jqgrid_column_date(table_id, {name:'created', index:'created', search:false}),
			{name:'cmd_action', index:'cmd_action', width:26, search:false, sortable:false}
		],
		afterInsertRow : function(rowid, rowdata, rowelem){
			jQuery("#" + table_id).jqGrid('setRowData', rowid, {
				cmd_action: 
					  '<button onclick="material_inventory_putaway_detail_list_remove(\'' + table_id + '\', ' + rowid.toString() + ')" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" style="height:20px;" title="Remove">'
					+ '	<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>'
					+ '	<span class="ui-button-text">Remove</span>'
					+ '</button>'
			});
		},
		pager: '#' + table_id + '_nav', 
		sortname: 'm_inventory_putawaydetail_id', 
		sortorder: "desc",
		loadComplete: function(data){
			var row_total = jQuery("#" + table_id).getGridParam('records');
			jQuery('#material_inventory_putaway_form_detail_counter_label').text(row_total);
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
	
	jQuery("#" + table_id).setGridHeight(250);
}

function material_inventory_putaway_detail_list_remove(table_id, id){
	jquery_show_confirm("Are your sure ?", function(){
		var row_data = jQuery("#material_inventory_putaway_detail_list_table").getRowData(id);
		
		jQuery.ajax({
			url: "<?php echo site_url('material/inventory_putaway/delete_detail');?>",
			type: "POST",
			dataType: "json",
			data : {
				m_inventory_putaway_id	: row_data.m_inventory_putaway_id,
				pallet					: row_data.pallet,
				m_gridto_id				: row_data.m_gridto_id
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

function material_inventory_putaway_detail_clear_field(){
	jQuery('#material_inventory_putaway_form_detail_pallet').val('');
	jQuery('#material_inventory_putaway_form_detail_pallet').focus();
	jQuery('#material_inventory_putaway_form_detail_m_gridto_code').val('');
}
</script>