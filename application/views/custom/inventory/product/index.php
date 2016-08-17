<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix content-commandbar">
<?php 
if (is_authorized('custom/inventory_product', 'insert')){?>
	<button id="custom_inventory_product_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('custom/inventory_product', 'update')){?>
	<button id="custom_inventory_product_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('custom/inventory_product', 'delete')){?>
	<button id="custom_inventory_product_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}?>
</div>

<table id="custom_inventory_product_list_table"></table>
<div id="custom_inventory_product_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	custom_inventory_product_list_load_table('custom_inventory_product_list_table');
	
	jQuery("#custom_inventory_product_new_btn").click(function(){
		jquery_dialog_form_open('custom_inventory_product_form_container', "<?php echo site_url('custom/inventory_product/form');?>", {
			form_action : "custom/inventory_product/insert"
		}, 
		function(form_dialog){
			custom_inventory_product_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#custom_inventory_product_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Product", 
			width : 500
		});
	});
	
	jQuery("#custom_inventory_product_edit_btn").click(function(){
		var id = jQuery("#custom_inventory_product_list_table").getGridParam("selrow");
		if (id)
		{
			jquery_dialog_form_open('custom_inventory_product_form_container', "<?php echo site_url('custom/inventory_product/form');?>", {
				form_action : "custom/inventory_product/update",
				id : id
			}, 
			function(form_dialog){
				custom_inventory_product_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#custom_inventory_product_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Product", 
				width : 500
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#custom_inventory_product_delete_btn").click(function(){
		var id = jQuery("#custom_inventory_product_list_table").getGridParam("selrow");
		if (id)
		{
			var _delete_url = "<?php echo site_url('custom/inventory_product/delete/@id');?>";
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
							jQuery("#custom_inventory_product_list_table").trigger('reloadGrid', [{current:true}]);
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
});

function custom_inventory_product_list_load_table(table_id){
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
		url: "<?php echo site_url('custom/inventory_product/get_list_json');?>", 
		editurl: "<?php echo site_url('custom/inventory_product/jqgrid_cud');?>",
		colNames: [
			'Id', 
			'SKU', 
			'Description',
			'Barcode Length',
			'Qty Start',
			'Qty End',
			'SKU Start',
			'SKU End',
			'Carton Start',
			'Carton End',
			'Date Packed Start',
			'Date Packed End'
		], 
		colModel: [
			{name:'id', index:'pro.id', key:true, hidden:true, frozen:true},
			{name:'sku', index:'pro.sku', width:150, frozen:true, editable:true, editrules:{required:true}, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'description', index:'pro.description', width:320, frozen:true, editable:true, editrules:{required:true}, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'barcode_length', index:'pro.barcode_length', width:100, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'qty_start', index:'pro.qty_start', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'qty_end', index:'pro.qty_end', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'sku_start', index:'pro.sku_start', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'sku_end', index:'pro.sku_end', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'carton_start', index:'pro.carton_start', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'carton_end', index:'pro.carton_end', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'date_packed_start', index:'pro.date_packed_start', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'date_packed_end', index:'pro.date_packed_end', width:80, formatter:'number', formatoptions:{decimalPlaces: 0}, align:'right', searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'pro.sku', 
		sortorder: "asc"
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
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -164));
}
</script>