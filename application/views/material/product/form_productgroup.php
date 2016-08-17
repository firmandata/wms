<table id="material_product_productgroup_list_table"></table>
<div id="material_product_productgroup_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	material_product_productgroup_list_load_table('material_product_productgroup_list_table');
});

function material_product_productgroup_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		pginput: false,
		rowNum: <?php echo $this->config->item('jqgrid_limit_per_page');?>, 
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('material/product/get_list_productgroup_json');?>", 
		editurl: "<?php echo site_url('material/productgroup/jqgrid_cud');?>",
		hidegrid: false,
		height: 300,
		colNames: [
			'Id', 
			'Code',
			'Name'
		], 
		colModel: [
			{name:'id', index:'id', key:true, hidden:true, frozen:true},
			{name:'code', index:'code', width:100, editable:true, editrules:{required:true}, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'name', index:'name', width:250, editable:true, editrules:{required:true}, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'name', 
		sortorder: "asc"
	});
	
	jQuery("#" + table_id).jqGrid('filterToolbar', {
		stringResult : true, 
		searchOperators : true
	});
	
	jQuery("#" + table_id).jqGrid('navGrid', '#' + table_id + '_nav', {
		/* -- Button Configuration -- */
<?php 
if (is_authorized('material/productgroup', 'insert') || is_authorized('material/productgroup', 'update'))
{?>
		add: true,
<?php 
} else {?>
		add: false,
<?php 
}
if (is_authorized('material/productgroup', 'insert') || is_authorized('material/productgroup', 'update'))
{?>
		edit: true,
<?php 
} else {?>
		edit: false,
<?php 
}
if (is_authorized('material/productgroup', 'delete'))
{?>
		del: true,
<?php 
} else {?>
		del: false,
<?php 
}?>
	},
	{
		/* -- Edit Configuration -- */
		afterSubmit: jqgrid_form_after_submit, 
		beforeSubmit: jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true,
		bottominfo: "*) Required"
	},
	{
		/* -- Add Configuration -- */
		afterSubmit : jqgrid_form_after_submit, 
		beforeSubmit : jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true,
		bottominfo: "*) Required"
	},
	{
		/* -- Delete Configuration -- */
		afterSubmit : jqgrid_form_after_submit, 
		beforeSubmit: jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true
	}
	);
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
}

function material_product_productgroup_form_submit(on_success){
	var id = jQuery("#material_product_productgroup_list_table").getGridParam("selrow");
	if (!id)
	{
		jquery_show_message("Please select the row data !", null, "ui-icon-alert");
		return;
	}
	
	jQuery.ajax({
		url: "<?php echo site_url('material/product/set_productgroup_by_ids');?>",
		data : {
			ids					: <?php echo json_encode($ids);?>,
			m_productgroup_id	: id
		},
		type: "POST",
		dataType: "json",
		async : false,
		error: jquery_ajax_error_handler,
		beforeSend: function(jqXHR, settings){
			jquery_blockui();
		},
		success: function(data, textStatus, jqXHR){
			on_success(data, textStatus, jqXHR);
		},
		complete: function(jqXHR, textStatus){
			jquery_unblockui();
		}
	});
}
</script>