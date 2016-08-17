<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix content-commandbar">
<?php 
if (is_authorized('material/productgroup', 'insert')){?>
	<button id="material_productgroup_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('material/productgroup', 'update')){?>
	<button id="material_productgroup_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('material/productgroup', 'delete')){?>
	<button id="material_productgroup_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}?>
</div>

<table id="material_productgroup_list_table"></table>
<div id="material_productgroup_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	material_productgroup_list_load_table('material_productgroup_list_table');
	
	jQuery("#material_productgroup_new_btn").click(function(){
		jQuery("#material_productgroup_list_table").jqGrid('editGridRow', 'new', material_productgroup_list_load_table_form_prop_add());
	});
	
	jQuery("#material_productgroup_edit_btn").click(function(){
		var id = jQuery("#material_productgroup_list_table").jqGrid('getGridParam', 'selrow');
		if (id != null)
			jQuery("#material_productgroup_list_table").jqGrid('editGridRow', id, material_productgroup_list_load_table_form_prop_edit());
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#material_productgroup_delete_btn").click(function(){
		var id = jQuery("#material_productgroup_list_table").jqGrid('getGridParam', 'selrow');
		if (id != null)
			jQuery("#material_productgroup_list_table").jqGrid('delGridRow', id, material_productgroup_list_load_table_form_prop_del());
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
});

function material_productgroup_list_load_table_form_prop_add(){
	return {
		/* -- Add Configuration -- */
		afterSubmit : jqgrid_form_after_submit, 
		beforeSubmit : jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true,
		bottominfo: "*) Required"
	};
}

function material_productgroup_list_load_table_form_prop_edit(){
	return {
		/* -- Edit Configuration -- */
		afterSubmit: jqgrid_form_after_submit, 
		beforeSubmit: jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true,
		bottominfo: "*) Required"
	};
}

function material_productgroup_list_load_table_form_prop_del(){
	return {
		/* -- Delete Configuration -- */
		afterSubmit : jqgrid_form_after_submit, 
		beforeSubmit: jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true
	};
}

function material_productgroup_list_load_table(table_id){
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
		url: "<?php echo site_url('material/productgroup/get_list_json');?>", 
		editurl: "<?php echo site_url('material/productgroup/jqgrid_cud');?>",
		hidegrid: false,
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
if (is_authorized('material/productgroup', 'insert'))
{?>
		add: true,
<?php 
} else {?>
		add: false,
<?php 
}
if (is_authorized('material/productgroup', 'update'))
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
	material_productgroup_list_load_table_form_prop_edit(),
	material_productgroup_list_load_table_form_prop_add(),
	material_productgroup_list_load_table_form_prop_del()
	);
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id)) - 23);
}
</script>