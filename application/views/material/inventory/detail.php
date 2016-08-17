<?php
$product_conditions = array_merge(array('' => ''), $this->config->item('product_conditions'));
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));?>
<div class="content-right-header-toolbar ui-state-default ui-corner-all ui-helper-clearfix">
	<div style="float:left;">
		<button id="material_inventory_detail_excel_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
			<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
			<span class="ui-button-text">Excel</span>
		</button>
	</div>
	<div style="float:right; height:100%;">
<?php 
echo form_open('material/inventory/detail',
	array(
		'name'	=> 'material_inventory_detail_list_form',
		'id'	=> 'material_inventory_detail_list_form',
		'style'	=> "height:100%;"
	)
);?>
		<div style="margin-top:3px; margin-right:8px;">
<?php 
echo form_checkbox(
	array(
		'name'		=> 'is_show_empty',
		'id'		=> 'material_inventory_detail_list_form_is_show_empty',
		'value'		=> 1,
		'checked'	=> FALSE,
		'style'		=> "vertical-align:middle;"
    )
);?>
			<label for="material_inventory_detail_list_form_is_show_empty">Show Empty</label>
		</div>
<?php 
echo form_close();?>
	</div>
</div>

<table id="material_inventory_detail_list_table"></table>
<div id="material_inventory_detail_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	material_inventory_detail_list_load_table('material_inventory_detail_list_table');
	
	jQuery('#material_inventory_detail_list_form').validate({
		submitHandler: function(form){
			jQuery('#material_inventory_detail_list_table').setGridParam({
				postData : material_inventory_detail_list_get_param()
			});
			jQuery('#material_inventory_detail_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery("#material_inventory_detail_excel_btn").click(function(e){
		e.preventDefault();
		
		var filters = jQuery('#material_inventory_detail_list_table').jqGrid('getGridParam', 'postData');
		filters.rows = 0;
		filters.page = 0;
		
		var params = jQuery.param(filters);
		document.location.href = "<?php echo site_url('material/inventory/get_detail_list_excel');?>?" + params;
	});
	
	jQuery('#material_inventory_detail_list_form_is_show_empty').change(function(){
		jQuery('#material_inventory_detail_list_form').submit();
	});
});

function material_inventory_detail_list_load_table(table_id){
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
		url: "<?php echo site_url('material/inventory/get_detail_list_json');?>", 
		postData : material_inventory_detail_list_get_param(),
		colNames: [
			'Product Id', 
			'Product Code', 
			'Product Name',
			'Grid',
			'Warehouse',
			'Product Group',
			'Exist',
			'Allocated',
			'Picked',
			'Onhand',
			'Exist',
			'Allocated',
			'Picked',
			'Onhand',
			'UOM',
			'Pack',
			'Barcode',
			'Pallet',
			'Carton No',
			'Packed Date',
			'Expired Date',
			'Lot No',
			'Condition',
			'Age',
			'Project'
		], 
		colModel: [
			{name:'m_product_id', index:'pro.id', hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_name', index:'pro.name', width:150, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_grid_code', index:'grd.code', width:90, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_warehouse_name', index:'wh.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_productgroup_name', index:'prog.name', width:110, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'quantity_box_exist', index:'quantity_box_exist', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_box_allocated', index:'quantity_box_allocated', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_box_picked', index:'quantity_box_picked', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_box_onhand', index:'quantity_box_onhand', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'quantity_exist', index:'quantity_exist', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity_allocated', index:'quantity_allocated', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity_picked', index:'quantity_picked', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'quantity_onhand', index:'quantity_onhand', width:80, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false},
			{name:'m_product_uom', index:'pro.uom', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_uoms);?>}
			},
			{name:'m_product_pack', index:'pro.pack', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'barcode', index:'inv.barcode', width:180, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'pallet', index:'inv.pallet', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'carton_no', index:'inv.carton_no', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_date(table_id, {name:'packed_date', index:'inv.packed_date'}),
			jqgrid_column_date(table_id, {name:'expired_date', index:'inv.expired_date'}),
			{name:'lot_no', index:'inv.lot_no', width:90, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'condition', index:'inv.condition', width:100,
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($product_conditions);?>}
			},
			{name:'inventory_age', index:'inventory_age', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', search:false},
			{name:'c_project_name', index:'prj.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		// sortname: 'pro.code', 
		// sortorder: "asc"
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
			{startColumnName: 'quantity_box_exist', numberOfColumns: 4, titleText: 'Box'},
			{startColumnName: 'quantity_exist', numberOfColumns: 4, titleText: 'Quantity'}
		]
	});

	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -281));
}

function material_inventory_detail_list_get_param(){
	return {
		is_show_empty : (jQuery('#material_inventory_detail_list_form_is_show_empty').is(":checked") ? 1 : 0)
	};
}
</script>