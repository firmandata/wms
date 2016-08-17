<?php
$product_conditions = array_merge(array('' => ''), $this->config->item('product_conditions'));
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));
$product_types = array_merge(array('' => ''), $this->config->item('product_types'));
$product_casings = array_merge(array('' => ''), $this->config->item('product_casings'));?>

<div id="material_inventory_picklist_detail_form_tabs">
	<ul>
		<li><a href="#material_inventory_picklist_detail_form_tab_select">
				Select Order Out Product
				(<span id="material_inventory_picklist_form_detail_total_label" style="">0</span>)
			</a>
		</li>
		<li><a href="#material_inventory_picklist_detail_form_tab_enroll">Enroll From Inventory</a></li>
		<li><a href="#material_inventory_picklist_detail_form_tab_result">
				Pick List Result
				(<span id="material_inventory_picklist_form_detail_counter_label" style="">0</span>)
			</a>
		</li>
	</ul>
	<div id="material_inventory_picklist_detail_form_tab_select">
		<table id="material_inventory_picklist_detail_ref_list_table"></table>
		<div id="material_inventory_picklist_detail_ref_list_table_nav"></div>
	</div>
	<div id="material_inventory_picklist_detail_form_tab_enroll">
		<div id="material_inventory_picklist_detail_form_tab_enroll_tabs">
			<ul>
				<li><a href="#material_inventory_picklist_detail_form_tab_enroll_auto">Auto</a></li>
				<li><a href="#material_inventory_picklist_detail_form_tab_enroll_manual">Manual</a></li>
			</ul>
			<div id="material_inventory_picklist_detail_form_tab_enroll_auto">
<?php
echo form_open('material/inventory_picklist_pakan/insert_detail',
	array(
		'name'	=> 'material_inventory_picklist_form_detail',
		'id'	=> 'material_inventory_picklist_form_detail'
	)
);?>
				<table>
					<tr>
						<td colspan="2">
							<table class="form-table">
								<tbody>
									<tr><th width="100"><label for="material_inventory_picklist_form_detail_barcode">Barcode</label></th>
										<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'barcode',
		'id' 		=> 'material_inventory_picklist_form_detail_barcode',
		'style'		=> "width:350px"
	)
);?>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<table class="form-table">
								<tbody>
									<tr><th width="100"><label for="material_inventory_picklist_form_detail_pallet">Pallet</label></th>
										<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'pallet',
		'id' 		=> 'material_inventory_picklist_form_detail_pallet',
		'style'		=> "width:180px"
	)
);?>
										</td>
									</tr>
									<tr><th><label for="material_inventory_picklist_form_detail_grid_code">Location</label></th>
										<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'grid_code',
		'id' 		=> 'material_inventory_picklist_form_detail_grid_code',
		'style'		=> "width:80px"
	)
);?>
										</td>
									</tr>
									<tr><th><label for="material_inventory_picklist_form_detail_condition">Condition</label></th>
										<td>
<?php 
echo form_dropdown('condition', $product_conditions, '', 'id="material_inventory_picklist_form_detail_condition"');?>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td valign="top">
							<table class="form-table">
								<tbody>
									<tr><th width="100"><label for="material_inventory_picklist_form_detail_quantity">Quantity</label></th>
										<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'quantity',
		'id' 		=> 'material_inventory_picklist_form_detail_quantity',
		'class'		=> 'number',
		'style'		=> "width:70px"
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
if (is_authorized('material/inventory_picklist_pakan', 'insert')){?>
							<button id="material_inventory_picklist_detail_process_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
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
			</div>
			<div id="material_inventory_picklist_detail_form_tab_enroll_manual">
				<table id="material_inventory_picklist_detail_form_tab_enroll_manual_table"></table>
				<div id="material_inventory_picklist_detail_form_tab_enroll_manual_table_nav"></div>
				<br/>
				<div>
					<div style="float:right;">
						<button id="material_inventory_picklist_detail_form_tab_enroll_manual_process_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
							<span class="ui-button-icon-primary ui-icon ui-icon-check"></span>
							<span class="ui-button-text">Process</span>
						</button>
					</div>
					<div id="material_inventory_picklist_detail_form_tab_enroll_manual_table_selected_total" style="font-size:20px;">0</div>
				</div>
			</div>
		</div>
	</div>
	<div id="material_inventory_picklist_detail_form_tab_result">
		<table id="material_inventory_picklist_detail_list_table"></table>
		<div id="material_inventory_picklist_detail_list_table_nav"></div>
	</div>
</div>

<script type="text/javascript">
var material_inventory_picklist_detail_form_tab_select = false,
	material_inventory_picklist_detail_form_tab_select_reload = false,
	material_inventory_picklist_detail_form_tab_select_selected = null,
	material_inventory_picklist_detail_form_tab_enroll = false,
	material_inventory_picklist_detail_form_tab_result = false,
	material_inventory_picklist_detail_form_tab_result_reload = false;
var material_inventory_picklist_detail_form_tab_enroll_auto = false,
	material_inventory_picklist_detail_form_tab_enroll_manual = false;
	material_inventory_picklist_detail_form_tab_enroll_manual_reload = false;
jQuery(document).ready(function(){
	$("#material_inventory_picklist_detail_form_tabs").tabs({
		activate: function(event, ui){
			var tab_id = ui.newPanel.attr('id');
			if (tab_id == 'material_inventory_picklist_detail_form_tab_select')
			{
				if (!material_inventory_picklist_detail_form_tab_select)
				{
					material_inventory_picklist_detail_ref_list_load_table('material_inventory_picklist_detail_ref_list_table');
					material_inventory_picklist_detail_form_tab_select = true;
				}
				else if (!material_inventory_picklist_detail_form_tab_select_reload)
				{
					jQuery("#material_inventory_picklist_detail_ref_list_table").trigger('reloadGrid', [{current:true}]);
					material_inventory_picklist_detail_form_tab_select_reload = true;
				}
			}
			else if (tab_id == 'material_inventory_picklist_detail_form_tab_enroll')
			{
				var tab_enroll_tab_active = $("#material_inventory_picklist_detail_form_tab_enroll_tabs" ).tabs("option", "active");
				if (tab_enroll_tab_active == 1)
				{
					if (!material_inventory_picklist_detail_form_tab_enroll_manual)
					{
						material_inventory_picklist_detail_inventory_list_load_table('material_inventory_picklist_detail_form_tab_enroll_manual_table');
						material_inventory_picklist_detail_form_tab_enroll_manual = true;
					}
					else if (!material_inventory_picklist_detail_form_tab_enroll_manual_reload)
					{
						material_inventory_picklist_detail_ref_list_select_on();
						material_inventory_picklist_detail_form_tab_enroll_manual_reload = true;
					}
				}
				if (!material_inventory_picklist_detail_form_tab_enroll)
					material_inventory_picklist_detail_form_tab_enroll = true;
			}
			else if (tab_id == 'material_inventory_picklist_detail_form_tab_result')
			{
				if (!material_inventory_picklist_detail_form_tab_result)
				{
					material_inventory_picklist_detail_list_load_table('material_inventory_picklist_detail_list_table');
					material_inventory_picklist_detail_form_tab_result = true;
					material_inventory_picklist_detail_form_tab_result_reload = true;
				}
				else if (!material_inventory_picklist_detail_form_tab_result_reload)
				{
					jQuery("#material_inventory_picklist_detail_list_table").trigger('reloadGrid', [{current:true}]);
					material_inventory_picklist_detail_form_tab_result_reload = true;
				}
			}
		}
	});
	
	$("#material_inventory_picklist_detail_form_tab_enroll_tabs").tabs({
		activate: function(event, ui){
			var tab_id = ui.newPanel.attr('id');
			if (!material_inventory_picklist_detail_form_tab_enroll_auto && tab_id == 'material_inventory_picklist_detail_form_tab_enroll_auto')
			{
				material_inventory_picklist_detail_form_tab_enroll_auto = true;
			}
			else if (tab_id == 'material_inventory_picklist_detail_form_tab_enroll_manual')
			{
				if (!material_inventory_picklist_detail_form_tab_enroll_manual)
				{
					material_inventory_picklist_detail_inventory_list_load_table('material_inventory_picklist_detail_form_tab_enroll_manual_table');
					material_inventory_picklist_detail_form_tab_enroll_manual = true;
				}
				else if (!material_inventory_picklist_detail_form_tab_enroll_manual_reload)
				{
					material_inventory_picklist_detail_ref_list_select_on();
					material_inventory_picklist_detail_form_tab_enroll_manual_reload = true;
				}
			}
		}
	});
	
	material_inventory_picklist_detail_ref_list_load_table('material_inventory_picklist_detail_ref_list_table');
	material_inventory_picklist_detail_form_tab_select = true;
	
	jQuery("#material_inventory_picklist_form_detail").validate({
		submitHandler: function(form){
			var ref_id = jQuery("#material_inventory_picklist_detail_ref_list_table").getGridParam("selrow");
			var ref_row_data = jQuery("#material_inventory_picklist_detail_ref_list_table").getRowData(ref_id);
			
			jQuery("#material_inventory_picklist_form_detail").ajaxSubmit({
				dataType: "json",
				async : false,
				data : {
					m_inventory_picklist_id	: <?php echo $id;?>,
					c_orderoutdetail_id		: ref_row_data.id
				},
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					if (data.response == false)
					{
						jquery_show_message(data.value, null, "ui-icon-close", function(){
							// material_inventory_picklist_detail_clear_field();
						});
					}
					else
					{
						// if (data.value.inventory_lost)
						// {
							// jquery_show_message("Inventory not covered " + (data.value.inventory_lost.quantity * -1), null, "ui-icon-close", function(){
								// jQuery("#material_inventory_picklist_detail_list_table").trigger('reloadGrid', [{current:true}]);
								// material_inventory_picklist_detail_clear_field();
							// });
						// }
						// else
						// {
							material_inventory_picklist_detail_form_tab_result_reload = false;
							material_inventory_picklist_detail_form_tab_select_reload = false;
							material_inventory_picklist_detail_clear_field();
						// }
					}
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
					material_inventory_picklist_detail_show_total();
				}
			});
		}
	});
	
	jQuery("#material_inventory_picklist_detail_process_btn").click(function(e){
		e.preventDefault();
		jQuery('#material_inventory_picklist_form_detail').submit();
	});
	
	jQuery("#material_inventory_picklist_detail_form_tab_enroll_manual_process_btn").click(function(e){
		e.preventDefault();
		material_inventory_picklist_detail_process_manual();
	});
	
	material_inventory_picklist_detail_show_total();
});

function material_inventory_picklist_detail_ref_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_picklist_pakan/get_list_form_ref_json');?>", 
		colNames: [
			'Id', 
			'Product Id', 
			'Product Code', 
			'Name', 
			'Order Out Code', 
			'Type', 
			'Casing', 
			'Quantity',
			'UOM', 
			'Business Partner', 
			'External No',
			'Order Out Date',
			'Project Id',
			'Project'
		], 
		colModel: [
			{name:'id', index:'ood.id', hidden:true, frozen:true},
			{name:'m_product_id', index:'ood.m_product_id', hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:220, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_orderout_code', index:'oo.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_type', index:'pro.type', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_types);?>}
			},
			{name:'m_product_casing', index:'pro.casing', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_casings);?>}
			},
			{name:'quantity', index:'quantity', width:90, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'m_product_uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'c_businesspartner_name', index:'bp.name', width:180, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_orderout_external_no', index:'oo.external_no', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'c_orderout_orderout_date', index:'oo.orderout_date'}),
			{name:'c_project_id', index:'oo.c_project_id', hidden:true},
			{name:'c_project_name', index:'prj.name', width:150, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		onSelectRow: function(rowid, status, e){
			if (material_inventory_picklist_detail_form_tab_select_selected == null || material_inventory_picklist_detail_form_tab_select_selected != rowid)
			{
				material_inventory_picklist_detail_form_tab_enroll_manual_reload = false;
				material_inventory_picklist_detail_form_tab_select_selected = rowid;
			}
			material_inventory_picklist_detail_show_total();
		},
		gridComplete: function(){
			if (material_inventory_picklist_detail_form_tab_select_selected)
				jQuery('#' + table_id).setSelection(material_inventory_picklist_detail_form_tab_select_selected);
		},
		pager: '#' + table_id + '_nav', 
		sortname: 'ood.created', 
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
	jQuery("#" + table_id).setGridHeight(500);
}

function material_inventory_picklist_detail_ref_list_select_on(){
	if (!material_inventory_picklist_detail_form_tab_enroll_manual)
		return;
	
	jQuery('#material_inventory_picklist_detail_form_tab_enroll_manual_table').setGridParam({
		postData : material_inventory_picklist_detail_ref_list_get_param()
	});
	jQuery('#material_inventory_picklist_detail_form_tab_enroll_manual_table').trigger("reloadGrid", [{page:1}]);
}

function material_inventory_picklist_detail_ref_list_get_param(){
	var ref_id = jQuery("#material_inventory_picklist_detail_ref_list_table").getGridParam("selrow");
	if (ref_id)
	{
		var ref_row_data = jQuery("#material_inventory_picklist_detail_ref_list_table").getRowData(ref_id);
		if (ref_row_data)
		{
			return {
				m_product_id	: ref_row_data.m_product_id,
				c_project_id	: ref_row_data.c_project_id
			};
		}
		else
			return {};
	}
	else
		return {};
}

function material_inventory_picklist_detail_inventory_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		multiselect: true,
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
		url: "<?php echo site_url('material/inventory_picklist_pakan/get_form_inventory_list_json');?>", 
		postData : material_inventory_picklist_detail_ref_list_get_param(),
		colNames: [
			'Product Id', 
			'Grid Id',
			'Location',
			'Location Group',
			'Box',
			'Quantity',
			'Quantity',
			'Barcode',
			'Pallet',
			'Carton No',
			'Packed Date',
			'Packed Date',
			'Expired Date',
			'Expired Date',
			'Lot No',
			'Condition',
			'Receive Date',
			'Age',
			'Price Buy',
			'Quantity Per Box',
			'Project Id',
			'Project'
		], 
		colModel: [
			{name:'m_product_id', index:'pro.id', hidden:true, frozen:true},
			{name:'m_grid_id', index:'grd.id', hidden:true, frozen:true},
			{name:'m_grid_code', index:'grd.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_warehouse_name', index:'wh.name', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box_exist', index:'quantity_box_exist', width:80, frozen:true, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_exist', index:'quantity_exist', width:80, frozen:true, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity', index:'inv.quantity_exist', width:80, align:'center', search:false, sortable:false,
			 formatter:function(cellvalue, options, rowObject){
				var quantity_element_id = "material_inventory_picklist_detail_inventory_list_quantity_" + options.rowId;
				return '<input type="text" name="' + quantity_element_id + '" id="' + quantity_element_id + '" value="' + rowObject.quantity_exist + '" style="width:65px; text-align:right; height:14px;" class="number" onchange="material_inventory_picklist_detail_process_manual_show_total();" />';
			 }
			},
			{name:'barcode', index:'inv.barcode', width:180, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'pallet', index:'inv.pallet', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'carton_no', index:'inv.carton_no', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'packed_date', index:'inv.packed_date'}),
			{name:'packed_date_2', index:'inv.packed_date', hidden:true, 
			 formatter:function(cellvalue, options, rowObject){
				return (rowObject.packed_date ? rowObject.packed_date : '');
			 }
			},
			jqgrid_column_date(table_id, {name:'expired_date', index:'inv.expired_date'}),
			{name:'expired_date_2', index:'inv.expired_date', hidden:true, 
			 formatter:function(cellvalue, options, rowObject){
				return (rowObject.expired_date ? rowObject.expired_date : '');
			 }
			},
			{name:'lot_no', index:'inv.lot_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'condition', index:'inv.condition', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_conditions);?>}
			},
			{name:'received_date', index:'inv.received_date', hidden:true, 
			 formatter:function(cellvalue, options, rowObject){
				return (rowObject.received_date ? rowObject.received_date : '');
			 }
			},
			{name:'inventory_age', index:'inventory_age', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'price_buy', index:'inv.price_buy', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'quantity_per_box', index:'inv.quantity_per_box', hidden:true},
			{name:'c_project_id', index:'prj.id', hidden:true},
			{name:'c_project_name', index:'prj.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		gridComplete: function(){
			material_inventory_picklist_detail_process_manual_show_total();
		},
		onSelectRow: function(rowid, status, e){
			material_inventory_picklist_detail_process_manual_show_total();
		},
		onSelectAll: function(aRowids, status){
			material_inventory_picklist_detail_process_manual_show_total();
		},
		pager: '#' + table_id + '_nav'
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
	jQuery("#" + table_id).setGridHeight(350);
}

function material_inventory_picklist_detail_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory_picklist_pakan/get_list_form_json');?>", 
		postData : {
			id : <?php echo $id;?>
		},
		colNames: [
			'Id', 
			'Inventory Picklist Id', 
			'Order Out Detail Id', 
			'Product Id',
			'Product Code', 
			'Name', 
			'Barcode', 
			'Pallet',
			'Grid Id',
			'Location',
			'Type', 
			'Casing', 
			'Quantity',
			'UOM',
			'Condition',
			'Carton No',
			'Lot No',
			'Packed Date',
			'Packed Date',
			'Expired Date',
			'Expired Date',
			'Receive Date',
			'Age',
			'Price Buy',
			'Project Id',
			'Project',
			''
		], 
		colModel: [
			{name:'id', index:'ipld.id', hidden:true, frozen:true},
			{name:'m_inventory_picklist_id', index:'ipld.m_inventory_picklist_id', hidden:true, frozen:true},
			{name:'c_orderoutdetail_id', index:'ipld.c_orderoutdetail_id', hidden:true, frozen:true},
			{name:'m_product_id', index:'ood.m_product_id', hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:220, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'barcode', index:'ipld.barcode', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'pallet', index:'ipld.pallet', width:120, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_grid_id', index:'ipld.m_grid_id', hidden:true, frozen:true},
			{name:'m_grid_code', index:'gri.code', width:80, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_type', index:'pro.type', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_types);?>}
			},
			{name:'m_product_casing', index:'pro.casing', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_casings);?>}
			},
			{name:'quantity', index:'quantity', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'m_product_uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'condition', index:'ipld.condition', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_conditions);?>}
			},
			{name:'carton_no', index:'ipld.carton_no', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'lot_no', index:'ipld.lot_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'packed_date', index:'ipld.packed_date'}),
			{name:'packed_date_2', index:'ipld.packed_date', hidden:true, 
			 formatter:function(cellvalue, options, rowObject){
				return (rowObject.packed_date ? rowObject.packed_date : '');
			 }
			},
			jqgrid_column_date(table_id, {name:'expired_date', index:'ipld.expired_date'}),
			{name:'expired_date_2', index:'ipld.expired_date', hidden:true, 
			 formatter:function(cellvalue, options, rowObject){
				return (rowObject.expired_date ? rowObject.expired_date : '');
			 }
			},
			{name:'received_date', index:'ipld.received_date', hidden:true, 
			 formatter:function(cellvalue, options, rowObject){
				return (rowObject.received_date ? rowObject.received_date : '');
			 }
			},
			{name:'inventory_age', index:'inventory_age', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'price_buy', index:'ipld.price_buy', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'c_project_id', index:'ipld.c_project_id', hidden:true},
			{name:'c_project_name', index:'prj.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'cmd_action', index:'cmd_action', width:26, search:false, sortable:false}
		],
		afterInsertRow : function(rowid, rowdata, rowelem){
			jQuery("#" + table_id).jqGrid('setRowData', rowid, {
				cmd_action: 
					  '<button onclick="material_inventory_picklist_detail_list_remove(\'' + table_id + '\', ' + rowid.toString() + ')" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" style="height:20px;" title="Remove">'
					+ '	<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>'
					+ '	<span class="ui-button-text">Remove</span>'
					+ '</button>'
			});
		},
		pager: '#' + table_id + '_nav', 
		sortname: 'ipld.id', 
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
	jQuery("#" + table_id).setGridHeight(500);
}

function material_inventory_picklist_detail_list_remove(table_id, id){
	jquery_show_confirm("Are your sure ?", function(){
		var row_data = jQuery("#material_inventory_picklist_detail_list_table").getRowData(id);
		
		jQuery.ajax({
			url: "<?php echo site_url('material/inventory_picklist_pakan/delete_detail');?>",
			type: "POST",
			dataType: "json",
			data : {
				m_inventory_picklist_id	: row_data.m_inventory_picklist_id,
				c_orderoutdetail_id		: row_data.c_orderoutdetail_id,
				m_product_id			: row_data.m_product_id,
				m_grid_id				: row_data.m_grid_id,
				barcode					: row_data.barcode,
				pallet					: row_data.pallet,
				condition				: row_data.condition,
				carton_no				: row_data.carton_no,
				lot_no					: row_data.lot_no,
				packed_date				: row_data.packed_date_2,
				expired_date			: row_data.expired_date_2,
				received_date			: row_data.received_date,
				price_buy				: row_data.price_buy,
				c_project_id			: row_data.c_project_id,
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
				{
					jQuery("#" + table_id).trigger('reloadGrid', [{current:true}]);
					material_inventory_picklist_detail_form_tab_select_reload = false;
					material_inventory_picklist_detail_form_tab_enroll_manual_reload = false;
				}
			},
			complete: function(jqXHR, textStatus){
				jquery_unblockui();
				material_inventory_picklist_detail_show_total();
			}
		});
	});
}

function material_inventory_picklist_detail_clear_field(){
	jQuery('#material_inventory_picklist_form_detail_barcode').val('');
	jQuery('#material_inventory_picklist_form_detail_barcode').focus();
	jQuery('#material_inventory_picklist_form_detail_quantity').val('');
	jQuery('#material_inventory_picklist_form_detail_grid_code').val('');
	jQuery('#material_inventory_picklist_form_detail_pallet').val('');
	jQuery('#material_inventory_picklist_form_detail_condition').val('');
}

function material_inventory_picklist_detail_show_total(){
	var ref_id = jQuery("#material_inventory_picklist_detail_ref_list_table").getGridParam("selrow");
	var ref_row_data = jQuery("#material_inventory_picklist_detail_ref_list_table").getRowData(ref_id);
	
	jQuery.ajax({
		url: "<?php echo site_url('material/inventory_picklist_pakan/get_detail_counter');?>",
		data : {
			c_orderoutdetail_id		: ref_row_data.id,
			m_inventory_picklist_id	: <?php echo $id;?>
		},
		type: "GET",
		dataType: "json",
		error: jquery_ajax_error_handler,
		beforeSend: function(jqXHR, settings){
			var total_label = jQuery('#material_inventory_picklist_form_detail_total_label').text() + "...";
			jQuery('#material_inventory_picklist_form_detail_total_label').html(total_label);
		},
		success: function(data, textStatus, jqXHR){
			if (data.response == false)
				jquery_show_message(data.value, null, "ui-icon-close");
			else
			{
				var c_order_out = data.value.c_order_out;
				var m_inventory_picklistdetail = data.value.m_inventory_picklistdetail;
				
				var total_label = "Free : " + (c_order_out.quantity - c_order_out.quantity_used);
				var counter_label = "Allocated : " + m_inventory_picklistdetail.quantity;
				
				jQuery('#material_inventory_picklist_form_detail_total_label').text(total_label);
				jQuery('#material_inventory_picklist_form_detail_counter_label').text(counter_label);
			}
		}
	});
}

function material_inventory_picklist_detail_process_manual(){
	var records = new Array();
	var record_ids = jQuery("#material_inventory_picklist_detail_form_tab_enroll_manual_table").getGridParam('selarrrow');
	jQuery.each(record_ids, function(idx, value){
		var inventory_data = jQuery("#material_inventory_picklist_detail_form_tab_enroll_manual_table").getRowData(value);
		records.push({
			m_product_id	: inventory_data.m_product_id,
			m_grid_id 		: inventory_data.m_grid_id,
			pallet			: inventory_data.pallet,
			barcode			: inventory_data.barcode,
			carton_no		: inventory_data.carton_no,
			lot_no			: inventory_data.lot_no,
			condition		: inventory_data.condition,
			packed_date		: inventory_data.packed_date_2,
			expired_date	: inventory_data.expired_date_2,
			received_date	: inventory_data.received_date,
			price_buy		: inventory_data.price_buy,
			quantity		: jQuery('#material_inventory_picklist_detail_inventory_list_quantity_' + value).val()
		});
	});
	if (records.length > 0)
	{
		var ref_id = jQuery("#material_inventory_picklist_detail_ref_list_table").getGridParam("selrow");
		var ref_row_data = jQuery("#material_inventory_picklist_detail_ref_list_table").getRowData(ref_id);
		
		jQuery.ajax({
			url: "<?php echo site_url('material/inventory_picklist_pakan/insert_detail_manual');?>",
			type: "POST",
			dataType: "json",
			async : false,
			data : {
				m_inventory_picklist_id	: <?php echo $id;?>,
				c_orderoutdetail_id		: ref_row_data.id,
				records					: records
			},
			error: jquery_ajax_error_handler,
			beforeSend: function(jqXHR, settings){
				jquery_blockui();
			},
			success: function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					jQuery("#material_inventory_picklist_detail_form_tab_enroll_manual_table").trigger('reloadGrid', [{current:true}]);
					material_inventory_picklist_detail_form_tab_result_reload = false;
					material_inventory_picklist_detail_form_tab_select_reload = false;
				}
			},
			complete: function(jqXHR, textStatus){
				jquery_unblockui();
				material_inventory_picklist_detail_show_total();
			}
		});
	}
	else
		jquery_show_message("Please select the row data !", null, "ui-icon-alert");
}

function material_inventory_picklist_detail_process_manual_show_total(){
	var total_quantity = 0,
		total_quantity_box = 0;
	var record_ids = jQuery("#material_inventory_picklist_detail_form_tab_enroll_manual_table").getGridParam('selarrrow');
	jQuery.each(record_ids, function(idx, value){
		var inventory_data = jQuery("#material_inventory_picklist_detail_form_tab_enroll_manual_table").getRowData(value);
		var user_quantity = parseFloat(jQuery('#material_inventory_picklist_detail_inventory_list_quantity_' + value).val());
		total_quantity += user_quantity;
		
		var quantity = parseFloat(inventory_data.quantity_exist);
		var quantity_box = parseInt(inventory_data.quantity_box_exist);
		var quantity_per_box = parseFloat(inventory_data.quantity_per_box);
		if (quantity_per_box <= 0 && quantity_box > 0)
			quantity_per_box = quantity / quantity_box;
		
		if (quantity_per_box > 0)
			total_quantity_box += Math.ceil(user_quantity / quantity_per_box);
		else
			total_quantity_box += 1;
	});
	jQuery("#material_inventory_picklist_detail_form_tab_enroll_manual_table_selected_total").text("Selected Quantity : " + total_quantity);
}
</script>