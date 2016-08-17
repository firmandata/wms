<table id="material_category_list_table"></table>
<div id="material_category_list_table_nav"></div>
<script type="text/javascript">
jQuery(function(){
	material_category_list_load_table('material_category_list_table');
});

function material_category_list_load_table(table_id){
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
		url: "<?php echo site_url('material/category/get_list_json');?>", 
		editurl: "<?php echo site_url('material/category/jqgrid_cud');?>",
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
if (is_authorized('material/category', 'insert'))
{?>
		add: true,
<?php 
} else {?>
		add: false,
<?php 
}
if (is_authorized('material/category', 'update'))
{?>
		edit: true,
<?php 
} else {?>
		edit: false,
<?php 
}
if (is_authorized('material/category', 'delete'))
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
</script>